<?php

require_once('classes/models/Content_Model.php');

/**
 * Short description of class Content_Controller
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Content_Controller
{
    private $content_model = null;
    public $all_contents = array();
    public $item_id = null; 

    /**
     * Short description of method __construct
     * @param  String db_file
     */
    function __construct($item_id) {
        $this->item_id = $item_id;
        $this->content_model = new Content_Model($this->item_id);
    }

    /**
     * Short description of method create_new
     *
     * @param 
     * @return Integer
     */
    public function create_new($created_by)
    {
        $written_text = null;
        $sound_file = null;
        $gesture = null;
        
        if($_POST['tts_enabled']==1) {
            $written_text = $_POST['written_text'];
            $this->convert_text_to_speech($written_text);
        }
        else {
            print('File to upload <pre>'.print_r($_FILES['sound_file'],true).'</pre>');
            $sound_file = $_FILES['sound_file'];
            // Configure The "php.ini" File
            // In your "php.ini" file, search for the file_uploads directive, and set it to On:
            // file_uploads = On
            // see bottom of https://www.w3schools.com/php/php_file_upload.asp for complete script, including validation
        }
        if(isset($_POST['gesture'])) $gesture = $_POST['gesture'];

        $name = $_POST['name'];
        $tts_enabled = $_POST['tts_enabled'];
        $next_content = $_POST['next_content'];
        $active = $_POST['active'];
        $item_id = $_POST['item_id'];

        $returnValue = -1;//unknown error
        if($this->content_model->create_new($item_id, $created_by, $name, $tts_enabled, $next_content, $active, $written_text, $gesture, $sound_file)==0) $returnValue = 0;
        return $returnValue;
    }

    /**
     * Short description of method edit
     *
     * @param  
     * @return Integer
     */
    public function edit($content_id, $heritage_id, $name, $location, $url, $active, $modified_by)
    {
        $returnValue = -1; //unknown error
        $this->content_model->populate_from_db($content_id);
        if($this->content_model->edit($content_id, $heritage_id, $name, $location, $url, $active, $modified_by)==0) $returnValue = 0; //successfully edited visitor
        else $returnValue = -2; //error with query
        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @param  content_id
     * @return Integer
     */
    public function delete($content_id)
    {
        $returnValue = -1; //unknown error
        $this->content_model->populate_from_db($content_id);
        if($this->content_model->delete($content_id)==0) $returnValue = 0; //successfully deleted the content
        else $returnValue = -2; //error with query
        return $returnValue;
    }

    /**
     * Short description of method populate_all_contentss
     *
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
        
        // print('populate_all_contents() <pre>'.print_r($this->all_contents,true).'</pre>');
    }

} /* end of class Content_Controller */

?>