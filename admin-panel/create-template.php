<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Create Landing Page Template';

require_once './components/header.php';

$message = '';
$message_type = '';

// Get all products
$products = getAllProducts();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = createLandingPageTemplate($_POST, $_FILES);
    
    if ($result['success']) {
        $message = $result['message'];
        $message_type = 'success';
        echo '<script>setTimeout(function(){ window.location.href = "manage-landing-templates.php"; }, 2000);</script>';
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
    
    .section-divider {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        margin: 30px 0 20px 0;
        font-weight: 700;
        font-size: 1.1rem;
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
    
    .image-preview-box {
        border: 2px dashed #ddd;
        border-radius: 8px;
        padding: 20px;
        min-height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
    
    .preview-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #ddd;
    }
    
    .help-text {
        background: #e3f2fd;
        padding: 10px 15px;
        border-radius: 5px;
        margin-top: 10px;
        font-size: 13px;
        color: #1565c0;
    }
    
    .info-box {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }
    
    .counter {
        background: #667eea;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-right: 10px;
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
                        <i class="fas fa-magic"></i> Create Landing Page Template
                    </h1>
                    <a href="manage-landing-templates.php" class="btn btn-secondary">
                        <i class="fas fa-list"></i> View Templates
                    </a>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <strong><i class="fas fa-info-circle"></i> Template Guide:</strong><br>
            Fill in all the text content and upload images for each section. This will create a dynamic landing page that can be used for your product. Leave fields empty if not needed.
        </div>

        <!-- Template Form -->
        <form method="POST" enctype="multipart/form-data" id="templateForm">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-9">
                    <!-- Basic Settings -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title text-white">Basic Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Template Name <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="template_name" required 
                                           placeholder="e.g., Lipstick Landing Page V1">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Select Product <span class="required">*</span></label>
                                    <select class="form-select" name="product_id" required>
                                        <option value="">-- Choose Product --</option>
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?php echo $product['id']; ?>">
                                                <?php echo htmlspecialchars($product['product_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Brand Name</label>
                                    <input type="text" class="form-control" name="brand_name" 
                                           placeholder="e.g., Beauty & Mine">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Brand Logo</label>
                                    <input type="file" class="form-control" name="brand_logo" 
                                           accept="image/*" onchange="previewSingleImage(event, 'logoPreview')">
                                    <div id="logoPreview" class="image-preview-box" style="display:none;">
                                        <img src="" alt="Logo Preview" class="preview-image">
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                        <label class="form-check-label">Set as Active Template</label>
                                    </div>
                                    <small class="text-muted">Only one template can be active per product</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 1: Hero -->
                    <div class="section-divider">
                        <span class="counter">1</span> Hero Section
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Hero Title</label>
                                <input type="text" class="form-control" name="hero_title" 
                                       placeholder="e.g., 💋 ঠোঁটের তাৎক্ষণাৎ মুখ ঘর বদলাও instantly 💋">
                                <div class="help-text">
                                    Main headline that grabs attention
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Hero Subtitle</label>
                                <input type="text" class="form-control" name="hero_subtitle" 
                                       placeholder="Optional subtitle">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Before Label</label>
                                    <input type="text" class="form-control" name="before_label" 
                                           value="আগের কবলন" placeholder="Before text">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">After Label</label>
                                    <input type="text" class="form-control" name="after_label" 
                                           value="আজীর কবলন" placeholder="After text">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Hero Comparison Image</label>
                                <input type="file" class="form-control" name="hero_comparison_image" 
                                       accept="image/*" onchange="previewSingleImage(event, 'heroImagePreview')">
                                <small class="text-muted">Before/After comparison image</small>
                                <div id="heroImagePreview" class="image-preview-box" style="display:none;">
                                    <img src="" alt="Hero Preview" class="preview-image">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Hero Benefit Text</label>
                                <textarea class="form-control" name="hero_benefit_text" rows="2"
                                          placeholder="e.g., ❤️ নোট নানথ নেতন, মসুম, আনৰ সনতজ সানৰানদন😊"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Product Features -->
                    <div class="section-divider">
                        <span class="counter">2</span> Product Features Section
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Features Section Title</label>
                                <input type="text" class="form-control" name="features_title" 
                                       placeholder="e.g., কেন Mirsist Jelly Lipstick আপনার ঠোঁটের বন্ধু?">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Feature 1</label>
                                <textarea class="form-control" name="feature_1_text" rows="2"
                                          placeholder="e.g., pH অনুযায়ী নিজের রঙ: আপনার ঠোঁটের জনা তৈনৰ ইউনিক শেড!"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Feature 2</label>
                                <textarea class="form-control" name="feature_2_text" rows="2"
                                          placeholder="Second feature description"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Feature 3</label>
                                <textarea class="form-control" name="feature_3_text" rows="2"
                                          placeholder="Third feature description"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Feature 4</label>
                                <textarea class="form-control" name="feature_4_text" rows="2"
                                          placeholder="Fourth feature description"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Product Showcase Image</label>
                                <input type="file" class="form-control" name="product_showcase_image" 
                                       accept="image/*" onchange="previewSingleImage(event, 'showcasePreview')">
                                <small class="text-muted">Main product image for this section</small>
                                <div id="showcasePreview" class="image-preview-box" style="display:none;">
                                    <img src="" alt="Showcase Preview" class="preview-image">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Customer Reviews -->
                    <div class="section-divider">
                        <span class="counter">3</span> Customer Reviews Section
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Reviews Section Title</label>
                                <input type="text" class="form-control" name="reviews_title" 
                                       value="আমাদের কাষ্টমার রিভিউ"
                                       placeholder="Customer Reviews Title">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Review Screenshots (Multiple)</label>
                                <input type="file" class="form-control" name="review_images[]" 
                                       multiple accept="image/*" onchange="previewMultipleImages(event, 'reviewPreview')">
                                <small class="text-muted">Upload 3-5 customer review screenshots</small>
                                <div id="reviewPreview" class="image-preview-box">
                                    <p class="text-muted mb-0">Image previews will appear here</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Product Selection -->
                    <div class="section-divider">
                        <span class="counter">4</span> Product Selection Section
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Product Slider Images (Multiple)</label>
                                <input type="file" class="form-control" name="product_slider_images[]" 
                                       multiple accept="image/*" onchange="previewMultipleImages(event, 'productSliderPreview')">
                                <small class="text-muted">Upload 3-5 product images for slider</small>
                                <div id="productSliderPreview" class="image-preview-box">
                                    <p class="text-muted mb-0">Image previews will appear here</p>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Order Section Title</label>
                                <input type="text" class="form-control" name="order_title" 
                                       placeholder="e.g., অৰ্ডার করলে যা যা পাচ্ছন:">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Order Subtitle</label>
                                <textarea class="form-control" name="order_subtitle" rows="2"
                                          placeholder="Additional information"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Benefit 1</label>
                                    <input type="text" class="form-control" name="order_benefit_1" 
                                           placeholder="e.g., Mirsist jelly Lipstick">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Benefit 2</label>
                                    <input type="text" class="form-control" name="order_benefit_2" 
                                           placeholder="e.g., প্রিমিয়াম প্যাকেজিং বক্স">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Contact -->
                    <div class="section-divider">
                        <span class="counter">5</span> Contact Section
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Contact Title</label>
                                <input type="text" class="form-control" name="contact_title" 
                                       placeholder="e.g., অৰ্ডার বা প্ৰশ্ন?">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Contact Subtitle</label>
                                <textarea class="form-control" name="contact_subtitle" rows="2"
                                          placeholder="e.g., কল করুন বা WhatsApp / Messenger-এ মেসেজ দিন"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">WhatsApp Number</label>
                                    <input type="text" class="form-control" name="whatsapp_number" 
                                           placeholder="e.g., 01712345678">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">WhatsApp Link</label>
                                    <input type="text" class="form-control" name="whatsapp_link" 
                                           placeholder="https://wa.me/8801712345678">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Messenger Link</label>
                                    <input type="text" class="form-control" name="messenger_link" 
                                           placeholder="https://m.me/yourpage">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 6: Checkout -->
                    <div class="section-divider">
                        <span class="counter">6</span> Checkout Section
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Checkout Title</label>
                                <input type="text" class="form-control" name="checkout_title" 
                                       placeholder="e.g., অৰ্ডারটি সম্পূর্ণ করুন">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Checkout Subtitle</label>
                                <input type="text" class="form-control" name="checkout_subtitle" 
                                       placeholder="e.g., আমরা শীঘ্রই আপনার সাথে যোগাযোগ করবো">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Dhaka Shipping Cost (৳)</label>
                                    <input type="number" class="form-control" name="shipping_dhaka_cost" 
                                           value="70" step="0.01" min="0">
                                    <small class="text-muted">Shipping within Dhaka city</small>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Urban Area Shipping Cost (৳)</label>
                                    <input type="number" class="form-control" name="shipping_urban_cost" 
                                           value="100" step="0.01" min="0">
                                    <small class="text-muted">Shipping to urban areas near Dhaka</small>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Outside Dhaka Shipping Cost (৳)</label>
                                    <input type="number" class="form-control" name="shipping_outside_cost" 
                                           value="130" step="0.01" min="0">
                                    <small class="text-muted">Shipping outside Dhaka</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Privacy Policy Text</label>
                                <textarea class="form-control" name="privacy_text" rows="3"
                                          placeholder="Your personal data will be used to process your order..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-3">
                    <!-- Quick Actions -->
                    <div class="card sticky-top" style="top: 20px;">
                        <div class="card-header">
                            <h5 class="card-title text-white">Actions</h5>
                        </div>
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-save"></i> Create Template
                            </button>
                            <button type="reset" class="btn btn-outline-secondary w-100 mb-2">
                                <i class="fas fa-redo"></i> Reset Form
                            </button>
                            <a href="manage-landing-templates.php" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>

                    <!-- Progress Tracker -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title text-white">Progress</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small><i class="fas fa-check text-success"></i> Basic Settings</small>
                            </div>
                            <div class="mb-2">
                                <small><i class="fas fa-circle text-muted"></i> Hero Section</small>
                            </div>
                            <div class="mb-2">
                                <small><i class="fas fa-circle text-muted"></i> Features Section</small>
                            </div>
                            <div class="mb-2">
                                <small><i class="fas fa-circle text-muted"></i> Reviews Section</small>
                            </div>
                            <div class="mb-2">
                                <small><i class="fas fa-circle text-muted"></i> Product Section</small>
                            </div>
                            <div class="mb-2">
                                <small><i class="fas fa-circle text-muted"></i> Contact Section</small>
                            </div>
                            <div class="mb-2">
                                <small><i class="fas fa-circle text-muted"></i> Checkout Section</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Preview single image
function previewSingleImage(event, containerId) {
    const container = document.getElementById(containerId);
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            container.style.display = 'flex';
            const img = container.querySelector('img');
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

// Preview multiple images
function previewMultipleImages(event, containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    
    const files = Array.from(event.target.files);
    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'preview-image';
            container.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
}

// Form validation
document.getElementById('templateForm').addEventListener('submit', function(e) {
    const templateName = document.querySelector('[name="template_name"]').value;
    const productId = document.querySelector('[name="product_id"]').value;
    
    if (!templateName || !productId) {
        e.preventDefault();
        alert('Please fill in Template Name and select a Product!');
        return false;
    }
});
</script>

<?php require_once './components/footer.php'; ?>