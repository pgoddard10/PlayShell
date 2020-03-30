<?php

if(!isset($user) && !in_array(1,$user->roles)) {
  header('Location: index.php');
  exit;
}

$all_staff = $staff_db->select_all_staff_details();

?>

  <!-- Custom styles for this page -->
  <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <?php if(isset($_GET['page'])) {
            $name = str_replace("_"," ",$_GET['page']); //replace the underscore with a space
            $name = ucwords($name); //capitalise the first char of each word
            echo '<h1 class="h2 mb-2 text-gray-800">'.$name.'</h1>'; //print the page name as the title
          }
          ?>
          <?php
          // if(isset($_GET['action'])) {
          //   $name = str_replace("_"," ",$_GET['action']); //replace the underscore with a space
          //   $name = ucwords($name); //capitalise the first char of each word
          //   echo '<h1 class="h3 mb-2 text-gray-800">'.$name.'</h1>'; //print the page name as the title
          // }
          // else {
          //   echo '<h1 class="h3 mb-2 text-gray-800">View Staff</h1>'; //print the page name as the title
          // }
          ?>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Username</th>
                      <th>Email</th>
                      <th>Role(s)</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach($all_staff as $staff) {
                        echo '<tr>';
                        echo '<td>'.$staff['first_name'].' '.$staff['last_name'].'</td>';
                        echo '<td>'.$staff['username'].'</td>';
                        echo '<td>'.$staff['email'].'</td>';
                        echo '<td>';
                            $all_roles = $staff_db->select_available_staff_roles();
                            $this_staff_roles = $staff_db->select_active_roles($staff['staff_id']);
                            foreach($all_roles as $role) {
                                if(in_array($role['role_id'],$this_staff_roles)) {
                                    echo $role['name'].'<br />';
                                }
                            }
                        echo '</td>';
                        echo '<td>
                                <a href="#" data-toggle="modal" data-target="#editModal-'.$staff['staff_id'].'"><i class=".btn-circle .btn-sm fas fa-edit"></i></a> | 
                                <a href="#" data-toggle="modal" data-target="#deleteModalCenter-'.$staff['staff_id'].'"><i class=".btn-circle .btn-sm fas fa-trash"></i></a>
                              </td>';
                        echo '</tr>';
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->


      <?php
      foreach($all_staff as $staff) {
        ?>

      <!-- Delete Modal -->
      <div class="modal fade" id="deleteModalCenter-<?php echo $staff['staff_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="deleteModalLongTitle">Confirm Deletion</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              Are you sure you wish to delete <?php echo $staff['first_name'].' '.$staff['last_name']; ?>?<br />
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-danger">Delete</button>
            </div>
          </div>
        </div>
      </div>


      <!-- Edit Modal -->
      <div class="modal fade" id="editModal-<?php echo $staff['staff_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editModalLabel">Edit <?php echo $staff['first_name'].' '.$staff['last_name']; ?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
            <?php echo $staff['first_name'].' '.$staff['last_name']; ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Save changes</button>
            </div>
          </div>
        </div>
      </div>

      <?php
      }
      ?>

            