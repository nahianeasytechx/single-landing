<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Dashboard';
require './components/header.php';

// Protect page - redirect to login if not authenticated
protectPage();

$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Dashboard';

// Get comprehensive statistics
$conn = getDatabaseConnection();
$stats = getEnhancedOrderStatistics($conn);

// Get recent orders for the table
$recent_orders = getRecentOrders(5);

// Get daily analytics for trends
$daily_analytics = getDailyAnalytics(7); // Last 7 days

// Calculate trends (comparing with previous period)
$today_orders = $stats['today_orders'];
$yesterday_query = "SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
$yesterday_result = $conn->query($yesterday_query);
$yesterday_orders = $yesterday_result ? $yesterday_result->fetch_assoc()['count'] : 0;

$this_month_orders = $stats['month_orders'];
$last_month_query = "SELECT COUNT(*) as count FROM orders WHERE YEAR(created_at) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(created_at) = MONTH(CURDATE() - INTERVAL 1 MONTH)";
$last_month_result = $conn->query($last_month_query);
$last_month_orders = $last_month_result ? $last_month_result->fetch_assoc()['count'] : 0;

// Get product statistics
$product_stats_query = "SELECT COUNT(*) as total_products, 
                        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_products,
                        SUM(CASE WHEN stock_quantity < 10 THEN 1 ELSE 0 END) as low_stock_products
                        FROM products";
$product_stats_result = $conn->query($product_stats_query);
$product_stats = $product_stats_result ? $product_stats_result->fetch_assoc() : ['total_products' => 0, 'active_products' => 0, 'low_stock_products' => 0];

$conn->close();


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
    0%, 100% {
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

  .card {
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

      <!-- Total Revenue -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-success">
          <div class="stats-icon">
            <i class="fa-solid fa-bangladeshi-taka-sign"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Total Revenue</h6>
            <h2 class="stats-value">৳ <?php echo number_format($stats['total_revenue'], 2); ?></h2>
          </div>
          <div class="stats-trend">
            <span class="trend-icon">↗</span>
          </div>
        </div>
      </div>

      <!-- Total Orders -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-danger">
          <div class="stats-icon">
            <i class="fa-solid fa-shopping-cart"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Total Orders</h6>
            <h2 class="stats-value"><?php echo $stats['total_orders']; ?></h2>
          </div>
          <div class="stats-trend">
            <span class="trend-icon">↗</span>
          </div>
        </div>
      </div>

      <!-- Today's Revenue -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-info">
          <div class="stats-icon">
            <i class="fa-solid fa-calendar-day"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Today's Revenue</h6>
            <h2 class="stats-value">৳ <?php echo number_format($stats['today_revenue'], 2); ?></h2>
          </div>
          <div class="stats-trend">
            <span class="trend-icon"><?php echo $today_orders > $yesterday_orders ? '↗' : ($today_orders < $yesterday_orders ? '↘' : '→'); ?></span>
          </div>
        </div>
      </div>

      <!-- Total Products -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-primary">
          <div class="stats-icon">
            <i class="fa-solid fa-box"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Total Products</h6>
            <h2 class="stats-value"><?php echo $product_stats['total_products']; ?></h2>
          </div>
          <div class="stats-trend">
            <span class="trend-icon">→</span>
          </div>
        </div>
      </div>

      <!-- Pending Orders -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-warning">
          <div class="stats-icon">
            <i class="fa-solid fa-clock"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Pending Orders</h6>
            <h2 class="stats-value"><?php echo $stats['pending_orders']; ?></h2>
          </div>
          <div class="stats-badge badge-warning">Action Required</div>
        </div>
      </div>

      <!-- Processing Orders -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-info">
          <div class="stats-icon">
            <i class="fa-solid fa-spinner"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Processing Orders</h6>
            <h2 class="stats-value"><?php echo $stats['processing_orders']; ?></h2>
          </div>
          <div class="stats-badge badge-info">In Progress</div>
        </div>
      </div>

      <!-- Delivered Orders -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-success">
          <div class="stats-icon">
            <i class="fa-solid fa-check-circle"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Delivered Orders</h6>
            <h2 class="stats-value"><?php echo $stats['delivered_orders']; ?></h2>
          </div>
          <div class="stats-badge badge-success">Completed</div>
        </div>
      </div>

      <!-- Cancelled Orders -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-dark">
          <div class="stats-icon">
            <i class="fa-solid fa-times-circle"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Cancelled Orders</h6>
            <h2 class="stats-value"><?php echo $stats['cancelled_orders']; ?></h2>
          </div>
          <div class="stats-badge badge-dark">Cancelled</div>
        </div>
      </div>

      <!-- Average Order Value -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-primary">
          <div class="stats-icon">
            <i class="fa-solid fa-chart-bar"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Avg Order Value</h6>
            <h2 class="stats-value">৳ <?php echo number_format($stats['average_order_value'], 2); ?></h2>
          </div>
          <div class="stats-trend">
            <span class="trend-icon">→</span>
          </div>
        </div>
      </div>

      <!-- Confirmed Orders -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-info">
          <div class="stats-icon">
            <i class="fa-solid fa-check"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Confirmed Orders</h6>
            <h2 class="stats-value"><?php echo $stats['confirmed_orders']; ?></h2>
          </div>
          <div class="stats-trend">
            <span class="trend-icon">↗</span>
          </div>
        </div>
      </div>

      <!-- Shipped Orders -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-purple">
          <div class="stats-icon">
            <i class="fa-solid fa-truck"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Shipped Orders</h6>
            <h2 class="stats-value"><?php echo $stats['shipped_orders']; ?></h2>
          </div>
          <div class="stats-badge badge-purple">Shipping</div>
        </div>
      </div>

      <!-- Active Products -->
      <div class="col-xl-3 col-md-6">
        <div class="stats-card stats-gradient-success">
          <div class="stats-icon">
            <i class="fa-solid fa-store"></i>
          </div>
          <div class="stats-content">
            <h6 class="stats-label">Active Products</h6>
            <h2 class="stats-value"><?php echo $product_stats['active_products']; ?></h2>
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

      <!-- New Order -->
      <button class="quick-action-card" onclick="window.location.href='manage-orders.php'">
        <div class="quick-action-icon new-notice">
          <i class="fa-solid fa-plus"></i>
        </div>
        <p class="quick-action-title">View Orders</p>
      </button>

      <!-- Add Product -->
      <button class="quick-action-card" onclick="window.location.href='products.php'">
        <div class="quick-action-icon upload-media">
          <i class="fa-solid fa-box-open"></i>
        </div>
        <p class="quick-action-title">Manage Products</p>
      </button>

      <!-- Landing Pages -->
      <button class="quick-action-card" onclick="window.location.href='manage-landing-templates.php'">
        <div class="quick-action-icon add-activity">
          <i class="fa-solid fa-pager"></i>
        </div>
        <p class="quick-action-title">Landing Pages</p>
      </button>

      <!-- Product Sets -->
      <button class="quick-action-card" onclick="window.location.href='manage-product-sets.php'">
        <div class="quick-action-icon edit-about">
          <i class="fa-solid fa-layer-group"></i>
        </div>
        <p class="quick-action-title">Product Sets</p>
      </button>

    </div>

    <!-- Recent Orders Table -->
    <div class="card p-3 mt-4">
      <div class="card-body">
        <h1 class="chart-title mb-1">Recent Orders</h1>
        <p>List of latest recent orders</p><br>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>SL</th>
                <th>Order Number</th>
                <th>Customer Name</th>
                <th>Phone</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($recent_orders)): ?>
                <tr>
                  <td colspan="9" class="text-center">No orders found</td>
                </tr>
              <?php else: ?>
                <?php foreach ($recent_orders as $index => $order): ?>
                  <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                    <td>৳ <?php echo number_format($order['total_amount'], 2); ?></td>
                    <td>
                      <?php
                      $status_badges = [
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'processing' => 'primary',
                        'shipped' => 'purple',
                        'delivered' => 'success',
                        'cancelled' => 'danger'
                      ];
                      $badge_class = $status_badges[$order['order_status']] ?? 'secondary';
                      ?>
                      <span class="badge bg-<?php echo $badge_class; ?>">
                        <?php echo ucfirst($order['order_status']); ?>
                      </span>
                    </td>
                    <td>
                      <?php
                      $payment_badges = [
                        'pending' => 'warning',
                        'partial' => 'info',
                        'paid' => 'success',
                        'refunded' => 'secondary',
                        'failed' => 'danger'
                      ];
                      $payment_badge = $payment_badges[$order['payment_status']] ?? 'secondary';
                      ?>
                      <span class="badge bg-<?php echo $payment_badge; ?>">
                        <?php echo ucfirst($order['payment_status']); ?>
                      </span>
                    </td>
                    <td><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></td>

                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <a href="manage-orders.php" class="p-3">
        <button class="btn btn-success py-2 px-4 rounded-0">See All Orders</button>
      </a>
    </div>

  </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require './components/footer.php'; ?>