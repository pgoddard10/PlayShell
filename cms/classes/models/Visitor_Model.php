<?php
/**
 * Class Visitor_Model
 * Responsible for database interaction
 *
 * @author	Paul Goddard
 * 			paul2.goddard@live.uwe.ac.uk
 * 			https://github.com/pgoddard10/
 * 			https://www.linkedin.com/in/pgoddard10/
 * 			https://twitter.com/pgoddard10
 * @date Spring 2020 
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
	 * method get_all_visitor_ids()
	 * gets all visitor_id from the visitor table
	 * @return array(int) $returnValue - an array of visitor_id
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
	 * method populate_from_db()
	 * takes the id provided populates this model from the database with it's details
	 * @param Integer $visitor_id
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
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
	 * method create_new()
	 * creates a new Visitor in the database
	 * @param  String $first_name
	 * @param  String $last_name
	 * @param  String $email
	 * @param  String $address_1
	 * @param  String $address_2
	 * @param  String $address_3
	 * @param  String $address_4
	 * @param  String $address_postcode
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
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
	 * method edit()
	 * replaces the database values with those provided
	 * @param  Integer $visitor_id
	 * @param  String $first_name
	 * @param  String $last_name
	 * @param  String $email
	 * @param  String $address_1
	 * @param  String $address_2
	 * @param  String $address_3
	 * @param  String $address_4
	 * @param  String $address_postcode
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
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
	 * method delete()
	 * removes the specified Visitor from the database
	 * @param  Integer $visitor_id
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function delete($visitor_id)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("DELETE FROM visitor WHERE visitor_id = ?"); //build the SQL
			$stm->bindParam(1, $visitor_id); //swap out the ? for the visitor_id
			if($stm->execute()) $returnValue = 0; //run the SQL in the database
            else $returnValue = -2; //db error
		}
        return $returnValue;
    }
	/**
	 * method insert_visitor_history()
	 * removes the specified Visitor from the database
	 * @param  Integer $content_id
	 * @param  String $time_scanned
	 * @param  Integer $visitor_id
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function insert_visitor_history($content_id, $time_scanned,$visitor_id)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("INSERT INTO visitor_history (content_id, time_scanned, visitor_id) VALUES (?,?,?)"); //build the SQL
			$stm->bindParam(1, $content_id); //swap out the ? for the content_id
			$stm->bindParam(2, $time_scanned); //swap out the ? for the time_scanned
			$stm->bindParam(3, $visitor_id); //swap out the ? for the visitor_id
			if($stm->execute()) $returnValue = 0; //run the SQL in the database
            else $returnValue = -2; //db error
		}
        return $returnValue;
    }
    
} /* end of class Visitor_Model */

?>