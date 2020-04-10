<?php
/**
 * The GUI for managing visitor details
 * The PHP file is included within the index.php page - access the GUI by loading index.php
 *
 * @author Paul Goddard, paul2.goddard@live.uwe.ac.uk
 * @date Spring 2020 
 * 
 */

require_once('config.php');
require_once('classes/views/Visitor_View.php');
require_once('classes/views/Authenticate_View.php');
$visitor_view = new Visitor_View();
$authenticate_view = new Authenticate_View();
$authenticate_view->has_session();
$authenticate_view->page_permissions(VISITOR_DB_MANAGER);

?>
<!-- Custom styles for this page -->
      <!-- Begin Page Content -->
      <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h2 mb-0 text-gray-800"><i class="fas fa-fw fa-users"></i> Manage Visitors</h1>
          <a href="#" data-toggle="modal" data-target="#addNewModal" class="btn btn-primary btn-icon-split"><span class="icon text-white-50"><i class="fas fa-user-plus"></i></span><span class="text">Add New</span></a>
        </div>
        <p class="mb-4">
          Add, edit and remove access for visitors.
        </p>

        <div id="div1"></div>
        <!-- DataTable of Entire Visitor -->
        <div class="card shadow mb-4">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" id="manage_visitor_data_table" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Actions</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
          </div>
        </div>

      </div>
      <!-- /.container-fluid -->

      <?php
        $visitor_view->delete_modal();
        $visitor_view->new_modal();
        $visitor_view->edit_modal();
        $visitor_view->check_out_modal();
      ?>