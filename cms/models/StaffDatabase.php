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
			$stm = $db->prepare("SELECT staff_role.role_id FROM staff_role JOIN staff ON staff.staff_id = staff_role.staff_id WHERE staff_role.staff_id = ?");
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
		$username = strtolower($username);
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT staff_id, username, password, first_name, last_name, email, active FROM staff WHERE username = ?");
			$stm->bindValue(1, $username, SQLITE3_TEXT);
            $res = $stm->execute();
            return $res->fetchArray();
		}
	}
	public function select_staff_username($staff_id) {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT username FROM staff WHERE staff_id = ?");
			$stm->bindParam(1, $staff_id);
            $res = $stm->execute();
			$username = $res->fetchArray();
			return strtolower($username[0]);
		}
	}
	public function select_all_staff_details() {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT staff_id, username, password, first_name, last_name, email, active FROM staff ORDER BY active, last_name, first_name");
			$res = $stm->execute();
			$results = array();
			while($row = $res->fetchArray()) {
				array_push($results,$row);
            }
			return $results;
		}
	}
	public function select_role_name($role_id) {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT name FROM role WHERE role_id = ?");
			$stm->bindParam(1, $role_id);
			$res = $stm->execute();
			$name = $res->fetchArray();
			return $name[0];
		}
	}
	public function number_of_roles($role_id) {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT count(role_id) FROM staff_role JOIN staff ON staff.staff_id = staff_role.staff_id WHERE role_id = ? AND staff.active = 1");
			$stm->bindParam(1, $role_id);
			$res = $stm->execute();
			$count = $res->fetchArray();
			return $count[0];
		}
	}
	public function insert_new_staff($first_name,$last_name,$username,$password,$email,$roles) {
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
				return true;
			}
			else
				return false;
		}
	}
	public function update_staff($staff_id,$first_name,$last_name,$username,$replace_password,$password,$email,$active) {
		if($db = new SQLite3($this->db_file)){
			$username = strtolower($username);
			if($replace_password) { //if a new password was set, update it
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
				return true;
			}
			else
				return false;
		}
	}
	public function deactivate_staff($staff_id) {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("UPDATE staff SET active = 0 WHERE staff_id = ?");
			$stm->bindParam(1, $staff_id);
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