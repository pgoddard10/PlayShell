<?php

require_once('config.php');
require_once('classes/views/Staff_View.php');
require_once('classes/views/Authenticate_View.php');
$staff_view = new Staff_View();
$authenticate_view = new Authenticate_View();
$authenticate_view->has_session();



function write_blank_file($filename) {
    $fp = fopen($filename, 'w');
    fwrite($fp, "");
    fclose($fp);
    chmod($filename,0666); //set permissions to allow both the C++ app and PHP system to write to the files
}


function create_tts($filename, $content) {
    $provider = new \duncan3dc\Speaker\Providers\PicottsProvider;
    $tts = new \duncan3dc\Speaker\TextToSpeech($content, $provider);
    if(file_put_contents($filename, $tts->getAudioData())) {
        return true;
    }
    else {
        return false;
    }
}

$content_id_file = "json/content.json"; //contains the outgoing content id (i.e. from the PHP script to the C++ app)
$nfc_details_file = "json/tag_data.json"; //contains the returning NFC tag id (i.e. from the C++ app to the PHP page)

if(!file_exists($content_id_file)) write_blank_file($content_id_file);
if(!file_exists($nfc_details_file)) write_blank_file($nfc_details_file);

if(isset($_GET['content_id'])) {
    //mimic a content ID being provided from the Database
    //convert the content ID into a JSON object and save into a file
    echo "Writing the content id to the .json file<br />";
    $posts['content_id'] = $_GET['content_id'];
    $fp = fopen($content_id_file, 'w');
    fwrite($fp, json_encode($posts));
    fclose($fp);
    echo ' [ <a href="?page=manage_content&cid='.$_GET['content_id'].'">I have scanned the tag</a> ]<br />';
}
else if($tag_data = file_get_contents($nfc_details_file)) {
    //if the NFC details have been provided from the C++ app
    //open the file, get the JSON
    $tag_data_json = json_decode($tag_data, true);
    print('<pre>'.print_r($tag_data_json,true).'</pre>');
    
    //check that the content_id in the file matches the one provided in the PHP (to ensure no accidental cross-over)
    if($tag_data_json['content_id']==$_GET['cid']) {
        echo "assign this content to the tag ".$tag_data_json['nfc_tag']."<br />";
        $filename = $tag_data_json['nfc_tag'].'.mp3';
        $content = "This is some faked content to read aloud";
        create_tts($filename, $content);
    }
    else {
        echo "something went wrong<br />";
    }

    //empty the files to prevent accidents on future reads
    write_blank_file($content_id_file);
    write_blank_file($nfc_details_file);
}
else {
    ?>

    <h4>New content:</h4>
    <ol>
    <li>Create some real content and insert into the database</li>
    <li>Get content_id from database and generate json file</li>
    <li>Tell user to go scan the tag on the NFC reader</li>
    <li>C++ app will generate the json response</li>
    <li>User must click a "I've scanned the tag" button (might be able to automate, but only if there is time)</li>
    <li>PHP page gets NFC tag ID and saves against the content_id in the database</li>
    <li>PHP page creates a .mp3 file</li>
    </ol>
    Some of this can be tested/faked...<br />
    <ol>
    <li>Add <i>&content_id=</i> to the URL to fake step 2 above</li>
    <li>Follow steps 3 to 5 as above</li>
    <li>The .mp3 file is also created</li>
    </ol>
    <?php
}

?>