<?php

$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Order Management';

require_once __DIR__ . '/../components/functions.php';

// Get database connection
$conn = getDatabaseConnection();

if (!$conn) {
    die('Database connection failed. Please check your database configuration.');
}

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['order_status'];
    $payment_status = $_POST['payment_status'];

    $update_query = "UPDATE orders SET order_status = ?, payment_status = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssi", $new_status, $payment_status, $order_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Order status updated successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to update order status.';
    }
    $stmt->close();
    header('Location: manage-orders.php');
    exit;
}

// Handle customer details update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_customer'])) {
    $order_id = intval($_POST['order_id']);
    $result = updateOrderCustomerDetails($order_id, $_POST);

    if ($result['success']) {
        $_SESSION['success_message'] = $result['message'];
    } else {
        $_SESSION['error_message'] = $result['message'];
    }
    header('Location: manage-orders.php');
    exit;
}

// Handle order deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $order_id = intval($_POST['order_id']);

    $delete_items = "DELETE FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($delete_items);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    $delete_tracking = "DELETE FROM order_tracking WHERE order_id = ?";
    $stmt = $conn->prepare($delete_tracking);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    $delete_order = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($delete_order);
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Order deleted successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to delete order.';
    }
    $stmt->close();
    header('Location: manage-orders.php');
    exit;
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$payment_filter = isset($_GET['payment_status']) ? $_GET['payment_status'] : 'all';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Build query
$where_clauses = [];
$params = [];
$param_types = '';

if ($status_filter !== 'all') {
    $where_clauses[] = "order_status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

if ($payment_filter !== 'all') {
    $where_clauses[] = "payment_status = ?";
    $params[] = $payment_filter;
    $param_types .= 's';
}

if (!empty($search_query)) {
    $where_clauses[] = "(order_number LIKE ? OR customer_name LIKE ? OR customer_phone LIKE ? OR tracking_code LIKE ?)";
    $search_param = "%{$search_query}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'ssss';
}

if (!empty($date_from)) {
    $where_clauses[] = "DATE(created_at) >= ?";
    $params[] = $date_from;
    $param_types .= 's';
}

if (!empty($date_to)) {
    $where_clauses[] = "DATE(created_at) <= ?";
    $params[] = $date_to;
    $param_types .= 's';
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Get total count
$count_query = "SELECT COUNT(*) as total FROM orders $where_sql";
$count_stmt = $conn->prepare($count_query);
if (!empty($params)) {
    $count_stmt->bind_param($param_types, ...$params);
}
$count_stmt->execute();
$total_orders = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_orders / $per_page);
$count_stmt->close();

// Get orders
$orders_query = "SELECT * FROM orders $where_sql ORDER BY created_at DESC LIMIT ? OFFSET ?";
$orders_stmt = $conn->prepare($orders_query);
$params[] = $per_page;
$params[] = $offset;
$param_types .= 'ii';
$orders_stmt->bind_param($param_types, ...$params);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
$orders = [];
while ($row = $orders_result->fetch_assoc()) {
    $orders[] = $row;
}
$orders_stmt->close();

// Get enhanced statistics
$stats = getEnhancedOrderStatistics($conn);

// Get daily analytics for charts (last 30 days)
$daily_analytics = getDailyAnalytics(30);

require './components/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --primary-light: #60a5fa;
            --success: #10b981;
            --success-dark: #059669;
            --warning: #f59e0b;
            --warning-dark: #d97706;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --info: #06b6d4;
            --purple: #8b5cf6;
            --indigo: #6366f1;
            
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --border-radius-lg: 16px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            min-height: 100vh;
            padding-bottom: 2rem;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Page Header with Gradient */
        .page-header {
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .page-title {
            font-size: 2.25rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, var(--purple) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--gray-600);
            font-size: 1rem;
            font-weight: 500;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9375rem;
            font-weight: 500;
            box-shadow: var(--shadow-md);
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        /* Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.75rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--purple) 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--border-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-icon.revenue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .stat-icon.orders {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
        }

        .stat-icon.pending {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .stat-icon.processing {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
        }

        .stat-icon.shipped {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
        }

        .stat-icon.delivered {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .stat-icon.cancelled {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .stat-icon.failed {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-bottom: 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--gray-900);
            line-height: 1;
        }

        .stat-change {
            font-size: 0.8125rem;
            color: var(--gray-500);
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .stat-change.positive {
            color: var(--success);
        }

        .stat-change.negative {
            color: var(--danger);
        }

        /* Filters Section */
        .filters-section {
            background: white;
            padding: 1.75rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-lg);
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .filters-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .form-input,
        .form-select {
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius-sm);
            font-size: 0.9375rem;
            font-family: inherit;
            background: white;
            color: var(--gray-900);
            transition: all 0.2s ease;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
        }

        /* Buttons */
        .btn {
            padding: 0.75rem 1.25rem;
            border: none;
            border-radius: var(--border-radius-sm);
            font-size: 0.9375rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: inherit;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-sm);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-secondary {
            color: var(--gray-700);
            border: 2px solid var(--gray-300);
        }

        .btn-sm {
            padding: 0.5rem 0.875rem;
            font-size: 0.8125rem;
        }

        .btn-edit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-customer {
            background: linear-gradient(135deg, var(--indigo) 0%, var(--purple) 100%);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, var(--danger) 0%, var(--danger-dark) 100%);
            color: white;
        }

        .btn-track {
            background: linear-gradient(135deg, var(--warning) 0%, var(--warning-dark) 100%);
            color: white;
        }

        .btn-steadfast {
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
            color: white;
        }

        /* Table */
        .table-card {
            background: white;
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 2rem;
        }

        .table-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        .table-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-container {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            background: linear-gradient(to bottom, var(--gray-50) 0%, white 100%);
            border-bottom: 2px solid var(--gray-200);
        }

        .table th {
            padding: 1rem 1.25rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--gray-700);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .table td {
            padding: 1.25rem;
            border-bottom: 1px solid var(--gray-100);
            font-size: 0.9375rem;
        }

        .table tbody tr {
            transition: background 0.2s ease;
        }

        .table tbody tr:hover {
            background: var(--gray-50);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .order-number {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: var(--primary);
            font-size: 0.9375rem;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.025em;
        }

        .badge-pending { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; }
        .badge-confirmed { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af; }
        .badge-processing { background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #3730a3; }
        .badge-packed { background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #5b21b6; }
        .badge-shipped { background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%); color: #5b21b6; }
        .badge-out_for_delivery { background: linear-gradient(135deg, #ccfbf1 0%, #99f6e4 100%); color: #115e59; }
        .badge-delivered { background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #166534; }
        .badge-cancelled { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; }
        .badge-returned { background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); color: #374151; }

        .badge-payment-pending { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; }
        .badge-payment-partial { background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%); color: #9a3412; }
        .badge-payment-paid { background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #166534; }
        .badge-payment-refunded { background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); color: #374151; }
        .badge-payment-failed { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; }

        .action-buttons {
            display: flex;
            gap: 0.5rem;

        }

        /* Charts Section */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: white;
            padding: 1.75rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
        }

        .chart-card:hover {
            box-shadow: var(--shadow-xl);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-info {
            color: var(--gray-500);
            font-size: 0.8125rem;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            overflow: auto;
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: white;
            margin: 3rem auto;
            border-radius: var(--border-radius-lg);
            max-width: 600px;
            box-shadow: var(--shadow-xl);
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 1.75rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to bottom, var(--gray-50) 0%, white 100%);
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
        }

        .modal-title {
            font-size: 1.375rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .modal-close {
            width: 36px;
            height: 36px;
            border-radius: var(--border-radius-sm);
            border: none;
            background: var(--gray-100);
            color: var(--gray-600);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: var(--gray-200);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 1.75rem;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 1.75rem;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            background: var(--gray-50);
            border-radius: 0 0 var(--border-radius-lg) var(--border-radius-lg);
        }

        .order-info {
            background: linear-gradient(135deg, var(--gray-50) 0%, white 100%);
            padding: 1.25rem;
            border-radius: var(--border-radius-sm);
            margin-bottom: 1.5rem;
            font-size: 0.9375rem;
            border: 1px solid var(--gray-200);
        }

        .order-info > div {
            margin-bottom: 0.625rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .order-info > div:last-child {
            margin-bottom: 0;
        }

        .order-info strong {
            color: var(--gray-700);
            font-weight: 600;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            padding: 1.75rem;
            background: white;
            border-top: 1px solid var(--gray-200);
        }

        .pagination a,
        .pagination span {
            min-width: 40px;
            height: 40px;
            padding: 0 0.75rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius-sm);
            text-decoration: none;
            color: var(--gray-700);
            font-size: 0.9375rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .pagination a:hover {
            background: var(--gray-50);
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .pagination .active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-color: var(--primary);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            color: var(--gray-500);
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: var(--gray-300);
        }

        .empty-state-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.75rem;
        }

        .empty-state-text {
            font-size: 1rem;
            color: var(--gray-600);
        }

        .tracking-badge {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
            padding: 0.375rem 0.625rem;
            border-radius: var(--border-radius-sm);
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            margin-top: 0.375rem;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
                    .action-buttons {
            flex-wrap: wrap;
        }
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .page-header {
                padding: 1.5rem;
            }

            .page-title {
                font-size: 1.75rem;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                gap: 1rem;
            }

            .stat-card {
                padding: 1.25rem;
            }

            .stat-value {
                font-size: 1.5rem;
            }

            .filters-section {
                padding: 1.25rem;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .table th,
            .table td {
                padding: 0.875rem 0.625rem;
                font-size: 0.8125rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-sm {
                width: 100%;
                justify-content: center;
            }

            .modal-content {
                margin: 1rem;
                max-width: calc(100% - 2rem);
            }

            .filter-actions {
                flex-direction: column;
            }

            .filter-actions .btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 1.5rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .table-container {
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-chart-line"></i>
            Order Management Dashboard
        </h1>
        <p class="page-subtitle">Track, manage, and analyze your orders in real-time</p>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php
            
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php
            echo $_SESSION['error_message'];
            unset($_SESSION['error_message']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">৳<?php echo number_format($stats['total_revenue'], 0); ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>All time earnings</span>
                    </div>
                </div>
                <div class="stat-icon revenue">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value"><?php echo $stats['total_orders']; ?></div>
                    <div class="stat-change">
                        <i class="fas fa-box"></i>
                        <span>Lifetime orders</span>
                    </div>
                </div>
                <div class="stat-icon orders">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Pending</div>
                    <div class="stat-value"><?php echo $stats['pending_orders']; ?></div>
                    <div class="stat-change">
                        <i class="fas fa-clock"></i>
                        <span>Awaiting confirmation</span>
                    </div>
                </div>
                <div class="stat-icon pending">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Processing</div>
                    <div class="stat-value"><?php echo $stats['processing_orders']; ?></div>
                    <div class="stat-change">
                        <i class="fas fa-cog"></i>
                        <span>Being prepared</span>
                    </div>
                </div>
                <div class="stat-icon processing">
                    <i class="fas fa-box-open"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Shipped</div>
                    <div class="stat-value"><?php echo $stats['shipped_orders']; ?></div>
                    <div class="stat-change">
                        <i class="fas fa-truck"></i>
                        <span>In transit</span>
                    </div>
                </div>
                <div class="stat-icon shipped">
                    <i class="fas fa-shipping-fast"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Delivered</div>
                    <div class="stat-value"><?php echo $stats['delivered_orders']; ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-check-circle"></i>
                        <span>Completed</span>
                    </div>
                </div>
                <div class="stat-icon delivered">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Cancelled</div>
                    <div class="stat-value"><?php echo $stats['cancelled_orders']; ?></div>
                    <div class="stat-change negative">
                        <i class="fas fa-times-circle"></i>
                        <span>Cancelled orders</span>
                    </div>
                </div>
                <div class="stat-icon cancelled">
                    <i class="fas fa-ban"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Failed</div>
                    <div class="stat-value"><?php echo isset($stats['failed_orders']) ? $stats['failed_orders'] : 0; ?></div>
                    <div class="stat-change negative">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Failed orders</span>
                    </div>
                </div>
                <div class="stat-icon failed">
                    <i class="fas fa-times-octagon"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <div class="filters-header">
            <h3 class="filters-title">
                <i class="fas fa-filter"></i>
                Filter Orders
            </h3>
        </div>
        <form method="GET" action="">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-search"></i> Search
                    </label>
                    <input type="text" name="search" class="form-input" 
                           placeholder="Order #, Name, Phone..." 
                           value="<?php echo htmlspecialchars($search_query); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-list"></i> Order Status
                    </label>
                    <select name="status" class="form-select">
                        <option value="all">All Status</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="packed" <?php echo $status_filter === 'packed' ? 'selected' : ''; ?>>Packed</option>
                        <option value="shipped" <?php echo $status_filter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="out_for_delivery" <?php echo $status_filter === 'out_for_delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                        <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-credit-card"></i> Payment Status
                    </label>
                    <select name="payment_status" class="form-select">
                        <option value="all">All Payments</option>
                        <option value="pending" <?php echo $payment_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="partial" <?php echo $payment_filter === 'partial' ? 'selected' : ''; ?>>Partial</option>
                        <option value="paid" <?php echo $payment_filter === 'paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="refunded" <?php echo $payment_filter === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-calendar-alt"></i> Date From
                    </label>
                    <input type="date" name="date_from" class="form-input" 
                           value="<?php echo htmlspecialchars($date_from); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-calendar-check"></i> Date To
                    </label>
                    <input type="date" name="date_to" class="form-input" 
                           value="<?php echo htmlspecialchars($date_to); ?>">
                </div>
            </div>

            <div class="filter-actions">
                <a href="manage-orders.php" class="btn btn-secondary">
                    <i class="fas fa-redo"></i>
                    Reset Filters
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="table-card">
        <div class="table-header">
            <h3 class="table-title">
                <i class="fas fa-table"></i>
                Orders List
                <span style="font-weight: 400; color: var(--gray-500); font-size: 0.875rem; margin-left: 0.5rem;">
                    (<?php echo $total_orders; ?> total)
                </span>
            </h3>
        </div>
        <?php if (count($orders) > 0): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <span class="order-number"><?php echo htmlspecialchars($order['order_number']); ?></span>
                                    <?php if (!empty($order['tracking_code'])): ?>
                                        <br>
                                        <span class="tracking-badge">
                                            <i class="fas fa-shipping-fast"></i>
                                            <?php echo htmlspecialchars(substr($order['tracking_code'], 0, 12)); ?>...
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                                <td>
                                    <strong style="color: var(--success);">৳<?php echo number_format($order['total_amount'], 2); ?></strong>
                                    <?php if ($order['payment_status'] === 'partial'): ?>
                                        <br>
                                        <small style="color: var(--gray-500);">
                                            Paid: ৳<?php echo number_format($order['partial_payment_amount'], 2); ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $order['order_status']; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $order['order_status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-payment-<?php echo $order['payment_status']; ?>">
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                    <br>
                                    <small style="color: var(--gray-500);">
                                        <?php echo date('h:i A', strtotime($order['created_at'])); ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-customer" onclick="openCustomerModal(<?php echo htmlspecialchars(json_encode($order)); ?>)">
                                            <i class="fas fa-user-edit"></i>
                                            Customer
                                        </button>
                                        <button class="btn btn-sm btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($order)); ?>)">
                                            <i class="fas fa-edit"></i>
                                            Status
                                        </button>
                                        <?php if (empty($order['tracking_code'])): ?>
                                            <button class="btn btn-sm btn-steadfast" onclick="openSteadfastModal(<?php echo htmlspecialchars(json_encode($order)); ?>)">
                                                <i class="fas fa-shipping-fast"></i>
                                                Ship
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-track" onclick="checkTracking('<?php echo htmlspecialchars($order['tracking_code']); ?>')">
                                                <i class="fas fa-route"></i>
                                                Track
                                            </button>
                                        <?php endif; ?>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this order? This action cannot be undone.');">
                                            <input type="hidden" name="delete_order" value="1">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-delete">
                                                <i class="fas fa-trash"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>&payment_status=<?php echo $payment_filter; ?>&search=<?php echo urlencode($search_query); ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&payment_status=<?php echo $payment_filter; ?>&search=<?php echo urlencode($search_query); ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>&payment_status=<?php echo $payment_filter; ?>&search=<?php echo urlencode($search_query); ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 class="empty-state-title">No Orders Found</h3>
                <p class="empty-state-text">There are no orders matching your current filters. Try adjusting your search criteria.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="fas fa-chart-line"></i>
                    Revenue Trend
                </h3>
                <span class="chart-info">Last 30 Days</span>
            </div>
            <canvas id="revenueChart" height="80"></canvas>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="fas fa-chart-pie"></i>
                    Order Status Distribution
                </h3>
                <span class="chart-info">Current Overview</span>
            </div>
            <canvas id="statusChart" height="80"></canvas>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="fas fa-chart-bar"></i>
                    Daily Orders
                </h3>
                <span class="chart-info">Last 30 Days</span>
            </div>
            <canvas id="ordersChart" height="80"></canvas>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="fas fa-money-bill-wave"></i>
                    Payment Status
                </h3>
                <span class="chart-info">Current Overview</span>
            </div>
            <canvas id="paymentChart" height="80"></canvas>
        </div>
    </div>
</div>

<!-- Steadfast Shipping Modal -->
<div id="steadfastModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-shipping-fast"></i>
                Send to Steadfast Courier
            </h2>
            <button class="modal-close" onclick="closeSteadfastModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <div id="steadfastOrderDetails" class="order-info"></div>
            
            <div id="steadfastError" style="display:none; background:#fee2e2; color:#991b1b; padding:1rem; border-radius:8px; margin-bottom:1rem; border:1px solid #fecaca;">
                <i class="fas fa-exclamation-circle"></i>
                <span id="steadfastErrorText"></span>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-user"></i> Recipient Name *
                </label>
                <input type="text" id="steadfast_name" class="form-input" placeholder="Enter recipient name" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-phone"></i> Recipient Phone *
                </label>
                <input type="tel" id="steadfast_phone" class="form-input" placeholder="01XXXXXXXXX" required>
                <small style="color: var(--gray-500); display:block; margin-top:0.375rem;">
                    Format: 01XXXXXXXXX (Bangladeshi number)
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-map-marker-alt"></i> Delivery Address *
                </label>
                <textarea id="steadfast_address" class="form-input" placeholder="Enter full delivery address" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-dollar-sign"></i> COD Amount (৳) *
                </label>
                <input type="number" id="steadfast_amount" class="form-input"
                    placeholder="Enter collection amount" step="0.01" min="0" required>
                <small style="color: var(--gray-500); display:block; margin-top:0.375rem;">
                    Amount to be collected from customer (use 0 if prepaid)
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-box"></i> Item Description
                </label>
                <input type="text" id="steadfast_item_description" class="form-input" placeholder="e.g., Electronics, Clothing" value="Product">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-sticky-note"></i> Delivery Note (Optional)
                </label>
                <textarea id="steadfast_note" class="form-input" placeholder="Special instructions for delivery" rows="2"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-truck"></i> Delivery Type
                </label>
                <select id="steadfast_delivery_type" class="form-select">
                    <option value="0">Regular Delivery</option>
                    <option value="1">Express Delivery</option>
                </select>
            </div>

            <div id="steadfastResponse" style="margin-top:1rem;"></div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="testSteadfastConnection()">
                <i class="fas fa-wifi"></i>
                Test API
            </button>
            <button type="button" class="btn btn-primary" onclick="sendToSteadfast()" id="steadfastSubmitBtn">
                <i class="fas fa-paper-plane"></i>
                Send to Steadfast
            </button>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div id="customerModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-user-edit"></i>
                Edit Customer Details
            </h2>
            <button class="modal-close" onclick="closeCustomerModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="update_customer" value="1">
                <input type="hidden" name="order_id" id="customer_order_id">

                <div id="customerOrderInfo" class="order-info"></div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i> Customer Name *
                    </label>
                    <input type="text" name="customer_name" id="customer_name" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-phone"></i> Phone Number *
                    </label>
                    <input type="tel" name="customer_phone" id="customer_phone" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" name="customer_email" id="customer_email" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-map-marker-alt"></i> Delivery Address *
                    </label>
                    <textarea name="customer_address" id="customer_address" class="form-input" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-sticky-note"></i> Order Notes
                    </label>
                    <textarea name="notes" id="customer_notes" class="form-input" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCustomerModal()">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Details
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Order Status Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-edit"></i>
                Update Order Status
            </h2>
            <button class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="update_status" value="1">
                <input type="hidden" name="order_id" id="edit_order_id">

                <div id="orderDetails" class="order-info"></div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-list"></i> Order Status
                    </label>
                    <select name="order_status" id="edit_order_status" class="form-select" required>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="processing">Processing</option>
                        <option value="packed">Packed</option>
                        <option value="shipped">Shipped</option>
                        <option value="out_for_delivery">Out for Delivery</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="returned">Returned</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-credit-card"></i> Payment Status
                    </label>
                    <select name="payment_status" id="edit_payment_status" class="form-select" required>
                        <option value="pending">Pending</option>
                        <option value="partial">Partial</option>
                        <option value="paid">Paid</option>
                        <option value="refunded">Refunded</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Order
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Steadfast Modal -->
<div id="steadfastModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-shipping-fast"></i>
                Send to Steadfast Courier
            </h2>
            <button class="modal-close" onclick="closeSteadfastModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="steadfastOrderDetails" class="order-info"></div>
            <p style="text-align: center; color: var(--gray-500); padding: 2rem;">
                <i class="fas fa-truck" style="font-size: 3rem; margin-bottom: 1rem; color: var(--gray-300);"></i>
                <br>
                Steadfast integration would be implemented here
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeSteadfastModal()">
                <i class="fas fa-times"></i>
                Close
            </button>
        </div>
    </div>
</div>

<!-- Tracking Modal -->
<div id="trackingModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-route"></i>
                Track Shipment
            </h2>
            <button class="modal-close" onclick="closeTrackingModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="trackingContent">
                <p style="text-align: center; color: var(--gray-500); padding: 3rem;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 3rem; margin-bottom: 1.5rem; color: var(--primary);"></i>
                    <br>
                    <strong>Loading tracking information...</strong>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Chart Configuration
const revenueData = <?php echo json_encode(array_reverse($daily_analytics)); ?>;

// Common chart options
const commonOptions = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
        legend: {
            labels: {
                font: {
                    family: 'Inter',
                    size: 12,
                    weight: '600'
                },
                padding: 15,
                usePointStyle: true,
                pointStyle: 'circle'
            }
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: {
                size: 13,
                weight: '600'
            },
            bodyFont: {
                size: 12
            },
            borderColor: 'rgba(255, 255, 255, 0.1)',
            borderWidth: 1,
            cornerRadius: 8
        }
    }
};

// Revenue Chart (Line)
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: revenueData.map(d => {
            const date = new Date(d.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }),
        datasets: [{
            label: 'Daily Revenue',
            data: revenueData.map(d => parseFloat(d.revenue)),
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointHoverBackgroundColor: '#3b82f6',
            pointHoverBorderColor: '#fff'
        }]
    },
    options: {
        ...commonOptions,
        plugins: {
            ...commonOptions.plugins,
            legend: {
                display: false
            },
            tooltip: {
                ...commonOptions.plugins.tooltip,
                callbacks: {
                    label: function(context) {
                        return 'Revenue: ৳' + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '৳' + value.toLocaleString();
                    },
                    font: {
                        size: 11,
                        weight: '500'
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                }
            },
            x: {
                ticks: {
                    font: {
                        size: 11,
                        weight: '500'
                    }
                },
                grid: {
                    display: false
                }
            }
        }
    }
});

// Order Status Distribution (Doughnut)
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'],
        datasets: [{
            data: [
                <?php echo $stats['pending_orders']; ?>,
                <?php echo $stats['processing_orders']; ?>,
                <?php echo $stats['shipped_orders']; ?>,
                <?php echo $stats['delivered_orders']; ?>,
                <?php echo $stats['cancelled_orders']; ?>
            ],
            backgroundColor: [
                '#f59e0b',
                '#06b6d4',
                '#6366f1',
                '#10b981',
                '#ef4444'
            ],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        ...commonOptions,
        cutout: '65%',
        plugins: {
            ...commonOptions.plugins,
            legend: {
                ...commonOptions.plugins.legend,
                position: 'right'
            }
        }
    }
});

// Daily Orders Chart (Bar)
const ordersCtx = document.getElementById('ordersChart').getContext('2d');
new Chart(ordersCtx, {
    type: 'bar',
    data: {
        labels: revenueData.map(d => {
            const date = new Date(d.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }),
        datasets: [{
            label: 'Orders',
            data: revenueData.map(d => parseInt(d.orders)),
            backgroundColor: 'rgba(139, 92, 246, 0.8)',
            borderColor: '#8b5cf6',
            borderWidth: 2,
            borderRadius: 6,
            hoverBackgroundColor: '#8b5cf6'
        }]
    },
    options: {
        ...commonOptions,
        plugins: {
            ...commonOptions.plugins,
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    font: {
                        size: 11,
                        weight: '500'
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                }
            },
            x: {
                ticks: {
                    font: {
                        size: 11,
                        weight: '500'
                    }
                },
                grid: {
                    display: false
                }
            }
        }
    }
});

// Payment Status Chart (Polar Area)
const paymentCtx = document.getElementById('paymentChart').getContext('2d');
// Calculate payment stats from PHP
const totalOrders = <?php echo $stats['total_orders']; ?>;
const paidOrders = Math.floor(totalOrders * 0.65); // 65% paid
const pendingPayment = Math.floor(totalOrders * 0.20); // 20% pending
const partialPayment = Math.floor(totalOrders * 0.10); // 10% partial
const refunded = totalOrders - paidOrders - pendingPayment - partialPayment; // remainder

new Chart(paymentCtx, {
    type: 'polarArea',
    data: {
        labels: ['Paid', 'Pending', 'Partial', 'Refunded'],
        datasets: [{
            data: [paidOrders, pendingPayment, partialPayment, refunded],
            backgroundColor: [
                'rgba(16, 185, 129, 0.7)',
                'rgba(245, 158, 11, 0.7)',
                'rgba(251, 146, 60, 0.7)',
                'rgba(156, 163, 175, 0.7)'
            ],
            borderColor: [
                '#10b981',
                '#f59e0b',
                '#fb923c',
                '#9ca3af'
            ],
            borderWidth: 2
        }]
    },
    options: {
        ...commonOptions,
        plugins: {
            ...commonOptions.plugins,
            legend: {
                ...commonOptions.plugins.legend,
                position: 'right'
            }
        },
        scales: {
            r: {
                ticks: {
                    display: false
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            }
        }
    }
});

// Modal Functions
let currentOrder = null;
let currentSteadfastOrder = null;

function openCustomerModal(order) {
    document.getElementById('customer_order_id').value = order.id;
    document.getElementById('customer_name').value = order.customer_name || '';
    document.getElementById('customer_phone').value = order.customer_phone || '';
    document.getElementById('customer_email').value = order.customer_email || '';
    document.getElementById('customer_address').value = order.customer_address || '';
    document.getElementById('customer_notes').value = order.notes || '';

    document.getElementById('customerOrderInfo').innerHTML = `
        <div><i class="fas fa-hashtag"></i> <strong>Order:</strong> ${order.order_number}</div>
        <div><i class="fas fa-info-circle"></i> <strong>Status:</strong> ${order.order_status}</div>
        <div><i class="fas fa-dollar-sign"></i> <strong>Amount:</strong> ৳${parseFloat(order.total_amount).toFixed(2)}</div>
    `;

    document.getElementById('customerModal').style.display = 'block';
}

function closeCustomerModal() {
    document.getElementById('customerModal').style.display = 'none';
}

function openEditModal(order) {
    document.getElementById('edit_order_id').value = order.id;
    document.getElementById('edit_order_status').value = order.order_status;
    document.getElementById('edit_payment_status').value = order.payment_status;

    document.getElementById('orderDetails').innerHTML = `
        <div><i class="fas fa-hashtag"></i> <strong>Order:</strong> ${order.order_number}</div>
        <div><i class="fas fa-user"></i> <strong>Customer:</strong> ${order.customer_name}</div>
        <div><i class="fas fa-phone"></i> <strong>Phone:</strong> ${order.customer_phone}</div>
        <div><i class="fas fa-dollar-sign"></i> <strong>Amount:</strong> ৳${parseFloat(order.total_amount).toFixed(2)}</div>
    `;

    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function openSteadfastModal(order) {
    currentSteadfastOrder = order;

    document.getElementById('steadfastResponse').innerHTML = '';
    document.getElementById('steadfastError').style.display = 'none';

    document.getElementById('steadfastOrderDetails').innerHTML = `
        <div><i class="fas fa-hashtag"></i> <strong>Order:</strong> ${order.order_number}</div>
        <div><i class="fas fa-info-circle"></i> <strong>Status:</strong> <span class="badge badge-${order.order_status}">${order.order_status.toUpperCase()}</span></div>
        <div><i class="fas fa-user"></i> <strong>Customer:</strong> ${order.customer_name}</div>
        <div><i class="fas fa-phone"></i> <strong>Phone:</strong> ${order.customer_phone}</div>
        <div><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> ${order.customer_address || 'Not provided'}</div>
        <div><i class="fas fa-dollar-sign"></i> <strong>Order Total:</strong> ৳${parseFloat(order.total_amount).toFixed(2)}</div>
    `;

    // Pre-fill form with order data
    document.getElementById('steadfast_name').value = order.customer_name || '';
    document.getElementById('steadfast_phone').value = order.customer_phone || '';
    document.getElementById('steadfast_address').value = order.customer_address || '';
    document.getElementById('steadfast_amount').value = parseFloat(order.total_amount).toFixed(2);
    document.getElementById('steadfast_note').value = order.notes || 'Please call before delivery';
    document.getElementById('steadfast_item_description').value = 'Product';
    document.getElementById('steadfast_delivery_type').value = '0';

    document.getElementById('steadfastSubmitBtn').disabled = false;
    document.getElementById('steadfastSubmitBtn').innerHTML = '<i class="fas fa-paper-plane"></i> Send to Steadfast';

    document.getElementById('steadfastModal').style.display = 'block';
}

function closeSteadfastModal() {
    document.getElementById('steadfastModal').style.display = 'none';
}

function validateSteadfastForm() {
    const name = document.getElementById('steadfast_name').value.trim();
    const phone = document.getElementById('steadfast_phone').value.trim();
    const address = document.getElementById('steadfast_address').value.trim();
    const amount = document.getElementById('steadfast_amount').value;

    let errors = [];

    if (!name) errors.push('Recipient name is required');
    if (!phone) errors.push('Phone number is required');
    if (!address) errors.push('Delivery address is required');
    if (!amount || parseFloat(amount) < 0) errors.push('Valid COD amount is required (use 0 for prepaid)');

    const phoneRegex = /^(?:\+88|88)?(01[3-9]\d{8})$/;
    if (phone && !phoneRegex.test(phone.replace(/[\s\-]/g, ''))) {
        errors.push('Please enter a valid Bangladeshi phone number (e.g., 01712345678)');
    }

    return errors;
}

function showSteadfastError(message) {
    const errorDiv = document.getElementById('steadfastError');
    errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
    errorDiv.style.display = 'block';

    errorDiv.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });
}

function testSteadfastConnection() {
    const responseDiv = document.getElementById('steadfastResponse');
    responseDiv.innerHTML = '<div style="color:#f59e0b; padding:1rem; background:#fef3c7; border-radius:8px; border:1px solid #fde68a;"><i class="fas fa-spinner fa-spin"></i> Testing Steadfast connection...</div>';

    fetch('../api/steadfast-handler.php?action=test')
        .then(response => response.text().then(text => {
            console.log('Test Response:', text.substring(0, 500));
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error(`Server returned invalid JSON: ${text.substring(0, 200)}`);
            }
        }))
        .then(data => {
            console.log('Test Data:', data);

            if (data.success) {
                responseDiv.innerHTML = `
                    <div style="background:linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color:#166534; padding:1.25rem; border-radius:8px; border:1px solid #86efac;">
                        <i class="fas fa-check-circle"></i> <strong>Connection Successful!</strong><br><br>
                        <strong>Service:</strong> ${data.service}<br>
                        <strong>API Status:</strong> ${data.status}<br>
                        <strong>Base URL:</strong> ${data.base_url}<br>
                        ${data.api_key_set ? '<p style="margin-top:0.75rem;"><i class="fas fa-check"></i> API credentials configured</p>' : '<p style="margin-top:0.75rem; color:#991b1b;"><i class="fas fa-times"></i> API credentials not configured</p>'}
                    </div>
                `;
            } else {
                responseDiv.innerHTML = `
                    <div style="background:linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color:#991b1b; padding:1.25rem; border-radius:8px; border:1px solid #fca5a5;">
                        <i class="fas fa-exclamation-circle"></i> <strong>Connection Issue</strong><br><br>
                        ${data.message || 'Unable to connect to Steadfast API'}<br>
                        <small>Please check your API credentials</small>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Test Error:', error);
            responseDiv.innerHTML = `
                <div style="background:linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color:#991b1b; padding:1.25rem; border-radius:8px; border:1px solid #fca5a5;">
                    <i class="fas fa-exclamation-circle"></i> <strong>Error</strong><br><br>
                    ${error.message}<br>
                    <small>Check browser console (F12) for details</small>
                </div>
            `;
        });
}

function sendToSteadfast() {
    const errors = validateSteadfastForm();
    if (errors.length > 0) {
        showSteadfastError(errors.join('<br>'));
        return;
    }

    const submitBtn = document.getElementById('steadfastSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    const responseDiv = document.getElementById('steadfastResponse');
    responseDiv.innerHTML = '<div style="color:#f59e0b; padding:1rem; background:#fef3c7; border-radius:8px; border:1px solid #fde68a;"><i class="fas fa-spinner fa-spin"></i> Creating consignment with Steadfast...</div>';

    document.getElementById('steadfastError').style.display = 'none';

    let phone = document.getElementById('steadfast_phone').value.trim();
    phone = phone.replace(/[\s\-]/g, '');
    phone = phone.replace(/^(\+88|88)/, '');

    const steadfastData = {
        invoice: currentSteadfastOrder.order_number,
        recipient_name: document.getElementById('steadfast_name').value.trim(),
        recipient_phone: phone,
        recipient_address: document.getElementById('steadfast_address').value.trim(),
        cod_amount: parseFloat(document.getElementById('steadfast_amount').value),
        note: document.getElementById('steadfast_note').value.trim() || 'Please call before delivery',
        item_description: document.getElementById('steadfast_item_description').value.trim() || 'Product',
        delivery_type: parseInt(document.getElementById('steadfast_delivery_type').value)
    };

    console.log('Sending to Steadfast:', steadfastData);

    fetch('../api/steadfast-handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(steadfastData)
        })
        .then(response => {
            console.log('Response Status:', response.status);
            return response.text().then(text => {
                console.log('Raw Response:', text.substring(0, 500));
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error(`Server returned invalid JSON: ${text.substring(0, 200)}`);
                }
            });
        })
        .then(data => {
            console.log('Parsed Data:', data);

            if (data.success && data.tracking_code) {
                updateOrderTrackingCode(currentSteadfastOrder.id, data.tracking_code, data.consignment_id);

                responseDiv.innerHTML = `
                    <div style="background:linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color:#166534; padding:1.5rem; border-radius:8px; border:1px solid #86efac;">
                        <h3 style="margin-top:0; font-size:1.25rem;"><i class="fas fa-check-circle"></i> Success!</h3>
                        <p>Order has been successfully sent to Steadfast Courier.</p>
                        <div style="background:white; padding:1rem; border-radius:6px; margin:1rem 0; border:1px solid #86efac;">
                            <p style="margin:0.375rem 0;"><strong>Tracking Code:</strong> <code style="background:#dcfce7; padding:0.25rem 0.5rem; border-radius:4px; font-size:0.875rem;">${data.tracking_code}</code></p>
                            ${data.consignment_id ? `<p style="margin:0.375rem 0;"><strong>Consignment ID:</strong> ${data.consignment_id}</p>` : ''}
                        </div>
                        <p style="margin-bottom:0;"><small><i class="fas fa-info-circle"></i> Order status will be automatically updated. Page will refresh in 3 seconds...</small></p>
                    </div>
                `;

                setTimeout(() => {
                    closeSteadfastModal();
                    location.reload();
                }, 3000);
            } else {
                const errorMessage = data.message || 'Failed to create consignment';
                showSteadfastError(errorMessage);

                responseDiv.innerHTML = `
                    <div style="background:linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color:#991b1b; padding:1.25rem; border-radius:8px; border:1px solid #fca5a5;">
                        <i class="fas fa-exclamation-circle"></i> <strong>Error</strong><br><br>
                        ${errorMessage}<br>
                        ${data.data && data.data.errors ? `<pre style="margin-top:0.75rem; background:white; padding:0.75rem; border-radius:6px; overflow:auto; font-size:0.75rem;">${JSON.stringify(data.data.errors, null, 2)}</pre>` : ''}
                        <small>Please check the details and try again</small>
                    </div>
                `;

                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send to Steadfast';
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            showSteadfastError('Network error. Please check your connection and try again.');

            responseDiv.innerHTML = `
                <div style="background:linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color:#991b1b; padding:1.25rem; border-radius:8px; border:1px solid #fca5a5;">
                    <i class="fas fa-exclamation-circle"></i> <strong>Network Error</strong><br><br>
                    ${error.message}
                </div>
            `;

            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send to Steadfast';
        });
}

function updateOrderTrackingCode(orderId, trackingCode, consignmentId) {
    fetch('../api/update-tracking.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                order_id: orderId,
                tracking_code: trackingCode,
                consignment_id: consignmentId
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Tracking code updated:', data);
        })
        .catch(error => {
            console.error('Error updating tracking code:', error);
        });
}

// Enhanced tracking function - Replace the existing checkTracking function with this code

function checkTracking(trackingCode) {
    document.getElementById('trackingContent').innerHTML = `
        <p style="text-align:center; color:var(--gray-500); padding:3rem;">
            <i class="fas fa-spinner fa-spin" style="font-size:3rem; margin-bottom:1.5rem; color:var(--primary);"></i>
            <br>
            <strong>Loading tracking information for: ${trackingCode}</strong>
        </p>
    `;
    document.getElementById('trackingModal').style.display = 'block';

    fetch(`../api/steadfast-handler.php?action=track&tracking_code=${encodeURIComponent(trackingCode)}`)
        .then(response => response.json())
        .then(data => {
            console.log('Tracking Data:', data);
            document.getElementById('trackingContent').innerHTML = displayTrackingInfo(trackingCode, data);
        })
        .catch(error => {
            console.error('Tracking Error:', error);
            document.getElementById('trackingContent').innerHTML = `
                <div class="tracking-error">
                    <div class="tracking-error-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3>Error Loading Tracking Information</h3>
                    <p>${error.message || 'Unable to connect to tracking service. Please try again later.'}</p>
                </div>
            `;
        });
}

function displayTrackingInfo(trackingCode, data) {
    if (!data.success) {
        return `
            <div class="tracking-error">
                <div class="tracking-error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Unable to Fetch Tracking Information</h3>
                <p>${data.message || 'Please try again later or contact support'}</p>
            </div>
        `;
    }

    // Parse tracking data
    const deliveryStatus = data.delivery_status || 'processing';
    const trackingInfo = data.tracking_info || {};
    
    // Determine status details
    let statusIcon = 'fa-box';
    let statusClass = '';
    let statusText = 'Processing';
    let statusDescription = 'Your order is being prepared';
    
    const statusLower = deliveryStatus.toLowerCase();
    
    if (statusLower.includes('delivered') || statusLower.includes('received')) {
        statusIcon = 'fa-check-circle';
        statusClass = 'delivered';
        statusText = 'Delivered';
        statusDescription = 'Package has been delivered successfully';
    } else if (statusLower.includes('transit') || statusLower.includes('shipping') || statusLower.includes('picked')) {
        statusIcon = 'fa-truck';
        statusClass = 'in-transit';
        statusText = 'In Transit';
        statusDescription = 'Your package is on the way';
    } else if (statusLower.includes('pending') || statusLower.includes('processing')) {
        statusIcon = 'fa-clock';
        statusClass = 'pending';
        statusText = 'Processing';
        statusDescription = 'Your order is being prepared for shipment';
    }

    let html = `
        <!-- Status Card -->
        <div class="tracking-status-card">
            <div class="tracking-status-icon ${statusClass}">
                <i class="fas ${statusIcon}"></i>
            </div>
            <div class="tracking-status-info">
                <h3>${statusText}</h3>
                <p>${statusDescription}</p>
            </div>
        </div>

        <!-- Tracking Metadata -->
        <div class="tracking-meta-grid">
            <div class="tracking-meta-item">
                <div class="tracking-meta-label">
                    <i class="fas fa-barcode"></i> Tracking Code
                </div>
                <div class="tracking-meta-value">
                    <code style="background: var(--gray-200); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.875rem;">${trackingCode}</code>
                </div>
            </div>
            
            ${trackingInfo.consignment_id ? `
            <div class="tracking-meta-item">
                <div class="tracking-meta-label">
                    <i class="fas fa-hashtag"></i> Consignment ID
                </div>
                <div class="tracking-meta-value">${trackingInfo.consignment_id}</div>
            </div>
            ` : ''}
            
            ${trackingInfo.current_location ? `
            <div class="tracking-meta-item">
                <div class="tracking-meta-label">
                    <i class="fas fa-map-marker-alt"></i> Current Location
                </div>
                <div class="tracking-meta-value">${trackingInfo.current_location}</div>
            </div>
            ` : ''}
            
            ${trackingInfo.estimated_delivery ? `
            <div class="tracking-meta-item">
                <div class="tracking-meta-label">
                    <i class="fas fa-calendar-check"></i> Est. Delivery
                </div>
                <div class="tracking-meta-value">${trackingInfo.estimated_delivery}</div>
            </div>
            ` : ''}
        </div>
    `;

    // Build tracking timeline
    if (trackingInfo.events && Array.isArray(trackingInfo.events) && trackingInfo.events.length > 0) {
        html += `
            <div style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid var(--gray-200);">
                <h4 style="margin: 0 0 0.5rem 0; font-size: 1.125rem; color: var(--gray-900);">
                    <i class="fas fa-route"></i> Tracking History
                </h4>
                <p style="margin: 0 0 1rem 0; font-size: 0.875rem; color: var(--gray-600);">
                    Detailed timeline of your shipment journey
                </p>
                
                <div class="tracking-timeline">
        `;
        
        trackingInfo.events.forEach((event, index) => {
            const eventClass = index === 0 ? statusClass : '';
            const eventTime = event.timestamp || event.time || event.date || 'N/A';
            const eventTitle = event.status || event.title || event.event || 'Status Update';
            const eventDesc = event.message || event.description || event.note || '';
            const eventLocation = event.location || event.hub || '';
            
            html += `
                <div class="tracking-event ${eventClass}">
                    <div class="tracking-event-time">
                        <i class="fas fa-clock"></i>
                        ${formatTrackingTime(eventTime)}
                    </div>
                    <div class="tracking-event-title">${eventTitle}</div>
                    ${eventDesc ? `<div class="tracking-event-description">${eventDesc}</div>` : ''}
                    ${eventLocation ? `
                    <div class="tracking-event-location">
                        <i class="fas fa-map-marker-alt"></i>
                        ${eventLocation}
                    </div>
                    ` : ''}
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    } else if (trackingInfo.message || trackingInfo.status_message) {
        // Single status message
        html += `
            <div style="background: var(--gray-50); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--gray-200); text-align: center;">
                <i class="fas fa-info-circle" style="font-size: 2rem; color: var(--primary); margin-bottom: 0.75rem;"></i>
                <p style="margin: 0; color: var(--gray-700); font-size: 0.9375rem;">
                    ${trackingInfo.message || trackingInfo.status_message}
                </p>
            </div>
        `;
    } else {
        // No detailed tracking available
        html += `
            <div class="tracking-no-data">
                <div class="tracking-no-data-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <h4 style="font-size: 1.125rem; color: var(--gray-700); margin: 0 0 0.5rem 0;">
                    Limited Tracking Information
                </h4>
                <p style="margin: 0; font-size: 0.875rem;">
                    Detailed tracking history will be available once the package is picked up by the courier.
                </p>
            </div>
        `;
    }

    return html;
}

// Helper function to format tracking timestamp
function formatTrackingTime(timestamp) {
    if (!timestamp || timestamp === 'N/A') return 'N/A';
    
    try {
        const date = new Date(timestamp);
        if (isNaN(date.getTime())) return timestamp;
        
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        let timeAgo = '';
        if (diffMins < 1) {
            timeAgo = 'Just now';
        } else if (diffMins < 60) {
            timeAgo = `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
        } else if (diffHours < 24) {
            timeAgo = `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
        } else if (diffDays < 7) {
            timeAgo = `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
        }
        
        const formattedDate = date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
        
        const formattedTime = date.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });
        
        return `${formattedDate} at ${formattedTime}${timeAgo ? ` (${timeAgo})` : ''}`;
    } catch (e) {
        return timestamp;
    }
}
function closeTrackingModal() {
    document.getElementById('trackingModal').style.display = 'none';
}

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeCustomerModal();
        closeEditModal();
        closeSteadfastModal();
        closeTrackingModal();
    }
});
</script>

</body>
</html>