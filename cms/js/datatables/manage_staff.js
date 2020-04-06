//Call the dataTables jQuery plugin

$(document).ready(function() {

  //Staff Management Table
  var staff_table = $('#manage_staff_data_table').DataTable( {
    "order": [[ 4, "desc" ],[ 0, "desc" ]],
    "ajax": {
      url: "ajax.staff_table_data.php?action=display_table",
      "contentType": "application/json",
      "dataSrc": "data"
    },
    "columns": [
          { "data": "name" },
          { "data": "username" },
          { "data": "email" },
          { "data": "roles" },
          { "data": "active" },
          { "data": "buttons" }
      ]
  } );


  /**
   * 
   * Add new staff
   * 
   */
    //Script to validate that both passwords match on creating new, or editing a staff member.
      var new_password = document.getElementById("new_password")
      , new_repeat_password = document.getElementById("new_repeat_password");

      function validatePassword(){
      if(new_password.value != new_repeat_password.value) {
        new_repeat_password.setCustomValidity("Passwords do not match");
      } else {
        new_repeat_password.setCustomValidity('');
      }
      }
      new_password.onchange = validatePassword;
      new_repeat_password.onkeyup = validatePassword;
    
      $('#form_new_staff').submit(function(event){
          event.preventDefault(); //cancels the form submission
          $('#addNewModal').modal('toggle'); //closes the modal box

          //build the URL to include GET request data from the form
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
          //send the data as a GET request to the PHP page specified in direct_to_url
            $.when(save_to_database()).done(function(a1){ //when the ajax request is complete
              staff_table.ajax.reload(); //reload the table with the new data
            });
            function save_to_database(){ //call the ajax for saving the changes
            return $.ajax({url: direct_to_url, success: function(result){
                $("#div1").html(result);
            }});
          }
      });

  /**
   * 
   * Edit staff 
   *    Populate the Modal Box with staff member's details
   */

  //Fill in the form fields on the Edit Modal Box with the appropriate data passed by clicked in the hyperlink
  //data is passed in the form of a JSON string.
  $(document).on("click", ".editModalBox", function () { //onclick of the Edit icon/button

    //grab the JSON data provided on the Edit icon/button and fill in the form input boxes
    staff_id = $(this).data('id').staff_id;
    $(".modal-body #edit_first_name").val($(this).data('id').first_name);
    $(".modal-body #edit_last_name").val($(this).data('id').last_name);
    $(".modal-body #edit_email").val($(this).data('id').email);
    $(".edit_active_options select").val($(this).data('id').active);
    var i;
    //set all roles checkboxes to unticked/off
    for (i = 1; i <= 5; i++) {
        $("#ckbox_edit_role_"+i).prop("checked", false);
    }

    //tick the checkboxes that match the roles this staff member has
    var roles = $(this).data('id').roles;
    $.each(roles, function(index, value) {
            $("#ckbox_edit_role_"+value.role_id).prop("checked", true);
        });
  });

  /**
   * 
   * Edit staff 
   *    Submission of the data in the Edit Modal Box
   */
  var staff_id;
  //Collect the form data and 'submit' the form via AJAX
  $('#edit_form').submit(function(event){
      event.preventDefault(); //cancels the form submission
      $('#editModalCenter').modal('toggle'); //closes the modal box
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
      $.when(save_to_database()).done(function(a1){ //when the ajax request is complete
        staff_table.ajax.reload(); //reload the table with the new data
      });
      function save_to_database(){ //call the ajax for saving the changes
        return $.ajax({url: direct_to_url, success: function(result){
            $("#div1").html(result);
        }});
      }
  });


  /**
   * 
   * Deactivate staff
   * 
   */
  var display_name;
  $(document).on("click", ".deactivateModalBox", function () {//onclick of the Deactivate icon/button
    //grab the data provided via JSON on the Deactivate icon/button
    display_name = $(this).data('id').display_name;
    staff_id = $(this).data('id').staff_id;
    $(".modal-body #span_name").text(display_name);
  });

  $("#btn_staff_deactivate").click(function(){ //on click of the confirmation deactivate button (AKA submit the form)
      //send the data as a GET request to the PHP page specified in direct_to_url
      $.when(save_to_database()).done(function(a1){ //when the ajax request is complete
        staff_table.ajax.reload(); //reload the table with the new data
      });
      function save_to_database(){ //call the ajax for saving the changes
        return $.ajax({url: "ajax.staff_actions.php?action=deactivate&staff_id="+staff_id, success: function(result){
            $("#div1").html(result);
        }});
      }
  });


} );