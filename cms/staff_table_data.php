<?php

require_once('config.php');

require_once('classes/views/Staff_View.php');
$staff_view = new Staff_View();
switch($_GET['action']) {
    case 'display_table':
        // $staff_view->display_table_all_staff();
        // $staff_view->new_modal();
        // $staff_view->deactivate_modal();
        // $staff_view->edit_modal();
        echo "Howdy";
        break;
}

?>