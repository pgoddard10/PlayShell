<?php
/**
 * Ajax requests from the Manage Staff page.
 * Allows add/edit/delete functionality
 *
 * @author Paul Goddard, paul2.goddard@live.uwe.ac.uk
 * @date Spring 2020 
 * 
 */
session_start(); //required for PHP session (used to confirm user is logged in)
require_once('config.php');
require_once('classes/views/Authenticate_View.php');
$authenticate_view = new Authenticate_View();
$authenticate_view->has_session();
$authenticate_view->page_permissions(STAFF_DB_MANAGER);

require_once('classes/views/Staff_View.php');
$staff_view = new Staff_View();
if(isset($_GET['roles'])){
    $roles = $_GET['roles'];
}
else {
    $roles = array();
}
switch($_GET['action']) {
    case 'new':
        $staff_view->create_new($_GET['first_name'],$_GET['last_name'],$_GET['username'],$_GET['password'],$_GET['repeat_password'],$_GET['email'],$roles);
        break;
    case 'edit':
        $staff_view->edit($_GET['staff_id'],$_GET['first_name'],$_GET['last_name'],$_GET['password'],$_GET['repeat_password'],$_GET['email'],$_GET['active'],$roles);
        break;
    case 'deactivate':
        $staff_view->deactivate($_GET['staff_id']);
        break;
}
?>