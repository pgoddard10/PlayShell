/****
 * 
 * Functions
 * 
 */

 //set onscreen message with appropriate icon. Will remove the action buttons in the footer if waiting
function setmsg(msg,type) {
  switch(type) {
    case "error":
      var icon = "<i class='fas fa-exclamation-triangle'></i>";
      break;
    case "info":
      var icon = "<i class='fas fa-info-circle'></i>";
      break;
    case "wait":
    default:
      var icon = "<i class='fas fa-spinner fa-spin'></i>";
  }

  $(".modal-body #deviceInteractionModal_bodytext").html(icon +" "+ msg);
  if(type=="wait") {
    $(".modal-content #deviceInteractionModalFooter").addClass("d-none");
  }
  else {
    $(".modal-content #deviceInteractionModalFooter").removeClass("d-none");
  }
}

 /****
 * 
 * Listeners
 * 
 */

$(document).ready(function() {
  //Retreive Visitor Data from devices and store into the CMS
    $(document).on("click", "#btn_retreiveVisitorDataModal", function () {//on click of the button
      //Set the "please wait" message
      setmsg("Downloading all visitor data, please wait...","wait");
      //trigger an ajax request to create the relevant JSON file.
      $.ajax({url: "ajax.device_actions.php?action=retreive_visitor_data", success: function(r){
        //Overwrite the "please wait" message upon completion of the request
          var msg = "";
          if(r.data.error) {
            msg = r.data.error.description + " Failed with error code " + r.data.error.code;
            setmsg(msg,"error");
          }
          else {
            msg = "Database successfully updated and visitors who left their email address have been emailed.";
            setmsg(msg,"info");
          } 
      }});
    });

  //Push the published content from the CMS onto all devices
    $(document).on("click", "#btn_pushContentToDevicesModal", function () {//on click of the button
      //Set the "please wait" message
      setmsg("Uploading published content to available devices, please wait...","wait");
      //trigger an ajax request to create the relevant JSON file.
      $.ajax({url: "ajax.device_actions.php?action=update_device", success: function(r){
        //Overwrite the "please wait" message upon completion of the request
          var msg = "";
          if(r.data.error) {
            msg = r.data.error.description + " Failed with error code " + r.data.error.code;
            setmsg(msg,"error");
          }
          else {
            msg = r.data.hostname + " is ready for use.";
            setmsg(msg,"info");
          } 
      }});
    });
});