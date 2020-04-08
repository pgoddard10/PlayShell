//Call the dataTables jQuery plugin

$(document).ready(function() {

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
      to_display += d.written_text;
    }
    else {
      to_display += "soundfile location";
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
          to_display += "<div class='border'><p class='text-lg'>'"+ d.name_without_url +"' contains the following tags/content:<p>";
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
          row.child().addClass( 'bg-info' ); //add a class to change the bg colour

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
                { "data": "tag_id" },
                { "data": "active" },
                { "data": "gesture" },
                { "data": "next_content" },
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
              row.child().addClass( 'bg-gradient-success' );//add a class to change the bg colour
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
        });
        function save_to_database(){ //call the ajax for saving the changes
        return $.ajax({url: direct_to_url, success: function(result){
            console.log("successfully created");
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

//grab the JSON data provided on the Edit icon/button and fill in the form input boxes
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
  var roles = [];
  var direct_to_url = "ajax.content_actions.php?action=edit_item&item_id="+item_id+"&";
  direct_to_url += $('#edit_form').serialize(); //grab all input boxes

  //send the data as a GET request to the PHP page specified in direct_to_url
  $.when(save_to_database()).done(function(a1){ //when the ajax request is complete
    item_table.ajax.reload(); //reload the table with the new data
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
  });
  function save_to_database(){ //call the ajax for saving the changes
    return $.ajax({url: "ajax.content_actions.php?action=delete_item&item_id="+item_id, success: function(result){
        console.log("successfully deleted");
        $("#div1").html(result);
    }});
  }
});

  
} );