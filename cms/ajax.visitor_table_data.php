<?php

header('Content-Type: application/json');
require_once('config.php');
require_once('classes/views/Visitor_View.php');
$visitor_view = new Visitor_View();
switch($_GET['action']) {
    case 'display_table':
        echo $visitor_view->JSONify_All_Visitors();
        break;
}
?>