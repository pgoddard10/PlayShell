

$(document).ready(function() {
    
  /**
  * 
  * Retreive Visitor Data from devices and store into the CMS
  * 
  */

  $(document).on("click", "#btn_retreiveVisitorDataModal", function () {//on click of the button
  //Set the "please wait" message
  $(".modal-body #deviceInteractionModal_bodytext").html("<i class='fas fa-spinner fa-spin'></i> Downloading all visitor data, please wait...");
  $(".modal-content #deviceInteractionModalFooter").addClass("d-none");


  //trigger an ajax request to create the relevant JSON file.
    $.ajax({url: "ajax.device_actions.php?action=retreive_visitor_data", success: function(r){
      //Overwrite the "please wait" message upon completion of the request
        var msg = "";
        if(r.data.error) msg = r.data.error.description + " Failed with error code " + r.data.error.code;
        else msg = "Database successfully updated and visitors who left their email address have been emailed.";
      $(".modal-body #deviceInteractionModal_bodytext").text(msg);
      $(".modal-content #deviceInteractionModalFooter").removeClass("d-none");
    }});
  });


  /**
  * 
  * Push the published content from the CMS onto all devices
  * 
  */
  
  $(document).on("click", "#btn_pushContentToDevicesModal", function () {//on click of the button
  //Set the "please wait" message
  $(".modal-body #deviceInteractionModal_bodytext").html("<i class='fas fa-spinner fa-spin'></i>Uploading published content to available devices, please wait...");
  $(".modal-content #deviceInteractionModalFooter").addClass("d-none");


  //trigger an ajax request to create the relevant JSON file.
    $.ajax({url: "ajax.device_actions.php?action=update_device", success: function(r){
      //Overwrite the "please wait" message upon completion of the request
        var msg = "";
        if(r.data.error) msg = r.data.error.description + " Failed with error code " + r.data.error.code;
        else msg = r.data.hostname + " is ready for use.";
      $(".modal-body #deviceInteractionModal_bodytext").text(msg);
      $(".modal-content #deviceInteractionModalFooter").removeClass("d-none");
    }});
  });



});