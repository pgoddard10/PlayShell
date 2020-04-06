<?php
/**
 * Short description of class Visitor_Model
 * @author Paul Goddard, <paul2.goddard@live.uwe.ac.uk>
 */
class Visitor_Model
{
    private $db_file = DATABASE_FILE;
	public $visitor_id = null;
	public $first_name = null;
	public $last_name = null;
    public $email = null;
    public $address = null;
	public $address_1 = null;
	public $address_2 = null;
	public $address_3 = null;
	public $address_4 = null;
	public $address_postcode = null;

    // --- OPERATIONS ---

    /**
     * Short description of method get_all_visitor_ids
     * @param
     * @return array(usernames : String)
     */
    public function get_all_visitor_ids()
    {
        $returnValue = array();
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT visitor_id FROM visitor");
            $visitor = $stm->execute();
            while($row = $visitor->fetchArray()) {
                $returnValue[] = $row;
            }
        }
        return $returnValue;
    }

    /**
     * Short description of method populate_from_db
     * @param  String visitor_id
     * @return Integer
     */
    public function populate_from_db($visitor_id)
    {
        $returnValue = -1; //unknown error
		$this->visitor_id = $visitor_id;
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT visitor_id, first_name, last_name, email, address_1, address_2, address_3, address_4, address_postcode FROM visitor WHERE visitor_id = :visitor_id");
			$stm->bindParam(':visitor_id', $visitor_id);
            $results = $stm->execute();
            if($visitor = $results->fetchArray()) {
                $this->first_name = $visitor['first_name'];
                $this->last_name = $visitor['last_name'];
                $this->email = $visitor['email'];
                $this->address_1 = $visitor['address_1'];
                $this->address_2 = $visitor['address_2'];
                $this->address_3 = $visitor['address_3'];
                $this->address_4 = $visitor['address_4'];
                $this->address_postcode = $visitor['address_postcode'];
                $this->address = $this->address_1."<br />";
                if(strlen($this->address_2)>0) $this->address = $this->address.$this->address_2."<br />";
                $this->address = $this->address.$this->address_3."<br />".$this->address_4."<br />".$this->address_postcode;
                $returnValue = 0; //success
            }
            $returnValue = -2; //unable to execute query
        }
        return $returnValue;
    }

    /**
     * Short description of method create_new
     * @param  
     * @return Integer
     */
    public function create_new($first_name, $last_name, $email, $address_1, $address_2, $address_3, $address_4, $address_postcode)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("INSERT INTO `visitor` (`first_name`,`last_name`,`email`,`address_1`,`address_2`,`address_3`,`address_4`,`address_postcode`) VALUES (:first_name,:last_name,:email,:address_1,:address_2,:address_3,:address_4,:address_postcode)");
			$stm->bindValue(':first_name', $first_name, SQLITE3_TEXT);
			$stm->bindValue(':last_name', $last_name, SQLITE3_TEXT);
			$stm->bindValue(':email', $email, SQLITE3_TEXT);
			$stm->bindValue(':address_1', $address_1, SQLITE3_TEXT);
			$stm->bindValue(':address_2', $address_2, SQLITE3_TEXT);
			$stm->bindValue(':address_3', $address_3, SQLITE3_TEXT);
			$stm->bindValue(':address_4', $address_4, SQLITE3_TEXT);
			$stm->bindValue(':address_postcode', $address_postcode, SQLITE3_TEXT);
			if($stm->execute()) $returnValue = 0;
            else $returnValue = -2;
        }
        return $returnValue;
    }

    /**
     * Short description of method edit
     * @param  
     * @return Integer
     */
    public function edit($visitor_id, $first_name, $last_name, $email, $address_1, $address_2, $address_3, $address_4, $address_postcode)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("UPDATE visitor SET `first_name`= :first_name,`last_name`=:last_name,`email`=:email, `address_1`=:address_1, `address_2`=:address_2, `address_3`=:address_3, `address_4`=:address_4, `address_postcode`=:address_postcode  WHERE visitor_id = :visitor_id");
			$stm->bindValue(':first_name', $first_name, SQLITE3_TEXT);
			$stm->bindValue(':last_name', $last_name, SQLITE3_TEXT);
			$stm->bindValue(':email', $email, SQLITE3_TEXT);
			$stm->bindValue(':address_1', $address_1, SQLITE3_TEXT);
			$stm->bindValue(':address_2', $address_2, SQLITE3_TEXT);
			$stm->bindValue(':address_3', $address_3, SQLITE3_TEXT);
			$stm->bindValue(':address_4', $address_4, SQLITE3_TEXT);
			$stm->bindValue(':address_postcode', $address_postcode, SQLITE3_TEXT);
			$stm->bindParam(':visitor_id', $visitor_id);
			if($stm->execute()) $returnValue = 0;
            else $returnValue = -2;
		}
        return $returnValue;
    }

    /**
     * Short description of method deactivate
     * @param  visitor_id
     * @return Integer
     */
    public function delete($visitor_id)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("DELETE FROM visitor WHERE visitor_id = ?");
			$stm->bindParam(1, $visitor_id);
			if($stm->execute()) $returnValue = 0;
            else $returnValue = -2;
		}
        return $returnValue;
    }

} /* end of class Visitor_Model */

?>