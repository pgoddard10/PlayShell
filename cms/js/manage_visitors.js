

$(document).ready(function() {

    //Visitor Management Table
    var visitor_table = $('#manage_visitor_data_table').DataTable( {
      "ajax": {
        url: "ajax.get_table_data.php?page=visitor",
        "contentType": "application/json",
        "dataSrc": "data"
      },
      "columns": [
            { "data": "name" },
            { "data": "email" },
            { "data": "address" },
            { "data": "buttons" }
        ]
    } );
  
  
    /**
     * 
     * Add new visitor
     * 
     */      
        $('#form_new_visitor').submit(function(event){
            event.preventDefault(); //cancels the form submission
            $('#addNewModal').modal('toggle'); //closes the modal box
            //build the URL to include GET request data from the form
            var roles = [];
            var direct_to_url = "ajax.visitor_actions.php?action=new&";
            direct_to_url += $('#form_new_visitor').serialize();
            //send the data as a GET request to the PHP page specified in direct_to_url
              $.when(save_to_database()).done(function(a1){ //when the ajax request is complete
                visitor_table.ajax.reload(); //reload the table with the new data
              });
              function save_to_database(){ //call the ajax for saving the changes
              return $.ajax({url: direct_to_url, success: function(result){
                  $("#div1").html(result);
              }});
            }
        });
  
    /**
     * 
     * Edit visitor 
     *    Populate the Modal Box with visitor's details
     */
  
    //Fill in the form fields on the Edit Modal Box with the appropriate data passed by clicked in the hyperlink
    //data is passed in the form of a JSON string.
    $(document).on("click", ".editModalBox", function () { //onclick of the Edit icon/button
      //grab the JSON data provided on the Edit icon/button and fill in the form input boxes
      visitor_id = $(this).data('id').visitor_id;
      $(".modal-body #edit_first_name").val($(this).data('id').first_name);
      $(".modal-body #edit_last_name").val($(this).data('id').last_name);
      $(".modal-body #edit_email").val($(this).data('id').email);
      $(".modal-body #edit_address_1").val($(this).data('id').address_1);
      $(".modal-body #edit_address_2").val($(this).data('id').address_2);
      $(".modal-body #edit_address_3").val($(this).data('id').address_3);
      $(".modal-body #edit_address_4").val($(this).data('id').address_4);
      $(".modal-body #edit_address_postcode").val($(this).data('id').address_postcode);
    });
  
    /**
     * 
     * Edit visitor 
     *    Submission of the data in the Edit Modal Box
     */
    var visitor_id;
    //Collect the form data and 'submit' the form via AJAX
    $('#edit_form').submit(function(event){
        event.preventDefault(); //cancels the form submission
        $('#editModalCenter').modal('toggle'); //closes the modal box
        var roles = [];
        var direct_to_url = "ajax.visitor_actions.php?action=edit&visitor_id="+visitor_id+"&";
        direct_to_url += $('#edit_form').serialize(); //grab all input boxes
  
        //send the data as a GET request to the PHP page specified in direct_to_url
        $.when(save_to_database()).done(function(a1){ //when the ajax request is complete
          visitor_table.ajax.reload(); //reload the table with the new data
        });
        function save_to_database(){ //call the ajax for saving the changes
          return $.ajax({url: direct_to_url, success: function(result){
              $("#div1").html(result);
          }});
        }
    });
  
  
    /**
     * 
     * Delete visitor
     * 
     */
    var name;
    $(document).on("click", ".deleteModalBox", function () {//onclick of the Delete icon/button
      //grab the data provided via JSON on the Delete icon/button
      name = $(this).data('id').first_name;
      name += " ";
      name += $(this).data('id').last_name;
      visitor_id = $(this).data('id').visitor_id;
      $(".modal-body #span_name").text(name);
    });

    $("#btn_visitor_delete").click(function(){ //on click of the confirmation delete button (AKA submit the form)
        //send the data as a GET request to the PHP page specified in direct_to_url
        $.when(save_to_database()).done(function(a1){ //when the ajax request is complete
          visitor_table.ajax.reload(); //reload the table with the new data
        });
        function save_to_database(){ //call the ajax for saving the changes
          return $.ajax({url: "ajax.visitor_actions.php?action=delete&visitor_id="+visitor_id, success: function(result){
              $("#div1").html(result);
          }});
        }
    });


  /**
  * 
  * Check-out device
  * 
  */

  $(document).on("click", ".btn_checkOutModal", function () {//on click of the "check out" button
  //Set the "please wait" message
  $(".modal-body #checkOutModal_bodytext").html("<i class='fas fa-spinner fa-spin'></i> Finding an available device, please wait...");
  $(".modal-content #checkOutModalFooter").addClass("d-none");
  //trigger an ajax request to create the relevant JSON file.
    visitor_id = $(this).data('id').visitor_id;
    $.ajax({url: "ajax.visitor_actions.php?action=check_out_device&visitor_id="+visitor_id, success: function(r){
      //Overwrite the "please wait" message upon completion of the request
        var msg = "";
        if(r.data.error) msg = r.data.error.description + " Failed with error code " + r.data.error.code;
        else msg = r.data.hostname + " is ready for use.";
      $(".modal-body #checkOutModal_bodytext").text(msg);
      $(".modal-content #checkOutModalFooter").removeClass("d-none");
    }});
  });
  
} );