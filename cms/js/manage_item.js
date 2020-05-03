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
   * Add new item
   * 
   */      
  $('#form_new_item').submit(function(event){
      event.preventDefault(); //cancels the form submission
      $('#addNewItemModal').modal('toggle'); //closes the modal box

      //build the URL to include GET request data from the form
      var direct_to_url = "ajax.content_actions.php?action=new_item&";
      direct_to_url += $('#form_new_item').serialize();
      //send the data as a GET request to the PHP page specified in direct_to_url
        $.when(save_to_database()).done(function(a1){ //when the ajax request is complete
          item_table.ajax.reload(); //reload the table with the new data
          set_localstorage();
        });
        function save_to_database(){ //call the ajax for saving the changes
        return $.ajax({url: direct_to_url, success: function(result){
            $("#div1").html(result);
        }});
      }
  });

  /**
  * 
  * Edit item 
  *    Populate the Modal Box with item's details
  */

  //Fill in the form fields on the Edit Modal Box with the appropriate data passed by clicked in the hyperlink
  //data is passed in the form of a JSON string.
  $(document).on("click", ".editItemModalBox", function () { //onclick of the Edit icon/button
    item_id = $(this).data('id');
    $.when(ajax_call("ajax.content_actions.php?action=get_item_json&item_id="+item_id)).done(function(result){ //when the ajax request is complete
      var item = result.data[0];
      $(".modal-body #edit_name").val(item.name);
      $(".modal-body #edit_url").val(item.url);
      $(".modal-body #edit_heritage_id").val(item.heritage_id);
      $(".modal-body #edit_location").val(item.location);
      $(".edit_active_options select").val(item.active);
    });
  }); 

  /**
  * 
  * Edit item 
  *    Submission of the data in the Edit Modal Box
  */
  var item_id;
  //Collect the form data and 'submit' the form via AJAX
  $('#edit_form').submit(function(event){
    event.preventDefault(); //cancels the form submission
    $('#editModalCenter').modal('toggle'); //closes the modal box
    var direct_to_url = "ajax.content_actions.php?action=edit_item&item_id="+item_id+"&";
    direct_to_url += $('#edit_form').serialize(); //grab all input boxes
    
    //send the data as a GET request to the PHP page specified in direct_to_url
    $.when(ajax_call(direct_to_url)).done(function(result){ //when the ajax request is complete
      $("#div1").html(result);
      item_table.ajax.reload(); //reload the table with the new data
    });
  });


  /**
  * 
  * Delete Item
  * 
  */
  var name;
  $(document).on("click", ".deleteItemModalBox", function () {//onclick of the Delete icon/button
  //grab the data provided via JSON on the Delete icon/button
    item_id = $(this).data('id');
    $.when(ajax_call("ajax.content_actions.php?action=get_item_json&item_id="+item_id)).done(function(item){ //when the ajax request is complete
      name = item.data[0].name;
      $(".modal-body #span_name").text(name);
    });
  });

  $("#btn_item_delete").click(function(){ //on click of the confirmation delete button (AKA submit the form)
    //send the data as a GET request to the PHP page specified in direct_to_url
    $.when(ajax_call("ajax.content_actions.php?action=delete_item&item_id="+item_id)).done(function(result){ //when the ajax request is complete
      $("#div1").html(result);
      item_table.ajax.reload(); //reload the table with the new data
    });
  });

  /**
  * 
  * Publish content
  * 
  */
  $("#btn_publishModal").click(function(){ //on click of the "publish for devices" button
    //trigger an ajax request to create the relevant JSON file.
    $.when(ajax_call("ajax.content_actions.php?action=publish")).done(function(r){ //when the ajax request is complete
      //Overwrite the "please wait" message upon completion of the request
      $(".modal-body #publishModal_bodytext").text(r.result);
      $(".modal-content #publishModalFooter").removeClass("d-none");
    });
  });
});