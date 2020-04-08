<?php
/**
 * Short description of class Item_Model
 * @author Paul Goddard, <paul2.goddard@live.uwe.ac.uk>
 */
require_once('classes/models/Content_Model.php');

class Item_Model
{
    private $db_file = DATABASE_FILE;
	public $item_id = null;
	public $heritage_id = null;
	public $name = null;
    public $location = null;
    public $created = null;
	public $last_modified = null;
	public $modified_by = null;
	public $url = null;
    public $active = null;
    public $content = array(); //an array of Content_Model objects

    // --- OPERATIONS ---

    /**
     * Short description of method get_all_item_ids
     * @param
     * @return array(usernames : String)
     */
    public function get_all_item_ids()
    {
        $returnValue = array();
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT item_id FROM item");
            $item = $stm->execute();
            while($row = $item->fetchArray()) {
                $returnValue[] = $row;
            }
        }
        return $returnValue;
    }

    /**
     * Short description of method populate_from_db
     * @param  String item_id
     * @return Integer
     */
    public function populate_from_db($item_id)
    {
        $returnValue = -1; //unknown error
		$this->item_id = $item_id;
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT tbl_item.heritage_id, tbl_item.name, tbl_item.location, tbl_item.created, tbl_item.last_modified, tbl_item.url, tbl_item.active,
                                        tbl_mod_staff.first_name, tbl_mod_staff.last_name
                                        FROM item tbl_item
                                        LEFT JOIN staff tbl_mod_staff ON tbl_item.modified_by = tbl_mod_staff.staff_id
                                        WHERE tbl_item.item_id = :item_id");
			$stm->bindParam(':item_id', $item_id);
            $results = $stm->execute();
            if($item = $results->fetchArray()) {
                $this->heritage_id = $item['heritage_id'];
                $this->name = $item['name'];
                $this->location = $item['location'];
                $this->created = $item['created'];
                $this->last_modified = $item['last_modified'];
                $this->modified_by = $item['first_name'].' '.$item['last_name'];
                $this->url = $item['url'];
                $this->active = $item['active'];
                $content_model = new Content_Model();
                $all_contents_ids = $content_model->get_all_content_ids($item_id);
                foreach($all_contents_ids as $content_id) {
                    $cm = new Content_Model();
                    $cm->populate_from_db($content_id);
                    $this->content[] = $cm;
                }
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
    public function create_new($heritage_id, $name, $location, $url, $active, $modified_by)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("INSERT INTO `item` (`heritage_id`,`name`,`location`,`modified_by`,`url`,`active`)
                                                VALUES (:heritage_id,:name,:location,:modified_by,:url,:active)");
			$stm->bindValue(':heritage_id', $heritage_id, SQLITE3_TEXT);
			$stm->bindValue(':name', $name, SQLITE3_TEXT);
			$stm->bindValue(':location', $location, SQLITE3_TEXT);
			$stm->bindValue(':modified_by', $modified_by, SQLITE3_TEXT);
			$stm->bindValue(':url', $url, SQLITE3_TEXT);
			$stm->bindValue(':active', $active, SQLITE3_TEXT);
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
    public function edit($item_id, $heritage_id, $name, $location, $url, $active, $modified_by)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("UPDATE item SET `heritage_id`= :heritage_id,`name`=:name,`location`=:location, `url`=:url, `last_modified` = CURRENT_TIMESTAMP, `active`=:active, `modified_by`=:modified_by WHERE item_id = :item_id");
			$stm->bindValue(':heritage_id', $heritage_id, SQLITE3_TEXT);
			$stm->bindValue(':name', $name, SQLITE3_TEXT);
			$stm->bindValue(':location', $location, SQLITE3_TEXT);
			$stm->bindValue(':url', $url, SQLITE3_TEXT);
			$stm->bindValue(':active', $active, SQLITE3_TEXT);
			$stm->bindValue(':modified_by', $modified_by, SQLITE3_TEXT);
			$stm->bindParam(':item_id', $item_id);
			if($stm->execute()) $returnValue = 0;
            else $returnValue = -2;
		}
        return $returnValue;
    }

    /**
     * Short description of method deactivate
     * @param  item_id
     * @return Integer
     */
    public function delete($item_id)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("DELETE FROM item WHERE item_id = ?");
			$stm->bindParam(1, $item_id);
			if($stm->execute()) $returnValue = 0;
            else $returnValue = -2;
		}
        return $returnValue;
    }

} /* end of class Item_Model */

?>