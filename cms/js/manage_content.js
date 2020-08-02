
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
  
  /**
   * 
   * Add new content
   * 
   */
  
  //Fill in the form fields on the New Modal Box with the appropriate data passed by clicked in the hyperlink
  //data is passed in the form of a JSON string.
  $(document).on("click", ".newContentModalBox", function () { //onclick of the New button
    
    item_id = $(this).data('id').item_id;
    $(".modal-body #item_id").val($(this).data('id').item_id);
    // $('#new_next_content').empty();
    // var db = JSON.parse(localStorage.getItem('db')); 
    // $.each(db.data[0].content, function (key, c) {
    //   $('#new_next_content').append('<option value="'+c.content_id+'">'+c.name+'</option>'); 
    // });
      $(".modal-body #new_name").val("");
      $(".modal-body #new_written_text").val("");
      $("#new_tts_enabled_yes").prop("checked", false);
      $("#new_tts_enabled_no").prop("checked", false);
      $(".modal-body #collapseOne").removeClass("show");
      $(".modal-body #collapseTwo").removeClass("show");

  });

  $('#form_new_content').submit(function(event){
    event.preventDefault(); //cancels the form submission
    $('#addNewContentModal').modal('toggle'); //closes the modal box

    //build the URL to include GET request data from the form
    var direct_to_url = "ajax.content_actions.php?action=new_content&";
    direct_to_url += $('#form_new_content').serialize();

    var data = new FormData();

    //Form data
    var form_data = $('#form_new_content').serializeArray();
    $.each(form_data, function (key, input) {
        data.append(input.name, input.value);
    });
    
    //File data
    var file_data = $('input[name="new_sound_file"]')[0].files;
    for (var i = 0; i < file_data.length; i++) {
        data.append("sound_file", file_data[i]);
    }
    
      $.when(save_to_database()).done(function(a1){ //when the ajax request is complete
        content_table[item_id].ajax.reload(); //reload the table with the new data
        set_localstorage();
        $('#form_new_content').reset();
      });
      function save_to_database(){ //call the ajax for saving the changes
        return $.ajax({
          url: direct_to_url,
          method: "post",
          processData: false,
          contentType: false,
          data: data,
          success: function (result) {
            $("#div1").html(result);
          },
          error: function (e) {
            console.log("create failed with error " + e);
          }
        });
      }
  });

  /**
  * 
  * Edit Content 
  *    Populate the Modal Box with content's details
  */
  var content_id = null;
  //Fill in the form fields on the Edit Modal Box with the appropriate data passed by clicked in the hyperlink
  //data is passed in the form of a JSON string.
  $(document).on("click", ".editContentModalBox", function () { //onclick of the Edit icon/button
    content_id = $(this).data('id');
    $.when(ajax_call("ajax.content_actions.php?action=get_content_json&content_id="+content_id)).done(function(result){ //when the ajax request is complete
      var content = result.data[0];
      item_id = content.item_id;
      $(".modal-body #edit_name").val(content.name);
      $(".modal-body #edit_content_id").val(content_id);
      var tag_id = "";
      if(content.tag_id!=null) var tag_id = content.tag_id;
          $(".modal-body #nfc_tag_id_label").text(": '"+tag_id+"'");
          $(".modal-body #tag_id").val(tag_id);
      // }
      $(".modal-body #nfc_tag_id_label_error").html("");
      if(content.tts_enabled==1) {
          $("#edit_tts_enabled_yes").prop("checked", true);
          $(".modal-body #edit_written_text").val(content.written_text);
          $(".modal-body #edit_collapseOne").addClass("show");
          $(".modal-body #edit_collapseTwo").removeClass("show");
      }
      else {
        $("#edit_tts_enabled_no").prop("checked", true);
        $(".modal-body #edit_sound_file_label").html('A <a download="'+content.name+'.wav" href="audio/'+content.item_id+'/'+content_id+'/sound.wav">sound file</a> has already been uploaded. Replace it: ');
        $(".modal-body #edit_collapseTwo").addClass("show");
        $(".modal-body #edit_collapseOne").removeClass("show");
      }
      $(".edit_gesture_options select").val(content.gesture_id);

      //ensure the NFC tag scanning buttons are shown for adding a new tag
      $(".modal-body #NFC_tag_details #please_wait").addClass('d-none');
      $(".modal-body #NFC_tag_details #id_and_button").removeClass('d-none');

      // $('#edit_next_content').empty();
      // var db = JSON.parse(localStorage.getItem('db')); 
      // $.each(db.data[0].content, function (key, c) {
      //   $('#edit_next_content').append('<option value="'+c.content_id+'">'+c.name+'</option>'); 
      // });
      

      $(".edit_active_options select").val(content.active);
    });
  });

  /**
  * 
  * Edit Content 
  *    Submission of the data in the Edit content Modal Box
  */
  //Collect the form data and 'submit' the form via AJAX
  $('#edit_content_form').submit(function(event){
    event.preventDefault(); //cancels the form submission
    $('#editContentModalCenter').modal('toggle'); //closes the modal box

    var data = new FormData();

    //Form data
    var form_data = $('#edit_content_form').serializeArray();
    $.each(form_data, function (key, input) {
        data.append(input.name, input.value);
    });
    
    //File data
    var file_data = $('input[name="edit_sound_file"]')[0].files;
    for (var i = 0; i < file_data.length; i++) {
        data.append("sound_file", file_data[i]);
    }

    //send the data as a GET request to the PHP page specified in direct_to_url
    $.when(save_to_database()).done(function(a1){ //when the ajax request is complete
      content_table[item_id].ajax.reload(); //reload the table with the new data
      set_localstorage();
    });
    function save_to_database(){ //call the ajax for saving the changes
      return $.ajax({
        url: 'ajax.content_actions.php?action=edit_content',
        method: "POST",
        processData: false,
        contentType: false,
        data: data,
        success: function (result) {
          $("#div1").html(result);
        },
        error: function (e) {
          console.log("create failed with error " + e);
        }
      });
    }
  });


  /**
  * 
  * Delete Content
  * 
  */
  var name;
  $(document).on("click", ".deleteContentModalBox", function () {//onclick of the Delete icon/button
    //grab the data provided via JSON on the Delete icon/button
    content_id = $(this).data('id');
    $.when(ajax_call("ajax.content_actions.php?action=get_content_json&content_id="+content_id)).done(function(result){ //when the ajax request is complete
      var content = result.data[0];
      item_id = content.item_id;
      name = "'" + content.name + "'";
      $(".modal-body #span_name").text(name);
    });
  });

  $("#btn_content_delete").click(function(){ //on click of the confirmation delete button (AKA submit the form)
    //send the data as a GET request to the PHP page specified in direct_to_url
    $.when(ajax_call("ajax.content_actions.php?action=delete_content&content_id="+content_id)).done(function(result){ //when the ajax request is complete
      $("#div1").html(result);
      content_table[item_id].ajax.reload(); //reload the table with the new data
    });
  });

 /**
  * 
  * Remove NFC Tag ID
  * 
  */

 $("#btn_removeNFCTag").click(function(){ //on click of the confirmation delete button (AKA submit the form)
    $(".modal-body #tag_id").val("");
    $(".modal-body #nfc_tag_id_label").text(": ");
    $(".modal-body #nfc_tag_id_label_error").html("");
  });

  /**
  * 
  * Scan NFC Tag -> Request to scan tag
  * 
  */

 $("#btn_newNFCTag").click(function(){ //on click of Add/Change NFC Tag button
  //send the data as a GET request to the PHP page specified in direct_to_url
  $.ajax({url: "ajax.content_actions.php?action=scan_nfc_tag&content_id="+content_id, success: function(result){
    $(".modal-body #NFC_tag_details #please_wait").removeClass('d-none');
    $(".modal-body #NFC_tag_details #id_and_button").addClass('d-none');
    }});
});

/**
* 
* Scan NFC Tag -> Scanning of tag confirmed by user
* 
*/
$("#btn_confirm_tag_scanned").click(function(){ //on click of the confirmation of user scanning the tag
 //send the data as a GET request to the PHP page specified in direct_to_url
  $(".modal-body #NFC_tag_details #please_wait").addClass('d-none');
  $(".modal-body #NFC_tag_details #id_and_button").removeClass('d-none');
 $.ajax({
   url: "ajax.content_actions.php?action=get_nfc_id&content_id="+content_id,
   success: function(result){
      var tag_id = result.tag_id;
      if(result==-1) {
        $(".modal-body #nfc_tag_id_label_error").html("Something went wrong. Please try again.<br />");
      }
      else {
        $(".modal-body #tag_id").val(tag_id);
        $(".modal-body #nfc_tag_id_label").text(": '"+tag_id+"'");
        $(".modal-body #nfc_tag_id_label_error").html("");
      }
   },
   error: function (jqXHR, exception) {
       var msg = '';
       if (jqXHR.status === 0) {
           msg = 'Not connect.\n Verify Network.';
       } else if (jqXHR.status == 404) {
           msg = 'Requested page not found. [404]';
       } else if (jqXHR.status == 500) {
           msg = 'Internal Server Error [500].';
       } else if (exception === 'parsererror') {
           msg = 'Requested JSON parse failed.';
       } else if (exception === 'timeout') {
           msg = 'Time out error.';
       } else if (exception === 'abort') {
           msg = 'Ajax request aborted.';
       } else {
           msg = 'Uncaught Error.\n' + jqXHR.responseText;
       }
       $(".modal-body #nfc_tag_id_label_error").html("Something went wrong. Please try again.<br />"+msg+"<br />");
   }
  });
});
  
});