<?php

header('Content-Type: application/json');
require_once('config.php');
require_once('classes/views/Staff_View.php');
$staff_view = new Staff_View();
switch($_GET['action']) {
    case 'display_table':
        echo $staff_view->JSONify_All_Staff();
        break;
}
?>