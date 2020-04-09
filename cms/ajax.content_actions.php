<?php
/**
 * Ajax requests from the Manage Content page.
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
$authenticate_view->page_permissions(CONTENT_MANAGER);
$logged_in_staff_id = $authenticate_view->get_staff_id();

require_once('classes/views/Content_View.php');
require_once('classes/views/Item_View.php');
$item_view = new Item_View();
if(isset($_GET['item_id'])) $content_view = new Content_View($_GET['item_id']);
else $content_view = new Content_View();
switch($_GET['action']) {
    case 'new_item':
        $item_view->create_new($_GET['heritage_id'],$_GET['name'],$_GET['location'],$_GET['url'],$_GET['active'],$logged_in_staff_id);
        break;
    case 'edit_item':
        $item_view->edit($_GET['item_id'],$_GET['heritage_id'],$_GET['name'],$_GET['location'],$_GET['url'],$_GET['active'],$logged_in_staff_id);
        break;
    case 'delete_item':
        $item_view->delete($_GET['item_id']);
        break;
    case 'new_content':
        $content_view->create_new($logged_in_staff_id);
        break;
    case 'delete_content':
        $content_view->delete();
        break;
    case 'edit_content':
        $content_view->edit($logged_in_staff_id);
        break;
    case 'scan_nfc_tag':
        $content_view->scan_nfc_tag();
        break;
    case 'get_nfc_id':
        $content_view->get_nfc_id();
        break;
}
?>