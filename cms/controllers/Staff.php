<?php

class Staff {

    private $model;

    public $username = "";
    public $staff_id = "";
    public $first_name = "";
    public $last_name = "";
    public $email = "";
    public $display_name = "";
    public $roles = "";
    public $active = 0;

    function __construct($model) {
        $this->model = $model;
    }
	public function hash_password($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    public function set_session(){
        $_SESSION['username'] = $this->username;
    }
    public function login($username,$password) {
        $staff = $this->model->select_staff_details($username);
        if($username==$staff['username'] && password_verify($password,$staff['password']) && $staff['active']==1) {
            $this->username = $username;
            $this->set_session();
            $this->populate_details($staff['username']);
            return true;
        }
        else {
            header('Location: login.php?invalid_login');
            exit;
        }
    }
    public function populate_details($username){
        $staff = $this->model->select_staff_details($username);
        $this->staff_id = $staff['staff_id'];
        $this->first_name = $staff['first_name'];
        $this->last_name = $staff['last_name'];
        $this->username = $staff['username'];
        $this->display_name = $staff['first_name'].' '.$staff['last_name'];
        $this->email = $staff['email'];
        $this->roles = $this->model->select_active_roles($staff['staff_id']);
        $this->active = $staff['active'];
    }
    public function create_new($first_name,$last_name,$username,$password,$email,$role) {
        if($this->model->insert_new_staff($first_name,$last_name,$username,$this->hash_password($password),$email,$role)){
          $action_message = "Successfully created ".$first_name.' '.$last_name;
          $action_success = true;
        }
        else{
          $action_message = "Unable to create ".$first_name.' '.$last_name;
          $action_success = false;
        }
        return array("success"=>$action_success,"message"=>$action_message);
    }
    public function edit($staff_id,$first_name,$last_name,$username,$password,$email,$active,$role) {
        if(in_array(STAFF_DB_MANAGER,$this->model->select_active_roles($staff_id)) && $this->model->number_of_roles(1)<=1) { //if there is 1 or less Staff Database Managers left, do not delete
          $action_message = "You cannot remove the Staff Database Manager role from the last Staff Database Manager";
          $action_success = false;
        }
        else {
          $replace_password=false;
          if(strlen($password)>8) $replace_password=true; //only replace password if one exists (i.e. is greated than 8 characters)
          if($this->model->update_staff($staff_id,$first_name,$last_name,$username,$replace_password,$this->hash_password($password),$email,$active)){
            $this->model->delete_roles_for_staff($staff_id);
            foreach($role as $role_id) {
              $this->model->insert_staff_role($staff_id,$role_id);
            }
            $action_message = "Saved changes for ".$first_name.' '.$last_name;
            $action_success = true;
          }
          else{
            $action_message = "Unable to edit ".$first_name.' '.$last_name;
            $action_success = false;
          }
        }
        return array("success"=>$action_success,"message"=>$action_message);
    }
    public function deactivate($staff_id) {
      if(in_array(STAFF_DB_MANAGER,$this->model->select_active_roles($staff_id)) && $this->model->number_of_roles(1)<=1) { //if there is 1 or less Staff Database Managers left, do not deactivate
        $action_message = "You cannot deactivate the last Staff Database Manager";
        $action_success = false;
      }
      else {
        if($this->model->deactivate_staff($staff_id)){
          $this->model->delete_roles_for_staff($staff_id);
          $action_message = "Successfully deactivated ".$first_name.' '.$last_name;
          $action_success = true;
        }
        else{
          $action_message = "Unable to deactivate ".$first_name.' '.$last_name;
          $action_success = false;
        }
      }
      return array("success"=>$action_success,"message"=>$action_message);
    }
}

?>