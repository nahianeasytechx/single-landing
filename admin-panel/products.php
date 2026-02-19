<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Products Management'; // Set the page title
require_once './components/header.php'; // Include functions for database operations

// Get all products
$products = getAllProducts();
?>


<!--=======================================================================-->
<!------------------------ Your Content Start From Here --------------------->
<!--=======================================================================-->
<style>
    .btn-group{
        margin-right: 5px;
    }
.btn
{
    color: white !important;
    height: 50px;
}
.btn i
{
font-size: 10px;
}
</style>
<div class="container-fluid">
    <div class="row">
    <!-- Page Title -->
    <div class="page-title-section mt-3">
      <div class="icon-box">
        <i class="fa-solid fa-box"></i>
      </div>
      <h1>Products</h1>
    </div>
    </div>
    
    <!-- Success/Error Messages -->
    <?php if(isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    <!-- Products List Card -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">All Products</h5>
                <a href="create-product.php" class="btn btn-primary" style="line-height:2.2;">
                    <i class="ri-add-line align-middle"></i> Add New Product
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if(empty($products)): ?>
                <div class="text-center py-5">
                    <i class="ri-inbox-line display-4 text-muted"></i>
                    <h5 class="mt-3">No Products Found</h5>
                    <p class="text-muted">Get started by adding your first product</p>
                    <a href="add-product.php" class="btn btn-primary">Add Product</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Regular Price</th>
                                <th>Offer Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $product): ?>
                                <tr>
                                    <td>#<?php echo $product['id']; ?></td>
                                    <td>
                                        <?php if(!empty($product['main_image'])): ?>
                                            <img src="../uploads/products/<?php echo $product['main_image']; ?>" 
                                                 alt="<?php echo $product['product_name']; ?>" 
                                                 class="rounded" width="50" height="50">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="ri-image-line text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($product['product_name']); ?></strong>
                                        <?php if(!empty($product['description'])): ?>
                                            <p class="text-muted mb-0 small">
                                                <?php echo substr($product['description'], 0, 50); ?>...
                                            </p>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['category'] ?? 'Uncategorized'); ?></td>
                                    <td>
                                        <span class="text-muted text-decoration-line-through">
                                            ৳<?php echo number_format($product['regular_price'], 2); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-danger">
                                            ৳<?php echo number_format($product['offer_price'], 2); ?>
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $product['stock_quantity'] < 10 ? 'bg-danger' : 'bg-success'; ?>">
                                            <?php echo $product['stock_quantity']; ?> units
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $product['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($product['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($product['is_featured']): ?>
                                            <span class="badge bg-info"><i class="ri-star-fill"></i> Featured</span>
                                        <?php endif; ?>
                                        <?php if($product['is_special_offer']): ?>
                                            <span class="badge bg-warning"><i class="ri-flashlight-fill"></i> Special</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group d-flex gap-1">
                                            <a href="edit-product.php?id=<?php echo $product['id']; ?>" 
                                               class="btn btn-primary  " title="Edit">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <a href="edit-product-set.php?product_id=<?php echo $product['id']; ?>" 
                                               class="btn btn-warning" title="Product Sets">
                                                <i class="fa-solid fa-box"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger delete-product" 
                                                    data-id="<?php echo $product['id']; ?>" 
                                                    data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                                                    title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <!-- Card Footer -->
        <?php if(!empty($products)): ?>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Showing <?php echo count($products); ?> products
                    </div>
                    <div>
                        <a href="export-products.php" class="btn btn-outline-primary">
                            <i class="ri-download-line align-middle"></i> Export
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Product Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="productNameToDelete"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone. All associated images and data will be permanently deleted.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteProductForm" method="POST" action="delete-product.php">
                    <input type="hidden" name="product_id" id="deleteProductId">
                    <button type="submit" class="btn btn-danger">Delete Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!--========================================================================-->
<!---------------------------- Your Content End Here ------------------------->
<!--========================================================================-->
<script>
$(document).ready(function() {
    // Delete product confirmation
    $('.delete-product').on('click', function() {
        var productId = $(this).data('id');
        var productName = $(this).data('name');
        
        $('#productNameToDelete').text(productName);
        $('#deleteProductId').val(productId);
        
        $('#deleteProductModal').modal('show');
    });
    
    // Search functionality
    $('#searchProducts').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    
    // Filter by status
    $('#statusFilter').on('change', function() {
        var status = $(this).val();
        if(status === 'all') {
            $('table tbody tr').show();
        } else {
            $('table tbody tr').each(function() {
                var rowStatus = $(this).find('td:nth-child(8) span').text().toLowerCase();
                $(this).toggle(rowStatus === status);
            });
        }
    });
});
</script>


