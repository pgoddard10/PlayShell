<?php
/**
 * Ajax requests from the Manage Staff page.
 * Responsible for getting the contents of the table
 * Table contains all staff details
 *
 * @author Paul Goddard, paul2.goddard@live.uwe.ac.uk
 * @date Spring 2020 
 * 
 */

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