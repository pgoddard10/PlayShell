<?php

require_once('classes/controllers/Staff_Controller.php');

/**
 * Short description of class Authenticate_View
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Authenticate_View
{
    private $staff_controller = null;

    /**
     * Short description of method __construct
     * @param  String db_file
     */
    function __construct() {
        $this->staff_controller = new Staff_Controller();
    }

    /**
     * Short description of method login
     * @param  String username
     * @param  String password
     */
    public function login($username,$password)
    {
        $success = $this->staff_controller->login($username,$password);
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
       if(!$this->staff_controller->has_role($page)) { ?>
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
        if(!$this->staff_controller->has_session()) {
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
        echo $this->staff_controller->get_display_name();
    }

    /**
     * Short description of method display_menu
     */
    public function display_menu()
    {
     if($this->staff_controller->has_role(STAFF_DB_MANAGER)) {
         ?>
        <!-- Nav Item - Staff Management -->
        <li class="nav-item active">
          <a class="nav-link" href="?page=manage_staff">
            <i class="fas fa-fw fa-id-card"></i>
            <span>Manage Staff</span></a>
        </li>
        <?php
       }
      if($this->staff_controller->has_role(CONTENT_MANAGER)) {
        ?>
        <!-- Nav Item - Content Collapse Menu -->
        <li class="nav-item active">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <i class="fas fa-fw fa-file-audio"></i>
            <span>Content</span>
          </a>
          <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
              <a class="collapse-item" href="?page=manage_content">Check out</a>
              <a class="collapse-item" href="?page=manage_content">Add New</a>
            </div>
          </div>
        </li>
        <?php
       }
      if($this->staff_controller->has_role(REPORT_MANAGER)) { 
        ?>
        <!-- Nav Item - Report Management -->
        <li class="nav-item active">
          <a class="nav-link" href="?page=manage_reports">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Reports</span></a>
        </li>
        <?php
       }
       if($this->staff_controller->has_role(VISITOR_MANAGER)) { 
        ?>
        <!-- Nav Item - Visitor Management -->
        <li class="nav-item active">
          <a class="nav-link" href="?page=manage_visitors">
            <i class="fas fa-fw fa-users"></i>
            <span>Manage Visitors</span></a>
        </li>
        <?php
       }
      if($this->staff_controller->has_role(DEVICE_MANAGER)) { 
        ?>
        <!-- Nav Item - Device Collapse Menu -->
        <li class="nav-item active">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
            <i class="fas fa-fw fa-hdd"></i>
            <span>Devices</span>
          </a>
          <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
              <a class="collapse-item" href="?page=manage_devices">Check out</a>
              <a class="collapse-item" href="?page=manage_devices">Add New</a>
            </div>
          </div>
        </li>
        <?php
      }
    }
}
?>