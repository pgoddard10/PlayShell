<?php
/**
 * Class Role_Model
 * Responsible for database interaction
 *
 * @author	Paul Goddard
 * 			paul2.goddard@live.uwe.ac.uk
 * 			https://github.com/pgoddard10/
 * 			https://www.linkedin.com/in/pgoddard10/
 * 			https://twitter.com/pgoddard10
 * @date Spring 2020 
 */
class Role_Model
{
    public $db_file = DATABASE_FILE;
    public $available_roles = null;

    // --- OPERATIONS ---

	/**
	 * method __construct()
	 * Sets up the available_roles array with all possible roles from the database
	 */
    function __construct() {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT role_id, name FROM role ORDER BY role_id ASC");
			$res = $stm->execute();
			$results = array();
			while($row = $res->fetchArray()) {
				$this->available_roles[] = $row;
			}
		}
	}

	/**
	 * method select_active_roles()
	 * Finds all active roles for the specified member of staff and returns them
	 * @param Integer $staff_id
	 * @return array(int) $results
	 */
	public function select_active_roles($staff_id) {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT staff_role.role_id, role.name FROM staff_role JOIN staff ON staff.staff_id = staff_role.staff_id JOIN role on staff_role.role_id = role.role_id WHERE staff_role.staff_id = ?");
			$stm->bindParam(1, $staff_id);
            $res = $stm->execute();
            $results = array();
			while($row = $res->fetchArray()) {
				$results[] = $row;
			}
			return $results;
		}
	}

} /* end of class Role_Model */

?>