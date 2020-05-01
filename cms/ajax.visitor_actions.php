<?php
/**
 * Ajax requests from the Manage Visitors page.
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
$authenticate_view->page_permissions(VISITOR_DB_MANAGER);

require_once('classes/views/Visitor_View.php');
$visitor_view = new Visitor_View();
switch($_GET['action']) {
    case 'new':
        $visitor_view->create_new();
        break;
    case 'edit':
        $visitor_view->edit();
        break;
    case 'delete':
        $visitor_view->delete();
        break;
    case 'check_out_device':
        $visitor_view->check_out_device();
        break;
}
?>