<?php

require_once('config.php');

require_once('classes/views/Staff_View.php');
//require_once('classes/views/Login_View.php');
$staff_view = new Staff_View();
//$login_view = new Login_View();

/*
  CHECK PERMISSIONS
*/

?>
<!-- Custom styles for this page -->
<link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
      <!-- Begin Page Content -->
      <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h2 mb-0 text-gray-800">Manage Staff</h1>
          <a href="#" data-toggle="modal" data-target="#addNewModal" class="btn btn-primary btn-icon-split"><span class="icon text-white-50"><i class="fas fa-user-plus"></i></span><span class="text">Add New</span></a>
        </div>

        <div>
          + Roles won't save when edited because the Model doesn't have the code to update the database...<br />
          + Need to work out where the search bar went, too.
        </div>
        <div id="div1"></div>

        </div>

      </div>
      <!-- /.container-fluid -->

      <?php
        $staff_view->deactivate_modal();
        $staff_view->new_modal();
      ?>

<script>
  $(document).ready(function(){
      $.ajax({url: "ajax.staff_table_data.php?action=display_table", success: function(result){
          $("#div1").html(result);
      }});
  });
</script>