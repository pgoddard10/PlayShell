<?php

require_once "vendor/autoload.php";
require_once('classes/models/Content_Model.php');

/**
 * Class Content_Controller
 * Responsible for the logic and processing for 'content' (i.e. NFC tag info)
 *
 * @author	Paul Goddard
 * 			paul2.goddard@live.uwe.ac.uk
 * 			https://github.com/pgoddard10/
 * 			https://www.linkedin.com/in/pgoddard10/
 * 			https://twitter.com/pgoddard10
 * @date Spring 2020 
 */
class Content_Controller
{
    private $content_model = null;
    public $all_contents = array();
    public $item_id = null; 

	/**
	 * method __construct()
	 * The constructor method, always called by default when an instance of Content_Controller is created.
     * Sets up a new instance of the Content_Model class for use throughout this class.
	 * @param  Integer $item_id
	 */
    function __construct($item_id) {
        $this->item_id = $item_id;
        $this->content_model = new Content_Model($this->item_id);
    }


	/**
	 * method sanitise_string()
	 * Takes a string value and processes it to prevent XSS attacks, etc
	 * @param  String $data
	 * @return String $data - but filtered and sanitised
	 */
   private function sanitise_string($data) {
       $data = filter_var($data, FILTER_SANITIZE_STRING);
       $data = trim($data); //remove whitespaces
       $data = stripslashes($data); //removes dodgy slashes to prevent escape characters (e.g. '\s becomes 's )
       $data = htmlspecialchars($data);
       return $data;
   }

	/**
	 * method JSONify_All_Contents()
	 * Takes the data from the Model and turns into JSON code.
     * Used to pass data around on the datatables in the GUI.
	 * @param  String $
	 * @return Integer $
	 * @return String json_encode($individual_content, JSON_PRETTY_PRINT);
	 */
    public function JSONify_All_Contents() {
        $array_of_contents = $this->all_contents;
        $individual_content = array();
        if(count($array_of_contents)<=0) return '{"data": []}'; //if array is empty, provide empty JSON for datatables to read correctly.
        else {
            foreach($array_of_contents as $obj=>$contents) {
                $content_details_array = array();
                $content_details_array['name'] = $contents->name;
                $content_details_array['tag_id'] = $contents->tag_id;
                $content_details_array['content_id'] = $contents->content_id;
                $content_details_array['item_id'] = $contents->item_id;
                if($contents->active==1)
                    $content_details_array['active'] = 'Yes';
                else
                    $content_details_array['active'] = 'No';
                $content_details_array['created'] = date("d/m/Y \a\\t H:i", strtotime($contents->created));
                $last_modified = date("d/m/Y \a\\t H:i", strtotime($contents->last_modified));
                if(strlen($contents->modified_by) > 1) $last_modified = $last_modified. ' by ' . $contents->modified_by;
                else $last_modified = $last_modified. ' by [deleted staff member]';
                $content_details_array['last_modified'] = $last_modified;
                $content_details_array['tts_enabled'] = $contents->tts_enabled;
                $content_details_array['written_text'] = $contents->written_text;
                $content_details_array['gesture_id'] = $contents->gesture_id;
                $content_details_array['gesture_name'] = $contents->gesture_name;
                $content_details_array['next_content_id'] = $contents->next_content_id;
                $content_details_array['next_content_name'] = $contents->next_content_name;

                $content_as_json = json_encode($contents, JSON_HEX_APOS);
                $content_details_array['buttons'] = "<a href='#' data-toggle='modal' data-id='$content_as_json' class='editContentModalBox btn-success btn-circle btn-sm' data-target='#editContentModalCenter'><i class='fas fa-edit bg-success'></i></a>";
                $content_details_array['buttons'] = $content_details_array['buttons'] . " <a href='#' data-toggle='modal' data-id='$content_as_json' class='deleteContentModalBox btn-success btn-circle btn-sm' data-target='#deleteContentModalCenter'><i class='fas fa-trash'></i></a>";

                $individual_content["data"][] = $content_details_array;
            }
            return json_encode($individual_content, JSON_HEX_APOS);
        }
    }

	/**
	 * method create_new()
	 * Creates a new 'content' in the database
	 * @param Integer $created_by //staff_id of the current logged in user
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function create_new($created_by)
    {
        $returnValue = -1; //unknown error
        $written_text = null;
        $gesture = null;
        $tts_enabled = filter_var($_POST['tts_enabled'], FILTER_VALIDATE_INT);
        
        if($tts_enabled==1) {
            $written_text = $_POST['written_text'];
            $written_text_for_tts = $_POST['written_text'];
            $written_text = $this->sanitise_string($_POST['written_text']);
        }
        if(isset($_POST['gesture'])) $gesture = $gesture = filter_var($_POST['gesture'], FILTER_VALIDATE_INT);

        $name = $this->sanitise_string($_POST['name']);
        $next_content = filter_var($_POST['next_content'], FILTER_VALIDATE_INT);
        $active = filter_var($_POST['active'], FILTER_VALIDATE_INT);
        $item_id = filter_var($_POST['item_id'], FILTER_VALIDATE_INT);
        //now that everything has been checked and filter, pass data to the model for database interaction
        $returnValue = $this->content_model->create_new($item_id, $created_by, $name, $tts_enabled, $next_content, $active, $written_text, $gesture);
        if($returnValue==0) { //if successfully saved
            if($tts_enabled==1) { //and TTS was requested
                $result = $this->content_model->convert_text_to_speech($written_text_for_tts); //convert the provided text to a sound file
            }
            $returnValue = 0;
        }
        return $returnValue;
    }

    /**
     * method edit()
     * Edits the 'content' in the database with the provided values from the GUI
     * @param  Integer $modified_by //staff_id of the current logged in user
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
     */
    public function edit($modified_by)
    {
        $returnValue = -1;//unknown error
        $content_id = $_POST['content_id'];
        $this->content_model->populate_from_db($content_id);
        $written_text = null;
        $gesture = null;
        $tts_enabled = filter_var($_POST['edit_tts_enabled'], FILTER_VALIDATE_INT);
        
        if($tts_enabled==1) { //no point sanitising if the data won't be used
            $written_text = $_POST['written_text'];
            $written_text_for_tts = $_POST['written_text'];
            $written_text = $this->sanitise_string($_POST['written_text']);
        }
        if(isset($_POST['gesture'])) $gesture = filter_var($_POST['gesture'], FILTER_VALIDATE_INT);

        $name = $this->sanitise_string($_POST['name']);
        $next_content = filter_var($_POST['next_content'], FILTER_VALIDATE_INT);
        $active = filter_var($_POST['active'], FILTER_VALIDATE_INT);
        $tag_id = $this->sanitise_string($_POST['tag_id']);

        //now that everything has been checked and filter, pass data to the model for database interaction
        $returnValue = $this->content_model->edit($modified_by, $name, $tts_enabled, $next_content, $active, $written_text, $gesture, $tag_id);
        if($returnValue==0) { //if successfully saved
            if($tts_enabled==1) { //and TTS was requested
                $result = $this->content_model->convert_text_to_speech($written_text_for_tts); //convert the provided text to a sound file
            }
            else $returnValue = -2; //created in database successfully but TTS failed
        }
        else $returnValue = -3; //error saving in the database
        return $returnValue;
    }

    /**
     * method delete()
     * Removes the 'content' associated with the provided content_id 
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
     */
    public function delete()
    {
        $returnValue = -1; //unknown error
        $content_id = filter_var($_GET['content_id'], FILTER_VALIDATE_INT);
        $this->content_model->populate_from_db($content_id);
        if($this->content_model->delete()==0) $returnValue = 0; //successfully deleted the content & soundfile
        else $returnValue = -2; //error with query
        return $returnValue;
    }

	/**
	 * method populate_all_contents()
	 * sets up the $this->all_contents variable with an array of all 'content'
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function populate_all_contents()
    {
        $this->all_contents = array();
        $content_ids = $this->content_model->get_all_content_ids();
        foreach($content_ids as $c_id) {
            $content = new Content_Model();
            if($content->populate_from_db($c_id)==0)
                $this->all_contents[] = $content;
            else
                return -1;
        }
    }

    
	/**
	 * method write_blank_file()
	 * creates an empty file, using the name provided
	 * @param  String $filename
	 */
    private function write_blank_file($filename) {
        $fp = fopen($filename, 'w');
        fwrite($fp, "");
        fclose($fp);
    }



	/**
	 * method scan_nfc_tag()
	 * Essentially a trigger from the user asking for the NFC tag id.
     * The tag id can only be retreived by some software on the server, so communication is done via a JSON file
     * The JSON file contains the content_id that the user wishes to associate with the NFC tag as a form a validation
	 */
    public function scan_nfc_tag() {
        //initialisation
        if(!file_exists(CONTENT_ID_FILE)) write_blank_file(CONTENT_ID_FILE);
        if(!file_exists(NFC_ID_FILE)) write_blank_file(NFC_ID_FILE);
        
        if(isset($_GET['content_id'])) {
            //convert the content ID into a JSON object and save into a file
            $posts['content_id'] = filter_var($_GET['content_id'], FILTER_VALIDATE_INT);
            $fp = fopen(CONTENT_ID_FILE, 'w');
            fwrite($fp, json_encode($posts,JSON_HEX_APOS));
            fclose($fp);
        }
    }

	/**
	 * method get_nfc_id()
	 * gets the NFC tag ID from the JSON file (this happens effectively on button press)
	 * @return Integer/String $returnValue - tag_id as JSON. Errors are negative numbers, default unknown error is -1
	 */
    public function get_nfc_id() {
        $returnValue = -1; //unknown error
        if($tag_data = file_get_contents(NFC_ID_FILE)) { //onclick of [I've scanned the tag] button send ajax request to perform the below
            //if the NFC details have been provided from the C++ app
            //open the file, get the JSON
            $tag_data_json = json_decode($tag_data, true);
            
            //check that the content_id in the file matches the one provided in the PHP (to ensure no accidental cross-over)
            if($tag_data_json['content_id']==filter_var($_GET['content_id'], FILTER_VALIDATE_INT)) {
                $returnValue = json_encode(array("tag_id"=>$tag_data_json['nfc_tag'],JSON_HEX_APOS));
            }
        
            //empty the files to prevent accidents on future reads
            $this->write_blank_file(CONTENT_ID_FILE);
            $this->write_blank_file(NFC_ID_FILE);
        }
        return $returnValue;
    }

} /* end of class Content_Controller */

?>