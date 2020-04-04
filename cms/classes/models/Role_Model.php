
<?php

/**
 * Short description of class Role_Model
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Role_Model
{
    public $db_file = DATABASE_FILE;
    public $available_roles = null;

    // --- OPERATIONS ---

    /**
     * Short description of method populate
     *
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
     * Short description of method select_active_roles
     * @param  String staff_id
     * @return Integer
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