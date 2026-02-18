<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Add Product';

require_once './components/header.php';

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = createProduct($_POST, $_FILES);
    
    if ($result['success']) {
        $message = $result['message'];
        $message_type = 'success';
        // Redirect after 2 seconds
        echo '<script>setTimeout(function(){ window.location.href = "products.php"; }, 2000);</script>';
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
    }
    
.card .card-title {
    color: white;
    margin-bottom: 0.75rem;
    text-transform: capitalize;
    font-family: "ubuntu-medium", sans-serif;
    font-size: 1.125rem;
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
    
    .image-preview-box {
        border: 2px dashed #ddd;
        border-radius: 8px;
        padding: 20px;
        min-height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .preview-image {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #ddd;
    }
    
    .color-option {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
</style>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
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
    <div class="page-title-section mt-3">
      <div class="icon-box">
        <i class="fa-solid fa-plus"></i>
      </div>
      <h1>Add new Product</h1>
    </div>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                </div>
            </div>
        </div>

        <!-- Product Form -->
        <form method="POST" enctype="multipart/form-data" id="productForm">
            <div class="row">
                <!-- Left Column -->
                <div class="col-lg-8">
                    <!-- Basic Info -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Basic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Product Name <span class="required">*</span></label>
                                <input type="text" class="form-control" name="product_name" required 
                                       placeholder="e.g., Mirsist Jelly Lipstick">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="4" 
                                          placeholder="Enter product description..."></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Regular Price (৳) <span class="required">*</span></label>
                                    <input type="number" class="form-control" name="regular_price" required 
                                           step="0.01" min="0" placeholder="450">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Offer Price (৳) <span class="required">*</span></label>
                                    <input type="number" class="form-control" name="offer_price" required 
                                           step="0.01" min="0" placeholder="290">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Quantity (pcs per set) <span class="required">*</span></label>
                                    <input type="number" class="form-control" name="quantity" required 
                                           min="1" value="1">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Stock Quantity <span class="required">*</span></label>
                                    <input type="number" class="form-control" name="stock_quantity" required 
                                           min="0" placeholder="100">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Images -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Product Images</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Upload Images <span class="required">*</span></label>
                                <input type="file" class="form-control" name="product_images[]" 
                                       multiple accept="image/*" required onchange="previewImages(event)">
                                <small class="text-muted">First image will be the main image. Max 5MB per image.</small>
                            </div>
                            
                            <div id="imagePreview" class="image-preview-box">
                                <p class="text-muted mb-0">Image previews will appear here</p>
                            </div>
                        </div>
                    </div>

                    <!-- Colors (Optional) -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Color Options (Optional)</h5>
                            <button type="button" class="btn btn-sm btn-light text-black" onclick="addColor()">
                                <i class="fas fa-plus"></i> Add Color
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="colorsContainer">
                                <div class="color-option">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="color_name[]" 
                                                   placeholder="Color name (e.g., Pink)">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="color" class="form-control form-control-color" 
                                                   name="color_code[]" value="#e91e63">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" name="color_stock[]" 
                                                   min="0" placeholder="Stock">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-sm btn-danger w-100" 
                                                    onclick="removeColor(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-4">
                    <!-- Status -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Status & Visibility</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1">
                                    <label class="form-check-label">Featured Product</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_special_offer" value="1">
                                    <label class="form-check-label">Special Offer</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Category & Tags</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category">
                                    <option value="">Select Category</option>
                                    <option value="lipstick">Lipstick</option>
                                    <option value="lip_gloss">Lip Gloss</option>
                                    <option value="lip_balm">Lip Balm</option>
                                    <option value="lip_care">Lip Care</option>
                                    <option value="makeup">Makeup</option>
                                    <option value="skincare">Skincare</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tags</label>
                                <input type="text" class="form-control" name="tags" 
                                       placeholder="jelly, lipstick, beauty">
                                <small class="text-muted">Separate with commas</small>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-save"></i> Create Product
                            </button>
                            <button type="reset" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-redo"></i> Reset Form
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Image Preview
    function previewImages(event) {
        const container = document.getElementById('imagePreview');
        container.innerHTML = '';
        
        const files = Array.from(event.target.files);
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-image';
                if (index === 0) {
                    img.style.border = '3px solid #667eea';
                }
                container.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }

    // Add Color
    function addColor() {
        const container = document.getElementById('colorsContainer');
        const colorHTML = `
            <div class="color-option">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="color_name[]" placeholder="Color name">
                    </div>
                    <div class="col-md-3">
                        <input type="color" class="form-control form-control-color" name="color_code[]" value="#e91e63">
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control" name="color_stock[]" min="0" placeholder="Stock">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-danger w-100" onclick="removeColor(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', colorHTML);
    }

    // Remove Color
    function removeColor(btn) {
        const colors = document.querySelectorAll('.color-option');
        if (colors.length > 1) {
            btn.closest('.color-option').remove();
        }
    }

    // Form Validation
    document.getElementById('productForm').addEventListener('submit', function(e) {
        const offerPrice = parseFloat(document.querySelector('[name="offer_price"]').value);
        const regularPrice = parseFloat(document.querySelector('[name="regular_price"]').value);
        
        if (offerPrice > regularPrice) {
            e.preventDefault();
            alert('Offer price cannot be greater than regular price!');
            return false;
        }
    });
</script>

<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->
<?php require './components/footer.php'; ?>