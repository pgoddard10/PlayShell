<?php

require_once("config.php");
require_once("classes/controllers/Device_Controller.php");

$dev = new Device_Controller();

// $dev->retreive_visitor_data();
$dev->copy_all();

?>