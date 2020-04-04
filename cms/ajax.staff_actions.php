<?php

require_once('config.php');

require_once('classes/views/Staff_View.php');
$staff_view = new Staff_View();
switch($_GET['action']) {
    case 'new':
        $staff_view->create_new($_GET['first_name'],$_GET['last_name'],$_GET['username'],$_GET['password'],$_GET['repeat_password'],$_GET['email'],$_GET['roles']);
        break;
    case 'edit':
        $staff_view->edit($_GET['staff_id'],$_GET['first_name'],$_GET['last_name'],$_GET['username'],$_GET['password'],$_GET['repeat_password'],$_GET['email'],$_GET['active'],$_GET['roles']);
        break;
    case 'deactivate':
        $staff_view->deactivate($_GET['staff_id']);
        break;
}
?>