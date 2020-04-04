<?php

require_once('classes/controllers/Staff_Controller.php');

/**
 * Short description of class Staff_View
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Staff_View
{

    private $staff_controller = null;

    /**
     * Short description of method __construct
     * @param  String db_file
     */
    function __construct() {
        $this->staff_controller = new Staff_Controller();
        $this->staff_controller->populate_all_staff();
    }

    /**
     * Short description of method create_new
     * 
     * @param first_name
     * @param last_name
     * @param username
     * @param password
     * @param email
     * @param  array<Integer>
     * @return void
     */
    public function create_new($first_name, $last_name, $username, $password, $repeat_password, $email, $roles)
    {
        $success = $this->staff_controller->create_new($first_name, $last_name, $username, $password, $repeat_password, $email, $roles);
        if($success==0) $msg = "Successfully created $first_name $last_name.";
        if($success==-1) $msg = "An unknown error occurred.";
        if($success==-2) $msg = "Password mis-match";
        ?>
              <!-- Add Message Card -->
                <div class="card mb-4 py-3 border-left-<?php if($success==0) echo 'success'; else echo 'danger'; //change colour depending on whether success or not ?>"> 
                    <div class="card-body">
                    <?php echo $msg; //print success/fail message ?>
                    </div>
                  </div>
        <?php
        $this->staff_controller->populate_all_staff();
        $this->display_table_all_staff();
        // $this->new_modal();
        // $this->deactivate_modal();
        // $this->edit_modal();
    }

    /**
     * Short description of method edit
     *
     * @param staff_id
     * @param first_name
     * @param last_name
     * @param username
     * @param password
     * @param email
     * @param active
     * @param  array<Integer> roles
     * @return void
     */
    public function edit($staff_id, $first_name, $last_name, $username, $password, $repeat_password, $email, $active, $roles)
    { 
        $success = $this->staff_controller->edit($staff_id, $first_name, $last_name, $username, $password, $repeat_password, $email, $active, $roles);
        if($success==0) $msg = "Successfully edited $first_name $last_name with roles ".print(print_r($roles));
        if($success==-1) $msg = "An unknown error occurred.";
        if($success==-2) $msg = "Password mis-match";
        ?>
                <!-- Edit Message Card -->
                <div class="card mb-4 py-3 border-left-<?php if($success==0) echo 'success'; else echo 'danger'; //change colour depending on whether success or not ?>"> 
                    <div class="card-body">
                    <?php echo $msg; //print success/fail message ?>
                    </div>
                    </div>
        <?php
        $this->staff_controller->populate_all_staff();
        $this->display_table_all_staff();
    }

    /**
     * Short description of method deactivate
     *
     * @param  staff_id
     * @return void
     */
    public function deactivate($staff_id)
    {
        $success = $this->staff_controller->deactivate($staff_id);
        if($success==0) $msg = "Successfully deactivated the member of staff.";
        if($success==-1) $msg = "An unknown error occurred.";
        ?>
              <!-- Deactivate Message Card -->
                <div class="card mb-4 py-3 border-left-<?php if($success==0) echo 'success'; else echo 'danger'; //change colour depending on whether success or not ?>"> 
                    <div class="card-body">
                    <?php echo $msg; //print success/fail message ?>
                    </div>
                  </div>
        <?php
        $this->staff_controller->populate_all_staff();
        $this->display_table_all_staff();
    }

    /**
     * Short description of method display_table_all_staff
     *
     * @return void
     */
    public function display_table_all_staff()
    {
        ?>
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
                  foreach($this->staff_controller->all_staff as $staff_member=>$details) {
                      echo '<tr>';
                      echo '<td>'.$details->display_name.'</td>';
                      echo '<td>'.$details->username.'</td>';
                      echo '<td>'.$details->email.'</td>';
                      echo '<td>';
                      if($details->roles) {
                        foreach($details->roles as $role) {
                            echo $role['name'].'<br />';
                        }
                      }
                      else {
                          echo "[No assigned roles]";
                      }
                      echo '</td>';
                      if($details->active==1)
                          echo '<td>Yes</td>';
                      else
                        echo '<td>No</td>';
                      echo '<td><a href="#" data-toggle="modal" data-id="'.$details->staff_id.'" data-target="#editModal_'.$details->staff_id.'"><i class=".btn-circle .btn-sm fas fa-edit"></i></a>'; 
                      if($details->active==1) {
                            $name = $details->display_name; //to workaround the escape charaters
                            echo " | <a href='#' data-toggle='modal' data-id='{\"staff_id\":".$details->staff_id.", \"name\":\"$name\"}' class='deactivateModalBox' data-target='#deactivateModalCenter'><i class='.btn-circle .btn-sm fas fa-trash'></i></a>";
                      }
                      echo '</td>';
                      echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
          <?php
          
        $this->edit_modal();
    }

    /**
     * Short description of method new_modal
     *
     * @return void
     */
    public function new_modal()
    {
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
                    <form class="user" id="form_new_staff">
                        <div class="modal-body">
                        <!-- form input -->
                            <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="text" class="form-control form-control-user" id="new_first_name" name="first_name" placeholder="First Name" required>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-user" id="new_last_name" name="last_name" placeholder="Last Name" required>
                            </div>
                            </div>
                            <div class="form-group">
                            <input type="email" class="form-control form-control-user" id="new_email" name="email" placeholder="Email Address">
                            </div>
                            <div class="form-group">
                            <input type="text" class="form-control form-control-user" id="new_username" name="username" placeholder="Username" required>
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
                            foreach($this->staff_controller->role_model->available_roles as $role) { ?>
                                <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ckbox_new_role_<?php echo $role['role_id']; ?>" name="role_<?php echo $role['role_id']; ?>" value="<?php echo $role['role_id']; ?>">
                                <label class="form-check-label" for="ckbox_new_role_<?php echo $role['role_id']; ?>">
                                    <?php echo $role['name']; ?>
                                </label>
                                </div>
                            <?php } ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button type="button" class="btn btn-primary" id="btn_staff_new" data-dismiss="modal">Create</button>
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
        <script>
        $(document).ready(function(){
            $("#btn_staff_new").click(function(){
                var roles = [];
                var direct_to_url = "ajax.staff_actions.php?action=new&";
                direct_to_url += $('#form_new_staff').serialize();
                $('#form_new_staff input[type=checkbox]').each(function() {     
                        if (this.checked) {
                            roles.push(this.name.replace("role_",""));
                        }
                    });
                $.each(roles, function(index, value) {
                    direct_to_url += "&roles[]="+value;
                });
                $.ajax({url: direct_to_url, success: function(result){
                    $("#div1").html(result);
                }});
            });
        });
        </script>

        <?php
    }

    /**
     * Short description of method edit_modal
     *
     * @return void
     */
    public function edit_modal()
    {
        
        foreach($this->staff_controller->all_staff as $staff_member=>$details) {
        ?>
        <!-- Edit Staff - Form Modal-->
        <div class="modal fade" id="editModal_<?php echo $details->staff_id; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit <?php echo $details->display_name; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <form class="user" id="edit_form_<?php echo $details->staff_id; ?>">
                <div class="modal-body">
                    <!-- form input -->
                        <div class="form-group row">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <input type="text" class="form-control form-control-user" id="edit_first_name_<?php echo $details->staff_id; ?>" name="first_name" placeholder="First Name" required value=<?php echo $details->first_name; ?>>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control form-control-user" id="edit_last_name_<?php echo $details->staff_id; ?>" name="last_name" placeholder="Last Name" required value=<?php echo $details->last_name; ?>>
                        </div>
                        </div>
                        <div class="form-group">
                        <input type="email" class="form-control form-control-user" id="edit_email_<?php echo $details->staff_id; ?>" name="email" placeholder="Email Address" value=<?php echo $details->email; ?>>
                        </div>
                        <div class="form-group">
                        <input type="text" class="form-control form-control-user" id="edit_username_<?php echo $details->staff_id; ?>" name="username" placeholder="Username" required value=<?php echo $details->username; ?>>
                        </div>
                        <div class="form-group row">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <input type="password" class="form-control form-control-user" id="edit_password_<?php echo $details->staff_id; ?>" name="password" placeholder="Replace Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
                        </div>
                        <div class="col-sm-6">
                            <input type="password" class="form-control form-control-user" id="edit_repeat_password_<?php echo $details->staff_id; ?>" name="repeat_password" placeholder="Repeat Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
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
                        foreach($this->staff_controller->role_model->available_roles as $role) { ?>
                            <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="ckbox_edit_role_<?php echo $role['role_id']; ?>_for_<?php echo $details->staff_id; ?>" name="role_<?php echo $role['role_id']; ?>" value=<?php echo '"'.$role['role_id'].'"'; if($details->has_role($role['role_id'])) echo 'checked="checked"';  ?>>
                            <label class="form-check-label" for="ckbox_edit_role_<?php echo $role['role_id']; ?>_for_<?php echo $details->staff_id; ?>">
                                <?php echo $role['name']; ?>
                            </label>
                            </div>
                        <?php } ?>
                        </div>
                        <div class="form-group">
                        Active? <?php echo 'active? 1/0 = '.$details->active.' | '; ?>
                        <select id="edit_active_<?php echo $details->staff_id; ?>" name="active" class="form-control-sm form-control-user-sm">
                            <option value="1">Yes</option>
                            <option value="0"<?php if($details->active==0) echo " selected" ?>>No</option>
                        </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <input type="hidden" name="staff_id" value="<?php echo $details->staff_id; ?>" />
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button id="btn_staff_edit_<?php echo $details->staff_id; ?>" type="button" class="btn btn-primary" data-dismiss="modal">Save</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
        <script>
        $(document).ready(function(){
            $("#btn_staff_edit_<?php echo $details->staff_id; ?>").click(function(){
                var roles = [];
                var direct_to_url = "ajax.staff_actions.php?action=edit&";
                direct_to_url += $('#edit_form_<?php echo $details->staff_id; ?>').serialize();
                $('#edit_form_<?php echo $details->staff_id; ?> input[type=checkbox]').each(function() {     
                        if (this.checked) {
                            roles.push(this.name.replace("role_",""));
                        }
                    });
                $.each(roles, function(index, value) {
                    direct_to_url += "&roles[]="+value;
                });
                $.ajax({url: direct_to_url, success: function(result){
                    $("#div1").html(result);
                }});
            });
        });
        </script>

            <?php
        }
    }

    /**
     * Short description of method deactivate_modal
     *
     * @return void
     */
    public function deactivate_modal()
    {
        ?>

        <!-- Deactivate Staff - Confirmation Modal -->
        <div class="modal fade" id="deactivateModalCenter" tabindex="-1" role="dialog" aria-labelledby="deactivateModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="deactivateModalLongTitle">Confirm Deactivation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
                <div class="modal-body">
                    Are you sure you wish to deactivate <span id="span_name">this member of staff</span>?<br />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn_staff_deactivate">Deactivate Account</button>
                </div>
            </div>
          </div>
        </div>

        <script>
        var staff_id;
        var name;
        $(document).ready(function(){
            $("#btn_staff_deactivate").click(function(){
                $.ajax({url: "ajax.staff_actions.php?action=deactivate&staff_id="+staff_id, success: function(result){
                    $("#div1").html(result);
                }});
            });
        });
        $(document).on("click", ".deactivateModalBox", function () {
            name = $(this).data('id').name;
            staff_id = $(this).data('id').staff_id;
            $(".modal-body #txt_box_staff_id").val( staff_id );
            $(".modal-body #span_name").text(name);
        });

        </script>
        <?php
    }

} /* end of class Staff_View */

?>