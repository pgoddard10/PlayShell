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
        switch($success) {
            case 0:
                $msg = "Successfully created '".$_POST['name']."'";
                $msg_type = "success";
            break;
            case -2:
                $msg = "Successfully created '".$_POST['name']."' in the database successfully but could not convert text to speech";
                $msg_type = "warning";
            break;
            case -3:
                $msg = "Successfully created '".$_POST['name']."' but unable to upload the soundfile. Please try using text-to-speech instead.";
                $msg_type = "warning";
            break;
            case -4://file is of non-accepted filetype
                $msg = "Successfully created '".$_POST['name']."' but unable to upload the soundfile. However, the filetype of your upload is not allowed. Please use the edit feature to try again.";
                $msg_type = "warning";
            break;
            case -5://could save file
                $msg = "Successfully created '".$_POST['name']."' but unable to upload the soundfile.";
                $msg_type = "warning";
            break;
            case -6://Soundfile not specified for non-TTS system
                $msg = "Unable to create '".$_POST['name']."'. You have not specified any audio content.";
                $msg_type = "danger";
            break;
            case -1:
            default:
                $msg = "Unable to create '".$_POST['name']."'. An unknown error occurred.";
                $msg_type = "danger";
            break;
        }
        ?>
              <!-- Add Message Card -->
                <div class="card mb-4 py-3 border-left-<?php echo $msg_type; //change colour depending on whether success or not ?>"> 
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
    public function edit($modified_by)
    { 
        $success = $this->content_controller->edit($modified_by);
        switch($success) {
            case 0:
                $msg = "Successfully edited '".$_POST['name']."'.";
                break;
            case -2:
                $msg = "Changes for '".$_POST['name']."' were not saved. There was a database error editing the content details.";
                break;
            case -1:
            default:
                $msg = "Changes for '".$_POST['name']."' were not saved. An unknown error occurred.";
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
    public function delete()
    {
        $success = $this->content_controller->delete();
        if($success==0) $msg = "Successfully deleted the content.";
        else $msg = "An unknown error occurred. $success";
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
                $content_details_array['content_id'] = $contents->content_id;
                $content_details_array['item_id'] = $contents->item_id;
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
                $content_details_array['gesture_id'] = $contents->gesture_id;
                $content_details_array['gesture_name'] = $contents->gesture_name;
                $content_details_array['next_content_id'] = $contents->next_content_id;
                $content_details_array['next_content_name'] = $contents->next_content_name;

                $content_as_json = json_encode($contents, JSON_HEX_APOS);
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
                            <div class="form-group new_gesture_options">
                                Once the audio has finished, which physical gesture needs to be performed?<br />
                                <select id="new_gesture" name="gesture" class="form-control-sm form-control-user-sm">
                                    <option value selected>None</option>
                                    <option value="0">Gesture 1</option>
                                    <option value="1">Gesture 2</option>
                                </select>
                            </div>
                            <div class="form-group edit_next_content_options">
                                After all of the above has been performed, which content should play next?<br />
                                <select id="new_next_content" name="next_content" class="form-control-sm form-control-user-sm">
                                    <option value selected>None</option>
                                </select>
                            </div>
                            <div class="form-group new_active_options">
                                Active?<br />
                                <select id="new_active" name="active" class="form-control-sm form-control-user-sm">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <p>You can associate an NFC tag after the content has been created.</p>
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
        <div class="modal hide fade" id="editContentModalCenter" tabindex="-1" role="dialog" aria-labelledby="editContentModalLabel" aria-hidden="true" data-focus-on="input:first">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="editContentModalLabel">Edit Content</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <form class="user" id="edit_content_form" enctype="multipart/form-data" onsubmit="return false">
                        <div class="modal-body">
                        <!-- form input -->
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user" id="edit_name" name="name" placeholder="Name" required>
                            </div>
                            <div class="form-group">
                                Use Text-To-Speech to create the audio file?<br />
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="edit_tts_enabled" id="edit_tts_enabled_yes" data-toggle="collapse" data-target="#edit_collapseOne" aria-expanded="false" aria-controls="edit_collapseOne" required>
                                    <label class="form-check-label" for="edit_tts_enabled_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="edit_tts_enabled" id="edit_tts_enabled_no" class="btn btn-link collapsed" data-toggle="collapse" data-target="#edit_collapseTwo" aria-expanded="false" aria-controls="edit_collapseTwo">
                                    <label class="form-check-label" for="edit_tts_enabled_no">No</label>
                                </div>

                                <div id="edit_accordion">
                                    <div id="edit_collapseOne" class="collapse" aria-labelledby="Yes" data-parent="#edit_accordion">
                                        Enter the text to convert into speech
                                        <textarea class="form-control" rows="10" id="edit_written_text" name="written_text"></textarea>
                                    </div>
                                    <div id="edit_collapseTwo" class="collapse" aria-labelledby="No" data-parent="#edit_accordion">
                                        <label for="edit_sound_file" id="edit_sound_file_label">Upload your own audio file</label>
                                        <input type="file" class="form-control-file" id="edit_sound_file" name="edit_sound_file" accept="audio/*">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group edit_gesture_options">
                                Once the audio has finished, which physical gesture needs to be performed?<br />
                                <select id="edit_gesture" name="gesture" class="form-control-sm form-control-user-sm">
                                    <option value selected>None</option>
                                    <option value="0">Gesture 1</option>
                                    <option value="1">Gesture 2</option>
                                </select>
                            </div>
                            <div class="form-group edit_next_content_options">
                                After all of the above has been performed, which content should play next?<br />
                                <select id="edit_next_content" name="next_content" class="form-control-sm form-control-user-sm">
                                    <option value selected>None</option>
                                </select>
                            </div>
                            <div class="form-group edit_active_options">
                                Active?<br />
                                <select id="edit_active" name="active" class="form-control-sm form-control-user-sm">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                NFC Tag <span id="nfc_tag_id_label"></span><br /><span id="nfc_tag_id_label_error"></span>
                                <input type="hidden" id="tag_id" name="tag_id" />
                                <a href='#' id='btn_newNFCTag' class='btn_newNFCTag btn-sm btn-success btn-icon-split' data-toggle="modal" data-target="#NFCTagModal"><span class='icon text-white-50'><i class='fas fa-tag'></i></span><span class='text'>Add/Change NFC Tag</span></a>
                            </div>
                            <input type="hidden" id="edit_content_id" name="content_id" />
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

    
    /**
     * Short description of method show_NFC_tag_modal
     *
     * @return void
     */
    public function show_NFC_tag_modal()
    {
        ?>
        <!-- Prompt to scan NFC tag -->
        <div class="modal hide fade" id="NFCTagModal" tabindex="-1" role="dialog" aria-labelledby="NFCTagModalTitle" aria-hidden="true" data-focus-on="input:first">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="NFCTagModalLongTitle">Add/Change NFC Tag</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="NFCTagModal_bodytext">Please wait...</span>
                </div>
                <div class="modal-footer d-none" id="NFCTagModalFooter">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="btn_confirm_tag_scanned">Tag Scanned</button>
                </div>
            </div>
          </div>
        </div>

        <?php
    }

    /**
     * Short description of method scan_nfc_tag
     *
     * @return void
     */
    public function scan_nfc_tag() {
        $success = $this->content_controller->scan_nfc_tag();
    }


    /**
     * Short description of method get_nfc_id
     *
     * @return void
     */
    public function get_nfc_id() {
        $tag_id_json = $this->content_controller->get_nfc_id();
        echo header('Content-Type: application/json');
        echo $tag_id_json;
    }
} /* end of class Content_View */

?>