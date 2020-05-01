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
switch($_GET['action']) {
    case 'new':
        $staff_view->create_new();
        break;
    case 'edit':
        $staff_view->edit();
        break;
    case 'delete':
        $staff_view->delete();
        break;
}
?>