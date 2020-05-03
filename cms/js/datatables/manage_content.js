var item_table;
var content_table;
$(document).ready(function() {

    //store all the ITems in the local storage
    function set_localstorage() {
      $.ajax({url: "ajax.get_table_data.php?page=item", success: function(result){
        localStorage.removeItem("db");
        localStorage.setItem("db",JSON.stringify(result));
      }});
    }
  
    set_localstorage();
  
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
    item_table = $('#manage_items_data_table').DataTable( {
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
        to_display += '<p>This text, below, was converted to speech. <a download="'+d.name+'.wav" href="audio/'+d.item_id+'/'+d.content_id+'/sound.wav">Download a copy</a><br />'+d.written_text+'</p>';
      }
      else {
        to_display += '<p><a download="'+d.name+'.wav" href="audio/'+d.item_id+'/'+d.content_id+'/sound.wav">'+d.name+'</a>  was uploaded to play when the tag is scanned.</p>';
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
            content_table = $('#'+child_table_name).DataTable({
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
    });
});