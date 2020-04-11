<?php
/**
 * Ajax requests from the admin pages.
 * Responsible for getting the contents of the table
 * Table contains all staff details
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

header('Content-Type: application/json');
require_once('config.php');
require_once('classes/views/Visitor_View.php');
require_once('classes/views/Item_View.php');
require_once('classes/views/Content_View.php');
require_once('classes/views/Staff_View.php');
switch($_GET['page']) {
    case 'visitor':
        $authenticate_view->page_permissions(VISITOR_DB_MANAGER);
        $visitor_view = new Visitor_View();
        $visitor_view->JSONify_All_Visitors();
        break;
    case 'staff':
        $authenticate_view->page_permissions(STAFF_DB_MANAGER);
        $staff_view = new Staff_View();
        $staff_view->JSONify_All_Staff();
        break;
    case 'item':
        $authenticate_view->page_permissions(CONTENT_MANAGER);
        $item_view = new Item_View();
        $item_view->JSONify_All_Items();
        break;
    case 'content':
        $authenticate_view->page_permissions(CONTENT_MANAGER);
        $content_view = new Content_View($_GET['item_id']);
        echo $content_view->JSONify_All_Contents();
        break;
}
?>