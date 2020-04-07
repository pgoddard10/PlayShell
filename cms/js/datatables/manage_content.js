//Call the dataTables jQuery plugin

function format ( d ) {
    // `d` is the original data object for the row
    var sub_table = "";
    sub_table += "<div><strong>Last Modified:</strong> " + d.last_modified + " & <strong>Created on:</strong> " + d.created + ".</div><br />";
    sub_table += "<div><h1 class='h5 mb-0'>'"+ d.name_without_url +"' contains the following tags/content:</h1></div>";
    sub_table += '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<thead>' +
            '<tr>' +
                '<th>Name</th>' +
                '<th>location</th>' +
                '<th>last_modified</th>' +
                '<th>ext</th>' +
                '<th>ssss sssssssss sssss</th>' +
            '</tr>' +
        '</thead>'+
            '<tbody>'+
        '<tr>';
    $.each(d.content_array, function(key, value){
        sub_table +='<tr>';
        $.each(value, function(key2, value2){
            sub_table +='<td>' + value2 + '</td>';
        });
        sub_table +='</tr>';
    });
    sub_table += '</tbody></tr>'+
    '</table>';
    return sub_table;
}


$(document).ready(function() {

    //Visitor Management Table
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
                "data": "buttons",
                "width": "100px"
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
    // Add event listener for opening and closing details
    $('#manage_items_data_table tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var tdi = tr.find("i.fa");
        var row = item_table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
            tdi.first().removeClass('fa-minus-square');
            tdi.first().addClass('fa-plus-square');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
            tdi.first().removeClass('fa-plus-square');
            tdi.first().addClass('fa-minus-square');
        }
    } );
  
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