//Call the dataTables jQuery plugin

$(document).ready(function() {

  //store all the ITems in the local storage
  function set_localstorage() {
    $.ajax({url: "ajax.get_table_data.php?page=item", success: function(result){
      localStorage.removeItem("db");
      localStorage.setItem("db",JSON.stringify(result));
    }});
  }

  /**
   * 
   * =====================================================================
   *  Start of Datatables display.
   * 
   *  This section gets all of the data and displays it into the placeholder <table>
   * =====================================================================
   * 
   */  
  //Item Management Table
  var item_table = $('#manage_items_data_table').DataTable( {
    "order": [ 1, "asc" ],
    "ajax": {
      url: "ajax.get_table_data.php?page=item",
      "contentType": "application/json",
      "dataSrc": "data"
    },
    "columns": [
          {
              "className":      'details-control',
              "orderable":      false,
              "data":           null,
              "defaultContent": '',
              "render": function () {
                  return '<i class="fa fa-plus-square" aria-hidden="true"></i>'; //see comments : https://datatables.net/examples/api/row_details.html
              },
              width: "15px"
          },
          { "data": "name_with_url" },
          { "data": "heritage_id" },
          { "data": "location" },
          { "data": "active" },
          {
              "data":         "buttons",
              "searchable":   false,
              "orderable":    false,
              "width":        "100px"
          },
          {
              "data": "created",
              "searchable": true,
              "visible": false 
          },
          {
              "data": "last_modified",
              "searchable": true,
              "visible": false 
          }
      ]
  } );

  // This will display under Content row, when expanded
  // i.e. drill down two levels to see it / displays under the second table
  function format_childs_child(d) {
    var to_display = "<div><strong>Last Modified:</strong> " + d.last_modified + " & <strong>Created on:</strong> " + d.created + ".</div><br />";
    if(d.tts_enabled==1) {
      to_display += '<p>This text, below, was converted to speech. <a download="'+d.name+'.mp3" href="audio/'+d.item_id+'/'+d.content_id+'/sound.mp3">Download a copy</a><br />'+d.written_text+'</p>';
    }
    else {
      to_display += '<p><a download="'+d.name+'.mp3" href="audio/'+d.item_id+'/'+d.content_id+'/sound.mp3">'+d.name+'</a>  was uploaded to play when the tag is scanned.</p>';
    }
    return to_display;
  }


  // Add event listener for opening and closing the Item row (first table)
  // Displays the child content via ajax calls
  $('#manage_items_data_table tbody').on('click', 'td.details-control', function () { //when the plus/minus is clicked
      var tr = $(this).closest('tr');
      var row = item_table.row( tr );
      var tdi = tr.find("i.fa");

      if (row.child.isShown() ) {
          // This row is already open - close it (remove the minus icon and display the plus icon instead)
          row.child.hide();
          tr.removeClass('shown');
          tdi.first().removeClass('fa-minus-square');
          tdi.first().addClass('fa-plus-square');
      }
      else {
          // ajax calls to get child content and displays on screen
          var d = row.data();
          var child_table_name = 'child_details_'+d.item_id; //each child (second-level) table needs a unique ID for correct styling if more than one is open at a time

          //start building the text to display under the opened row in the Item (first) table
          var to_display = "";
          to_display += "<div><strong>Last Modified:</strong> " + d.last_modified + " & <strong>Created on:</strong> " + d.created + ".</div><br />";


          to_display += "<div class='d-sm-flex align-items-center justify-content-between mb-4'>";
          to_display +=   '<p class="text-lg mb-0">'+ d.name_without_url +' contains the following tags/content:</p>';
          var item_id_json = '{"item_id" : "'+d.item_id+'"}';
          to_display +=   "<a href='#' data-toggle='modal' data-target='#addNewContentModal' class='newContentModalBox btn btn-success btn-icon-split' data-id='"+item_id_json+"'><span class='icon text-white-50'><i class='fas fa-plus-circle'></i></span><span class='text'>Add New</span></a>";
          to_display += '</div>';

          to_display += '<table id = "'+child_table_name+'" width="100%">';
          to_display += '<thead>' +
              '<tr>' +
                  '<th></th>' +
                  '<th>Name</th>' +
                  '<th>NFC Tag ID</th>' +
                  '<th>Active?</th>' +
                  '<th>Gesture</th>' +
                  '<th>Next Content</th>' +
                  '<th></th>' +
              '</tr>' +
          '</thead>'+
              '<tbody>';
          to_display += '</tbody></table></div>';
          row.child(to_display).show(); //actually display the stuff that's been built just above on the screen
          row.child().addClass( 'bg-gray-800 text-gray-100' ); //add a class to change the bg colour

          //now start work on the second level table
          //this table will display the Content data, i.e. about NFC tags.
          var content_table = $('#'+child_table_name).DataTable({
            "order": [ 1, "asc" ],
            destroy: true,
            "ajax": {
              url: "ajax.get_table_data.php?page=content&item_id="+d.item_id,
              "contentType": "application/json",
              "dataSrc": "data"
            },
              "columns": [
                {
                    "className":      'child-details-control',
                    "orderable":      false,
                    "data":           null,
                    "defaultContent": '',
                    "render": function () {
                        return '<i class="fa fa-plus-square" aria-hidden="true"></i>'; //see comments : https://datatables.net/examples/api/row_details.html
                    },
                    width: "15px"
                },
                {
                  "data":         "name",
                  "width":        "20%"
                },
                {
                  "data":         "tag_id",
                  "width":        "100px"
                },
                { "data": "active" },
                { "data": "gesture_name" },
                { "data": "next_content_name" },
                { 
                  "data":         "buttons",
                  "searchable":   false,
                  "orderable":    false,
                  "width":        "80px"
                },
                {
                    "data": "created",
                    "searchable": true,
                    "visible": false 
                },
                {
                    "data": "last_modified",
                    "searchable": true,
                    "visible": false 
                }
              ]
          });
          // This row is already closed so open it (remove the plus icon and display the minus icon instead)
          tr.addClass('shown');
          tdi.first().removeClass('fa-plus-square');
          tdi.first().addClass('fa-minus-square');


          //within the second-level table (displaying the Content / NFC tag details), if the plus/minus is clicked
          $('#'+child_table_name+' tbody').on('click', 'td.child-details-control', function () {
            var tr = $(this).closest('tr');
            var row = content_table.row( tr );
            var tdi = tr.find("i.fa");
      
            if (row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
                tdi.first().removeClass('fa-minus-square');
                tdi.first().addClass('fa-plus-square');
            }
            else {
              row.child(format_childs_child(row.data())).show();; //call the format_childs_child() function to display some additional data once the plus is clicked
              row.child().addClass( 'bg-gray-100 text-gray-900' );//add a class to change the bg colour
              // This row is already closed so open it (remove the plus icon and display the minus icon instead)
              tr.addClass('shown');
              tdi.first().removeClass('fa-plus-square');
              tdi.first().addClass('fa-minus-square');
            }
          });

      }
  } );

  



/**
 * 
 * =====================================================================
 *  End of Datatables display.
 * 
 *  Start of button functionality
 * 
 *     ITEM - Level
 * 
 * =====================================================================
 * 
 */  


  
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
  item_id = $(this).data('id').item_id;
  $(".modal-body #edit_name").val($(this).data('id').name);
  $(".modal-body #edit_url").val($(this).data('id').url);
  $(".modal-body #edit_heritage_id").val($(this).data('id').heritage_id);
  $(".modal-body #edit_location").val($(this).data('id').location);
  $(".edit_active_options select").val($(this).data('id').active);
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
* Delete Item
* 
*/
var name;
$(document).on("click", ".deleteItemModalBox", function () {//onclick of the Delete icon/button
//grab the data provided via JSON on the Delete icon/button
name = "'" + $(this).data('id').name + "'";
item_id = $(this).data('id').item_id;
$(".modal-body #span_name").text(name);
});

$("#btn_item_delete").click(function(){ //on click of the confirmation delete button (AKA submit the form)
  //send the data as a GET request to the PHP page specified in direct_to_url
  $.when(save_to_database()).done(function(a1){ //when the ajax request is complete
    item_table.ajax.reload(); //reload the table with the new data
    set_localstorage();
  });
  function save_to_database(){ //call the ajax for saving the changes
    return $.ajax({url: "ajax.content_actions.php?action=delete_item&item_id="+item_id, success: function(result){
        console.log("successfully deleted");
        $("#div1").html(result);
    }});
  }
});



  /**
 * 
 * =====================================================================
 *  
 * 
 *  END of ITEM Level functionality
 *  START of CONTENT Level functionality
 * 
 * =====================================================================
 * 
 */  

  
  /**
   * 
   * Add new content
   * 
   */
  
  //Fill in the form fields on the Edit Modal Box with the appropriate data passed by clicked in the hyperlink
  //data is passed in the form of a JSON string.
  $(document).on("click", ".newContentModalBox", function () { //onclick of the Edit icon/button
    //grab the JSON data provided on the Edit icon/button and fill in the form input boxes
    item_id = $(this).data('id').item_id;
    $(".modal-body #item_id").val($(this).data('id').item_id);
    var db = JSON.parse(localStorage.getItem('db')); 
    $.each(db.data[0].content, function (key, c) {
      $('#new_next_content').append('<option value="'+c.content_id+'">'+c.name+'</option>'); 
    });
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
        data.append("sound_file[]", file_data[i]);
    }
    
    //Custom data
    // data.append('key', 'value');
    
      $.when(save_to_database()).done(function(a1){ //when the ajax request is complete
        item_table.ajax.reload(); //reload the table with the new data
        set_localstorage();
      });
      function save_to_database(){ //call the ajax for saving the changes
        return $.ajax({
          url: direct_to_url,
          method: "post",
          processData: false,
          contentType: false,
          data: data,
          success: function (result) {
            console.log("successfully created");
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

    //grab the JSON data provided on the Edit icon/button and fill in the form input boxes
    content_id = $(this).data('id').content_id;
    $(".modal-body #edit_name").val($(this).data('id').name);
    $(".modal-body #edit_content_id").val($(this).data('id').content_id);
    var tag_id = "";
    if($(this).data('id').tag_id!=null) var tag_id = $(this).data('id').tag_id;
        $(".modal-body #nfc_tag_id_label").text(": '"+tag_id+"'");
        $(".modal-body #tag_id").val(tag_id);
    // }
    $(".modal-body #nfc_tag_id_label_error").html("");
    if($(this).data('id').tts_enabled==1) {
        $("#edit_tts_enabled_yes").prop("checked", true);
        $(".modal-body #edit_written_text").val($(this).data('id').written_text);
        $(".modal-body #edit_collapseOne").addClass("show");
        $(".modal-body #edit_collapseTwo").removeClass("show");
    }
    else {
      $("#edit_tts_enabled_no").prop("checked", true);
      $(".modal-body #edit_sound_file_label").html('A <a download="'+$(this).data('id').name+'.mp3" href="audio/'+$(this).data('id').item_id+'/'+$(this).data('id').content_id+'/sound.mp3">sound file</a> has already been uploaded. Replace it: ');
      $(".modal-body #edit_collapseTwo").addClass("show");
      $(".modal-body #edit_collapseOne").removeClass("show");
    }
    
    $(".edit_gesture_options select").val($(this).data('id').gesture_id);


    var db = JSON.parse(localStorage.getItem('db')); 
    $.each(db.data[0].content, function (key, c) {
      $('#edit_next_content').append('<option value="'+c.content_id+'">'+c.name+'</option>'); 
    });


    $(".edit_active_options select").val($(this).data('id').active);
  });

  /**
  * 
  * Edit Content 
  *    Submission of the data in the Edit content Modal Box
  */
  var item_id;
  //Collect the form data and 'submit' the form via AJAX
  $('#edit_content_form').submit(function(event){
    event.preventDefault(); //cancels the form submission
    $('#editContentModalCenter').modal('toggle'); //closes the modal box
    // var roles = [];
    // var direct_to_url = "ajax.content_actions.php?action=edit_content&content_id="+content_id+"&";
    // direct_to_url += $('#edit_content_form').serialize(); //grab all input boxes

    var data = new FormData();

    //Form data
    var form_data = $('#edit_content_form').serializeArray();
    $.each(form_data, function (key, input) {
        data.append(input.name, input.value);
    });
    
    //File data
    var file_data = $('input[name="edit_sound_file"]')[0].files;
    for (var i = 0; i < file_data.length; i++) {
        data.append("sound_file[]", file_data[i]);
    }
    
    //Custom data
    data.append('key', 'value');


    //send the data as a GET request to the PHP page specified in direct_to_url
    $.when(save_to_database()).done(function(a1){ //when the ajax request is complete
      item_table.ajax.reload(); //reload the table with the new data
      set_localstorage();
    });
    function save_to_database(){ //call the ajax for saving the changes
      return $.ajax({
        url: 'ajax.content_actions.php?action=edit_content',
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
  * Delete Content
  * 
  */
  var name;
  $(document).on("click", ".deleteContentModalBox", function () {//onclick of the Delete icon/button
    //grab the data provided via JSON on the Delete icon/button
    name = "'" + $(this).data('id').name + "'";
    content_id = $(this).data('id').content_id;
    $(".modal-body #span_name").text(name);
  });

  $("#btn_content_delete").click(function(){ //on click of the confirmation delete button (AKA submit the form)
    //send the data as a GET request to the PHP page specified in direct_to_url
    $.when(save_to_database()).done(function(a1){ //when the ajax request is complete
      item_table.ajax.reload(); //reload the table with the new data
      set_localstorage();
    });
    function save_to_database(){ //call the ajax for saving the changes
      return $.ajax({url: "ajax.content_actions.php?action=delete_content&content_id="+content_id, success: function(result){
          $("#div1").html(result);
      }});
    }
  });


  /**
  * 
  * Scan NFC Tag -> Request to scan tag
  * 
  */

 $("#btn_newNFCTag").click(function(){ //on click of the confirmation delete button (AKA submit the form)
  //send the data as a GET request to the PHP page specified in direct_to_url
  $.ajax({url: "ajax.content_actions.php?action=scan_nfc_tag&content_id="+content_id, success: function(result){
       $(".modal-body #NFCTagModal_bodytext").text("Please scan the NFC tag on the server device.");
       $(".modal-content #NFCTagModalFooter").removeClass("d-none");
    }});
});

/**
* 
* Scan NFC Tag -> Scanning of tag confirmed by user
* 
*/
$("#btn_confirm_tag_scanned").click(function(){ //on click of the confirmation delete button (AKA submit the form)
 //send the data as a GET request to the PHP page specified in direct_to_url
 console.log('Line 547');
 $.ajax({
   url: "ajax.content_actions.php?action=get_nfc_id&content_id="+content_id,
   success: function(result){
      var tag_id = result.tag_id;
      console.log("tag_id type: "+ typeof(tag_id));
      console.log("tag_id : "+ tag_id);
      console.log("result type: "+ typeof(result));
      console.log("result : "+ result);
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
       console.log(msg);
   }
  });
});
  
});