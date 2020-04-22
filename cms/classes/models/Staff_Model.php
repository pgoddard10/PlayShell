<?php
/**
 * Class Staff_Model
 * Responsible for database interaction
 *
 * @author	Paul Goddard
 * 			paul2.goddard@live.uwe.ac.uk
 * 			https://github.com/pgoddard10/
 * 			https://www.linkedin.com/in/pgoddard10/
 * 			https://twitter.com/pgoddard10
 * @date Spring 2020 
 */
require_once('classes/models/Role_Model.php');

class Staff_Model
{
    private $db_file = DATABASE_FILE;
    public $username = null;
    public $staff_id = null;
    public $first_name = null;
    public $last_name = null;
    public $display_name = null;
    public $email = null;
    public $active = null;
    public $roles = array();
    private $password = null;
    private $role_model = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     * @param  String db_file
     */
    function __construct() {
        //$this->staff_model = new Staff_Model();
        $this->role_model = new Role_Model();
    }

    /**
	 * method get_all_staff_ids()
	 * gets all staff_id from the item table
	 * @return array(int) $returnValue - an array of staff_id
	 */
    public function get_all_staff_ids()
    {
        $returnValue = null;
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT staff_id FROM staff");
            $staff = $stm->execute();
            while($row = $staff->fetchArray()) {
                $returnValue[] = $row;
            }
        }
        return $returnValue;
    }

	/**
	 * method populate_from_db()
	 * takes the id provided populates this model from the database with it's details
	 * @param Integer $staff_id
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function populate_from_db($staff_id)
    {
        $returnValue = -1; //unknown error
		$this->staff_id = $staff_id;
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT staff_id, username, password, first_name, last_name, email, active FROM staff WHERE staff_id = :staff_id");
			$stm->bindParam(':staff_id', $staff_id);
            $results = $stm->execute();
            if($staff = $results->fetchArray()) {
                $this->username = $staff['username'];
                $this->password = $staff['password'];
                $this->first_name = $staff['first_name'];
                $this->last_name = $staff['last_name'];
                $this->display_name = $staff['first_name'].' '.$staff['last_name'];
                $this->email = $staff['email'];
                $this->roles = $this->role_model->select_active_roles($staff['staff_id']);
                $this->active = $staff['active'];
                $returnValue = 0; //success
            }
            $returnValue = -2; //unable to execute query
        }
        return $returnValue;
    }

    /**
	 * method has_role()
     * Checks the provided role_id against the one stored in the model
     * @param  Integer $role_id
     * @return boolean
     */
    public function has_role($role_id)
    {
        foreach($this->roles as $role) {
            if($role['role_id']==$role_id) return true;
        }
        return false;
    }

	/**
	 * method create_new()
	 * creates a new Staff in the database
	 * @param  String $first_name
	 * @param  String $last_name
	 * @param  String $username
	 * @param  String $password
	 * @param  String $repeat_password
	 * @param  String $email
	 * @param  array (int) $roles
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function create_new($first_name, $last_name, $username, $password, $repeat_password, $email, $roles)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$username = strtolower($username);
			$stm = $db->prepare("INSERT INTO `staff` (`first_name`,`last_name`,`username`,`password`,`email`,`active`) VALUES (?,?,?,?,?,1)");
			$stm->bindValue(1, $first_name, SQLITE3_TEXT);
			$stm->bindValue(2, $last_name, SQLITE3_TEXT);
			$stm->bindValue(3, $username, SQLITE3_TEXT);
			$stm->bindValue(4, $password, SQLITE3_TEXT);
			$stm->bindValue(5, $email, SQLITE3_TEXT);
			if($stm->execute()) {
				$staff_id = $db->lastInsertRowID();
				foreach($roles as $role_id) {
					$stm = $db->prepare("INSERT INTO `staff_role` (`staff_id`,`role_id`) VALUES (:staff_id,?)");
                    $stm->bindParam(':staff_id', $staff_id);
					$stm->bindParam(2, $role_id);
					$stm->execute();
				}
				$returnValue = 0;
			}
        }
        return $returnValue;
    }

	/**
	 * method edit()
	 * replaces the database values with those provided
	 * @param  Integer $staff_id
	 * @param  String $first_name
	 * @param  String $last_name
	 * @param  String $password
	 * @param  String $email
	 * @param  Boolean $active
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function edit($staff_id, $first_name, $last_name, $password, $email, $active)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			if(strlen($password)>8) { //only replace password if one was provided
				$stm = $db->prepare("UPDATE staff SET `first_name`= :first_name,`last_name`=:last_name, `password`=:password, `email`=:email, `active`=:active WHERE staff_id = :staff_id");
			}
			else{ //if no new password, do not update
				$stm = $db->prepare("UPDATE staff SET `first_name`= :first_name,`last_name`=:last_name,`email`=:email, `active`=:active  WHERE staff_id = :staff_id");
			}
			$stm->bindValue(':first_name', $first_name, SQLITE3_TEXT);
			$stm->bindValue(':last_name', $last_name, SQLITE3_TEXT);
			$stm->bindValue(':password', $password, SQLITE3_TEXT);
			$stm->bindValue(':email', $email, SQLITE3_TEXT);
			$stm->bindValue(':active', $active, SQLITE3_INTEGER);
			$stm->bindParam(':staff_id', $staff_id);
			if($stm->execute()) {
				$returnValue = 0;
			}
		}
        return $returnValue;
    }

    
	/**
	 * method edit_roles()
	 * replaces the roles with those provided for this member of staff
	 * @param  array (int) $roles
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function edit_roles($roles)
    {
        $returnValue = 0;
        if($db = new SQLite3($this->db_file)){
            //reset the roles to all be empty
            $stm = $db->prepare("DELETE FROM `staff_role` WHERE `staff_id` = :staff_id");
            $stm->bindParam(':staff_id', $this->staff_id);
            if($stm->execute()) {
                foreach($roles as $role_id) {
                    if($returnValue == 0) { //if a single error has occurred, stop processing the rest
                        $stm = $db->prepare("INSERT INTO `staff_role` (`staff_id`,`role_id`) VALUES (:staff_id,:role_id)");
                        $stm->bindParam(':staff_id', $this->staff_id);
                        $stm->bindParam(':role_id', $role_id);
                        if($stm->execute()) {
                            $returnValue = 0;
                        }
                        else {
                            $returnValue = -1; //unknown error
                        }
                    }
                }
            }
            else {
                $returnValue = -1; //unknown error
            }
        }

        return $returnValue;
    }

	/**
	 * method deactivate()
	 * marks the specified staff as inactive from the database
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function deactivate()
    {
        $returnValue = -1; //unknown error
        $staff_id = $_GET['staff_id'];
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("UPDATE staff SET active = 0 WHERE staff_id = ?");
			$stm->bindParam(1, $staff_id);
			if($stm->execute())
				$returnValue = 0;
		}
        return $returnValue;
    }

    
	/**
	 * method total_num_active_staff_with_role()
	 * A count of the number of staff with the role_id provided
	 * @return Integer $returnValue - number of roles, or error which confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function total_num_active_staff_with_role($role_id)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT count(staff.staff_id) FROM staff_role JOIN staff ON staff_role.staff_id = staff.staff_id WHERE staff_role.role_id = :role_id AND staff.active = 1");
			$stm->bindParam(':role_id', $role_id);
            $results = $stm->execute();
            if($staff = $results->fetchArray()) {
               $returnValue = $staff[0];
            }
            else
                $returnValue = -2; //unable to execute query
        }
        return $returnValue;
    }

	/**
	 * method get_id_from_username()
	 * Turns the username into the staff ID from a database query
	 * @return Integer $returnValue - ID number, or error which confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function get_id_from_username($username)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT staff_id FROM staff WHERE username = :username");
			$stm->bindParam(':username', $username);
            $results = $stm->execute();
            if($staff = $results->fetchArray()) {
               $returnValue = $staff[0];
            }
            else
                $returnValue = -2; //unable to execute query
        }
        return $returnValue;
    }

	/**
	 * method get_password()
	 * Returns the password of this member of staff (model)
	 * @return String $password
	 */
    public function get_password() {
        return $this->password;
    }

} /* end of class Staff_Model */

?>