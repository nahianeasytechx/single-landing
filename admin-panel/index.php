<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Dashboard';
require './components/header.php';
?>
<style>
  /* Modern Stats Card Styles */
  .stats-card {
    position: relative;
    padding: 24px;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    overflow: visible;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    border: 1px solid rgba(0, 0, 0, 0.05);
    min-height: 140px;
    display: flex;
    flex-direction: column;
  }

  .stats-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    border-radius: 0 0 20px 20px;
  }

  .stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 20px 20px 0 0;
  }

  .stats-card:hover::before {
    opacity: 1;
  }

  .stats-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
  }

  .stats-card:hover .stats-icon {
    transform: rotate(10deg) scale(1.1);
  }

  .stats-icon i {
    font-size: 28px;
    color: #fff;
  }

  .stats-content {
    position: relative;
    z-index: 1;
    padding-right: 76px;
  }

  .stats-label {
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 12px;
  }

  .stats-value {
    font-size: 36px;
    font-weight: 600;
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0;
    line-height: 1.2;
    margin-bottom: 16px;
  }

  .stats-trend {
    margin-top: auto;
  }

  .trend-icon {
    font-size: 22px;
    font-weight: bold;
    animation: bounce 2s infinite;
  }

  @keyframes bounce {

    0%,
    100% {
      transform: translateY(0);
    }

    50% {
      transform: translateY(-5px);
    }
  }

  .stats-badge {
    margin-top: auto;
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    display: inline-block;
    width: fit-content;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }

  /* Gradient Variants */
  .stats-gradient-danger {
    --gradient-start: #ef4444;
    --gradient-end: #dc2626;
  }

  .stats-gradient-info {
    --gradient-start: #3b82f6;
    --gradient-end: #2563eb;
  }

  .stats-gradient-success {
    --gradient-start: #10b981;
    --gradient-end: #059669;
  }

  .stats-gradient-primary {
    --gradient-start: #8b5cf6;
    --gradient-end: #7c3aed;
  }

  .stats-gradient-warning {
    --gradient-start: #f59e0b;
    --gradient-end: #d97706;
  }

  .stats-gradient-purple {
    --gradient-start: #a855f7;
    --gradient-end: #9333ea;
  }

  .stats-gradient-dark {
    --gradient-start: #64748b;
    --gradient-end: #475569;
  }

  /* Badge variants */
  .badge-warning {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
  }

  .badge-info {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
  }

  .badge-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
  }

  .badge-purple {
    background: linear-gradient(135deg, #e9d5ff, #d8b4fe);
    color: #6b21a8;
  }

  .badge-dark {
    background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
    color: #334155;
  }
.card{
  box-shadow: var(--shadow-sm);
  border: none;
  outline: none;
  margin-bottom: 30px;
}
  /* Responsive */
  @media (max-width: 1199px) {
    .stats-value {
      font-size: 28px;
    }

    .stats-icon {
      width: 50px;
      height: 50px;
    }

    .stats-icon i {
      font-size: 24px;
    }
  }

  @media (max-width: 767px) {
    .stats-card {
      padding: 20px;
    }

    .stats-value {
      font-size: 24px;
    }

    .stats-icon {
      width: 46px;
      height: 46px;
      top: 16px;
      right: 16px;
    }

    .stats-icon i {
      font-size: 20px;
    }
  }
</style>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
  <div class="dashboard">

    <!-- Page Title -->
    <div class="page-title-section">
      <div class="icon-box">
        <i class="fa-solid fa-chart-line"></i>
      </div>
      <h1>Dashboard</h1>
    </div>

    <!-- Dashboard Stats Area -->
    <div class="row g-4">

      <!-- Total Applications -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-danger">
          <div class="stats-icon">
            <i class="fa-solid fa-graduation-cap"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Total Applications</h6>
            <h2 class="stats-value">156</h2>
          </div>
          <div class="stats-trend">
            <span class="trend-icon">↗</span>
          </div>
        </div>
      </div>

      <!-- Scholarship Categories -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-info">
          <div class="stats-icon">
            <i class="fa-solid fa-list"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Scholarship Categories</h6>
            <h2 class="stats-value">5</h2>
          </div>
          <div class="stats-trend">
            <span class="trend-icon">→</span>
          </div>
        </div>
      </div>

      <!-- Total Awarded -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-success">
          <div class="stats-icon">
            <i class="fa-solid fa-award"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Total Awarded</h6>
            <h2 class="stats-value">89</h2>
          </div>
          <div class="stats-trend">
            <span class="trend-icon">↗</span>
          </div>
        </div>
      </div>

      <!-- Total Students -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-primary">
          <div class="stats-icon">
            <i class="fa-solid fa-users"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Total Students</h6>
            <h2 class="stats-value">342</h2>
          </div>
          <div class="stats-trend">
            <span class="trend-icon">↗</span>
          </div>
        </div>
      </div>

      <!-- Total Donations -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-primary">
          <div class="stats-icon">
            <i class="fa-solid fa-hand-holding-dollar"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Total Donations</h6>
            <h2 class="stats-value">245</h2>
          </div>
          <div class="stats-trend">
            <span class="trend-icon">↗</span>
          </div>
        </div>
      </div>

      <!-- Donation Amount -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-success">
          <div class="stats-icon">
            <i class="fa-solid fa-bangladeshi-taka-sign"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Donation Amount</h6>
            <h2 class="stats-value">৳ 5,830</h2>
          </div>
          <div class="stats-trend">
            <span class="trend-icon">↗</span>
          </div>
        </div>
      </div>

      <!-- Pending Applications -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-warning">
          <div class="stats-icon">
            <i class="fa-solid fa-clock"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Pending Applications</h6>
            <h2 class="stats-value">23</h2>
          </div>
          <div class="stats-badge badge-warning">Action Required</div>
        </div>
      </div>

      <!-- Processed Applications -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-info">
          <div class="stats-icon">
            <i class="fa-solid fa-spinner"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Processed Applications</h6>
            <h2 class="stats-value">98</h2>
          </div>
          <div class="stats-badge badge-info">In Progress</div>
        </div>
      </div>

      <!-- Approved Scholarships -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-success">
          <div class="stats-icon">
            <i class="fa-solid fa-check-circle"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Approved Scholarships</h6>
            <h2 class="stats-value">67</h2>
          </div>
          <div class="stats-badge badge-success">Completed</div>
        </div>
      </div>

      <!-- Rejected Applications -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-dark">
          <div class="stats-icon">
            <i class="fa-solid fa-times-circle"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Rejected Applications</h6>
            <h2 class="stats-value">12</h2>
          </div>
          <div class="stats-badge badge-dark">Cancelled</div>
        </div>
      </div>

      <!-- Active Notices -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-purple">
          <div class="stats-icon">
            <i class="fa-solid fa-bullhorn"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Active Notices</h6>
            <h2 class="stats-value">8</h2>
          </div>
          <div class="stats-badge badge-purple">Shipping</div>
        </div>
      </div>

      <!-- Upcoming Activities -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-success">
          <div class="stats-icon">
            <i class="fa-solid fa-calendar-days"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Upcoming Activities</h6>
            <h2 class="stats-value">15</h2>
          </div>
          <div class="stats-trend">
            <span class="trend-icon">↗</span>
          </div>
        </div>
      </div>

    </div>

    <!-- Quick Actions -->
    <h2 class="quick-actions-title mt-5">Quick Actions</h2>
    <div class="quick-actions-container">

      <!-- New Application -->
      <button class="quick-action-card" onclick="window.location.href='add-activity.php'">
        <div class="quick-action-icon new-notice">
          <i class="fa-solid fa-plus"></i>
        </div>
        <p class="quick-action-title">New Activity</p>
      </button>

      <!-- View Applications -->
      <button class="quick-action-card" onclick="window.location.href='scholarship-application-list.php'">
        <div class="quick-action-icon upload-media">
          <i class="fa-solid fa-list-check"></i>
        </div>
        <p class="quick-action-title">View Applications</p>
      </button>

      <!-- Add Notice -->
      <button class="quick-action-card" onclick="window.location.href='add-notice.php'">
        <div class="quick-action-icon add-activity">
          <i class="fa-solid fa-bullhorn"></i>
        </div>
        <p class="quick-action-title">Add Notice</p>
      </button>

      <!-- Upload Media -->
      <button class="quick-action-card" onclick="window.location.href='gallery.php'">
        <div class="quick-action-icon edit-about">
          <i class="fa-solid fa-images"></i>
        </div>
        <p class="quick-action-title">Upload Media</p>
      </button>

    </div>


    <!-- <div class="row grid-margin stretch-card">
        <div class="table-responsive w-100">
          <table class="table table-bordered">
            <thead>
              <tr class="text-dark">
                <th><b>SL</b></th>
                <th><b>Name</b></th>
                <th><b>Phone No.</b></th>
                <th><b>Category</b></th>
                <th><b>Colletion Ammount</b></th>
                <th><b>Transaction Id</b></th>
                <th><b>Date</b></th>

              </tr>
            </thead>

            <tbody id="donationTableBody">
              <tr>
                <td>15</td>
                <td>INV-68DQXNMYZ</td>
                <td>2,290.00 Tk.</td>
                <td>Cash On Delivery</td>
                <td>2025-09-23 16:08:14</td>

                <td>
                  <a href="order_details.php?invoice_no=INV-68DQXNMYZ">
                    <button class="btn btn-info">View Details <span class="mdi mdi-details"></span></button>
                  </a>
                </td>

              </tr>

              <tr>
                <td>14</td>
                <td>INV-68DQXNMYZ</td>
                <td>1,250.00 Tk.</td>
                <td>Cash On Delivery</td>
                <td>2025-09-23 16:08:14</td>

                <td>
                  <a href="order_details.php?invoice_no=INV-68DQXNMYZ">
                    <button class="btn btn-info">View Details <span class="mdi mdi-details"></span></button>
                  </a>
                </td>

              </tr>

              <tr>
                <td>16</td>
                <td>INV-68DQXNMYZ</td>
                <td>2,290.00 Tk.</td>
                <td>Cash On Delivery</td>
                <td>2025-09-23 16:08:14</td>

                <td>
                  <a href="order_details.php?invoice_no=INV-68DQXNMYZ">
                    <button class="btn btn-info">View Details <span class="mdi mdi-details"></span></button>
                  </a>
                </td>

              </tr>

            </tbody>
            <tfoot>
              <tr>
                <td></td>


                <td></td>
                <td></td>
                <td><b>Total Collections</b></td>
                <td><b>5,830.00 Tk.</b></td>
                <td></td>

              </tr>
            </tfoot>
          </table>
        </div>
      </div> -->

    <div class="card p-3">
      <div class="card-body">
        <h1 class="chart-title mb-1">Recent donations</h1>
        <p>List of latest recent donations</p><br>
        <div class="table-responsive">
          <table class="table table-bordered">
            <tbody>
              <tr>
                <th>SL</th>
                <th>Name</th>
                <th>Customer Phone</th>
                <th>Invoice No</th>
                <th>Ammount</th>
                <th>Date</th>
                <th>Tansaction ID</th>
                <th colspan="2">Action</th>
              </tr>
            </tbody>
            <tbody id="donationTableBody">
            </tbody>
          </table>
        </div>
      </div>
      <a href="donation-list.php" class="p-3">
        <button class="btn btn-success py-2 px-4 rounded-0">See All Donations</button>
      </a>
    </div>

  </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->
<!-- Add these scripts before footer -->
<script src="js/donationData.js"></script>
<script src="js/dashboardDonations.js"></script>


<?php require './components/footer.php'; ?>