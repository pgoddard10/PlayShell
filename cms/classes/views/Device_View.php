<?php
/**
 * Class Device_View
 * Responsible for printing data used within the Device Management functionality.
 *
 * @author	Paul Goddard
 * 			paul2.goddard@live.uwe.ac.uk
 * 			https://github.com/pgoddard10/
 * 			https://www.linkedin.com/in/pgoddard10/
 * 			https://twitter.com/pgoddard10
 * @date Spring 2020 
 */
require_once("config.php");
require_once("classes/controllers/Device_Controller.php");

class Device_View
{
  private $device_controller = null;
 
	/**
	 * method __construct()
	 * sets up this class and an instance of the Device_Controller
	 */
  function __construct() {
      $this->device_controller = new Device_Controller();
  }
 
	/**
	 * method retreive_visitor_data()
	 * Prints the JSON response for getting all visitor data from all devices
	 */
  public function retreive_visitor_data(){
      header('Content-Type: application/json');
      echo $this->device_controller->retreive_visitor_data();
      $this->device_controller->compose_email();
  }
   
	/**
	 * method update_device()
	 * Prints the JSON response for updating all devices
	 */
  public function update_device(){
      header('Content-Type: application/json');
      echo $this->device_controller->update_device();
  }
  
 
	/**
	 * method device_interaction_modal()
	 * prints the contents of the modal window.
   * This is a template of sorts to be reused for both types of device synching.
   * The human-readable text and button actions are handled by JavaScript & AJAX calls.
	 */
  public function device_interaction_modal()
  {
      ?>
      <!-- Device Interaction Modal -->
      <div class="modal fade" id="deviceInteractionModalCenter" tabindex="-1" role="dialog" aria-labelledby="deviceInteractionModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="deviceInteractionModalLongTitle">Manage Devices</h5>
              <button type="button" class="close btn_deviceInteractionModalClose" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
              <div class="modal-body">
                  <span id="deviceInteractionModal_bodytext"><i class="fas fa-spinner fa-spin"></i> Please wait...</span>
              </div>
              <div class="modal-footer d-none"  id="deviceInteractionModalFooter">
                  <button type="button" class="btn btn-primary btn_deviceInteractionModalClose" data-dismiss="modal">Close</button>
              </div>
          </div>
        </div>
      </div>
      <?php
  }
}



?>