<?php

require_once('classes/controllers/Authenticate_Controller.php');

/**
 * Short description of class Authenticate_View
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Authenticate_View
{
    private $authenticate_controller = null;

    /**
     * Short description of method __construct
     * @param  String db_file
     */
    function __construct() {
        $this->authenticate_controller = new Authenticate_Controller();
    }

    /**
     * Short description of method login
     * @param  String username
     * @param  String password
     */
    public function login($username,$password)
    {
        $success = $this->authenticate_controller->login($username,$password);
        if($success==0){
            ?>
              <div class="card mb-4 py-3 border-bottom-success">
                <div class="card-body">
                  You are successfully logged in.
                </div>
              </div>
        <script>window.location.replace("index.php");</script>
        <?php
        }
        else {
            ?>
              <div class="card mb-4 py-3 border-bottom-danger">
                <div class="card-body">
                  Invalid login details. Please try again.
                </div>
              </div>
        <?php
        }
    }
    /**
     * Short description of method logout
     */
    public function logout()
    {
        session_destroy();
    }

    /**
     * Short description of method page_permissions
     * @param  String username
     * @param  String password
     */
    public function page_permissions($page)
    {    
       if(!$this->authenticate_controller->has_role($page)) { ?>
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h2 mb-0 text-gray-800">Permissions Error</h1>
            </div>
            <div class="card mb-4 py-3 border-left-danger">
                <div class="card-body">You do not have permissions to view this page.</div>
            </div>
        </div>
        <?php
        exit;
       }
    }

    /**
     * Short description of method has_session
     */
    public function has_session()
    {
        if(!$this->authenticate_controller->has_session()) {
            echo "You are not logged in!";
            header('Location: login.php');
            exit;
        }
    }
    /**
     * Short description of method display_name
     */
    public function display_name()
    {
        echo $this->authenticate_controller->get_display_name();
    }
    
    /**
     * Short description of method display_name
     */
    public function get_staff_id()
    {
        return $this->authenticate_controller->get_staff_id();
    }

    /**
     * Short description of method display_menu
     */
    public function display_menu()
    {
     if($this->authenticate_controller->has_role(STAFF_DB_MANAGER)) {
         ?>
        <!-- Nav Item - Staff Management -->
        <li class="nav-item active">
          <a class="nav-link" href="?page=manage_staff">
            <i class="fas fa-fw fa-id-card"></i>
            <span>Manage Staff</span></a>
        </li>
        <?php
       }
      if($this->authenticate_controller->has_role(CONTENT_MANAGER)) {
        ?>
        <!-- Nav Item - Content Collapse Menu -->
        <li class="nav-item active">
          <a class="nav-link" href="?page=manage_content">
            <i class="fas fa-fw fa-file-audio"></i>
            <span>Manage Content</span></a>
        </li>
        <?php
       }
      if($this->authenticate_controller->has_role(REPORT_MANAGER)) { 
        ?>
        <!-- Nav Item - Report Management -->
        <li class="nav-item active">
          <a class="nav-link" href="?page=manage_reports">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Manage Reports</span></a>
        </li>
        <?php
       }
       if($this->authenticate_controller->has_role(VISITOR_DB_MANAGER)) { 
        ?>
        <!-- Nav Item - Visitor Management -->
        <li class="nav-item active">
          <a class="nav-link" href="?page=manage_visitors">
            <i class="fas fa-fw fa-users"></i>
            <span>Manage Visitors</span></a>
        </li>
        <?php
       }
    }
}
?>