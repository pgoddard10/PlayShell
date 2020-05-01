<?php
require_once('classes/models/Staff_Model.php');

/**
 * Class Authenticate_Controller
 * Responsible for the Authentication logic
 *
 * @author	Paul Goddard
 * 			paul2.goddard@live.uwe.ac.uk
 * 			https://github.com/pgoddard10/
 * 			https://www.linkedin.com/in/pgoddard10/
 * 			https://twitter.com/pgoddard10
 * @date Spring 2020 
 */
class Authenticate_Controller
{
    private $staff_model = null; //instance of the Staff_Model class

	/**
	 * method __construct()
	 * The constructor method, always called by default when an instance of Authenticate_Controller is created.
     * Sets up a new instance of the Staff_Model class for use throughout this class.
	 */
    function __construct() {
        $this->staff_model = new Staff_Model();
    }

	/**
	 * method login()
     * does what is says on the tin - matches user input username & password for that in the database
	 * @param  String $username
	 * @param  String $password
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function login($username,$password)
    {
        $returnValue = -1; //unknown error
        $username = strtolower($username);
        $staff_id = $this->staff_model->get_id_from_username($username); //switch username to staff ID
        if($staff_id > 0) {
            $this->staff_model->populate_from_db($staff_id); //setup the staff model appropriately
            if(password_verify($password,$this->staff_model->get_password()) && $this->staff_model->active==1) {
                //confirm password matches the database and set the session
                $returnValue = 0;
                $_SESSION["username"] = $username;
            }
        }
        return $returnValue;
    }


	/**
	 * method has_session()
     * checks whether a PHP session is active
	 * @return Boolean $returnValue - true/false
	 */
    public function has_session()
    {
        $returnValue = false;
        if(isset($_SESSION['username'])){
            $staff_id = $this->staff_model->get_id_from_username($_SESSION['username']); //switch username to staff ID
            if($staff_id > 0) {
                $this->staff_model->populate_from_db($staff_id); //setup the staff model appropriately
                $returnValue = true;
            }
        }
        return $returnValue;
    }

	/**
	 * method has_role()
	 * Checks whether the role provided matches one assigned to this staff member in the database
	 * @param Integer $role_id
	 * @return Boolean $returnValue - true/false
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
	 * method get_display_name()
	 * gets the display name from the Staff_Model object
	 * @return String $this->staff_model->display_name - returns the display name from the Staff_Model object
	 */
    public function get_display_name()
    {
        return $this->staff_model->display_name;
    }
    
	/**
	 * method get_staff_id()
	 * gets the display name from the Staff_Model object
	 * @return String $this->staff_model->staff_id - returns the taff_id from the Staff_Model object
	 */
    public function get_staff_id()
    {
        return $this->staff_model->staff_id;
    }

} /* end of class Authenticate_Controller */

?>