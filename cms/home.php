<?php

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

            <!-- Visitors (Last 30 days) Card -->
            <div class="col-xl-3 col-md-6 mb-3">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Visitors (Last 30 days)</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">39</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Visitors (Last 12 Months) Card -->
            <div class="col-xl-3 col-md-6 mb-3">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Visitors (Last 12 Months)</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">256</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <!-- Interactions (Last 30 Days) Card -->
            <div class="col-xl-3 col-md-6 mb-3">
              <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Interactions (Last 30 Days)</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">65</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-hdd fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Interactions (Last 12 months) Card -->
            <div class="col-xl-3 col-md-6 mb-3">
              <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Interactions (Last 12 months)</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">256</div>
                    </div>
                    <div class="col-auto">
                      <i class="far fa-hdd fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>


          <div class="row">

            <div class="col-lg-6">

              <!-- Default Card Example -->
              <div class="card mb-4">
                <div class="card-header text-primary font-weight-bold">
                  Welcome
                </div>
                <div class="card-body">
                This card uses Bootstrap's default styling with no utility classes added. Global styles are the only things modifying the look and feel of this default card example.
                This card uses Bootstrap's default styling with no utility classes added. Global styles are the only things modifying the look and feel of this default card example.
                </div>
              </div>

              <!-- Default Card Example -->
              <div class="card mb-4">
                <div class="card-header text-primary font-weight-bold">
                  Welcome
                </div>
                <div class="card-body">
                  This card uses Bootstrap's default styling with no utility classes added. Global styles are the only things modifying the look and feel of this default card example.
                </div>
              </div>

            </div>

            <div class="col-lg-6">

              <!-- Default Card Example -->
              <div class="card mb-4">
                <div class="card-header text-primary font-weight-bold">
                  Default Card Example
                </div>
                <div class="card-body">
                  This card uses Bootstrap's default styling with no utility classes added. Global styles are the only things modifying the look and feel of this default card example.
                </div>
              </div>

              <!-- Default Card Example -->
              <div class="card mb-4">
                <div class="card-header text-primary font-weight-bold">
                  Default Card Example
                </div>
                <div class="card-body">
                  This card uses Bootstrap's default styling with no utility classes added. Global styles are the only things modifying the look and feel of this default card example.
                </div>
              </div>

            </div>

          </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->