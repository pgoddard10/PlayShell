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
    public function edit($staff_id, $first_name, $last_name, $password, $repeat_password, $email, $active, $roles)
    { 
        $success = $this->staff_controller->edit($staff_id, $first_name, $last_name, $password, $repeat_password, $email, $active, $roles);
        switch($success) {
            case 0:
                $msg = "Successfully edited $first_name $last_name.";
                break;
            case -2:
                $msg = "Changes for $first_name $last_name were not saved. The specified passwords do not match.";
                break;
            case -3:
                $msg = "Changes for $first_name $last_name were not saved. You cannot remove the role for the last Staff Database Manager.";
                break;
            case -4:
                $msg = "Changes for $first_name $last_name were not saved. There was a database error editing the staff details.";
                break;
            case -5:
                $msg = "Changes for $first_name $last_name were not saved. There was a database error editing the roles.";
                break;
            case -1:
            default:
                $msg = "Changes for $first_name $last_name were not saved. An unknown error occurred.";
                break;
        }
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
                        $staff_as_json = json_encode($details);
                      echo "<td><a href='#' data-toggle='modal' data-id='$staff_as_json' class='editModalBox' data-target='#editModalCenter'><i class='.btn-circle .btn-sm fas fa-edit'></i></a>";
                      if($details->active==1) {
                            $display_name = $details->display_name; //to workaround the escape charaters
                            echo " | <a href='#' data-toggle='modal' data-id='{\"staff_id\":".$details->staff_id.", \"name\":\"$display_name\"}' class='deactivateModalBox' data-target='#deactivateModalCenter'><i class='.btn-circle .btn-sm fas fa-trash'></i></a>";
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
                                <input type="password" class="form-control form-control-user" id="new_password" name="password" placeholder="Password" autocomplete="off" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
                            </div>
                            <div class="col-sm-6">
                                <input type="password" class="form-control form-control-user" id="new_repeat_password" name="repeat_password" placeholder="Repeat Password" autocomplete="off" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
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
                        <button type="submit" class="btn btn-primary" id="btn_staff_new" data-dismiss="modal">Create</button>
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

                for(var i=0; i < document.getElementById('form_new_staff').elements.length; i++){
                    var e = form.elements[i];
                    console.log(e.name+"="+e.value);
                }

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
        ?>
        <!-- Edit Staff - Form Modal-->
        <div class="modal fade" id="editModalCenter" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <form class="user" id="edit_form">
                    <div class="modal-body">
                        <!-- form input -->
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="text" class="form-control form-control-user" id="edit_first_name" name="first_name" placeholder="First Name" required />
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-user" id="edit_last_name" name="last_name" placeholder="Last Name" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control form-control-user" id="edit_email" name="email" placeholder="Email Address" />
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="password" class="form-control form-control-user" id="edit_password" name="password" autocomplete="off" placeholder="Replace Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
                            </div>
                            <div class="col-sm-6">
                                <input type="password" class="form-control form-control-user" id="edit_repeat_password" name="repeat_password" autocomplete="off" placeholder="Repeat Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
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
                                <input class="form-check-input" type="checkbox" id="ckbox_edit_role_<?php echo $role['role_id']; ?>" name="role_<?php echo $role['role_id']; ?>" value="<?php echo $role['role_id']; ?>">
                                <label class="form-check-label" for="ckbox_edit_role_<?php echo $role['role_id']; ?>">
                                    <?php echo $role['name']; ?>
                                </label>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="form-group edit_active_options">
                            Active?
                            <select id="edit_active" name="active" class="form-control-sm form-control-user-sm">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button id="btn_staff_edit" type="submit" class="btn btn-primary" data-dismiss="modal">Save</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
        <script>
        var staff_id;
        //Collect the form data and 'submit' the form via AJAX
        $(document).ready(function(){
            $("#btn_staff_edit").click(function(){
                var roles = [];
                var direct_to_url = "ajax.staff_actions.php?action=edit&staff_id="+staff_id+"&";
                direct_to_url += $('#edit_form').serialize(); //grab all input boxes

                //grab the role tickbox data
                $('#edit_form input[type=checkbox]').each(function() {     
                        if (this.checked) {
                            roles.push(this.name.replace("role_",""));
                        }
                    });
                $.each(roles, function(index, value) {
                    direct_to_url += "&roles[]="+value;
                });

                //send the data as a GET request to the PHP page specified in direct_to_url
                $.ajax({url: direct_to_url, success: function(result){
                    $("#div1").html(result);
                }});
            });
        });

        //Fill in the form fields on the Edit Modal Box with the appropriate data passed by clicked in the hyperlink
        //data is passed in the form of a JSON string.
        $(document).on("click", ".editModalBox", function () {
            staff_id = $(this).data('id').staff_id;
            $(".modal-body #edit_first_name").val($(this).data('id').first_name);
            $(".modal-body #edit_last_name").val($(this).data('id').last_name);
            $(".modal-body #edit_email").val($(this).data('id').email);
            $(".edit_active_options select").val($(this).data('id').active);
            var i;
            for (i = 1; i <= 5; i++) {
                    $("#ckbox_edit_role_"+i).prop("checked", false);
            }

            //tick the checkboxes that match the roles this staff member has
            var roles = $(this).data('id').roles;
            $.each(roles, function(index, value) {
                    $("#ckbox_edit_role_"+value.role_id).prop("checked", true);
                });
        });
        </script>

    <?php
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