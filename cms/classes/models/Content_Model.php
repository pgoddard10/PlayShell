<?php
/**
 * Class Content_Model
 * Responsible for database interaction
 *
 * @author	Paul Goddard
 * 			paul2.goddard@live.uwe.ac.uk
 * 			https://github.com/pgoddard10/
 * 			https://www.linkedin.com/in/pgoddard10/
 * 			https://twitter.com/pgoddard10
 * @date Spring 2020 
 */
class Content_Model
{
    private $db_file = DATABASE_FILE;
	public $content_id = null;
	public $name = null;
	public $tag_id = null;
    public $tts_enabled = null; //boolean
	public $written_text = null; //text for TTS
	public $next_content_name = null;
	public $next_content_id = null;
	public $created = null; //timestamp
	public $last_modified = null; //timestamp
	public $modified_by = null;
	public $active = null;
	public $gesture_id = null;
	public $gesture_name = null;
	public $item_id = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     * @param  String db_file
     */
    function __construct($item_id = null) {
        $this->item_id = $item_id;
    }

	/**
	 * method get_all_content_ids()
	 * gets all content_id from the content table
	 * @return array(int) $returnValue - an array of content_id
	 */
    public function get_all_content_ids()
    {
        $returnValue = array();
		if($db = new SQLite3($this->db_file)){
            if($this->item_id) {
                $stm = $db->prepare("SELECT content_id FROM content WHERE item_id = :item_id");
                $stm->bindValue(':item_id', $this->item_id, SQLITE3_TEXT);
            }
            else {
                $stm = $db->prepare("SELECT content_id FROM content");
            }
            $content = $stm->execute();
            while($row = $content->fetchArray()) {
                $returnValue[] = $row[0];
            }
        }
        return $returnValue;
    }

	/**
	 * method populate_from_db()
	 * takes the id provided populates this model from the database with it's details
	 * @param Integer $content_id
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function populate_from_db($content_id)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("SELECT tbl_content.tag_id, tbl_content.name AS 'content_name', tbl_content.tts_enabled, tbl_content.written_text, 
                                        tbl_content.next_content AS 'next_content_id', tbl_content.created, tbl_content.last_modified, tbl_content.modified_by, tbl_content.active, 
                                        tbl_content.gesture_id, tbl_gesture.name AS 'gesture_name', tbl_content.item_id,
                                        tbl_mod_staff.first_name, tbl_mod_staff.last_name, tbl_next_content.name AS 'next_content_name'
                                        FROM content tbl_content
                                        LEFT JOIN staff tbl_mod_staff ON tbl_content.modified_by = tbl_mod_staff.staff_id
                                        LEFT JOIN gesture tbl_gesture ON tbl_content.gesture_id = tbl_gesture.gesture_id
                                        LEFT JOIN content tbl_next_content ON tbl_content.next_content = tbl_next_content.content_id
                                        WHERE tbl_content.content_id = :content_id");
            $stm->bindParam(':content_id', $content_id);
            $results = $stm->execute();
            if($row = $results->fetchArray()) {
                $this->content_id = $content_id;
                $this->name = $row['content_name'];
                $this->tag_id = $row['tag_id'];
                $this->tts_enabled = $row['tts_enabled'];
                $this->written_text = $row['written_text'];
                $this->next_content_id = $row['next_content_id'];
                $this->next_content_name = $row['next_content_name'];
                $this->created = $row['created'];
                $this->last_modified = $row['last_modified'];
                $this->modified_by = $row['first_name'].' '.$row['last_name'];
                $this->active = $row['active'];
                $this->gesture_name = $row['gesture_name'];
                $this->gesture_id = $row['gesture_id'];
                $this->item_id = $row['item_id'];
                $returnValue = 0; //success
            }
            else $returnValue = -2; //unable to execute query
        }
        return $returnValue;
    }

	/**
	 * method create_new()
	 * creates a new Content in the database
	 * @param Integer $item_id
	 * @param Integer $created_by
	 * @param  String $name
	 * @param Boolean $tts_enabled
	 * @param Integer $next_content
	 * @param Boolean $active
	 * @param  String $written_text
	 * @param Integer $gesture_id
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function create_new($item_id, $created_by, $name, $tts_enabled, $next_content, $active, $written_text=null, $gesture_id=null)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
            
            if(($tts_enabled==0) && (!isset($_FILES['sound_file']))) {
                $returnValue = -6; //Soundfile not specified for non-TTS system
            }
            else {
                $stm = $db->prepare("INSERT INTO `content` (`name`,`tts_enabled`,`written_text`,`next_content`,`active`,`modified_by`,`gesture_id`,`item_id`)
                                                    VALUES (:name,:tts_enabled,:written_text,:next_content,:active,:created_by,:gesture_id,:item_id)");
                    $stm->bindValue(':name', $name, SQLITE3_TEXT);
                    $stm->bindValue(':tts_enabled', $tts_enabled, SQLITE3_TEXT);
                    $stm->bindValue(':written_text', $written_text, SQLITE3_TEXT);
                    $stm->bindValue(':next_content', $next_content, SQLITE3_TEXT);
                    $stm->bindValue(':active', $active, SQLITE3_TEXT);
                    $stm->bindValue(':created_by', $created_by, SQLITE3_TEXT);
                    $stm->bindValue(':gesture_id', $gesture_id, SQLITE3_TEXT);
                    $stm->bindValue(':item_id', $item_id, SQLITE3_TEXT);
                if($stm->execute()) {
                    $this->content_id = $db->lastInsertRowID();
                    $this->populate_from_db($this->content_id);
                    $returnValue = 0; //saved to db
                }
                else $returnValue = -2; //unable to execute query
            }
        }
        return $returnValue;
    }

	/**
	 * method edit()
	 * replaces the database values with those provided
	 * @param Integer $modified_by
	 * @param  String $name
	 * @param Boolean $tts_enabled
	 * @param Integer $next_content
	 * @param Boolean $active
	 * @param  String $written_text
	 * @param Integer $gesture
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function edit($modified_by, $name, $tts_enabled, $next_content, $active, $written_text, $gesture, $tag_id)
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("UPDATE content SET `name`= :name,`tts_enabled`=:tts_enabled,`written_text`=:written_text, 
                                    `last_modified` = CURRENT_TIMESTAMP, `next_content`=:next_content, `active`=:active, `modified_by`=:modified_by, 
                                    `gesture_id`=:gesture_id, `tag_id`=:tag_id WHERE content_id = :content_id");
			$stm->bindValue(':name', $name, SQLITE3_TEXT);
			$stm->bindValue(':tts_enabled', $tts_enabled, SQLITE3_TEXT);
			$stm->bindValue(':written_text', $written_text, SQLITE3_TEXT);
			$stm->bindValue(':next_content', $next_content, SQLITE3_TEXT);
			$stm->bindValue(':active', $active, SQLITE3_TEXT);
			$stm->bindValue(':modified_by', $modified_by, SQLITE3_TEXT);
			$stm->bindValue(':gesture_id', $gesture, SQLITE3_TEXT);
			$stm->bindValue(':tag_id', $tag_id, SQLITE3_TEXT);
			$stm->bindParam(':content_id', $this->content_id);
			if($stm->execute()) {
                $this->populate_from_db($this->content_id);
                $returnValue = 0; //saved to db
            }
            else $returnValue = -2;
		}
        return $returnValue;
    }

	/**
	 * method delete()
	 * removes this row from the database
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function delete()
    {
        $returnValue = -1; //unknown error
		if($db = new SQLite3($this->db_file)){
			$stm = $db->prepare("DELETE FROM content WHERE content_id = ?");
			$stm->bindParam(1, $this->content_id);
			if($stm->execute()) {
                $this->delete_soundfile();
                $returnValue = 0;
            }
            else $returnValue = -2; //unable to delete from db
		}
        return $returnValue;
    }


    public function upload_file() {
        $returnValue = -1;
        if(isset($_FILES['sound_file'])) {
            $sound_file = $_FILES['sound_file'];
            $dir_name = AUDIO_FOLDER.$this->item_id.'/';
            if (!is_dir($dir_name)) {
                //Create our directory if it does not exist
                mkdir($dir_name);
            }
            $dir_name = $dir_name . $this->content_id.'/';
            if (!is_dir($dir_name)) {
                //Create our directory if it does not exist
                mkdir($dir_name);
            }
            $complete_file_path = $dir_name."sound.wav";
            $file_type = strtolower(pathinfo($complete_file_path,PATHINFO_EXTENSION));
            // Allow certain file formats
            if($file_type != "wav") {
                $returnValue = -3; //file is of non-accepted filetype
            }
            else {
                if (move_uploaded_file($_FILES["sound_file"]["tmp_name"], $complete_file_path)) {
                    $returnValue = 0; //everything successful
                } else {
                    $returnValue = -4; //could not save file
                }
            }
        }
        else $returnValue = -2;
        return $returnValue;
    }

    
	/**
	 * method convert_text_to_speech()
	 * Runs the Text-to-Speech library and converts the text into a sound file & saves the sound file
	 * @param  String $written_text
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
	public function convert_text_to_speech($written_text) {
        $returnValue = -1;
		$provider = new \duncan3dc\Speaker\Providers\PicottsProvider;
        $tts = new \duncan3dc\Speaker\TextToSpeech($written_text, $provider);
        $dir_name = AUDIO_FOLDER.$this->item_id.'/';
        if (!is_dir($dir_name)) {
            //Create our directory if it does not exist
            mkdir($dir_name);
        }
        $dir_name = $dir_name . $this->content_id.'/';
        if (!is_dir($dir_name)) {
            //Create our directory if it does not exist
            mkdir($dir_name);
        }

		if(file_put_contents($dir_name.'sound.wav', $tts->getAudioData())) {
			$returnValue = 0;
        }
        return $returnValue;
	}
    
	/**
	 * method delete_soundfile()
	 * removes the specified sound file from the server's file system
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function delete_soundfile()
    {
        $returnValue = -1; //unknown error
        $file_name = AUDIO_FOLDER.$this->item_id.'/'. $this->content_id.'/sound.wav';
        if (file_exists($file_name)) {
            if(unlink($file_name)) $returnValue = 0;
        }
        $file_name = AUDIO_FOLDER.$this->item_id.'/'. $this->content_id;
        if (file_exists($file_name)) {
            if(rmdir($file_name)) $returnValue = 0;
        }
        
        return $returnValue;
    }


} /* end of class Content_Model */

?>