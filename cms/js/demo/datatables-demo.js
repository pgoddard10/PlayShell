//Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable();
});

$(document).ready(function() {
  var staff_table = $('#manage_staff_data_table').DataTable( {
    "order": [[ 4, "desc" ],[ 0, "desc" ]],
    "ajax": {
      url: "ajax.staff_table_data.php?action=display_table",
      "contentType": "application/json"
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

  //Fill in the form fields on the Edit Modal Box with the appropriate data passed by clicked in the hyperlink
  //data is passed in the form of a JSON string.
  $(document).on("click", ".editModalBox", function () {
    staff_id = $(this).data('id').staff_id;
    $(".modal-body #edit_first_name").val($(this).data('id').first_name);
    $(".modal-body #edit_last_name").val($(this).data('id').last_name);
    $(".modal-body #edit_email").val($(this).data('id').email);
    $(".edit_active_options select").val($(this).data('id').active);
    var i;
    for (i = 1; i <= 5; i++) {
            $("#ckbox_edit_role_"+i).prop("checked", false);
    }

    //tick the checkboxes that match the roles this staff member has
    var roles = $(this).data('id').roles;
    $.each(roles, function(index, value) {
            $("#ckbox_edit_role_"+value.role_id).prop("checked", true);
        });
  });

  var staff_id;
  //Collect the form data and 'submit' the form via AJAX
  $("#btn_staff_edit").click(function(){
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
      $.when(save_to_database()).done(function(a1){
        staff_table.ajax.reload();
      });
      function save_to_database(){
        return $.ajax({url: direct_to_url, success: function(result){
            $("#div1").html(result);
        }});
      }
  });


  var display_name;
  $(document).on("click", ".deactivateModalBox", function () {
    display_name = $(this).data('id').display_name;
    staff_id = $(this).data('id').staff_id;
    $(".modal-body #span_name").text(display_name);
  });

  $("#btn_staff_deactivate").click(function(){
      //send the data as a GET request to the PHP page specified in direct_to_url
      $.when(save_to_database()).done(function(a1){
        staff_table.ajax.reload();
      });
      function save_to_database(){
        return $.ajax({url: "ajax.staff_actions.php?action=deactivate&staff_id="+staff_id, success: function(result){
            $("#div1").html(result);
        }});
      }
  });


} );