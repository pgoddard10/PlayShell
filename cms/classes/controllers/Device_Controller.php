<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once "vendor/autoload.php";

/**
 * Short description of class Device_Controller
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Device_Controller
{
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
     * Short description of method check_out_device
     *
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
        echo "set status to $status, $name";
    }


    private function check_status_on_device($host) {
        $returnValue["data"]["error"] = array("code"=>-1,"description"=>"An unknown error has occurred");
        $host = "ac-device-1";
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
    
    // retreive_visitor_data from device and copy into db (user_history)
    public function retreive_visitor_data() {
        $host = $_GET['device'];
        $host = "ac-device-1";
        $current_status = $this->check_status_on_device($host);
        if($current_status['status']['code']==DEVICE_READY) {
            $visitor_json = $this->read_remote_json_file($host,DEVICE_DATA_FOLDER."outgoing_visitor_data.json"); //read remote JSON file

            $this->update_status_on_device(CMS_UPDATING,$host); //update status.json to make device unavailable for check-out
            
            print('<pre>'.print_r($visitor_json, true).'</pre>');
            //remote_visitor.last_update
            //loop through json
                //if (remote.(content_id, visitor_id, timestamp) != db.user_history.(content_id, visitor_id, timestamp)
                    //save contents into DB
            $this->update_status_on_device(DEVICE_READY,$host); //update status.json to make device available for check-out
        }
        else {
            echo "retreive_visitor_data() device is not ready for use";
        }
    }

    // push content updates over to the device
    public function update_device() {
        $host = $_GET['device'];
        $host = "ac-device-1";
        $current_status = $this->check_status_on_device($host);
        if($current_status['status']['code']==DEVICE_READY) {
            $local_file = PUBLISHED_CONTENT_FOLDER.PUBLISHED_CONTENT_FILE;
            $remote_file = DEVICE_DATA_FOLDER."published_content.json";
            $this->update_status_on_device(DEVICE_UPDATING,$host); //update status.json to make device unavailable for check-out
            $this->upload_file($host,$local_file,$remote_file); //copy published_content.json
            //copy entire audio folder
            $this->update_status_on_device(DEVICE_READY,$host); //update status.json to make device available for check-out
        }
        else {
            echo "update_device() device is not ready for use";
        }
    }

    public function copy_all(){
        $host = $_GET['device'];
        $host = "ac-device-1";
        $current_status = $this->check_status_on_device($host);
        if($current_status['status']['code']==DEVICE_READY) {
            $this->update_status_on_device(DEVICE_UPDATING,$host); //update status.json to make device unavailable for check-out
            $remote_file = DEVICE_DATA_FOLDER."audio";
            $local_file = PUBLISHED_CONTENT_FOLDER."audio";
            foreach (
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($local_file, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST) as $item
                ) {
                    if ($item->isDir()) {
                        $this->mk_remote_dir($host, $remote_file . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                    } else {
                        $response = $this->upload_file($host,$local_file,$remote_file . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                        print('<pre>'.print_r($response, true).'</pre>');
                    }
                }
            $this->update_status_on_device(DEVICE_READY,$host); //update status.json to make device available for check-out
        }
        else {
            echo "copy_all() device is not ready for use";
        }
    }

    
    public function send_email($to_email,$to_name,$body) {
        $mail = new PHPMailer();
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp-mail.outlook.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = '######';                     // SMTP username
            $mail->Password   = '#########';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        
            //Recipients
            $mail->setFrom('###', 'Paul Goddard');
            $mail->addAddress($to_email, $to_name);     // Add a recipient
        
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'This is a test email';
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);
        
            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }


} /* end of class Device_Controller */

?>
