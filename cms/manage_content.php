<?php
if(!isset($staff_controller)) { //if trying to load the page directly, redirect
    header('Location: index.php');
    exit;
}
if(!in_array(CONTENT_MANAGER,$staff_controller->roles)){
    exit("You do not have permission to use this page.");
}


function write_blank_file($filename) {
    $fp = fopen($filename, 'w');
    fwrite($fp, "");
    fclose($fp);
    chmod($filename,0666); //set permissions to allow both the C++ app and PHP system to write to the files
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
    echo '<a href="?page=manage_content&cid='.$_GET['content_id'].'">Continue</a><br />';
}
else if($tag_data = file_get_contents($nfc_details_file)) {
    //if the NFC details have been provided from the C++ app
    //open the file, get the JSON
    $tag_data_json = json_decode($tag_data, true);
    print('<pre>'.print_r($tag_data_json,true).'</pre>');
    
    //check that the content_id in the file matches the one provided in the PHP (to ensure no accidental cross-over)
    if($tag_data_json['content_id']==$_GET['cid']) {
        echo "assign this content to the tag ".$tag_data_json['nfc_tag']."<br />";
    }
    else {
        echo "something went wrong<br />";
    }

    //empty the files to prevent accidents on future reads
    write_blank_file($content_id_file);
    write_blank_file($nfc_details_file);
}
else {
    echo "Welcome to the page.";
}

?>