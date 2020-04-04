<?php
require_once('classes/models/Role_Model.php');
/**
 * Short description of class Staff_Model
 * @author Paul Goddard, <paul2.goddard@live.uwe.ac.uk>
 */
class Staff_Model
{
    public $db_file = DATABASE_FILE;
    public $username = null;
    public $staff_id = null;
    public $first_name = null;
    public $last_name = null;
    public $display_name = null;
    public $email = null;
    public $active = null;
    public $roles = array();
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
     * Short description of method get_all_usernames
     * @param
     * @return array(usernames : String)
     */
    public function get_all_usernames()
    {
        $returnValue = null;
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT username FROM staff");
            $staff = $stm->execute();
            while($row = $staff->fetchArray()) {
                $returnValue[] = $row;
            }
        }
        return $returnValue;
    }

    /**
     * Short description of method populate
     * @param  String username
     * @return Integer
     */
    public function populate_from_db($username)
    {
        $returnValue = -1; //unknown error
		$this->username = strtolower($username);
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT staff_id, username, password, first_name, last_name, email, active FROM staff WHERE username = ?");
			$stm->bindValue(1, $this->username, SQLITE3_TEXT);
            $results = $stm->execute();
            if($staff = $results->fetchArray()) {
                $this->staff_id = $staff['staff_id'];
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
     * Short description of method has_role
     * @param  array<> staff_data
     * @return Integer
     */
    public function has_role($role_id)
    {
        // print('hello 1: <pre>'.print_r($this->roles[0]['role_id'],true).'</pre>');
        // print('hello 2: <pre>'.print_r($this->roles[1]['role_id'],true).'</pre>');
        foreach($this->roles as $role) {
            // echo ' | Matching '.$role_id.' with '.$role['role_id'];
            if($role['role_id']==$role_id) return true;
        }
        return false;
    }

    /**
     * Short description of method create_new
     * @param  array<> staff_data
     * @return Integer
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
					$stm = $db->prepare("INSERT INTO `staff_role` (`staff_id`,`role_id`) VALUES (?,?)");
					$stm->bindParam(1, $staff_id);
					$stm->bindParam(2, $role_id);
					$stm->execute();
				}
				$returnValue = 0;
			}
        }
        return $returnValue;
    }

    /**
     * Short description of method edit
     * @param  array<> staff_data
     * @return Integer
     */
    public function edit($staff_id, $first_name, $last_name, $username, $password, $repeat_password, $email, $active)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$username = strtolower($username);
            $replace_password=false;
			if(strlen($password)>0) { //only replace password if one was provided
				$stm = $db->prepare("UPDATE staff SET `first_name`= :first_name,`last_name`=:last_name,`username`=:username, `password`=:password, `email`=:email, `active`=:active WHERE staff_id = :staff_id");
			}
			else{ //if no new password, do not update
				$stm = $db->prepare("UPDATE staff SET `first_name`= :first_name,`last_name`=:last_name,`username`=:username,`email`=:email, `active`=:active  WHERE staff_id = :staff_id");
			}
			$stm->bindValue(':first_name', $first_name, SQLITE3_TEXT);
			$stm->bindValue(':last_name', $last_name, SQLITE3_TEXT);
			$stm->bindValue(':username', $username, SQLITE3_TEXT);
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
     * Short description of method deactivate
     * @param  staff_id
     * @return Integer
     */
    public function deactivate($staff_id)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("UPDATE staff SET active = 0 WHERE staff_id = ?");
			$stm->bindParam(1, $staff_id);
			if($stm->execute())
				$returnValue = 0;
		}
        return $returnValue;
    }

} /* end of class Staff_Model */

?>