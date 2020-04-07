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
        $visitor_view->create_new($_GET['first_name'],$_GET['last_name'],$_GET['email'],$_GET['address_1'],$_GET['address_2'],$_GET['address_3'],$_GET['address_4'],$_GET['address_postcode']);
        break;
    case 'edit':
        $visitor_view->edit($_GET['visitor_id'],$_GET['first_name'],$_GET['last_name'],$_GET['email'],$_GET['address_1'],$_GET['address_2'],$_GET['address_3'],$_GET['address_4'],$_GET['address_postcode']);
        break;
    case 'delete':
        $visitor_view->delete($_GET['visitor_id']);
        break;
}
?>