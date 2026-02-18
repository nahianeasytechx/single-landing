<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Edit Product Set';

require_once './components/header.php';

$message = '';
$message_type = '';
$set_id = $_GET['id'] ?? 0;

// Get product set data
$set = getProductSetById($set_id);

if (!$set) {
 echo "<script>window.location.href='manage-product-sets.php'</script>";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = updateProductSet($set_id, $_POST);
    
    if ($result['success']) {
        $message = $result['message'];
        $message_type = 'success';
        // Reload set data
        $set = getProductSetById($set_id);
        echo '<script>setTimeout(function(){ window.location.href = "manage-product-sets.php?product_id=' . $set['product_id'] . '"; }, 2000);</script>';
    } else {
        $message = $result['message'];
        $message_type = 'danger';
    }
}
?>

<style>
    .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px 10px 0 0 !important;
        padding: 15px 20px;
    }
    
    .card-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #fff;
    }
    
    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
    }
    
    .form-label {
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
    }
    
    .required {
        color: #dc3545;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 12px 30px;
        font-weight: 600;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    .calculated-price {
        background: #e3f2fd;
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
    }
    
    .calculated-price .label {
        font-weight: 600;
        color: #666;
        margin-bottom: 5px;
    }
    
    .calculated-price .amount {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1976d2;
    }
    
    .product-info-box {
        background: #f5f5f5;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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
                        <i class="fas fa-edit"></i> Edit Product Set
                    </h1>
                    <a href="manage-product-sets.php?product_id=<?php echo $set['product_id']; ?>" 
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Sets
                    </a>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <form method="POST" id="editSetForm">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Product Info -->
                    <div class="product-info-box">
                        <strong><i class="fas fa-box"></i> Product:</strong> 
                        <?php echo htmlspecialchars($set['product_name']); ?>
                    </div>

                    <!-- Set Details -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Set Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Set Name</label>
                                    <input type="text" class="form-control" name="set_name" 
                                           value="<?php echo htmlspecialchars($set['set_name']); ?>"
                                           placeholder="e.g., Single Pack">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pieces in Set <span class="required">*</span></label>
                                    <input type="number" class="form-control" name="pieces_count" 
                                           value="<?php echo $set['pieces_count']; ?>"
                                           min="1" required onchange="calculatePrice()">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Unit Price (per piece) <span class="required">*</span></label>
                                    <input type="number" class="form-control" name="unit_price" 
                                           value="<?php echo $set['unit_price']; ?>"
                                           step="0.01" min="0" required onchange="calculatePrice()">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Stock Quantity</label>
                                    <input type="number" class="form-control" name="stock_quantity" 
                                           value="<?php echo $set['stock_quantity']; ?>"
                                           min="0">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" class="form-control" name="display_order" 
                                           value="<?php echo $set['display_order']; ?>"
                                           min="0">
                                    <small class="text-muted">Lower numbers appear first</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="active" <?php echo $set['status'] === 'active' ? 'selected' : ''; ?>>
                                            Active
                                        </option>
                                        <option value="inactive" <?php echo $set['status'] === 'inactive' ? 'selected' : ''; ?>>
                                            Inactive
                                        </option>
                                    </select>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               name="is_special_offer" value="1"
                                               <?php echo $set['is_special_offer'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label">
                                            Mark as Special Offer
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Price Calculator -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Price Calculator</h5>
                        </div>
                        <div class="card-body">
                            <div class="calculated-price">
                                <div class="label">Total Price</div>
                                <div class="amount" id="totalPrice">
                                    ৳<?php echo number_format($set['total_price'], 2); ?>
                                </div>
                                <small class="text-muted" id="calculation">
                                    <?php echo $set['pieces_count']; ?> × ৳<?php echo number_format($set['unit_price'], 2); ?>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Actions</h5>
                        </div>
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-save"></i> Update Set
                            </button>
                            <a href="manage-product-sets.php?product_id=<?php echo $set['product_id']; ?>" 
                               class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>

                    <!-- Set Info -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Set Information</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>Created:</strong><br>
                                <?php echo date('M d, Y H:i', strtotime($set['created_at'])); ?>
                            </p>
                            <p class="mb-0">
                                <strong>Last Updated:</strong><br>
                                <?php echo date('M d, Y H:i', strtotime($set['updated_at'])); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function calculatePrice() {
    const pieces = parseFloat(document.querySelector('[name="pieces_count"]').value) || 0;
    const unitPrice = parseFloat(document.querySelector('[name="unit_price"]').value) || 0;
    const totalPrice = pieces * unitPrice;
    
    document.getElementById('totalPrice').textContent = '৳' + totalPrice.toFixed(2);
    document.getElementById('calculation').textContent = 
        pieces + ' × ৳' + unitPrice.toFixed(2);
}

// Calculate on page load
calculatePrice();
</script>

<?php require './components/footer.php'; ?>