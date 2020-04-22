<?php
/**
 * Class Authenticate_View
 * Responsible for displaying all things related to the Authentication MVC/interactions
 *
 * @author	Paul Goddard
 * 			paul2.goddard@live.uwe.ac.uk
 * 			https://github.com/pgoddard10/
 * 			https://www.linkedin.com/in/pgoddard10/
 * 			https://twitter.com/pgoddard10
 * @date Spring 2020 
 */

require_once('classes/controllers/Authenticate_Controller.php');

class Authenticate_View
{
  private $authenticate_controller = null;

	/**
	 * method __construct()
	 * The constructor method, always called by default when an instance of Authenticate_View is created.
	 */
    function __construct() {
      $this->authenticate_controller = new Authenticate_Controller();
    }

   /**
	 * method login()
	 * starts the MVC process for logging in. Displays relevant error message
	 * @param  String $username
	 * @param  String $password
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
	 * method logout()
	 * removes any outstanding/open the session
	 */
    public function logout()
    {
        session_destroy();
    }

	/**
	 * method page_permissions()
	 * prints permission error and stops anything else happening
	 * @param  String $page
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
	 * method has_session()
	 * displays error if there is no active session
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
	 * method display_name()
	 * prints the saved display name
	 */
    public function display_name()
    {
        echo $this->authenticate_controller->get_display_name();
    }
    
	/**
	 * method get_staff_id()
	 * grabs the staff id
	 */
    public function get_staff_id()
    {
        return $this->authenticate_controller->get_staff_id();
    }

	/**
	 * method display_menu()
	 * prints the main menu (left-hand side of page)
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
       if($this->authenticate_controller->has_role(DEVICE_MANAGER)) { 
         ?>
         <!-- Nav Item - Device Collapse Menu -->
         <li class="nav-item active">
           <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
             <i class="fas fa-fw fa-hdd"></i>
             <span>Manage Devices</span>
           </a>
           <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionSidebar">
             <div class="bg-white py-2 collapse-inner rounded">
               <a class="collapse-item" href="#" data-toggle="modal" data-target="#deviceInteractionModalCenter" id="btn_retreiveVisitorDataModal">Retreive visitor data</a>
               <a class="collapse-item" href="#" data-toggle="modal" data-target="#deviceInteractionModalCenter" id="btn_pushContentToDevicesModal">Push content to devices</a>
             </div>
           </div>
         </li>
         <?php
       }
 
    }
}
?>