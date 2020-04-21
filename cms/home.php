<?php
/**
 * Default landing page once logged in
 * General info to be displayed on this page
 *
 * @author Paul Goddard, paul2.goddard@live.uwe.ac.uk
 * @date Spring 2020 
 * 
 */

require_once('config.php');
require_once('classes/views/Staff_View.php');
require_once('classes/views/Authenticate_View.php');
$staff_view = new Staff_View();
$authenticate_view = new Authenticate_View();
$authenticate_view->has_session();

?>


        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h2 mb-0 text-gray-800">Home</h1>
        </div>

          <div class="row">

            <div class="col-lg-6">

              <!-- Default Card Example -->
              <div class="card mb-4">
                <div class="card-header text-primary font-weight-bold">
                  Welcome
                </div>
                <div class="card-body">
                Use the menu on the left to navigate this system.
                </div>
              </div>

            </div>

          </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->