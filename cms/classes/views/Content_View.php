<?php

require_once('classes/controllers/Content_Controller.php');

/**
 * Short description of class Content_View
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Content_View
{
    private $content_controller = null;
    private $item_id = null;

    /**
     * Short description of method __construct
     * @param  String db_file
     */
    function __construct($item_id=null) {
        $this->item_id = $item_id;
        $this->content_controller = new Content_Controller($this->item_id);
        $success = $this->content_controller->populate_all_contents(); 
        if($success!=0) echo "Error populating contents array: ".$success;
    }

    /**
     * Short description of method create_new
     * 
     * @return void
     */
    public function create_new($created_by)
    {
        $success = $this->content_controller->create_new($created_by);
        if($success==0) $msg = "Successfully created '".$_POST['name']."'";
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
    public function edit($content_id, $heritage_id, $name, $location, $url, $active, $modified_by)
    { 
        $success = $this->content_controller->edit($content_id, $heritage_id, $name, $location, $url, $active, $modified_by);
        switch($success) {
            case 0:
                $msg = "Successfully edited $name.";
                break;
            case -2:
                $msg = "Changes for $name were not saved. There was a database error editing the content details.";
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
     * @param  content_id
     * @return void
     */
    public function delete($content_id)
    {
        $success = $this->content_controller->delete($content_id);
        if($success==0) $msg = "Successfully deleted the content.";
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
    public function JSONify_All_Contents() {
        $array_of_contents = $this->content_controller->all_contents;
        // print('array_of_contents: <pre>'.print_r($array_of_contents,true).'</pre>');
        $individual_content = array();
        if(count($array_of_contents)<=0) return '{"data": []}'; //if array is empty, provide empty JSON for datatables to read correctly.
        else {
            foreach($array_of_contents as $obj=>$contents) {

                $content_details_array = array();
                $content_details_array['name'] = $contents->name;
                $content_details_array['tag_id'] = $contents->tag_id;
                if($contents->active==1)
                    $content_details_array['active'] = 'Yes';
                else
                    $content_details_array['active'] = 'No';
                $content_details_array['created'] = date("d/m/Y \a\\t H:i", strtotime($contents->created));
                $last_modified = date("d/m/Y \a\\t H:i", strtotime($contents->last_modified));
                if(strlen($contents->modified_by) > 1) $last_modified = $last_modified. ' by ' . $contents->modified_by;
                else $last_modified = $last_modified. ' by [deleted staff member]';
                $content_details_array['last_modified'] = $last_modified;
                $content_details_array['tts_enabled'] = $contents->tts_enabled;
                $content_details_array['written_text'] = $contents->written_text;
                $content_details_array['soundfile_location'] = $contents->soundfile_location;
                $content_details_array['gesture'] = 'I_V->jsonify all contents -> ges_id'.$contents->gesture_id;
                $content_details_array['next_content'] = 'I_V->jsonify all contents -> nxt_cntnt'.$contents->next_content;


                $content_as_json = json_encode($contents);
                $content_details_array['buttons'] = "<a href='#' data-toggle='modal' data-id='$content_as_json' class='editContentModalBox btn-success btn-circle btn-sm' data-target='#editContentModalCenter'><i class='fas fa-edit bg-success'></i></a>";
                $content_details_array['buttons'] = $content_details_array['buttons'] . " <a href='#' data-toggle='modal' data-id='$content_as_json' class='deleteContentModalBox btn-success btn-circle btn-sm' data-target='#deleteContentModalCenter'><i class='fas fa-trash'></i></a>";


                $individual_content["data"][] = $content_details_array;
            }
            return json_encode($individual_content, JSON_PRETTY_PRINT );
        }
    }

    /**
     * Short description of method new_content_modal
     *
     * @return void
     */
    public function new_content_modal()
    {
        ?>
            <!-- Add New Content - Form Modal -->
            <div class="modal fade" id="addNewContentModal" tabindex="-1" role="dialog" aria-labelledby="addNewContentModal" aria-hidden="true">
                <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="addNewContentModalLabel">Add New Content</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <form class="user" id="form_new_content" enctype="multipart/form-data" onsubmit="return false">
                        <div class="modal-body">
                        <!-- form input -->
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="new_name" name="name" placeholder="Name" required>
                            </div>
                            <div class="form-group">
                                Use Text-To-Speech to create the audio file?<br />
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tts_enabled" id="new_tts_enabled_yes" value="1" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne" required>
                                    <label class="form-check-label" for="new_tts_enabled_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tts_enabled" id="new_tts_enabled_no" value="0" class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    <label class="form-check-label" for="new_tts_enabled_no">No</label>
                                </div>

                                <div id="accordion">
                                    <div id="collapseOne" class="collapse" aria-labelledby="Yes" data-parent="#accordion">
                                        Enter the text to convert into speech
                                        <textarea class="form-control" rows="10" id="new_written_text" name="written_text"></textarea>
                                    </div>
                                    <div id="collapseTwo" class="collapse" aria-labelledby="No" data-parent="#accordion">
                                        <label for="new_sound_file">Upload your own audio file</label>
                                        <input type="file" class="form-control-file" id="new_sound_file" name="new_sound_file" accept="audio/*">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="new_next_content" name="next_content" placeholder="Next content link: TO DO">
                            </div>
                            <div class="form-group new_gesture_options">
                                Once the audio has finished, which physical gesture needs to be performed?<br />
                                <select id="new_gesture" name="gesture" class="form-control-sm form-control-user-sm">
                                    <option value selected>None</option>
                                    <option value="0">Gesture 1</option>
                                    <option value="1">Gesture 2</option>
                                </select>
                            </div>
                            <div class="form-group new_active_options">
                                Active?<br />
                                <select id="new_active" name="active" class="form-control-sm form-control-user-sm">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <input type="hidden" id="item_id" name="item_id" />
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary" id="btn_content_new_content">Create</button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        <?php
    }

    /**
     * Short description of method edit_content_modal
     *
     * @return void
     */
    public function edit_content_modal()
    {
        ?>
        <!-- Edit Content - Form Modal-->
        <div class="modal fade" id="editContentModalCenter" tabindex="-1" role="dialog" aria-labelledby="editContentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="editContentModalLabel">Edit</h5>
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
                        <button id="btn_content_edit" type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    <?php
    }

    /**
     * Short description of method delete_content_modal
     *
     * @return void
     */
    public function delete_content_modal()
    {
        ?>
        <!-- Delete Content - Confirmation Modal -->
        <div class="modal fade" id="deleteContentModalCenter" tabindex="-1" role="dialog" aria-labelledby="deleteContentModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="deleteContentModalLongTitle">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
                <div class="modal-body">
                    Are you sure you wish to delete <span id="span_name">this content</span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn_content_delete">Delete Content</button>
                </div>
            </div>
          </div>
        </div>
        <?php
    }

} /* end of class Content_View */

?>