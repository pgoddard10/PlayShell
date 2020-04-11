<?php
/**
 * Sets styles, header & footers etc. Responsible for displaying the menu and grabbing the correct page contents
 *
 * @author Paul Goddard, paul2.goddard@live.uwe.ac.uk
 * @date Spring 2020 
 * 
 */

session_start(); //required for PHP session (used to confirm user is logged in)
require_once('config.php');

//specify page to display
if(isset($_GET['page'])) define("PAGE",strtolower($_GET['page']));
else define("PAGE","home");

require_once('classes/views/Authenticate_View.php');
$authenticate_view = new Authenticate_View();

$authenticate_view->has_session(); //check that the user is logged in

?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Audio Culture Admin</title>

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
          <i class="fas fa-unlock-alt"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Admin</div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Home -->
      <li class="nav-item active">
        <a class="nav-link" href="index.php">
          <i class="fas fa-fw fa-home"></i>
          <span>Home</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Main
      </div>
      <?php $authenticate_view->display_menu(); ?>
    
      <!-- Divider -->
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        System
      </div>
        <!-- Nav Item - Staff Management -->
        <li class="nav-item active">
          <a class="nav-link" href="?page=about">
            <i class="fas fa-fw fa-info-circle"></i>
            <span>About</span></a>
        </li>
      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>
          
          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 text-gray-600 small"><i class="fas fa-user-circle fa-lg"></i>&nbsp;&nbsp;<?php $authenticate_view->display_name(); ?></span>
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>
          </ul>

        </nav>
        <!-- End of Topbar -->


      <!-- Begin Page Content -->
      <?php
        switch(PAGE){
          case 'manage_staff':
            require_once('manage_staff.php');
            break;
          case 'manage_content':
            require_once('manage_content.php');
            break;
          case 'manage_reports':
            require_once('manage_reports.php');
            break;
          case 'manage_visitors':
            require_once('manage_visitors.php');
            break;
          case 'manage_devices':
            require_once('manage_devices.php');
            break;
          case 'home':
            require_once('home.php');
            break;
          case 'about':
            require_once('about.php');
            break;
          default:
            require_once('404.html');
        }
      ?>
      <!-- End of Main Content -->
      
      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; <a href="?page=about">Paul Goddard - UWE Bristol</a>, 2020</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" id="btn_logout" href="#">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="vendor/fontawesome-free/css/all.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

  <?php
      if(PAGE) {
        switch(PAGE){
          case 'manage_staff':?>
              <!-- Manage Staff -->
                <!-- Page level plugins -->
                <script src="vendor/datatables/jquery.dataTables.min.js"></script>
                <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

                <!-- Page level custom scripts -->
                <script src="js/datatables/manage_staff.js"></script>

                <!-- Page level custom style -->
                <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
        <?php
            break;
          case 'manage_visitors':?>
                <!-- Manage Visitors -->
                <!-- Page level plugins -->
                <script src="vendor/datatables/jquery.dataTables.min.js"></script>
                <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

                <!-- Page level custom scripts -->
                <script src="js/datatables/manage_visitors.js"></script>

                <!-- Page level custom style -->
                <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
        <?php
          break;
          case 'manage_content':?>
                <!-- Manage Content -->
                  <!-- Page level plugins -->
                <script src="vendor/datatables/jquery.dataTables.min.js"></script>
                <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

                <!-- Page level custom scripts -->
                <script src="js/datatables/manage_content.js"></script>

                <!-- Page level custom style -->
                <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
          <?php
            break;
        }
      }
      ?>


  <script>
  //ajax actions for the logout button
  $(document).ready(function() {
    $("#btn_logout").click(function(){ 
      $.when(logout()).done(function(a1){ //when the ajax request is complete
        window.location.replace("login.php?logged_out"); //redirect to the login page
      });
      function logout(){ //call the ajax for saving the changes
        return $.ajax({url: "ajax.auth.php?action=logout", success: function(result){
        }});
      }
    });
  });
  </script>

</body>

</html>
