<?php
/**
 * Class Device_Controller
 * Responsible for handling the logic around device sync/interactions
 *
 * @author	Paul Goddard
 * 			paul2.goddard@live.uwe.ac.uk
 * 			https://github.com/pgoddard10/
 * 			https://www.linkedin.com/in/pgoddard10/
 * 			https://twitter.com/pgoddard10
 * @date Spring 2020 
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once "vendor/autoload.php";
require_once('classes/models/Visitor_Model.php');

class Device_Controller
{
    /**
    * method read_remote_json_file()
    * This goes off to the visitor device specified and reads the file specified
    * @param  String $host  - remote device network name
    * @param  String $remote_file - file location of the JSON
    * @return JSON String $returnValue - JSON confirms whether successful or not. Errors are negative numbers, default unknown error is -1
    */
    private function read_remote_json_file($host,$remote_file)
    {
        $returnValue["data"]["error"] = array("code"=>-1,"description"=>"An unknown error has occurred");
        // connect to FTP server
        $port = 22;
        $connection = @ssh2_connect($host, $port);
        if ($connection) {
            ssh2_auth_password($connection, FTP_USERNAME, FTP_PASSWORD);
            $sftp = ssh2_sftp($connection);
            if($sftp) {
                $stream = @fopen("ssh2.sftp://$sftp$remote_file", 'r');
                if(($stream) && (filesize("ssh2.sftp://$sftp$remote_file")>0)) { //if the file has a size > 0
                    $returnValue = fread($stream, filesize("ssh2.sftp://$sftp$remote_file")); 
                    @fclose($stream);
                }
                else
                    $returnValue["data"]["error"] = array("code"=>-3,"description"=>"File does not exist, or it's 0 bytes.");
            }
            else {
                $returnValue["data"]["error"] = array("code"=>-4,"description"=>"Device found but unable to log in. Please check the username & password.");
            }
        }
        else {
            $returnValue["data"]["error"] = array("code"=>-5,"description"=>"Device not found.");
        }
        return $returnValue;
    }

	/**
	 * method upload_file()
	 * uploads the local file to the remote location on the specified device
	 * @param  String $host  - remote device network name
	 * @param  String $local_file location on this server
	 * @param  String $remote_file - location on the $host
     * @return JSON String $returnValue - JSON confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    private function upload_file($host,$local_file,$remote_file)
    {
        $returnValue["data"]["error"] = array("code"=>-1,"description"=>"An unknown error has occurred");
        // connect to FTP server
        $port = 22;
        $connection = @ssh2_connect($host, $port);
        if ($connection) {
            ssh2_auth_password($connection, FTP_USERNAME, FTP_PASSWORD);
            $sftp = ssh2_sftp($connection);
            if($sftp) {
                $stream = @fopen("ssh2.sftp://$sftp$remote_file", 'w');
                if (!$stream)
                    $returnValue["data"]["error"] = array("code"=>-4,"description"=>"Could not open file: $remote_file.");
                $data_to_send = @file_get_contents($local_file);
                if ($data_to_send === false)
                    $returnValue["data"]["error"] = array("code"=>-5,"description"=>"Could not open local file: $local_file.");
                if (@fwrite($stream, $data_to_send) === false)
                    $returnValue["data"]["error"] = array("code"=>-6,"description"=>"Could not send data from file: $local_file.");
                @fclose($stream);
                $returnValue["data"] = array("success" => array("code"=>0,"description"=>"Successfully transferred file."));
            }
            else {
                $returnValue["data"]["error"] = array("code"=>-3,"description"=>"Device found but unable to log in. Please check the username & password.");
            }
        }
        else
            $returnValue["data"]["error"] = array("code"=>-2,"description"=>"Device not found");

        return json_encode($returnValue, JSON_HEX_APOS);
    }

	/**
	 * method mk_remote_dir()
	 * mkdir() but on the $host
	 * @param  String $host  - remote device network name
	 * @param  String $path - remote file path
     * @return JSON String $returnValue - JSON confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    private function mk_remote_dir($host,$path)
    {
        $returnValue["data"]["error"] = array("code"=>-1,"description"=>"An unknown error has occurred");
        // connect to FTP server
        $port = 22;
        $connection = @ssh2_connect($host, $port);
        if ($connection) {
            ssh2_auth_password($connection, FTP_USERNAME, FTP_PASSWORD);
            $sftp = ssh2_sftp($connection);
            if($sftp) {
               if(ssh2_sftp_mkdir($sftp, $path))  $returnValue["data"] = array("success" => array("code"=>0,"description"=>"Successfully created the directory."));
               else  $returnValue["data"]["error"] = array("code"=>-4,"description"=>"Connected and logged in, but unable to create the directory.");
            }
            else {
                $returnValue["data"]["error"] = array("code"=>-3,"description"=>"Device found but unable to log in. Please check the username & password.");
            }
        }
        else
            $returnValue["data"]["error"] = array("code"=>-2,"description"=>"Device not found");

        $returnValue = json_encode($returnValue, JSON_HEX_APOS);
        return $returnValue;
    }

	/**
	 * method update_status_on_device()
	 * Sets the device status on the $host - prevents 
	 * @param  int $status - device status number
	 * @param  String $host  - remote device network name
	 */
    private function update_status_on_device($status,$host) {
        $local_file = PUBLISHED_CONTENT_FOLDER."status.json";
        if     ($status==DEVICE_READY) $name = "ready";
        else if($status==DEVICE_IN_USE) $name = "in use";
        else if($status==DEVICE_UPDATING) $name = "Device updating";
        else if($status==CMS_UPDATING) $name = "CMS updating";
        $status_data["status"] = array("code"=>$status, "name"=>$name);
        $status_json = json_encode($status_data, JSON_HEX_APOS);
        $fp = fopen($local_file, 'w');
        fwrite($fp, $status_json);
        fclose($fp);
        chmod($local_file,0666); //set permissions
        $this->upload_file($host,$local_file,DEVICE_DATA_FOLDER."status.json");
    }

	/**
	 * method check_status_on_device()
	 * log onto the $host and grab the status JSON file, decode it to pass the device status back
	 * @param  String $host  - remote device network name
     * @return JSON String $returnValue - JSON confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    private function check_status_on_device($host) {
        $returnValue["data"]["error"] = array("code"=>-1,"description"=>"An unknown error has occurred");
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
                    $returnValue = json_decode($contents, true);
                }
                else
                    $returnValue["data"]["error"] = array("code"=>-3,"description"=>"File does not exist, or it's 0 bytes.");
            }
            else 
                $returnValue["data"]["error"] = array("code"=>-4,"description"=>"Device found but unable to log in. Please check the username & password.");
        }
        else
            $returnValue["data"]["error"] = array("code"=>-2,"description"=>"Connection failed.");

        return $returnValue;
    }
    
    /**
	 * method send_email()
	 * sends email via SMTP
	 * @param  String $to_email - email address
	 * @param  String $to_name - name for the To: field
	 * @param  String $body - email body
	 */
    private function send_email($to_email,$to_name,$body) {
        $returnValue["data"]["error"] = array("code"=>-1,"description"=>"An unknown error has occurred");
        $mail = new PHPMailer();
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp-mail.outlook.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'yyyyy';                     // SMTP username
            $mail->Password   = 'xxxxx';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        
            //Recipients
            $mail->setFrom('yyyy', 'Paul Goddard');
            $mail->addAddress($to_email, $to_name);     // Add a recipient
        
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Your recent visit to our centre';
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);
        
            $mail->send();
        } catch (Exception $e) {

        }
    }

    /**
	 * method retreive_visitor_data()
	 * Gets the visitor data off of all available devices on the network, calls the Model to load into the database
     * @return JSON String $returnValue - JSON confirms whether successful or not. Errors are negative numbers, default unknown error is -1
	 */
    // retreive_visitor_data from device and copy into db (user_history)
    public function retreive_visitor_data() {
        $returnValue["data"]["error"] = array("code"=>-1,"description"=>"An unknown error has occurred.");

        for($i = 1; $i < (NUMBER_OF_VISITOR_DEVICES+1); $i++) { //+1 as NUMBER_OF_VISITOR_DEVICES is human number, not computer number
            $host = VISITOR_DEVICE_PREFIX.'-'.$i;
            $current_status = $this->check_status_on_device($host);
            if(isset($current_status['status']) && $current_status['status']['code']==DEVICE_READY) {
                $visitor_json = $this->read_remote_json_file($host,DEVICE_DATA_FOLDER."outgoing_visitor_data.json"); //read remote JSON file

                $this->update_status_on_device(CMS_UPDATING,$host); //update status.json to make device unavailable for check-out
                
                $visitor_details = json_decode($visitor_json, true);
                foreach($visitor_details["data"] as $row) {
                    $visitor_model = new Visitor_Model();
                    $visitor_model->insert_visitor_history($row['content_id'], $row['time_scanned'], $row['visitor_id']);
                }
                $returnValue["data"] = array("success" => array("code"=>0,"description"=>"Successfully copied visitor interactions from the device into the CMS."));
                $this->update_status_on_device(DEVICE_READY,$host); //update status.json to make device unavailable for check-out
            }
            else {
                $returnValue["data"]["error"] = array("code"=>-2,"description"=>"The device is currently busy.");
            }
        }

        $this->compose_email();

        $returnValue = json_encode($returnValue, JSON_HEX_APOS);
        return $returnValue;
    }

	/**
	 * method compose_email()
	 * Sets up the emails and calls the send_email function which does the actual sending
	 */
    public function compose_email() {
		if($db = new SQLite3(DATABASE_FILE)){
			$stm = $db->prepare("SELECT item.name, item.url, content.item_id, visitor.first_name, visitor.email FROM visitor_history             LEFT JOIN content ON visitor_history.content_id = content.content_id            LEFT JOIN item ON item.item_id = content.item_id            LEFT JOIN visitor ON visitor.visitor_id = visitor_history.visitor_id            WHERE visitor.email NOT NULL            GROUP BY visitor_history.visitor_id, item.item_id"); //build the SQL
            $visitor = $stm->execute();
            $to_email = array();
            while($row = $visitor->fetchArray()) {
                $visitor_email = $row['email'];
                $visitor_first_name = $row['first_name'];
                $item_name = $row['name'];
                $item_url = $row['url'];
                $to_email[$visitor_email][] = array("email" => $row['email'],"visitor_first_name" => $row['first_name'], "name"=>$item_name,"url"=>$item_url);
            }

            foreach ($to_email as $value) {
                $visitor_first_name = $value[0]["visitor_first_name"];
                $email = $value[0]["email"];
                $item_name = $value[0]["name"];
                $item_url = $value[0]["url"];
                $body = "Dear $visitor_first_name, Many thanks for visiting our wonderful centre. You interacted with $item_name. Find out more at $item_url!";
                $this->send_email($email, $visitor_first_name, $body);
            }
        }
    }

    /**
	 * method update_device()
	 * Copies all published content over to every audio device on the network and in the status of "ready"
	 * @return Integer $returnValue - JSON containing successful or not. Errors are negative numbers, default unknown error is -1
	 */
    public function update_device() {
        $returnValue["data"]["error"] = array("code"=>-1,"description"=>"An unknown error has occurred.");

        //loop through all devices specified in the config.php file
        for($i = 1; $i < (NUMBER_OF_VISITOR_DEVICES+1); $i++) { //+1 as NUMBER_OF_VISITOR_DEVICES is human number, not computer number
            $host = VISITOR_DEVICE_PREFIX.'-'.$i;
            //if the status of this device is ready, i.e. not "in use" or "updating" then we can update
            //otherwise we can't update right now as the system is in use!
            $current_status = $this->check_status_on_device($host);
            if(isset($current_status['status']) && $current_status['status']['code']==DEVICE_READY) {
                $local_file = PUBLISHED_CONTENT_FOLDER.PUBLISHED_CONTENT_FILE;
                $remote_file = DEVICE_DATA_FOLDER."published_content.json";
                $this->update_status_on_device(DEVICE_UPDATING,$host); //update status.json to make device unavailable for check-out
                $this->upload_file($host,$local_file,$remote_file); //copy published_content.json
                
                //copy entire audio folder
                $remote_audio_folder = DEVICE_DATA_FOLDER."audio";
                $local_audio_folder = PUBLISHED_CONTENT_FOLDER."audio";
                $success = true;
                //loop through the directory and its contents
                foreach (
                    $iterator = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($local_audio_folder, \RecursiveDirectoryIterator::SKIP_DOTS),
                        \RecursiveIteratorIterator::SELF_FIRST) as $item
                    ) {
                        if ($item->isDir()) {
                            //make the dir on the remote system
                            $this->mk_remote_dir($host, $remote_audio_folder . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                        } else {
                            //copy the file on the remote system
                            $response_json = $this->upload_file($host,$local_audio_folder,$remote_audio_folder . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                            $response = json_decode($response_json, true);
                            if($success && isset($response['data']['success']) && $response['data']['success']['code']==0)
                                $success = true;
                            else
                                $success = false;
                        }
                    }

                if($success) $returnValue["data"] = array("success" => array("code"=>0,"description"=>"Successfully updated the device."));
                else $returnValue["data"]["error"] = array("code"=>-3,"description"=>"File upload failed.");
                
            }
            else {
                $returnValue["data"]["error"] = array("code"=>-2,"description"=>"The device is not ready to be updated.");
            }
        }
        $returnValue = json_encode($returnValue, JSON_HEX_APOS);
        return $returnValue;
    }


} /* end of class Device_Controller */

?>
