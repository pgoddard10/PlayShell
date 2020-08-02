<?php
/**
 * Class Item_Controller
 * Responsible for handling the logic surrounding the Item
 *
 * @author	Paul Goddard
 * 			paul2.goddard@live.uwe.ac.uk
 * 			https://github.com/pgoddard10/
 * 			https://www.linkedin.com/in/pgoddard10/
 * 			https://twitter.com/pgoddard10
 * @date Spring 2020 
 */

require_once('classes/models/Item_Model.php');

class Item_Controller
{
    private $item_model = null;
    public $all_items = array();

	/**
	 * method __construct()
	 * Sets up the model with Item rows from the database
	 */
    function __construct() {
        $this->item_model = new Item_Model();
        $this->populate_all_items();
    }

    /**
     * method sanitise_string()
     * Takes a string and performs sanitising techniques to help avoid xss attacks etc.
     * 
     * @param  String data
     * @param  Bool isurl
     * @return String data
     */
    private function sanitise_string($data,$isurl=false) {
        $data = filter_var($data, FILTER_SANITIZE_STRING);
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        if($isurl) $data = filter_var($data, FILTER_VALIDATE_URL); //if the url is not valid, just don't save it as it's not a required field
        return $data;
    }

    
    /**
     * method check_for_duplicate()
     * Checks that the submitted data from the form doesn't already exist.
     * The purpose is to minimise duplicate visitors being created
     * 
     *  @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers
     */
    public function check_for_duplicate() {
        $returnValue = 0;// no duplicate found
        
        if(isset($_GET['item_id'])) $item_id = filter_var($_GET['item_id'], FILTER_VALIDATE_INT);
        $name = $this->sanitise_string($_GET['name']);
        $heritage_id = $this->sanitise_string($_GET['heritage_id']);

        foreach($this->all_items as $item=>$details) {
            if((!isset($_GET['item_id']) || ($item_id != $details->item_id))) {
                if($name!="") {
                    if($name == $details->name) return -3;
                }
                if($heritage_id!="") {
                    if($heritage_id == $details->heritage_id) return -4;
                }
            }
        }

        return $returnValue;
    }

	/**
	 * method create_new()
	 * Sanitises the form data and calls the model, which creates a new Item in the database
	 * @param Integer $modified_by - current logged in staff_id
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function create_new($modified_by)
    {
        $returnValue = -1;//unknown error
        $returnValue = $this->check_for_duplicate();
        if($returnValue==0){
            $heritage_id = $this->sanitise_string($_GET['heritage_id']);
            $name = $this->sanitise_string($_GET['name']);
            $location = $this->sanitise_string($_GET['location']);
            $url = $this->sanitise_string($_GET['url'],true);
            $active = filter_var($_GET['active'], FILTER_VALIDATE_INT);
            $modified_by = filter_var($modified_by, FILTER_VALIDATE_INT);
            //now that everything has been checked and filter, pass data to the model for database interaction
            if($this->item_model->create_new($heritage_id, $name, $location, $url, $active, $modified_by)==0) $returnValue = 0;
            else $returnValue = -2;
        }
        return $returnValue;
    }

	/**
	 * method edit()
	 * Sanitises the form data and calls the model, which edits the Item in the database with the new values
	 * @param Integer $modified_by - current logged in staff_id
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function edit($modified_by)
    {
        $returnValue = -1; //unknown error
        $returnValue = $this->check_for_duplicate();
        if($returnValue==0) {
            $heritage_id = $this->sanitise_string($_GET['heritage_id']);
            $name = $this->sanitise_string($_GET['name']);
            $location = $this->sanitise_string($_GET['location']);
            $url = $this->sanitise_string($_GET['url'],true);
            $active = filter_var($_GET['active'], FILTER_VALIDATE_INT);
            $item_id = filter_var($_GET['item_id'], FILTER_VALIDATE_INT);
            $this->item_model->populate_from_db($item_id);
            //now that everything has been checked and filter, pass data to the model for database interaction
            if($this->item_model->edit($item_id, $heritage_id, $name, $location, $url, $active, $modified_by)==0) $returnValue = 0; //successfully edited visitor
            else $returnValue = -2; //error with query
        }
        return $returnValue;
    }

	/**
	 * method delete()
	 * Removes the Item with the referenced ID from the database (via the model)
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function delete()
    {
        $returnValue = -1; //unknown error
        $item_id = filter_var($_GET['item_id'], FILTER_VALIDATE_INT);
        $this->item_model->populate_from_db($item_id);
        if($this->item_model->delete($item_id)==0) $returnValue = 0; //successfully deleted the item
        else $returnValue = -2; //error with query
        return $returnValue;
    }

	/**
	 * method publish()
	 * Creates a JSON file of all Item & Content data from the database and copies the sound files into the Publish folder
     * This sets everything up for device synching (handled in the Device_Controller class)
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function publish()
    {
        $returnValue = -1; //unknown error
        
        //initialisation
        $complete_path = PUBLISHED_CONTENT_FOLDER.PUBLISHED_CONTENT_FILE;
        
        $fp = fopen($complete_path, 'w');
        if(fwrite($fp, $this->JSONify_All_Items())) $returnValue = 0;
        fclose($fp);

        //to ensure that audio files are not stranded in this folder:
        $this->delete_files(PUBLISHED_CONTENT_FOLDER.'audio/'); //remove all existing audio files
        $this->recurse_copy(AUDIO_FOLDER,PUBLISHED_CONTENT_FOLDER.'audio/'); //replace with new copies

        return $returnValue;
    }

	/**
	 * method recurse_copy()
	 * Recursively copies the files and folders from the $src location to the $dst
     * copied from https://stackoverflow.com/a/2050909
	 * @param  String $src
	 * @param  String $dst
	 */
    public function recurse_copy($src,$dst) { 
        $dir = opendir($src); 
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    $this->recurse_copy($src . '/' . $file,$dst . '/' . $file); 
                } 
                else { 
                    copy($src . '/' . $file,$dst . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
    } 

    

	/**
	 * method delete_files()
	 * removes all files and folders in the given $target path
     * copied from https://paulund.co.uk/php-delete-directory-and-files-in-directory
	 * @param  String $target
	 */
    // copied from https://paulund.co.uk/php-delete-directory-and-files-in-directory
    public function delete_files($target) {
        if(is_dir($target)){
            $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

            foreach( $files as $file ){
                $this->delete_files( $file );      
            }

            rmdir( $target );
        } elseif(is_file($target)) {
            unlink( $target );  
        }
    }
    
	/**
	 * method JSONify_All_Items()
	 * Loops through the $all_items array (which contains Item_Model objects) and turns into an array. The json_encode function turns the array into a JSON object
     * @return JSON String $data - all Item data as JSON obj
	 */
    public function JSONify_All_Items()
    {
        $data = array();
        if(count($this->all_items)<=0) echo '{"data": []}'; //if array is empty, provide empty JSON for datatables to read correctly.
        else {
            foreach($this->all_items as $item=>$details) {
                $individual_item = array();
                
                $individual_item['item_id'] = $details->item_id;
                if(strlen($details->url) > 1) $individual_item['name_with_url'] = '<a href="'.$details->url.'" target="_blank">' . $details->name .'</a>';
                else $individual_item['name_with_url'] = $details->name;
                $individual_item['name_without_url'] = $details->name;
                $individual_item['heritage_id'] = $details->heritage_id;
                $individual_item['location'] = $details->location;
                $individual_item['created'] = date("d/m/Y \a\\t H:i", strtotime($details->created));
                $last_modified = date("d/m/Y \a\\t H:i", strtotime($details->last_modified));
                if(strlen($details->modified_by) > 1) $last_modified = $last_modified. ' by ' . $details->modified_by;
                else $last_modified = $last_modified. ' by [deleted staff member]';
                $individual_item['last_modified'] = $last_modified;
                if($details->active==1)
                    $individual_item['active'] = 'Yes';
                else
                    $individual_item['active'] = 'No';

                $individual_item['content'] = $details->content;

                $individual_item['buttons'] = "<a href='#' data-toggle='modal' data-id='$details->item_id' class='editItemModalBox btn-circle btn-sm btn-primary' data-target='#editModalCenter'><i class='fas fa-edit'></i></a>";
                $individual_item['buttons'] = $individual_item['buttons'] . " <a href='#' data-toggle='modal' data-id='$details->item_id' class='deleteItemModalBox btn-circle btn-sm btn-primary' data-target='#deleteItemModalCenter'><i class='fas fa-trash'></i></a>";
                $data["data"][] = $individual_item;
            }
            return json_encode($data, JSON_HEX_APOS|JSON_PRETTY_PRINT);
        }
    }

    public function JSONify_item_details() {
        $item_id = filter_var($_GET['item_id'], FILTER_VALIDATE_INT);
        $this->item_model->populate_from_db($item_id);
        $this_item = array();
        $this_item['name'] = $this->item_model->name;
        $this_item['url'] = $this->item_model->url;
        $this_item['heritage_id'] = $this->item_model->heritage_id;
        $this_item['location'] = $this->item_model->location;
        $this_item['active'] = $this->item_model->active;
        $data["data"][] = $this_item;
        return json_encode($data, JSON_HEX_APOS|JSON_PRETTY_PRINT);
    }

	/**
	 * method populate_all_items()
	 * sets up the $all_items array (which contains Item_Model objects) and turns into an array. 
	 */
    public function populate_all_items()
    {
        $model = new Item_Model();
        $this->all_items = array();
        $item_ids = $model->get_all_item_ids();
        foreach($item_ids as $id) {
            $item = new Item_Model();
            $item->populate_from_db($id[0]);
            $this->all_items[] = $item;
        }
    }


} /* end of class Item_Controller */

?>