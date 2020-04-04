<?php

require_once('config.php');

require_once('classes/views/Staff_View.php');
$staff_view = new Staff_View();
switch($_GET['action']) {
    case 'display_table':
        $staff_view->display_table_all_staff();
        break;
}

?>