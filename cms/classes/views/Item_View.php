<?php

require_once('classes/controllers/Item_Controller.php');

/**
 * Short description of class Item_View
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Item_View
{
    private $item_controller = null;

    /**
     * Short description of method __construct
     * @param  String db_file
     */
    function __construct() {
        $this->item_controller = new Item_Controller();
        $this->item_controller->populate_all_items();
    }

    /**
     * Short description of method create_new
     * 
     * @return void
     */
    public function create_new($modified_by)
    {
        $success = $this->item_controller->create_new($modified_by);
        if($success==0) $msg = "Successfully created the item.";
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
        $success = $this->item_controller->edit();
        switch($success) {
            case 0:
                $msg = "Successfully edited the item.";
                break;
            case -2:
                $msg = "Changes for the item were not saved. There was a database error editing the item details.";
                break;
            case -1:
            default:
                $msg = "Changes for the item were not saved. An unknown error occurred.";
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
     * @param  item_id
     * @return void
     */
    public function delete()
    {
        $success = $this->item_controller->delete();
        if($success==0) $msg = "Successfully deleted the item.";
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
     * Short description of method JSONify_All_Items
     *
     * @return void
     */
    public function JSONify_All_Items()
    {
        echo $this->item_controller->JSONify_All_Items();
    }

    /**
     * Short description of method publish
     *
     * @return void
     */
    public function publish()
    {
        echo header('Content-Type: application/json');
        $returnValue = json_encode(array("result"=>"Content not published. An unknown error occurred"));
        if($this->item_controller->publish()==0) $returnValue = json_encode(array("result"=>"Successfully published all content."));
        echo $returnValue;
    }

    /**
     * Short description of method new_item_modal
     *
     * @return void
     */
    public function new_item_modal()
    {
        ?>
            <!-- Add New Item - Form Modal -->
            <div class="modal fade" id="addNewItemModal" tabindex="-1" role="dialog" aria-labelledby="addNewItemModal" aria-hidden="true">
                <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="addNewItemModalLabel">Add New Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <form class="user" id="form_new_item">
                        <div class="modal-body">
                        <!-- form input -->
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="new_name" name="name" placeholder="Name" required>
                            </div>
                            <div class="form-group">
                                <input type="url" class="form-control form-control-user" id="new_url" name="url" placeholder="URL">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="new_heritage_id" name="heritage_id" placeholder="Your ID">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="new_location" name="location" placeholder="Location">
                            </div>
                            <div class="form-group new_active_options">
                                Active?
                                <select id="new_active" name="active" class="form-control-sm form-control-user-sm">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary" id="btn_item_new">Create</button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        <?php
    }

    /**
     * Short description of method edit_item_modal
     *
     * @return void
     */
    public function edit_item_modal()
    {
        ?>
        <!-- Edit Item - Form Modal-->
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
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="edit_name" name="name" placeholder="Name" required>
                            </div>
                            <div class="form-group">
                                <input type="url" class="form-control form-control-user" id="edit_url" name="url" placeholder="URL">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="edit_heritage_id" name="heritage_id" placeholder="Your ID">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="edit_location" name="location" placeholder="Location">
                            </div>
                            <div class="form-group edit_active_options">
                                Active?
                                <select id="edit_active" name="active" class="form-control-sm form-control-user-sm">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <small id="activeHelpBlock" class="form-text text-muted">
                                    <p>Setting to 'No' will also set all child content to inactive. Setting to 'Yes' will <i>not</i> change any child content.</p>
                                </small>
                            </div>
                        </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button id="btn_item_edit" type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    <?php
    }

    /**
     * Short description of method delete_item_modal
     *
     * @return void
     */
    public function delete_item_modal()
    {
        ?>
        <!-- Delete Content - Confirmation Modal -->
        <div class="modal fade" id="deleteItemModalCenter" tabindex="-1" role="dialog" aria-labelledby="deleteItemModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="deleteItemModalLongTitle">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
                <div class="modal-body">
                    Deleting <span id="span_name">this item</span> will also remove <strong>all</strong> associated content/tags.<br />
                    Are you sure you wish to continue?<br />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn_item_delete">Delete Item</button>
                </div>
            </div>
          </div>
        </div>
        <?php
    }

    
    /**
     * Short description of method show_publish_modal
     *
     * @return void
     */
    public function show_publish_modal()
    {
        ?>
        <!-- Confirming Publish -->
        <div class="modal hide fade" id="publishModal" tabindex="-1" role="dialog" aria-labelledby="publishModalTitle" aria-hidden="true" data-focus-on="input:first">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="publishModalLongTitle">Publish All Content</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="publishModal_bodytext">Publishing the content, please wait...</span>
                </div>
                <div class="modal-footer d-none" id="publishModalFooter">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
          </div>
        </div>

        <?php
    }

} /* end of class Item_View */

?>