<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Manage Product Sets';

require_once './components/header.php';

$message = '';
$message_type = '';
$filter_product_id = $_GET['product_id'] ?? '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_set'])) {
        $result = deleteProductSet($_POST['set_id']);
        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'danger';
    } elseif (isset($_POST['update_status'])) {
        $result = updateProductSetStatus($_POST['set_id'], $_POST['status']);
        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'danger';
    }
}

// Get all product sets
$all_sets = getAllProductSets();

// Filter by product if specified
if ($filter_product_id) {
    $all_sets = array_filter($all_sets, function($set) use ($filter_product_id) {
        return $set['product_id'] == $filter_product_id;
    });
}

// Get all products for filter dropdown
$products = getAllProducts();
?>

<style>
    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
    }
    
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .stats-card .icon {
        font-size: 2rem;
        margin-bottom: 10px;
    }
    
    .stats-card .number {
        font-size: 2rem;
        font-weight: 700;
        color: #667eea;
    }
    
    .stats-card .label {
        color: #666;
        font-size: 0.9rem;
    }
    
    .filter-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .table-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .product-img-sm {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 5px;
    }
    
    .badge-special {
        background: #ff5722;
        color: white;
        padding: 4px 10px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .status-badge {
        padding: 5px 12px;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-active {
        background: #4caf50;
        color: white;
    }
    
    .status-inactive {
        background: #9e9e9e;
        color: white;
    }
    
    .action-btn {
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin: 2px;
        font-size: 13px;
    }
    
    .btn-edit {
        background: #2196f3;
        color: white;
    }
    
    .btn-delete {
        background: #f44336;
        color: white;
    }
    
    .btn-toggle {
        background: #ff9800;
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    
    .empty-state i {
        font-size: 1rem;
        color: #ccc;

    }
    
    .group-header {
        background: #f5f5f5;
        padding: 12px 15px;
        font-weight: 700;
        color: #333;
        border-left: 4px solid #667eea;
        margin-top: 20px;
        margin-bottom: 10px;
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Alert Messages -->
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <strong><?php echo $message_type === 'success' ? 'Success!' : 'Error!'; ?></strong> <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="page-title">
                        <i class="fas fa-layer-group"></i> Manage Product Sets
                    </h1>
                    <a href="add-product-sets.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Sets
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="icon text-primary">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="number"><?php echo count($all_sets); ?></div>
                    <div class="label">Total Sets</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="icon text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="number">
                        <?php echo count(array_filter($all_sets, fn($s) => $s['status'] === 'active')); ?>
                    </div>
                    <div class="label">Active Sets</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="icon text-warning">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="number">
                        <?php echo count(array_filter($all_sets, fn($s) => $s['is_special_offer'])); ?>
                    </div>
                    <div class="label">Special Offers</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="icon text-info">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="number">
                        <?php echo array_sum(array_column($all_sets, 'stock_quantity')); ?>
                    </div>
                    <div class="label">Total Stock</div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="filter-card">
            <form method="GET" class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Filter by Product</label>
                    <select class="form-select" name="product_id" onchange="this.form.submit()">
                        <option value="">All Products</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product['id']; ?>" 
                                    <?php echo $filter_product_id == $product['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($product['product_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <?php if ($filter_product_id): ?>
                        <a href="manage-product-sets.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Product Sets Table -->
        <div class="table-card">
            <?php if (empty($all_sets)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Product Sets Found</h3>
                    <p>Start by adding product sets to your products.</p>
                    <a href="add-product-sets.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Your First Set
                    </a>
                </div>
            <?php else: ?>
                <?php 
                // Group sets by product
                $grouped_sets = [];
                foreach ($all_sets as $set) {
                    $grouped_sets[$set['product_id']][] = $set;
                }
                ?>
                
                <?php foreach ($grouped_sets as $product_id => $sets): ?>
                    <div class="group-header">
                        <i class="fas fa-box"></i> <?php echo htmlspecialchars($sets[0]['product_name']); ?>
                        <a href="add-product-sets.php?product_id=<?php echo $product_id; ?>" 
                           class="btn btn-sm btn-primary float-end">
                            <i class="fas fa-plus"></i> Add More Sets
                        </a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="15%">Set Name</th>
                                    <th width="10%">Pieces</th>
                                    <th width="12%">Unit Price</th>
                                    <th width="12%">Total Price</th>
                                    <th width="10%">Stock</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Special</th>
                                    <th width="16%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sets as $index => $set): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($set['set_name']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $set['pieces_count']; ?> pcs</span>
                                    </td>
                                    <td>৳<?php echo number_format($set['unit_price'], 2); ?></td>
                                    <td>
                                        <strong>৳<?php echo number_format($set['total_price'], 2); ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($set['stock_quantity'] < 10): ?>
                                            <span class="badge bg-danger"><?php echo $set['stock_quantity']; ?></span>
                                        <?php else: ?>
                                            <?php echo $set['stock_quantity']; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $set['status']; ?>">
                                            <?php echo ucfirst($set['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($set['is_special_offer']): ?>
                                            <span class="badge-special">SPECIAL</span>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        
                                        <a href="edit-product-set.php?id=<?php echo $set['id']; ?>" 
                                           class="action-btn btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this set?');">
                                            <input type="hidden" name="set_id" value="<?php echo $set['id']; ?>">
                                            <button type="submit" name="delete_set" class="action-btn btn-delete" 
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require './components/footer.php'; ?>