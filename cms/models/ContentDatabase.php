<?php
require_once 'Database.php';

class ContentDatabase extends Database {

	public function select_content_data() {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT content.name, content.created, content.last_modified, content.tag_id, content.written_text, staff.first_name, staff.last_name FROM content LEFT JOIN staff on content.modified_by = staff.staff_id WHERE content.tts_enabled = 1 AND content.active = 1 AND DATE(content.last_modified) > '2020-03-19'");
			$res = $stm->execute();
			$results = array();
			while($row = $res->fetchArray()) {
				array_push($results,$row);
			}

			return $results;
		}
	}
	public function insert_content_data($name,$tag_id,$tts_enabled,$written_text,$soundfile_location,$active,$gesture_id,$item_id) {
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("INSERT INTO `content`(`name`,`tag_id`,`tts_enabled`,`soundfile_location`,`written_text`,`next_content`,`modified_by`,`active`,`gesture_id`,`item_id`) VALUES (?,?,?,?,?,NULL,1,?,?,?)");
			$stm->bindParam(1, $name);
			$stm->bindParam(2, $tag_id);
			$stm->bindParam(3, $tts_enabled);
			$stm->bindParam(4, $soundfile_location);
			$stm->bindParam(5, $written_text);
			$stm->bindParam(6, $active);
			$stm->bindParam(7, $gesture_id);
			$stm->bindParam(8, $item_id);
			if($stm->execute())
				return true;
			else
				return false;
		}
	}

}

?>