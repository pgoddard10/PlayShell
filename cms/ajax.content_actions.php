<?php
/**
 * Ajax requests from the Manage Content page.
 * Allows add/edit/delete functionality
 *
 * @author Paul Goddard, paul2.goddard@live.uwe.ac.uk
 * @date Spring 2020 
 * 
 */

require_once('config.php');
require_once('classes/views/Item_View.php');
$item_view = new Item_View();
switch($_GET['action']) {
    case 'new':
        $item_view->create_new($_GET['heritage_id'],$_GET['name'],$_GET['location'],$_GET['url'],$_GET['active'],1);
        break;
    case 'edit':
        $item_view->edit($_GET['item_id'],$_GET['heritage_id'],$_GET['name'],$_GET['location'],$_GET['url'],$_GET['active'],1);
        break;
    case 'delete':
        $item_view->delete($_GET['item_id']);
        break;
}
?>