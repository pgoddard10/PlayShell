<?php

require_once('Database.php');

class StaffDatabase extends Database {
	
	public function select_available_staff_roles() {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT role_id, name FROM role ORDER BY role_id ASC");
			$res = $stm->execute();
			$results = array();
			while($row = $res->fetchArray()) {
				array_push($results,$row);
			}

			return $results;
		}
	}
	public function select_active_roles($staff_id) {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT role_id FROM staff_role WHERE staff_id = ?");
			$stm->bindParam(1, $staff_id);
			$res = $stm->execute();
			$results = array();
			while($row = $res->fetchArray()) {
				array_push($results,$row['role_id']);
			}
			return $results;
		}
	}
	public function select_staff_details($username) {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT staff_id, username, password, first_name, last_name, email FROM staff WHERE username = ?");
			$stm->bindValue(1, $username, SQLITE3_TEXT);
            $res = $stm->execute();
            return $res->fetchArray();
		}
	}
	public function select_all_staff_details() {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT staff_id, username, password, first_name, last_name, email FROM staff ORDER BY last_name, first_name");
			$res = $stm->execute();
			$results = array();
			while($row = $res->fetchArray()) {
				array_push($results,$row);
            }
			return $results;
		}
	}
	public function insert_new_staff($first_name,$last_name,$username,$password,$email) {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("INSERT INTO `staff` (`first_name`,`last_name`,`username`,`password`,`email` VALUES (?,?,?,?,?)");
			$stm->bindParam(1, $first_name);
			$stm->bindParam(2, $last_name);
			$stm->bindParam(3, $username);
			$stm->bindParam(4, $password);
			$stm->bindParam(5, $email);
			if($stm->execute())
				return true;
			else
				return false;
		}
	}
	public function delete_roles_for_staff($staff_id) {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("DELETE FROM `staff_role` WHERE `staff_id` = ?");
			$stm->bindParam(1, $staff_id);
			if($stm->execute())
				return true;
			else
				return false;
		}
	}
	public function insert_staff_role($staff_id,$role_id) {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("INSERT INTO `staff_role` (`staff_id`,`role_id`) VALUES (?,?)");
			$stm->bindParam(1, $staff_id);
			$stm->bindParam(2, $role_id);
			if($stm->execute())
				return true;
			else
				return false;
		}
	}
}
?>