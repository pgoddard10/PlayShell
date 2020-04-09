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
    public function create_new($heritage_id, $name, $location, $url, $active, $modified_by)
    {
        $success = $this->item_controller->create_new($heritage_id, $name, $location, $url, $active, $modified_by);
        if($success==0) $msg = "Successfully created $name.";
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
    public function edit($item_id, $heritage_id, $name, $location, $url, $active, $modified_by)
    { 
        $success = $this->item_controller->edit($item_id, $heritage_id, $name, $location, $url, $active, $modified_by);
        switch($success) {
            case 0:
                $msg = "Successfully edited $name.";
                break;
            case -2:
                $msg = "Changes for $name were not saved. There was a database error editing the item details.";
                break;
            case -1:
            default:
                $msg = "Changes for $name were not saved. An unknown error occurred.";
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
    public function delete($item_id)
    {
        $success = $this->item_controller->delete($item_id);
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
        $data = array();
        if(count($this->item_controller->all_items)<=0) echo '{"data": []}'; //if array is empty, provide empty JSON for datatables to read correctly.
        else {
            foreach($this->item_controller->all_items as $item=>$details) {
                $individual_item = array();
                
                $individual_item['item_id'] = $details->item_id;
                if(strlen($details->url) > 1) $individual_item['name_with_url'] = '<a href="'.$details->url.'" target="_blank">' . $details->name .'</a>';
                else $individual_item['name_with_url'] = $details->name;
                $individual_item['name_without_url'] = $details->name;
                $individual_item['heritage_id'] = $details->heritage_id;
                $individual_item['location'] = $details->location;
                $individual_item['created'] = date("d/m/Y \a\\t H:i", strtotime($details->created));
                $last_modified = date("d/m/Y \a\\t H:i", strtotime($details->last_modified));
                if(strlen($details->modified_by) > 1) $last_modified = $last_modified. ' by ' . $details->modified_by;
                else $last_modified = $last_modified. ' by [deleted staff member]';
                $individual_item['last_modified'] = $last_modified;
                if($details->active==1)
                    $individual_item['active'] = 'Yes';
                else
                    $individual_item['active'] = 'No';
                    
                $items_as_json = json_encode($details, JSON_HEX_APOS);
                $individual_item['buttons'] = "<a href='#' data-toggle='modal' data-id='$items_as_json' class='editItemModalBox btn-circle btn-sm btn-primary' data-target='#editModalCenter'><i class='fas fa-edit'></i></a>";
                $individual_item['buttons'] = $individual_item['buttons'] . " <a href='#' data-toggle='modal' data-id='$items_as_json' class='deleteItemModalBox btn-circle btn-sm btn-primary' data-target='#deleteItemModalCenter'><i class='fas fa-trash'></i></a>";
                $data["data"][] = $individual_item;
            }
            echo json_encode($data, JSON_PRETTY_PRINT );
        }
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

} /* end of class Item_View */

?>