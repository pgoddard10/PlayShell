// Call the dataTables jQuery plugin
// $(document).ready(function() {
//   $('#dataTable').DataTable();
// });

$(document).ready(function() {
  $('#manage_staff_data_table').DataTable( {
      "order": [[ 4, "desc" ],[ 0, "desc" ]]
  } );
} );