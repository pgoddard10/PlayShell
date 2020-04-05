<?php

require_once('classes/models/Role_Model.php');
//require_once('classes/controllers/Staff_Controller.php');
require_once('classes/models/Staff_Model.php');

/**
 * Short description of class Staff_Controller
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Staff_Controller
{
    private $staff_model = null;
    public $role_model = null;
    public $all_staff = null;

    /**
     * Short description of method __construct
     * @param  String db_file
     */
    function __construct() {
        $this->staff_model = new Staff_Model();
        $this->role_model = new Role_Model();
    }

    /**
     * Short description of method create_new
     *
     * @param  array<> staff_data
     * @return Integer
     */
    public function create_new($first_name, $last_name, $username, $password, $repeat_password, $email, $roles)
    {
        $returnValue = -1;
        if($password != $repeat_password) $returnValue =-2; //password mis-match
        else {
            $password = password_hash($password, PASSWORD_DEFAULT); //encrypt password
            if($this->staff_model->create_new($first_name, $last_name, $username, $password, $repeat_password, $email, $roles)==0) $returnValue = 0;
        }
        return $returnValue;
    }

    /**
     * Short description of method edit
     *
     * @param  array<> staff_data
     * @return Integer
     */
    public function edit($staff_id, $first_name, $last_name, $password, $repeat_password, $email, $active, $roles)
    {
        $this->staff_model->populate_from_db($staff_id);
        $returnValue = -1; //unknown error
        if($password != $repeat_password) $returnValue =-2; //password mis-match
        else {
            $password = password_hash($password, PASSWORD_DEFAULT); //encrypt password
            //check to see if this person is staff DB manager in DB
            $staff_db_mgr = false;
            if($this->staff_model->roles) { //if this person has any roles
                foreach($this->staff_model->roles as $role) { //loop through each of the existing roles
                    if($role['role_id']==STAFF_DB_MANAGER) { //check if any are the staff DB manager
                        $staff_db_mgr = true;
                        break;
                    }
                }
            }
            if(($staff_db_mgr) && ($this->staff_model->total_num_active_staff_with_role(STAFF_DB_MANAGER)==1) && (!in_array(STAFF_DB_MANAGER,$roles))) { //check how many staffDBmanagers exist in total
                $returnValue = -3; //cannot remove the role for last DB manager
            }
            else {
                if($this->staff_model->edit($staff_id, $first_name, $last_name, $password, $repeat_password, $email, $active)==0) {
                    if($this->staff_model->edit_roles($roles)==0)
                        $returnValue = 0;
                    else $returnValue = -5; //unable to edit roles
                }
                else $returnValue = -4; //unable to edit staff details
            }
        }
        return $returnValue;
    }

    /**
     * Short description of method deactivate
     *
     * @param  staff_id
     * @return Integer
     */
    public function deactivate($staff_id)
    {
        $returnValue = -1;
        //check to see if this person is staff DB manager in DB
        $staff_db_mgr = false;
        if($this->staff_model->roles) { //if this person has any roles
            foreach($this->staff_model->roles as $role) { //loop through each of the existing roles
                if($role['role_id']==STAFF_DB_MANAGER) { //check if any are the staff DB manager
                    $staff_db_mgr = true;
                    break;
                }
            }
        }
        if(($staff_db_mgr) && ($this->staff_model->total_num_active_staff_with_role(STAFF_DB_MANAGER)==1) && (!in_array(STAFF_DB_MANAGER,$roles))) { //check how many staffDBmanagers exist in total
            $returnValue = -3; //cannot remove the role for last DB manager
        }
        else if($this->staff_model->deactivate($staff_id)==0) {
            $returnValue = 0;
        }
        return $returnValue;
    }

    /**
     * Short description of method populate_all_staff
     *
     */
    public function populate_all_staff()
    {
        $this->all_staff = null;
        $model = new Staff_Model();
        $staff_ids = $model->get_all_staff_ids();
        //print('<pre>'.print_r($usernames,true).'</pre>');
        foreach($staff_ids as $id) {
            $staff_member = new Staff_Model();
            $staff_member->populate_from_db($id[0]);
            $this->all_staff[] = $staff_member;
        }
        //print('hello: <pre>'.print_r($this->all_staff,true).'</pre>');
    }


    /**
     * Short description of method login
     *
     */
    public function login($username,$password)
    {
        $returnValue = -1;
        $staff_id = $this->staff_model->get_id_from_username($username);
        if($staff_id > 0) {
            $staff = $this->staff_model->populate_from_db($staff_id);
            if(password_verify($password,$this->staff_model->get_password()) && $this->staff_model->active==1) {
                $returnValue = 0;
                $this->staff_model->populate_from_db($staff_id);
                $_SESSION["username"] = $username;
            }
        }
        return $returnValue;
    }


    /**
     * Short description of method has_session
     */
    public function has_session()
    {
        $returnValue = false;
        if(isset($_SESSION['username'])){
            $staff_id = $this->staff_model->get_id_from_username($_SESSION['username']);
            if($staff_id > 0) {
                $this->staff_model->populate_from_db($staff_id);
                $returnValue = true;
            }
        }
        return $returnValue;
    }

    /**
     * Short description of method has_role
     *
     */
    public function has_role($role_id) {
        $returnValue = false;
        if($this->staff_model->roles) { //if this person has any roles
            foreach($this->staff_model->roles as $role) { //loop through each of the existing roles
                if($role['role_id']==$role_id) { //check if any are the staff DB manager
                    $returnValue = true;
                    break;
                }
            }
        }
        return $returnValue;
    }

    /**
     * Short description of method get_name
     */
    public function get_display_name()
    {
        return $this->staff_model->display_name;
    }

} /* end of class Staff_Controller */

?>