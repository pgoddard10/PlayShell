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
     * Short description of method __construct
     * @param  String db_file
     */
    function __construct() {
        $this->visitor_model = new Visitor_Model();
        $this->populate_all_visitors();
    }

    /**
     * Short description of method create_new
     *
     * @param 
     * @return Integer
     */
    public function create_new()
    {
        $returnValue = -1;//unknown error
        $first_name = $_GET['first_name'];
        $last_name = $_GET['last_name'];
        $email = $_GET['email'];
        $address_1 = $_GET['address_1'];
        $address_2 = $_GET['address_2'];
        $address_3 = $_GET['address_3'];
        $address_4 = $_GET['address_4'];
        $address_postcode = $_GET['address_postcode'];
        if($this->visitor_model->create_new($first_name, $last_name, $email, $address_1, $address_2, $address_3, $address_4, $address_postcode)==0) $returnValue = 0;
        return $returnValue;
    }

    /**
     * Short description of method edit
     *
     * @param  
     * @return Integer
     */
    public function edit()
    {
        $returnValue = -1; //unknown error
        $visitor_id = $_GET['visitor_id'];
        $first_name = $_GET['first_name'];
        $last_name = $_GET['last_name'];
        $email = $_GET['email'];
        $address_1 = $_GET['address_1'];
        $address_2 = $_GET['address_2'];
        $address_3 = $_GET['address_3'];
        $address_4 = $_GET['address_4'];
        $address_postcode = $_GET['address_postcode'];
        $this->visitor_model->populate_from_db($visitor_id);
        if($this->visitor_model->edit($visitor_id, $first_name, $last_name, $email, $address_1, $address_2, $address_3, $address_4, $address_postcode)==0) $returnValue = 0; //successfully edited visitor
        else $returnValue = -2; //error with query
        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @param  visitor_id
     * @return Integer
     */
    public function delete()
    {
        $returnValue = -1; //unknown error
        $visitor_id = $_GET['visitor_id'];
        $this->visitor_model->populate_from_db($visitor_id);
        if($this->visitor_model->delete($visitor_id)==0) $returnValue = 0; //successfully deleted visitor
        else $returnValue = -2; //error with query
        return $returnValue;
    }

    /**
     * Short description of method JSONify_All_Visitors
     *
     * @return void
     */
    public function JSONify_All_Visitors()
    {
        $data = array();
        if(count($this->all_visitors)<=0) return '{"data": []}'; //empty JSON for datatables to read correctly.
        foreach($this->all_visitors as $visitor=>$details) {
            $myvisitor = array();
            $myvisitor['name'] = $details->first_name.' '.$details->last_name;
            $myvisitor['email'] = $details->email;
            $myvisitor['address'] = $details->address;
            $visitor_as_json = json_encode($details, JSON_HEX_APOS);
            $myvisitor['buttons'] = "<a href='#' data-toggle='modal' data-id='$visitor_as_json' class='editModalBox' data-target='#editModalCenter'><i class='.btn-circle .btn-sm fas fa-edit'></i></a>";
            $myvisitor['buttons'] = $myvisitor['buttons'] . " | <a href='#' data-toggle='modal' data-id='$visitor_as_json' class='deleteModalBox' data-target='#deleteModalCenter'><i class='.btn-circle .btn-sm fas fa-trash'></i></a>";
            $myvisitor['buttons'] = $myvisitor['buttons'] . " | <a href='#' data-toggle='modal' data-id='$visitor_as_json' class='checkOutModalBox' data-target='#checkOutModalCenter'><i class='.btn-circle .btn-sm fas fa-sign-out-alt'></i></a>";
            $data["data"][] = $myvisitor;
        }
        return json_encode($data);
    }

    /**
     * Short description of method populate_all_visitors
     *
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
     * Short description of method check_out_device
     *
     */
    public function check_out_device()
    {
        $returnValue["xxxxxxxxxxx"]["error"][] = array("code"=>-1,"description"=>"An unknown error has occurred");
        // connect to FTP server
        $host = 'ac-device-23';
        $port = 22;
        $connection = @ssh2_connect($host, $port);
        if ($connection) {
            ssh2_auth_password($connection, 'pi', 'raspberry');

            $sftp = ssh2_sftp($connection);
            if($sftp) {
                $remote_file = "/visitor/device/path/to/status.json"; //is this device ready for a visitor to take out, already in use, or performing a system update?
                $stream = @fopen("ssh2.sftp://$sftp$remote_file", 'r');
                if(($stream) && (filesize("ssh2.sftp://$sftp$remote_file")>0)) { //if the file has a size > 0
                    $contents = fread($stream, filesize("ssh2.sftp://$sftp$remote_file"));   
                    
                    //now read the JSON file - is this device ready for a visitor to take out, already in use, or performing a system update?
                    print('<pre>'.print_r($contents,true).'</pre>');

                    //if yes, ready{
                        //copy over visitor ID via JSON
                                
                        //upload:
                        // $local_file = "";
                        // $remote_file = "/var/www/html/tts/audio_culture/cms/json/device_data_exchange/published_content.jsonn";
                        // $stream = @fopen("ssh2.sftp://$sftp$remote_file", 'w');
                        // if (! $stream)
                        //     throw new Exception("Could not open file: $remote_file");
                        // $data_to_send = @file_get_contents($local_file);
                        // if ($data_to_send === false)
                        //     throw new Exception("Could not open local file: $local_file.");
                        // if (@fwrite($stream, $data_to_send) === false)
                        //     throw new Exception("Could not send data from file: $local_file.");
                        // @fclose($stream);

                        //report back the device ID in a nice, positive way.
                    //}

                    @fclose($stream);
                }
                else
                    $returnValue["xxxxxxxxxxx"]["error"][] = array("code"=>-3,"description"=>"File does not exist, or it's 0 bytes.");
            }
            else {
                $returnValue["xxxxxxxxxxx"]["error"][] = array("code"=>-4,"description"=>"Device found but unable to log in. Please check the username & password.");
            }
        }
        else
            $returnValue["xxxxxxxxxxx"]["error"][] = array("code"=>-2,"description"=>"Could not connect to host - it probably isn't on the network.");
        
        return json_encode($returnValue, JSON_PRETTY_PRINT );

    }

} /* end of class Visitor_Controller */

?>