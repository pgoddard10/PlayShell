<?php

require_once('classes/models/Visitor_Model.php');

/**
 * Short description of class Visitor_Controller
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Visitor_Controller
{
    private $visitor_model = null;
    public $all_visitors = array();

    /**
	 * method __construct()
	 * constructor that sets up the Models
	 */
    function __construct() {
        $this->visitor_model = new Visitor_Model();
        $this->populate_all_visitors();
    }


    /**
     * method sanitise_string()
     * Takes a string and performs sanitising techniques to help avoid xss attacks etc.
     * 
     * @param  String data
     * @param  Bool isemail
     * @return String data
     */
    private function sanitise_string($data,$isemail=false) {
        $data = filter_var($data, FILTER_SANITIZE_STRING);
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        if($isemail) $data = filter_var($data, FILTER_VALIDATE_EMAIL); //if the email address is not valid, just don't save it as it's not a required field
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
        
        if(isset($_GET['visitor_id'])) $visitor_id = filter_var($_GET['visitor_id'], FILTER_VALIDATE_INT);
        $email = $this->sanitise_string($_GET['email'],true);
        $address_1 = $this->sanitise_string($_GET['address_1']);
        $address_postcode = $this->sanitise_string($_GET['address_postcode']);
        $first_name = $this->sanitise_string($_GET['first_name']);
        $last_name = $this->sanitise_string($_GET['last_name']);

        foreach($this->all_visitors as $visitor=>$details) {
            if((!isset($_GET['visitor_id']) || ($visitor_id != $details->visitor_id))) {
                if($email == $details->email) return -2;
                if(($address_1 == $details->address_1) && ($address_postcode == $details->address_postcode)) {
                    if(($first_name == $details->first_name) && ($last_name == $details->last_name)) {
                        return -3;
                    }
                }
            }
        }

        return $returnValue;
    }

	/**
	 * method create_new()
	 * Sanitises the form data and calls the model, which creates a new Visitor in the database
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function create_new()
    {
        $returnValue = -1;//unknown error
        $returnValue = $this->check_for_duplicate();
        if($returnValue==0) {
            $first_name = $this->sanitise_string($_GET['first_name']);
            $last_name = $this->sanitise_string($_GET['last_name']);
            $email = $this->sanitise_string($_GET['email'],true);
            $address_1 = $this->sanitise_string($_GET['address_1']);
            $address_2 = $this->sanitise_string($_GET['address_2']);
            $address_3 = $this->sanitise_string($_GET['address_3']);
            $address_4 = $this->sanitise_string($_GET['address_4']);
            $address_postcode = $this->sanitise_string($_GET['address_postcode']);
            //now that everything has been checked and filter, pass data to the model for database interaction
            if($this->visitor_model->create_new($first_name, $last_name, $email, $address_1, $address_2, $address_3, $address_4, $address_postcode)==0) $returnValue = 0;
        }
        return $returnValue;
    }

    /**
	 * method edit()
	 * Sanitises the form data and calls the model, which edits the Visitor in the database with the new values
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function edit()
    {
        $returnValue = -1; //unknown error
        $returnValue = $this->check_for_duplicate();
        if($returnValue==0) {
            $visitor_id = filter_var($_GET['visitor_id'], FILTER_VALIDATE_INT);
            $first_name = $this->sanitise_string($_GET['first_name']);
            $last_name = $this->sanitise_string($_GET['last_name']);
            $email = $this->sanitise_string($_GET['email'],true);
            $address_1 = $this->sanitise_string($_GET['address_1']);
            $address_2 = $this->sanitise_string($_GET['address_2']);
            $address_3 = $this->sanitise_string($_GET['address_3']);
            $address_4 = $this->sanitise_string($_GET['address_4']);
            $address_postcode = $this->sanitise_string($_GET['address_postcode']);
            $this->visitor_model->populate_from_db($visitor_id);
            //now that everything has been checked and filter, pass data to the model for database interaction
            if($this->visitor_model->edit($visitor_id, $first_name, $last_name, $email, $address_1, $address_2, $address_3, $address_4, $address_postcode)==0) {
                $returnValue = 0; //successfully edited visitor
            }
            else $returnValue = -4; //error with query
        }
        return $returnValue;
    }

	/**
	 * method deactivate()
	 * deletes the Visitor with the referenced ID from the database (via the model)
	 * @return Integer $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function delete()
    {
        $returnValue = -1; //unknown error
        $visitor_id = filter_var($_GET['visitor_id'], FILTER_VALIDATE_INT);
        $this->visitor_model->populate_from_db($visitor_id);
        if($this->visitor_model->delete($visitor_id)==0) $returnValue = 0; //successfully deleted visitor
        else $returnValue = -2; //error with query
        return $returnValue;
    }

	/**
	 * method JSONify_All_Visitors()
	 * Loops through the $all_visitors array (which contains Visitor_Model objects) and turns into an array. The json_encode function turns the array into a JSON object
     * @return JSON String $data - all Item data as JSON obj
	 */
    public function JSONify_all_visitors()
    {
        $data = array();
        if(count($this->all_visitors)<=0) return '{"data": []}'; //empty JSON for datatables to read correctly.
        foreach($this->all_visitors as $visitor=>$details) {
            $this_visitor = array();
            $this_visitor['name'] = $details->first_name.' '.$details->last_name;
            $this_visitor['email'] = $details->email;
            $this_visitor['address'] = $details->address;
            $this_visitor['buttons'] = "<a href='#' data-toggle='modal' data-id='$details->visitor_id' class='editModalBox btn-circle btn-sm btn-primary' data-target='#editModalCenter'><i class='fas fa-edit'></i></a>";
            $this_visitor['buttons'] = $this_visitor['buttons'] . " <a href='#' data-toggle='modal' data-id='$details->visitor_id' class='deleteModalBox btn-circle btn-sm btn-primary' data-target='#deleteModalCenter'><i class='fas fa-trash'></i></a>";
            $this_visitor['buttons'] = $this_visitor['buttons'] . " <a href='#' data-toggle='modal' data-id='$details->visitor_id' class='btn_checkOutModal btn-circle btn-sm btn-primary' data-target='#checkOutModalCenter'><i class='fas fa-sign-out-alt'></i></a>";
            $data["data"][] = $this_visitor;
        }
        return json_encode($data, JSON_HEX_APOS|JSON_PRETTY_PRINT);
    }



    public function JSONify_visitor_details() {
        $visitor_id = filter_var($_GET['visitor_id'], FILTER_VALIDATE_INT);
        $this->visitor_model->populate_from_db($visitor_id);
        $this_visitor = array();
        $this_visitor['visitor_id'] = $this->visitor_model->visitor_id;
        $this_visitor['first_name'] = $this->visitor_model->first_name;
        $this_visitor['last_name'] = $this->visitor_model->last_name;
        $this_visitor['email'] = $this->visitor_model->email;
        $this_visitor['address_1'] =  $this->visitor_model->address_1;
        $this_visitor['address_2'] =  $this->visitor_model->address_2;
        $this_visitor['address_3'] =  $this->visitor_model->address_3;
        $this_visitor['address_4'] =  $this->visitor_model->address_4;
        $this_visitor['address_postcode'] =  $this->visitor_model->address_postcode;
        $data["data"][] = $this_visitor;
        return json_encode($data, JSON_HEX_APOS|JSON_PRETTY_PRINT);
    }


	/**
	 * method populate_all_visitors()
	 * sets up the $all_visitors array (which contains Visitor_Model objects) and turns into an array. 
	 */
    public function populate_all_visitors()
    {
        $model = new Visitor_Model();
        $this->all_visitors = array();
        $visitor_ids = $model->get_all_visitor_ids();
        foreach($visitor_ids as $id) {
            $visitor = new Visitor_Model();
            $visitor->populate_from_db($id[0]);
            $this->all_visitors[] = $visitor;
        }
    }
    
	/**
	 * method check_out_device()
	 * Loops through all possible device hostnames, finds the first one which has a status.json saying it's available, marks it as in use, transfers the visitor ID over and reports back.
	 * @return JSON object $returnValue - confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function check_out_device()
    {
        $returnValue["data"]["error"] = array("code"=>-1,"description"=>"An unknown error has occurred");
        $visitor_id = filter_var($_GET['visitor_id'], FILTER_VALIDATE_INT);
        for($i = 1; $i < (NUMBER_OF_VISITOR_DEVICES+1); $i++) { //+1 as NUMBER_OF_VISITOR_DEVICES is human number, not computer number
            // connect to FTP server
            $host = VISITOR_DEVICE_PREFIX.'-'.$i;
            $port = 22;
            $connection = @ssh2_connect($host, $port);
            if ($connection) {
                ssh2_auth_password($connection, FTP_USERNAME, FTP_PASSWORD);

                $sftp = ssh2_sftp($connection);
                if($sftp) {
                    $remote_file = DEVICE_DATA_FOLDER."status.json"; //is this device ready for a visitor to take out, already in use, or performing a system update?
                    $stream = @fopen("ssh2.sftp://$sftp$remote_file", 'r');
                    if(($stream) && (filesize("ssh2.sftp://$sftp$remote_file")>0)) { //if the file has a size > 0
                        $contents = fread($stream, filesize("ssh2.sftp://$sftp$remote_file"));   
                        $device_ready_json = json_decode($contents, true);
                        //now read the JSON file - is this device ready for a visitor to take out, already in use, or performing a system update?
                        if ($device_ready_json['status']['code']==DEVICE_READY) { //if device is ready, copy over visitor ID in JSON via SFTP
                            $visitor["data"] = array("visitor_id"=>$visitor_id);
                            $visitor_json = json_encode($visitor, JSON_HEX_APOS|JSON_PRETTY_PRINT);
                            
                            $local_file = PUBLISHED_CONTENT_FOLDER."visitor.json";
                            $fp = fopen($local_file, 'w');
                            fwrite($fp, $visitor_json);
                            fclose($fp);
                            chmod($local_file,0666); //set permissions
                            $remote_file = DEVICE_DATA_FOLDER."incoming_visitor_id.json";
                            $stream = @fopen("ssh2.sftp://$sftp$remote_file", 'w');
                            if (! $stream)
                                break; //throw new Exception("Could not open file: $remote_file");
                            $data_to_send = @file_get_contents($local_file);
                            if ($data_to_send === false)
                                break; //throw new Exception("Could not open local file: $local_file.");
                            if (@fwrite($stream, $data_to_send) === false)
                                break; //throw new Exception("Could not send data from file: $local_file.");
                            @fclose($stream);

                            //now clear the contents of the file
                            $fp = fopen($local_file, 'w');
                            fwrite($fp, "");
                            fclose($fp);


                            //change the status on the device to 'in use'
                            $local_file = PUBLISHED_CONTENT_FOLDER."status.json";
                            $status_data["status"] = array("code"=>DEVICE_IN_USE, "name"=>"in use");
                            $status_json = json_encode($status_data, JSON_HEX_APOS|JSON_PRETTY_PRINT);
                            $fp = fopen($local_file, 'w');
                            fwrite($fp, $status_json);
                            fclose($fp);
                            chmod($local_file,0666); //set permissions
                            $remote_file = DEVICE_DATA_FOLDER."status.json";
                            $stream = @fopen("ssh2.sftp://$sftp$remote_file", 'w');
                            if (! $stream)
                                break; //throw new Exception("Could not open file: $remote_file");
                            $data_to_send = @file_get_contents($local_file);
                            if ($data_to_send === false)
                                break; //throw new Exception("Could not open local file: $local_file.");
                            if (@fwrite($stream, $data_to_send) === false)
                                break; //throw new Exception("Could not send data from file: $local_file.");
                            @fclose($stream);

                            //report back the device ID in a nice, positive way.
                            $returnValue["data"] = array("hostname"=>$host,"status"=>"ready");

                            $i = NUMBER_OF_VISITOR_DEVICES+99; //inelegant way to end the loop
                        }

                        @fclose($stream);
                    }
                    else
                        $returnValue["data"]["error"] = array("code"=>-3,"description"=>"File does not exist, or it's 0 bytes.");
                }
                else {
                    $returnValue["data"]["error"] = array("code"=>-4,"description"=>"Device found but unable to log in. Please check the username & password.");
                }
            }
            else
                $returnValue["data"]["error"] = array("code"=>-2,"description"=>"There are currently no available devices for use. Please ensure any returned devices have been checked-in.");
        }
        return json_encode($returnValue, JSON_HEX_APOS|JSON_PRETTY_PRINT);

    }

} /* end of class Visitor_Controller */

?>
