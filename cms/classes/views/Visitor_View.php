<?php

require_once('classes/controllers/Visitor_Controller.php');

/**
 * Short description of class Visitor_View
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Visitor_View
{
    private $visitor_controller = null;

    /**
     * Short description of method __construct
     * @param  String db_file
     */
    function __construct() {
        $this->visitor_controller = new Visitor_Controller();
        $this->visitor_controller->populate_all_visitors();
    }

    /**
     * Short description of method create_new
     * 
     * @return void
     */
    public function create_new()
    {
        $success = $this->visitor_controller->create_new();
        if($success==0) $msg = "Successfully created the visitor.";
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
     * Short description of method edit
     *
     * @return void
     */
    public function edit()
    { 
        $success = $this->visitor_controller->edit();
        switch($success) {
            case 0:
                $msg = "Successfully edited the visitor.";
                break;
            case -2:
                $msg = "Changes for the visitor were not saved. There was a database error editing the visitor details.";
                break;
            case -1:
            default:
                $msg = "Changes for the visitor were not saved. An unknown error occurred.";
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
     * Short description of method delete
     *
     * @param  visitor_id
     * @return void
     */
    public function delete()
    {
        $success = $this->visitor_controller->delete();
        if($success==0) $msg = "Successfully deleted the visitor.";
        else $msg = "An unknown error occurred.";
        ?>
              <!-- Delete Message Card -->
                <div class="card mb-4 py-3 border-left-<?php if($success==0) echo 'success'; else echo 'danger'; //change colour depending on whether success or not ?>"> 
                    <div class="card-body">
                    <?php echo $msg; //print success/fail message ?>
                    </div>
                  </div>
        <?php
    }

    /**
     * Short description of method JSONify_All_Visitors
     *
     * @return void
     */
    public function JSONify_All_Visitors()
    {
        echo $this->visitor_controller->JSONify_All_Visitors();
    }

    /**
     * Short description of method check_out_device
     *
     * @return void
     */
    public function check_out_device()
    {
        header('Content-Type: application/json');
        echo $this->visitor_controller->check_out_device();
    }

    /**
     * Short description of method new_modal
     *
     * @return void
     */
    public function new_modal()
    {
        ?>
            <!-- Add New Visitor - Form Modal -->
            <div class="modal fade" id="addNewModal" tabindex="-1" role="dialog" aria-labelledby="addNewModal" aria-hidden="true">
                <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="addNewModalLabel">Add New Visitor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <form class="user" id="form_new_visitor">
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
                            <small id="emailHelpBlock" class="form-text text-muted">
                                <p>After the visitor has finished with the device, an email will automatically be sent to the specified email.<br />
                                The email will contain details of the content that the visitor has interacted with.<br />
                                Leave the email field blank to prevent the email being sent.</p>
                            </small>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="new_address_1" name="address_1" placeholder="Address Line 1" required>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="new_address_2" name="address_2" placeholder="Address Line 2">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="new_address_3" name="address_3" placeholder="Address Line 3" required>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="new_address_4" name="address_4" placeholder="Address Line 4" required>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="new_address_postcode" name="address_postcode" placeholder="Postcode" required pattern="[A-Za-z]{1,2}[0-9Rr][0-9A-Za-z]? [0-9][ABD-HJLNP-UW-Zabd-hjlnp-uw-z]{2}">
                            </div>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary" id="btn_visitor_new">Create</button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
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
        <!-- Edit Visitor - Form Modal-->
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
                                <input type="text" class="form-control form-control-user" id="edit_first_name" name="first_name" placeholder="First Name" required>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-user" id="edit_last_name" name="last_name" placeholder="Last Name" required>
                            </div>
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control form-control-user" id="edit_email" name="email" placeholder="Email Address">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="edit_address_1" name="address_1" placeholder="Address Line 1" required>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="edit_address_2" name="address_2" placeholder="Address Line 2">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="edit_address_3" name="address_3" placeholder="Address Line 3" required>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="edit_address_4" name="address_4" placeholder="Address Line 4" required>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="edit_address_postcode" name="address_postcode" placeholder="Postcode" required pattern="[A-Za-z]{1,2}[0-9Rr][0-9A-Za-z]? [0-9][ABD-HJLNP-UW-Zabd-hjlnp-uw-z]{2}">
                            </div>
                        </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button id="btn_visitor_edit" type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    <?php
    }

    /**
     * Short description of method delete_modal
     *
     * @return void
     */
    public function delete_modal()
    {
        ?>
        <!-- Delete Visitor - Confirmation Modal -->
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
                    Are you sure you wish to delete <span id="span_name">this visitor</span>?<br />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn_visitor_delete">Delete Visitor</button>
                </div>
            </div>
          </div>
        </div>
        <?php
    }
    
    /**
     * Short description of method delete_modal
     *
     * @return void
     */
    public function check_out_modal()
    {
        ?>
        <!-- Check-Out Modal -->
        <div class="modal fade" id="checkOutModalCenter" tabindex="-1" role="dialog" aria-labelledby="checkOutModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="checkOutModalLongTitle">Check Out Device</h5>
                <button type="button" class="close btn_checkOutModalClose" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
                <div class="modal-body">
                    <span id="checkOutModal_bodytext"><i class="fas fa-spinner fa-spin"></i> Finding an available device, please wait...</span>
                </div>
                <div class="modal-footer d-none"  id="checkOutModalFooter">
                    <button type="button" class="btn btn-primary btn_checkOutModalClose" data-dismiss="modal">Close</button>
                </div>
            </div>
          </div>
        </div>
        <?php
    }

} /* end of class Visitor_View */

?>