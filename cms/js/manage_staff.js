
//AJAX call function
//Takes a URL as input and returns the response (which is usually JSON)
function ajax_call(ajax_address){
  return $.ajax({
    url: ajax_address,
    success: function(result){
        return result;
    },
    error: function (jqXHR, exception) {
        var err = '';
        if (jqXHR.status === 0) {
          err = {code: jqXHR.status, msg: 'Not connect.\n Verify Network.'};
        } else if (jqXHR.status == 404) {
          err = {code: jqXHR.status, msg: 'Requested page not found. [404]'};
        } else if (jqXHR.status == 500) {
          err = {code: jqXHR.status, msg: 'Internal Server Error [500].'};
        } else if (exception === 'parsererror') {
          err = {code: exception, msg: 'Requested JSON parse failed.'};
        } else if (exception === 'timeout') {
          err = {code: exception, msg: 'Time out error.'};
        } else if (exception === 'abort') {
          err = {code: exception, msg: 'Ajax request aborted.'};
        } else {
          err = {code: null, msg: 'Uncaught Error.\n' + jqXHR.responseText};
        }
        return err;
    }
    });
}

$(document).ready(function() {

  //Staff Management Table
  var staff_table = $('#manage_staff_data_table').DataTable( {
    "order": [[ 4, "desc" ],[ 0, "desc" ]],
    "ajax": {
      url: "ajax.get_table_data.php?page=staff",
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
    $.when(ajax_call(direct_to_url)).done(function(result){ //when the ajax request is complete
      $("#div1").html(result);
      staff_table.ajax.reload(); //reload the table with the new data
    });
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
    staff_id = $(this).data('id');
    $.when(ajax_call("ajax.staff_actions.php?action=get_staff_json&staff_id="+staff_id)).done(function(result){ //when the ajax request is complete
      var staff = result.data[0];
      console.log(staff);
      $(".modal-body #edit_first_name").val(staff.first_name);
      $(".modal-body #edit_last_name").val(staff.last_name);
      $(".modal-body #edit_email").val(staff.email);
      $(".modal-body #edit_password").val("");
      $(".modal-body #edit_repeat_password").val("");
      $(".edit_active_options select").val(staff.active);
      var i;
      //set all roles checkboxes to unticked/off
      for (i = 1; i <= 5; i++) {
          $("#ckbox_edit_role_"+i).prop("checked", false);
      }
  
      //tick the checkboxes that match the roles this staff member has
      var roles = staff.roles;
      $.each(roles, function(index, value) {
          $("#ckbox_edit_role_"+value.role_id).prop("checked", true);
      });
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
    $.when(ajax_call(direct_to_url)).done(function(result){ //when the ajax request is complete
      $("#div1").html(result);
      staff_table.ajax.reload(); //reload the table with the new data
    });
  });

  
  /**
   * 
   * Delete staff
   * 
   */
  var name;
  $(document).on("click", ".deleteModalBox", function () {//onclick of the Delete icon/button
    //grab the data provided via JSON on the Delete icon/button
    staff_id = $(this).data('id');
    $.when(ajax_call("ajax.staff_actions.php?action=get_staff_json&staff_id="+staff_id)).done(function(staff){ //when the ajax request is complete
      name = staff.data[0].display_name;
      $(".modal-body #span_name").text(name);
    });
  });
  $("#btn_staff_delete").click(function(){ //on click of the confirmation delete button (AKA submit the form)
      //send the data as a GET request to the PHP page specified in direct_to_url
      $.when(ajax_call("ajax.staff_actions.php?action=delete&staff_id="+staff_id)).done(function(result){ //when the ajax request is complete
        $("#div1").html(result);
        staff_table.ajax.reload(); //reload the table with the new data
      });
  });


} );