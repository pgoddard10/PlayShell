<?php
/**
 * The GUI for managing the cultural centre's digital content
 * The PHP file is included within the index.php page - access the GUI by loading index.php
 *
 * @author Paul Goddard, paul2.goddard@live.uwe.ac.uk
 * @date Spring 2020 
 * 
 */

require_once('config.php');
require_once('classes/views/Content_View.php');
require_once('classes/views/Item_View.php');
require_once('classes/views/Authenticate_View.php');
$content_view = new Content_View();
$item_view = new Item_View();
$authenticate_view = new Authenticate_View();
$authenticate_view->has_session();
$authenticate_view->page_permissions(CONTENT_MANAGER);

?>
<!-- Custom styles for this page -->
<style>
/* To allow Modal boxes to stack - used when adding NFC tags */
.modal:nth-of-type(even) {
    z-index: 1052 !important;
}
.modal-backdrop.show:nth-of-type(even) {
    z-index: 1051 !important;
}
</style>
      <!-- Begin Page Content -->
      <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h2 mb-0 text-gray-800">
              <i class="fas fa-fw fa-file-audio"></i>
              Manage Content
            </h1>
          <a href="#" data-toggle="modal" data-target="#addNewItemModal" class="btn btn-primary btn-icon-split"><span class="icon text-white-50"><i class="fas fa-plus-circle"></i></span><span class="text">Add New</span></a>
        </div>
        <p class="mb-4">
          Add, edit and remove all digital content.<br />
          <strong>Note:</strong> All names, locations and text content is used in emails to visitors who have interacted with that content.
        </p>

        <div id="div1"></div>
        <!-- DataTable of Entire Staff -->
        <div class="card shadow mb-4">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" id="manage_items_data_table" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th></th>
                    <th>Name & URL <sup><a href="#" data-toggle="tooltip" title="This item's name, which links to the 'see more' web address to be sent to visitors' email address"><i class="fas fa-fw fa-question-circle"></i></a></sup>
                    <th>Your ID <sup><a href="#" data-toggle="tooltip" title="The unique ID you might be using in other systems"><i class="fas fa-fw fa-question-circle"></i></a></sup></th>
                    <th>Location <sup><a href="#" data-toggle="tooltip" title="Where is this item displayed/stored?"><i class="fas fa-fw fa-question-circle"></i></a></sup></th>
                    <th>Active?</th>
                    <th></th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
          </div>
        </div>

      </div>
      <!-- /.container-fluid -->

      <?php
        $item_view->delete_item_modal();
        $item_view->new_item_modal();
        $item_view->edit_item_modal();

        $content_view->delete_content_modal();
        $content_view->new_content_modal();
        $content_view->edit_content_modal();
        $content_view->show_NFC_tag_modal();
      ?>

