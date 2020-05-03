<?php
/**
 * Class Staff_View
 * Responsible for displaying all things related to the Staff MVC/interactions
 *
 * @author	Paul Goddard
 * 			paul2.goddard@live.uwe.ac.uk
 * 			https://github.com/pgoddard10/
 * 			https://www.linkedin.com/in/pgoddard10/
 * 			https://twitter.com/pgoddard10
 * @date Spring 2020 
 */

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
	 * method __construct()
	 * The constructor method, always called by default when an instance of Staff_View is created.
	 */
    function __construct() {
        $this->staff_controller = new Staff_Controller();
        $this->staff_controller->populate_all_staff();
    }

	/**
	 * method create_new()
	 * calls the methods for creating a new staff, prints the success or error message
	 */
    public function create_new()
    {
        $success = $this->staff_controller->create_new();
        if($success==0) $msg = "Successfully created the staff member.";
        else if($success==-2) $msg = "Staff member not saved. Password mis-match";
        else if($success==-6) $msg = "Staff member not saved. The specified email address already exists.";
        else if($success==-7) $msg = "Staff member not saved. The specified username already exists.";
        else $msg = "An unknown error occurred.";
        ?>
              <!-- Add Message Card -->
                <div class="card mb-4 py-3 border-left-<?php if($success==0) echo 'success'; else echo 'danger'; //change colour depending on whether success or not ?>"> 
                    <div class="card-body">
                    <?php echo $msg; //print success/fail message ?>
                    </div>
                  </div>
        <?php
    }

	/**
	 * method edit()
	 * calls the methods for editing the staff, prints the success or error message
	 */
    public function edit()
    { 
        $success = $this->staff_controller->edit();
        switch($success) {
            case 0:
                $msg = "Successfully edited staff member.";
                break;
            case -2:
                $msg = "Changes for staff member were not saved. The specified passwords do not match.";
                break;
            case -3:
                $msg = "Changes for staff member were not saved. You cannot remove the role for the last Staff Database Manager.";
                break;
            case -4:
                $msg = "Changes for staff member were not saved. There was a database error editing the staff details.";
                break;
            case -5:
                $msg = "Changes for staff member were not saved. There was a database error editing the roles.";
                break;
            case -6:
                $msg = "Changes for staff member were not saved. The specified email address already exists against someone else.";
                break;
            case -7:
                $msg = "Changes for staff member were not saved. The specified username already exists against someone else.";
                break;
            case -1:
            default:
                $msg = "Changes for staff member were not saved. An unknown error occurred.";
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
    }

	/**
	 * method delete()
	 * prints the outcome of the call to delete the staff
	 */
    public function delete()
    {
        $success = $this->staff_controller->delete();
        if($success==0) $msg = "Successfully deleted the member of staff.";
        if($success==-1) $msg = "An unknown error occurred.";
        ?>
              <!-- delete Message Card -->
                <div class="card mb-4 py-3 border-left-<?php if($success==0) echo 'success'; else echo 'danger'; //change colour depending on whether success or not ?>"> 
                    <div class="card-body">
                    <?php echo $msg; //print success/fail message ?>
                    </div>
                  </div>
        <?php
    }

	/**
	 * method print_table_json()
	 * prints the returned json
	 */
    public function print_table_json()
    {
        header('Content-Type: application/json');
        echo $this->staff_controller->JSONify_All_Staff();
    }

	/**
	 * method print_staff_json()
	 * prints the returned json
	 */
    public function print_staff_json()
    {
        header('Content-Type: application/json');
        echo $this->staff_controller->JSONify_staff_details();
    }

	/**
	 * method new_modal()
	 * prints the modal & form used for new_modal()
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
                                <input type="text" class="form-control form-control-user" id="new_first_name" name="first_name" placeholder="First Name" required pattern="[a-zA-Z]+">
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-user" id="new_last_name" name="last_name" placeholder="Last Name" required pattern="[a-zA-Z]+">
                            </div>
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control form-control-user" id="new_email" name="email" placeholder="Email Address">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="new_username" name="username" placeholder="Username" required pattern="^[a-zA-Z][a-zA-Z0-9-_\.]{2,20}$">
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
                        <button type="submit" class="btn btn-primary" id="btn_staff_new">Create</button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        <?php
    }

	/**
	 * method edit_modal()
	 * prints the modal & form used for edit_modal()
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button id="btn_staff_edit" type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    <?php
    }

	/**
	 * method delete_modal()
	 * prints the modal & form used for delete_modal()
	 */
    public function delete_modal()
    {
        ?>
        <!-- delete Staff - Confirmation Modal -->
        <div class="modal fade" id="deleteModalCenter" tabindex="-1" role="dialog" aria-labelledby="deleteModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLongTitle">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
                <div class="modal-body">
                    Are you sure you wish to delete <span id="span_name">this member of staff</span>?<br />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn_staff_delete">Delete Account</button>
                </div>
            </div>
          </div>
        </div>
        <?php
    }

} /* end of class Staff_View */

?>