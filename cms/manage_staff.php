<?php
/**
 * The GUI for managing staff user accounts
 * The PHP file is included within the index.php page - access the GUI by loading index.php
 *
 * @author Paul Goddard, paul2.goddard@live.uwe.ac.uk
 * @date Spring 2020 
 * 
 */

require_once('config.php');
require_once('classes/views/Staff_View.php');
require_once('classes/views/Authenticate_View.php');
$staff_view = new Staff_View();
$authenticate_view = new Authenticate_View();
$authenticate_view->has_session();
$authenticate_view->page_permissions(STAFF_DB_MANAGER);

?>
<!-- Custom styles for this page -->
      <!-- Begin Page Content -->
      <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h2 mb-0 text-gray-800"><i class="fas fa-fw fa-id-card"></i> Manage Staff</h1>
          <a href="#" data-toggle="modal" data-target="#addNewModal" class="btn btn-primary btn-icon-split"><span class="icon text-white-50"><i class="fas fa-user-plus"></i></span><span class="text">Add New</span></a>
        </div>
        <p class="mb-4">
          Add, edit and remove access for staff through this page. You can also set access-level permissions.
        </p>

        <div id="div1"></div>
        <!-- DataTable of Entire Staff -->
        <div class="card shadow mb-4">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" id="manage_staff_data_table" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th>Name <sup><a href="#" data-toggle="tooltip" title="Name of the member of staff"><i class="fas fa-fw fa-question-circle"></i></a></sup></th>
                    <th>Username <sup><a href="#" data-toggle="tooltip" title="Username used to log into this system"><i class="fas fa-fw fa-question-circle"></i></a></sup></th>
                    <th>Email <sup><a href="#" data-toggle="tooltip" title="Staff member's email address"><i class="fas fa-fw fa-question-circle"></i></a></sup></th>
                    <th>Role(s) <sup><a href="#" data-toggle="tooltip" title="Access to areas of this system"><i class="fas fa-fw fa-question-circle"></i></a></sup></th>
                    <th>Active? <sup><a href="#" data-toggle="tooltip" title="Allow this member to login?"><i class="fas fa-fw fa-question-circle"></i></a></sup></th>
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
        $staff_view->delete_modal();
        $staff_view->new_modal();
        $staff_view->edit_modal();
      ?>