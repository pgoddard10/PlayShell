<?php
/**
 * Class Staff_Controller
 * Responsible for handling the logic for processing staff
 *
 * @author	Paul Goddard
 * 			paul2.goddard@live.uwe.ac.uk
 * 			https://github.com/pgoddard10/
 * 			https://www.linkedin.com/in/pgoddard10/
 * 			https://twitter.com/pgoddard10
 * @date Spring 2020 
 */

require_once('classes/models/Role_Model.php');
require_once('classes/models/Staff_Model.php');


class Staff_Controller
{
    private $staff_model = null;
    public $role_model = null;
    public $all_staff = null;

	/**
	 * method __construct()
	 * constructor that sets up the Models
	 */
    function __construct() {
        $this->staff_model = new Staff_Model();
        $this->role_model = new Role_Model();
    }


    /**
     * method sanitise_string()
     * Takes a string and performs sanitising techniques to help avoid xss attacks etc.
     * 
     * @param  String data
     * @param  Bool isemail
     * @return String data
     */
    private function sanitise_string($data,$isemail=false) {
        $data = filter_var($data, FILTER_SANITIZE_STRING);
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        if($isemail) {
            $data = filter_var($data, FILTER_VALIDATE_EMAIL); //if the email address is not valid, just don't save it as it's not a required field
            $data = strtolower($data); //convert all emails to lowercase because emails are not case sensitive
        }
        return $data;
    }

	/**
	 * method JSONify_All_Staff()
	 * Loops through the $all_staff array (which contains Staff_model objects) and turns into an array. The json_encode function turns the array into a JSON object
     * @return JSON String $data - all Item data as JSON obj
	 */
    public function JSONify_All_Staff()
    {
        $data = array();
        if(count($this->all_staff)<=0) return '{"data": []}'; //empty JSON for datatables to read correctly.
        foreach($this->all_staff as $staff_member=>$details) {
            $this_staff = array();
            $this_staff['name'] = $details->display_name;
            $this_staff['username'] = $details->username;
            $this_staff['email'] = $details->email;
            $this_staff['roles'] = null;
            if($details->roles) {
                foreach($details->roles as $role) {
                    $this_staff['roles'] = $this_staff['roles'].$role['name'].'<br />';
                }
            }
            else {
                $this_staff['roles'] = "[No assigned roles]";
            }
            if($details->active==1)
                $this_staff['active'] = 'Yes';
            else
                $this_staff['active'] = 'No';
            $this_staff['buttons'] = "<a href='#' data-toggle='modal' data-id='$details->staff_id' class='editModalBox btn-circle btn-sm btn-primary' data-target='#editModalCenter'><i class='fas fa-edit'></i></a>";
            $this_staff['buttons'] = $this_staff['buttons'] . " <a href='#' data-toggle='modal' data-id='$details->staff_id' class='deleteModalBox btn-circle btn-sm btn-primary' data-target='#deleteModalCenter'><i class='fas fa-trash'></i></a>";
            $data["data"][] = $this_staff;
        }
        return json_encode($data, JSON_HEX_APOS|JSON_PRETTY_PRINT);
    }

    
    public function JSONify_staff_details() {
        $staff_id = filter_var($_GET['staff_id'], FILTER_VALIDATE_INT);
        $this->staff_model->populate_from_db($staff_id);
        $this_staff = array();
        $this_staff['display_name'] = $this->staff_model->display_name;
        $this_staff['first_name'] = $this->staff_model->first_name;
        $this_staff['last_name'] = $this->staff_model->last_name;
        $this_staff['username'] = $this->staff_model->username;
        $this_staff['email'] = $this->staff_model->email;
        $this_staff['roles'] = $this->staff_model->roles;
        $this_staff['active'] = $this->staff_model->active;
        $data["data"][] = $this_staff;
        return json_encode($data, JSON_HEX_APOS|JSON_PRETTY_PRINT);
    }

    
    /**
     * method check_for_duplicate()
     * Checks that the submitted data from the form doesn't already exist.
     * The purpose is to minimise duplicate staff being created
     * 
     *  @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers
     */
    public function check_for_duplicate() {
        $returnValue = 0;// no duplicate found
        
        if(isset($_GET['staff_id'])) $staff_id = filter_var($_GET['staff_id'], FILTER_VALIDATE_INT);
        $email = $this->sanitise_string($_GET['email'],true);
        if(isset($_GET['username'])) $username = $this->sanitise_string($_GET['username']);

        foreach($this->all_staff as $staff_member=>$details) {
            if((!isset($_GET['staff_id']) || ($staff_id != $details->staff_id))) {
                if($email!="") {
                    if($email == $details->email) return -6;
                }
                if(isset($_GET['username'])) {
                    if($username == $details->username) return -7;
                }
            }
        }
    }

	/**
	 * method create_new()
	 * Sanitises the form data and calls the model, which creates a new Staff in the database
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function create_new()
    {
        $returnValue = -1;
        $returnValue = $this->check_for_duplicate();
        if($returnValue==0) {
            if(isset($_GET['roles'])){
                $roles = $_GET['roles'];
            }
            else {
                $roles = array();
            }
            $first_name = $this->sanitise_string($_GET['first_name']);
            $last_name = $this->sanitise_string($_GET['last_name']);
            $username = $this->sanitise_string($_GET['username']);
            $password  = $_GET['password'];
            $repeat_password  = $_GET['repeat_password'];
            $email = $this->sanitise_string($_GET['email'],true);

            $username = strtolower($username);
            if($password != $repeat_password) $returnValue =-2; //password mis-match
            else {
                $password = password_hash($password, PASSWORD_DEFAULT); //encrypt password
                //now that everything has been checked and filter, pass data to the model for database interaction
                if($this->staff_model->create_new($first_name, $last_name, $username, $password, $repeat_password, $email, $roles)==0) $returnValue = 0;
            }
        }
        return $returnValue;
    }

	/**
	 * method edit()
	 * Sanitises the form data and calls the model, which edits the Staff in the database with the new values
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function edit()
    {
        $returnValue = -1; //unknown error

        $returnValue = $this->check_for_duplicate();
        if($returnValue==0) {
            if(isset($_GET['roles'])){
                $roles = $_GET['roles'];
            }
            else {
                $roles = array();
            }
            $staff_id = filter_var($_GET['staff_id'], FILTER_VALIDATE_INT);
            $first_name = $this->sanitise_string($_GET['first_name']);
            $last_name = $this->sanitise_string($_GET['last_name']);
            $password  = $_GET['password'];
            $repeat_password  = $_GET['repeat_password'];
            $email = $this->sanitise_string($_GET['email'],true);
            $active = filter_var($_GET['active'], FILTER_VALIDATE_INT);

            $this->staff_model->populate_from_db($staff_id);
            if($password != $repeat_password) $returnValue =-2; //password mis-match
            else {
                if(strlen($password)>8) { //only replace password if one was provided
                    $password = password_hash($password, PASSWORD_DEFAULT); //encrypt password
                }
                else {
                    $password = null;
                }
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
                    //now that everything has been checked and filter, pass data to the model for database interaction
                    if($this->staff_model->edit($staff_id, $first_name, $last_name, $password, $email, $active)==0) {
                        if($this->staff_model->edit_roles($roles)==0)
                            $returnValue = 0;
                        else $returnValue = -5; //unable to edit roles
                    }
                    else $returnValue = -4; //unable to edit staff details
                }
            }
        }
        return $returnValue;
    }

	/**
	 * method delete()
	 * deletes the Staff with the referenced ID from the database (via the model)
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function delete()
    {
        $returnValue = -1;
        
        $staff_id = filter_var($_GET['staff_id'], FILTER_VALIDATE_INT);
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
        else if($this->staff_model->delete($staff_id)==0) {
            $returnValue = 0;
        }
        return $returnValue;
    }

	/**
	 * method populate_all_staff()
	 * sets up the $all_staff array (which contains Staff_Model objects) and turns into an array. 
	 */
    public function populate_all_staff()
    {
        $this->all_staff = null;
        $model = new Staff_Model();
        $staff_ids = $model->get_all_staff_ids();
        foreach($staff_ids as $id) {
            $staff_member = new Staff_Model();
            $staff_member->populate_from_db($id[0]);
            $this->all_staff[] = $staff_member;
        }
    }


} /* end of class Staff_Controller */

?>