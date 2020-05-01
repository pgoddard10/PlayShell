<?php
/**
 * Ajax requests from the login page to check authentication.
 * Links to the login and logout methods for staff members.
 *
 * @author Paul Goddard, paul2.goddard@live.uwe.ac.uk
 * @date Spring 2020 
 * 
 */

session_start();
require_once('config.php');
require_once('classes/views/Authenticate_View.php');
$authenticate_view = new Authenticate_View();
switch($_GET['action']) {
    case 'login':
        $authenticate_view->login($_GET['username'],$_GET['password']);
        break;
    case 'logout':
        $authenticate_view->logout();
        break;
}
?>