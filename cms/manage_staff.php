<?php

if(!isset($user)) { //if trying to load the page directly, redirect
  header('Location: index.php');
  exit;
}
if(!in_array(STAFF_DB_MANAGER,$user->roles)){
  exit("You do not have permission to use this page.");
}

$all_staff = $staff_db->select_all_staff_details();
$staff_members = array();

$all_roles = $staff_db->select_available_staff_roles();
foreach($all_staff as $staff) {
  $staff_member = new Staff();
  $staff_member->staff_id = $staff['staff_id'];
  $staff_member->first_name = $staff['first_name'];
  $staff_member->last_name = $staff['last_name'];
  $staff_member->username = $staff['username'];
  $staff_member->display_name = $staff['first_name'].' '.$staff['last_name'];
  $staff_member->email = $staff['email'];
  $staff_member->roles = $staff_db->select_active_roles($staff['staff_id']);
  $staff_member->active = $staff['active'];
  array_push($staff_members,$staff_member);
}


//Controls for add/edit/deactivate staff member
$action_message = "";
$action_success = false;
if(isset($_GET['action'])) {
  if($_GET['action']=='new') {
    if($staff_db->insert_new_staff($_POST['first_name'],$_POST['last_name'],$_POST['username'],$user->hash_password($_POST['password']),$_POST['email'],$_POST['role'])){
      $action_message = "Successfully created ".$_POST['first_name'].' '.$_POST['last_name'];
      $action_success = true;
    }
    else{
      $action_message = "Unable to create ".$_POST['first_name'].' '.$_POST['last_name'];
      $action_success = false;
    }
  }
  else if(isset($_POST['staff_id'])) {
    $staff_id = $_POST['staff_id'];
    $staff_details = $staff_db->select_staff_details($staff_db->select_staff_username($staff_id));
    if($_GET['action']=='edit') {
      if(in_array(STAFF_DB_MANAGER,$staff_db->select_active_roles($staff_id)) && $staff_db->number_of_roles(1)<=1) { //if there is 1 or less Staff Database Managers left, do not delete
        $action_message = "You cannot remove the Staff Database Manager role from the last Staff Database Manager";
        $action_success = false;
      }
      else {
        $replace_password=false;
        if(strlen($_POST['password'])>8) $replace_password=true; //only replace password if one exists (i.e. is greated than 8 characters)
        if($staff_db->update_staff($_POST['staff_id'],$_POST['first_name'],$_POST['last_name'],$_POST['username'],$replace_password,$user->hash_password($_POST['password']),$_POST['email'],$_POST['active'])){
          $staff_db->delete_roles_for_staff($staff_id);
          foreach($_POST['role'] as $role_id) {
            $staff_db->insert_staff_role($staff_id,$role_id);
          }
          $action_message = "Saved changes for ".$staff_details['first_name'].' '.$staff_details['last_name'];
          $action_success = true;
        }
        else{
          $action_message = "Unable to edit ".$staff_details['first_name'].' '.$staff_details['last_name'];
          $action_success = false;
        }
      }
    }
    elseif($_GET['action']=='deactivate') {
      if(in_array(STAFF_DB_MANAGER,$staff_db->select_active_roles($staff_id)) && $staff_db->number_of_roles(1)<=1) { //if there is 1 or less Staff Database Managers left, do not deactivate
        $action_message = "You cannot deactivate the last Staff Database Manager";
        $action_success = false;
      }
      else {
        if($staff_db->deactivate_staff($staff_id)){
          $staff_db->delete_roles_for_staff($staff_id);
          $action_message = "Successfully deactivated ".$staff_details['first_name'].' '.$staff_details['last_name'];
          $action_success = true;
        }
        else{
          $action_message = "Unable to deactivate ".$staff_details['first_name'].' '.$staff_details['last_name'];
          $action_success = false;
        }
      }
    }
  }
}

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
          <!-- Add/Edit/Deactivate Message Card -->
          <?php if(strlen($action_message)>0) { ?>
            <div class="card mb-4 py-3 border-left-<?php if($action_success) echo 'success'; else echo 'danger'; //change colour depending on whether success or not ?>"> 
                <div class="card-body">
                <?php echo $action_message; //print success/fail message ?>
                </div>
              </div>
          <?php } ?>

          <!-- DataTable of Entire Staff -->
          <div class="card shadow mb-4">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="manage_staff_data_table" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Username</th>
                      <th>Email</th>
                      <th>Role(s)</th>
                      <th>Active?</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach($staff_members as $staff=>$details) {
                        echo '<tr>';
                        echo '<td>'.$details->display_name.'</td>';
                        echo '<td>'.$details->username.'</td>';
                        echo '<td>'.$details->email.'</td>';
                        echo '<td>';
                        foreach($details->roles as $role_id) {
                          echo $staff_db->select_role_name($role_id).'<br />';
                        }
                        echo '</td>';
                        if($details->active==1)
                            echo '<td>Yes</td>';
                        else
                          echo '<td>No</td>';
                        echo '<td><a href="#" data-toggle="modal" data-target="#editModal-'.$details->staff_id.'"><i class=".btn-circle .btn-sm fas fa-edit"></i></a>'; 
                        if($details->active==1)
                                echo ' | <a href="#" data-toggle="modal" data-target="#deactivateModalCenter-'.$details->staff_id.'"><i class=".btn-circle .btn-sm fas fa-trash"></i></a>';
                        echo '</td>';
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
      foreach($staff_members as $staff=>$details) {
        ?>

      <!-- Deactivate Staff - Confirmation Modal -->
      <div class="modal fade" id="deactivateModalCenter-<?php echo $details->staff_id; ?>" tabindex="-1" role="dialog" aria-labelledby="deactivateModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="deactivateModalLongTitle">Confirm Deactivation</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              Are you sure you wish to deactivate <?php echo $details->display_name; ?>?<br />
            </div>
            <div class="modal-footer">
              <form class="user" action="index.php?page=manage_staff&action=deactivate" method="post">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <input type="hidden" name="staff_id" value="<?php echo $details->staff_id; ?>" />
                <button type="submit" class="btn btn-danger">Deactivate Account</button>
              </form>
            </div>
          </div>
        </div>
      </div>


      <!-- Edit Staff - Form Modal-->
      <div class="modal fade" id="editModal-<?php echo $details->staff_id; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editModalLabel">Edit <?php echo $details->display_name; ?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
              <form class="user" action="index.php?page=manage_staff&action=edit" method="post">
            <div class="modal-body">
                  <!-- form input -->
                    <div class="form-group row">
                      <div class="col-sm-6 mb-3 mb-sm-0">
                        <input type="text" class="form-control form-control-user" id="first_name" name="first_name" placeholder="First Name" required value=<?php echo $details->first_name; ?>>
                      </div>
                      <div class="col-sm-6">
                        <input type="text" class="form-control form-control-user" id="last_name" name="last_name" placeholder="Last Name" required value=<?php echo $details->last_name; ?>>
                      </div>
                    </div>
                    <div class="form-group">
                      <input type="email" class="form-control form-control-user" id="email" name="email" placeholder="Email Address" value=<?php echo $details->email; ?>>
                    </div>
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="username" name="username" placeholder="Username" required value=<?php echo $details->username; ?>>
                    </div>
                    <div class="form-group row">
                      <div class="col-sm-6 mb-3 mb-sm-0">
                        <input type="password" class="form-control form-control-user" id="edit_password" name="password" placeholder="Replace Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
                      </div>
                      <div class="col-sm-6">
                        <input type="password" class="form-control form-control-user" id="edit_repeat_password" name="repeat_password" placeholder="Repeat Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
                      </div>
                    </div>
                    <div class="form-group">
                      <small id="passwordHelpBlock" class="form-text text-muted">
                        <p>Passwords must be at least 8 characters long, contain a number, lowercase and uppercase letters.</p>
                      </small>
                    </div>
                    <div class="form-group">
                      Roles:
                      <?php
                      $all_roles = $staff_db->select_available_staff_roles();
                      foreach($all_roles as $role) { ?>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="role-<?php echo $role['role_id']; ?>" name="role[]" value="<?php echo $role['role_id'].'"'; if(in_array($role['role_id'],$details->roles)) echo 'checked="checked"';  ?>>
                          <label class="form-check-label" for="role-<?php echo $role['role_id']; ?>">
                            <?php echo $role['name']; ?>
                          </label>
                        </div>
                      <?php } ?>
                    </div>
                    <div class="form-group">
                      Active?
                      <select id="active" name="active" class="form-control-sm form-control-user-sm">
                        <option value="1">Yes</option>
                        <option value="0"<?php if($details->active==0) echo " selected" ?>>No</option>
                      </select>
                    </div>
                </div>
                <div class="modal-footer">
                  <input type="hidden" name="staff_id" value="<?php echo $details->staff_id; ?>" />
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="reset" class="btn btn-secondary">Reset</button>
                  <button type="submit" class="btn btn-primary">Save</button>
                </div>
              </form>
          </div>
        </div>
      </div>

      <?php
      }
      ?>

      
      <!-- Add New Staff - Form Modal -->
      <div class="modal fade" id="addNewModal" tabindex="-1" role="dialog" aria-labelledby="addNewModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addNewModalLabel">Add New Staff Member</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
              <form class="user" action="index.php?page=manage_staff&action=new" method="post">
                <div class="modal-body">
                  <!-- form input -->
                    <div class="form-group row">
                      <div class="col-sm-6 mb-3 mb-sm-0">
                        <input type="text" class="form-control form-control-user" id="first_name" name="first_name" placeholder="First Name" required>
                      </div>
                      <div class="col-sm-6">
                        <input type="text" class="form-control form-control-user" id="last_name" name="last_name" placeholder="Last Name" required>
                      </div>
                    </div>
                    <div class="form-group">
                      <input type="email" class="form-control form-control-user" id="email" name="email" placeholder="Email Address">
                    </div>
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="username" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group row">
                      <div class="col-sm-6 mb-3 mb-sm-0">
                        <input type="password" class="form-control form-control-user" id="new_password" name="password" placeholder="Password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
                      </div>
                      <div class="col-sm-6">
                        <input type="password" class="form-control form-control-user" id="new_repeat_password" name="repeat_password" placeholder="Repeat Password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
                      </div>
                    </div>
                    <div class="form-group">
                      <small id="passwordHelpBlock" class="form-text text-muted">
                        <p>Passwords must be at least 8 characters long, contain a number, lowercase and uppercase letters.</p>
                      </small>
                    </div>
                    <div class="form-group">
                      Roles:
                      <?php
                      $all_roles = $staff_db->select_available_staff_roles();
                      foreach($all_roles as $role) { ?>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="role-<?php echo $role['role_id']; ?>" name="role[]" value="<?php echo $role['role_id']; ?>">
                          <label class="form-check-label" for="role-<?php echo $role['role_id']; ?>">
                            <?php echo $role['name']; ?>
                          </label>
                        </div>
                      <?php } ?>
                    </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="reset" class="btn btn-secondary">Reset</button>
                  <button type="submit" class="btn btn-primary">Create</button>
                </div>
              </form>
          </div>
        </div>
      </div>

<script>
//Script to validate that both passwords match on creating new, or editing a staff member.
    var new_password = document.getElementById("new_password")
      , new_repeat_password = document.getElementById("new_repeat_password");

    function validatePassword(){
      if(password.value != repeat_password.value) {
        repeat_password.setCustomValidity("Passwords do not match");
      } else {
        repeat_password.setCustomValidity('');
      }
    }

    new_password.onchange = validatePassword;
    new_repeat_password.onkeyup = validatePassword;

</script>
            