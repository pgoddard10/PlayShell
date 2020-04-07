<?php

require_once('classes/models/Staff_Model.php');

/**
 * Short description of class Authenticate_Controller
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Authenticate_Controller
{
    private $staff_model = null;

    /**
     * Short description of method __construct
     * @param  String db_file
     */
    function __construct() {
        $this->staff_model = new Staff_Model();
    }

    /**
     * Short description of method login
     *
     */
    public function login($username,$password)
    {
        $returnValue = -1;
        $username = strtolower($username);
        $staff_id = $this->staff_model->get_id_from_username($username);
        if($staff_id > 0) {
            $this->staff_model->populate_from_db($staff_id);
            if(password_verify($password,$this->staff_model->get_password()) && $this->staff_model->active==1) {
                $returnValue = 0;
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
    
    /**
     * Short description of method get_staff_id
     */
    public function get_staff_id()
    {
        return $this->staff_model->staff_id;
    }

} /* end of class Staff_Controller */

?>