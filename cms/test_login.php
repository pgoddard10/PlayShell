<?php

session_start();
require_once('config.php');
require_once('classes/views/Authenticate_View.php');
$authenticate_view = new Authenticate_View();
$authenticate_view->login($_GET['username'],$_GET['password']);

?>