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
          <?php if(isset($_GET['action'])) {
            $name = str_replace("_"," ",$_GET['action']); //replace the underscore with a space
            $name = ucwords($name); //capitalise the first char of each word
            echo '<h1 class="h3 mb-2 text-gray-800">'.$name.'</h1>'; //print the page name as the title
          }
          else {
            echo '<h1 class="h3 mb-2 text-gray-800">View Staff</h1>'; //print the page name as the title
          }
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
                        echo '<td><a href="?page=manage_staff&action=edit&staff_id='.$staff['staff_id'].'"><i class=".btn-circle .btn-sm fas fa-edit"></i></a> | <a href="?page=manage_staff&action=delete&staff_id='.$staff['staff_id'].'"><i class=".btn-circle .btn-sm fas fa-trash"></i></a></td>';
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


      