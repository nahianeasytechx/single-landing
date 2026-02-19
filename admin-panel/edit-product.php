<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Edit Product: ' ;
require './components/header.php'; 
// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = intval($_GET['id']);

// Get product data
$product = getProductById($product_id);

if (!$product) {
    header("Location: products.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form data
    $result = updateProduct($product_id, $_POST, $_FILES);
    
    if ($result['success']) {
        $success_message = 'Product updated successfully';
        // Refresh product data
        $product = getProductById($product_id);
    } else {
        $error_message = $result['message'];
    }
}


?>

<style>
    .card-body .btn {
    width: 133px;
    padding: 10px;
    margin-bottom: 0;
    font-size: 12px;
}
</style>

<!--=======================================================================-->
<!------------------------ Your Content Start From Here --------------------->
<!--=======================================================================-->


<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Edit Product</h4>
            </div>
        </div>
    </div>
    
    <!-- Success/Error Messages -->
    <?php if(isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if(isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-8">
                                <h5 class="mb-3">Basic Information</h5>
                                
                                <div class="mb-3">
                                    <label for="product_name" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name" 
                                           value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="regular_price" class="form-label">Regular Price (৳)</label>
                                        <input type="number" class="form-control" id="regular_price" name="regular_price" 
                                               step="0.01" min="0" value="<?php echo $product['regular_price']; ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="offer_price" class="form-label">Offer Price (৳)</label>
                                        <input type="number" class="form-control" id="offer_price" name="offer_price" 
                                               step="0.01" min="0" value="<?php echo $product['offer_price']; ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" 
                                               min="1" value="<?php echo $product['quantity']; ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="stock_quantity" class="form-label">Stock Quantity</label>
                                        <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" 
                                               min="0" value="<?php echo $product['stock_quantity']; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sidebar -->
                            <div class="col-md-4">
                                <h5 class="mb-3">Settings</h5>
                                
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-select" id="category" name="category">
                                        <option value="">Select Category</option>
                                        <option value="clothing" <?php echo ($product['category'] == 'clothing') ? 'selected' : ''; ?>>Clothing</option>
                                        <option value="electronics" <?php echo ($product['category'] == 'electronics') ? 'selected' : ''; ?>>Electronics</option>
                                        <option value="home" <?php echo ($product['category'] == 'home') ? 'selected' : ''; ?>>Home</option>
                                        <option value="beauty" <?php echo ($product['category'] == 'beauty') ? 'selected' : ''; ?>>Beauty</option>
                                        <option value="other" <?php echo ($product['category'] == 'other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" <?php echo ($product['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($product['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                        <option value="draft" <?php echo ($product['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="tags" class="form-label">Tags</label>
                                    <input type="text" class="form-control" id="tags" name="tags" 
                                           value="<?php echo htmlspecialchars($product['tags']); ?>">
                                    <small class="text-muted">Separate with commas</small>
                                </div>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                           value="1" <?php echo $product['is_featured'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_featured">Featured Product</label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_special_offer" name="is_special_offer" 
                                           value="1" <?php echo $product['is_special_offer'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_special_offer">Special Offer</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Current Images -->
                        <?php if(!empty($product['images'])): ?>
                            <div class="mb-4">
                                <h5 class="mb-3">Current Images</h5>
                                <div class="row">
                                    <?php foreach($product['images'] as $image): ?>
                                        <div class="col-3 col-md-2 mb-3">
                                            <div class="border rounded p-2 text-center">
                                                <img src="../uploads/products/<?php echo $image['image_path']; ?>" 
                                                     class="img-fluid mb-2" style="height: 80px; object-fit: cover;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="main_image" 
                                                           value="<?php echo $image['id']; ?>" 
                                                           <?php echo $image['is_main'] ? 'checked' : ''; ?>>
                                                    <label class="form-check-label small">Main</label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Add New Images -->
                        <div class="mb-4">
                            <h5 class="mb-3">Add New Images</h5>
                            <input type="file" class="form-control" id="product_images" name="product_images[]" 
                                   accept="image/*" multiple>
                            <small class="text-muted">Upload JPG, PNG, WEBP, GIF images. Max 5 files.</small>
                        </div>
                        
                        <!-- Color Options -->
                        <div class="mb-4">
                            <h5 class="mb-3">Color Options</h5>
                            <div id="colorOptionsContainer">
                                <?php if(!empty($product['colors'])): ?>
                                    <?php foreach($product['colors'] as $color): ?>
                                        <div class="row mb-2">
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="color_name[]" 
                                                       value="<?php echo htmlspecialchars($color['color_name']); ?>" placeholder="Color Name">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="color" class="form-control form-control-color" name="color_code[]" 
                                                       value="<?php echo $color['color_code']; ?>" title="Choose color">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" class="form-control" name="color_stock[]" 
                                                       value="<?php echo $color['stock']; ?>" placeholder="Stock" min="0">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger btn-sm remove-color">Remove</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm " id="addColorOption">
                                Add Color Option
                            </button>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="products.php" class="btn btn-secondary">Back to Products</a>
                            <div>
                                <a href="view-product.php?id=<?php echo $product_id; ?>" class="btn btn-info me-2">View Product</a>
                                <button type="submit" class="btn btn-primary ">Update Product</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!--========================================================================-->
<!---------------------------- Your Content End Here ------------------------->
<!--========================================================================-->

<?php require './components/footer.php'; ?>

<script>
$(document).ready(function() {
    // Price validation
    $('#offer_price').on('change', function() {
        var regularPrice = parseFloat($('#regular_price').val());
        var offerPrice = parseFloat($(this).val());
        
        if (offerPrice > regularPrice) {
            alert('Offer price cannot be greater than regular price');
            $(this).val(regularPrice);
        }
    });
    
    // Add color option
    $('#addColorOption').on('click', function() {
        var colorOption = 
            '<div class="row mb-2">' +
            '  <div class="col-md-4">' +
            '    <input type="text" class="form-control" name="color_name[]" placeholder="Color Name">' +
            '  </div>' +
            '  <div class="col-md-3">' +
            '    <input type="color" class="form-control form-control-color" name="color_code[]" value="#000000" title="Choose color">' +
            '  </div>' +
            '  <div class="col-md-3">' +
            '    <input type="number" class="form-control" name="color_stock[]" placeholder="Stock" value="0" min="0">' +
            '  </div>' +
            '  <div class="col-md-2">' +
            '    <button type="button" class="btn btn-danger btn-sm remove-color">Remove</button>' +
            '  </div>' +
            '</div>';
        
        $('#colorOptionsContainer').append(colorOption);
    });
    
    // Remove color option
    $(document).on('click', '.remove-color', function() {
        $(this).closest('.row').remove();
    });
});
</script>