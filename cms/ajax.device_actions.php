<?php
/**
 * Ajax requests from the Manage Device section.
 *
 * @author Paul Goddard, paul2.goddard@live.uwe.ac.uk
 * @date Spring 2020 
 * 
 */
session_start(); //required for PHP session (used to confirm user is logged in)
require_once('config.php');

//check user is logged in & has permissions
require_once('classes/views/Authenticate_View.php');
$authenticate_view = new Authenticate_View();
$authenticate_view->has_session();
$authenticate_view->page_permissions(DEVICE_MANAGER);

require_once('classes/views/Device_View.php');
$device_view = new Device_View();
switch($_GET['action']) {
    case 'retreive_visitor_data':
        $device_view->retreive_visitor_data();
        break;
    case 'update_device':
        $device_view->update_device();
        break;
}
?>