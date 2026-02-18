<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'View Landing Page Template';

require_once './components/header.php';   // <-- session started, functions loaded, protectPage() called

$message = '';
$message_type = '';

// Display session messages if any
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Get template ID from URL
$template_id = isset($_GET['template_id']) ? intval($_GET['template_id']) : 0;

if (!$template_id) {
    $_SESSION['message'] = 'Invalid template ID provided';
    $_SESSION['message_type'] = 'danger';
    header("Location: manage-landing-templates.php");
    exit();
}

// Get template details
$template = getLandingPageTemplate($template_id);

if (!$template) {
    $_SESSION['message'] = "Template with ID $template_id not found";
    $_SESSION['message_type'] = 'danger';
    header("Location: manage-landing-templates.php");
    exit();
}

// Get template images
$template_images = getTemplateImages($template_id);

// Organize images by section
$images_by_section = [];
foreach ($template_images as $img) {
    $images_by_section[$img['section']][] = $img;
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
    
    .info-label {
        font-weight: 600;
        color: #555;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }
    
    .info-value {
        color: #333;
        margin-bottom: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
        min-height: 40px;
    }
    
    .info-value.empty {
        color: #999;
        font-style: italic;
    }
    
    .badge {
        padding: 8px 15px;
        font-size: 0.85rem;
    }
    
    .image-display {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        border: 2px solid #ddd;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .image-container {
        margin-bottom: 15px;
    }
    
    .image-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 10px;
    }
    
    .gallery-item {
        flex: 0 0 calc(33.333% - 10px);
    }
    
    .gallery-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #ddd;
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
    
    .status-badge {
        font-size: 1rem;
        padding: 10px 20px;
    }
    
    .btn-action {
        margin: 5px;
    }
    
    .feature-list {
        list-style: none;
        padding: 0;
    }
    
    .feature-list li {
        padding: 10px;
        margin-bottom: 10px;
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        border-radius: 5px;
    }
    
    .contact-info {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    
    .contact-info i {
        font-size: 1.2rem;
        color: #667eea;
    }
    
    .shipping-costs {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-top: 10px;
    }
    
    .shipping-cost-card {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        border: 2px solid #ddd;
    }
    
    .shipping-cost-card .amount {
        font-size: 1.5rem;
        font-weight: 700;
        color: #667eea;
        margin: 10px 0;
    }
    
    .shipping-cost-card .label {
        font-weight: 600;
        color: #555;
    }
    
    @media (max-width: 768px) {
        .gallery-item {
            flex: 0 0 calc(50% - 8px);
        }
        
        .shipping-costs {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Alert Messages -->
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <strong><?php echo $message_type === 'success' ? 'Success!' : 'Error!'; ?></strong> <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="page-title">
                        <i class="fas fa-eye"></i> View Template: <?php echo htmlspecialchars($template['template_name']); ?>
                    </h1>
                    <div>
                        <a href="edit-landing-template.php?id=<?php echo $template_id; ?>" class="btn btn-primary btn-action">
                            <i class="fas fa-edit"></i> Edit Template
                        </a>
                        <a href="manage-landing-templates.php" class="btn btn-secondary btn-action">
                            <i class="fas fa-list"></i> All Templates
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Basic Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title text-white">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-label">Template Name</div>
                                <div class="info-value">
                                    <?php echo htmlspecialchars($template['template_name']); ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-label">Product</div>
                                <div class="info-value">
                                    <?php echo htmlspecialchars($template['product_name'] ?? 'N/A'); ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-label">Brand Name</div>
                                <div class="info-value <?php echo empty($template['brand_name']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($template['brand_name']) ? htmlspecialchars($template['brand_name']) : 'Not specified'; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-label">Status</div>
                                <div class="info-value">
                                    <?php if ($template['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($template['brand_logo'])): ?>
                            <div class="col-12">
                                <div class="info-label">Brand Logo</div>
                                <div class="image-container">
                                    <img src="../uploads/templates/<?php echo htmlspecialchars($template['brand_logo']); ?>" 
                                         alt="Brand Logo" class="image-display" style="max-width: 200px;">
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Section 1: Hero -->
                <div class="section-divider">
                    <span class="counter">1</span> Hero Section
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="info-label">Hero Title</div>
                                <div class="info-value <?php echo empty($template['hero_title']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($template['hero_title']) ? htmlspecialchars($template['hero_title']) : 'Not set'; ?>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="info-label">Hero Subtitle</div>
                                <div class="info-value <?php echo empty($template['hero_subtitle']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($template['hero_subtitle']) ? htmlspecialchars($template['hero_subtitle']) : 'Not set'; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-label">Before Label</div>
                                <div class="info-value">
                                    <?php echo htmlspecialchars($template['before_label'] ?? 'আগের কবলন'); ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-label">After Label</div>
                                <div class="info-value">
                                    <?php echo htmlspecialchars($template['after_label'] ?? 'আজীর কবলন'); ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($template['hero_comparison_image'])): ?>
                            <div class="col-12">
                                <div class="info-label">Hero Comparison Image</div>
                                <div class="image-container">
                                    <img src="../uploads/templates/<?php echo htmlspecialchars($template['hero_comparison_image']); ?>" 
                                         alt="Hero Comparison" class="image-display">
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="col-12">
                                <div class="info-label">Hero Benefit Text</div>
                                <div class="info-value <?php echo empty($template['hero_benefit_text']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($template['hero_benefit_text']) ? htmlspecialchars($template['hero_benefit_text']) : 'Not set'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Features -->
                <div class="section-divider">
                    <span class="counter">2</span> Product Features Section
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="info-label">Features Section Title</div>
                                <div class="info-value <?php echo empty($template['features_title']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($template['features_title']) ? htmlspecialchars($template['features_title']) : 'Not set'; ?>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="info-label">Features</div>
                                <ul class="feature-list">
                                    <?php if (!empty($template['feature_1_text'])): ?>
                                    <li>
                                        <i class="fas fa-check-circle text-success"></i>
                                        <?php echo htmlspecialchars($template['feature_1_text']); ?>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($template['feature_2_text'])): ?>
                                    <li>
                                        <i class="fas fa-check-circle text-success"></i>
                                        <?php echo htmlspecialchars($template['feature_2_text']); ?>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($template['feature_3_text'])): ?>
                                    <li>
                                        <i class="fas fa-check-circle text-success"></i>
                                        <?php echo htmlspecialchars($template['feature_3_text']); ?>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($template['feature_4_text'])): ?>
                                    <li>
                                        <i class="fas fa-check-circle text-success"></i>
                                        <?php echo htmlspecialchars($template['feature_4_text']); ?>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php if (empty($template['feature_1_text']) && empty($template['feature_2_text']) && 
                                              empty($template['feature_3_text']) && empty($template['feature_4_text'])): ?>
                                    <li class="empty">No features added</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            
                            <?php if (!empty($template['product_showcase_image'])): ?>
                            <div class="col-12">
                                <div class="info-label">Product Showcase Image</div>
                                <div class="image-container">
                                    <img src="../uploads/templates/<?php echo htmlspecialchars($template['product_showcase_image']); ?>" 
                                         alt="Product Showcase" class="image-display">
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Reviews -->
                <div class="section-divider">
                    <span class="counter">3</span> Customer Reviews Section
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="info-label">Reviews Section Title</div>
                                <div class="info-value">
                                    <?php echo htmlspecialchars($template['reviews_title'] ?? 'আমাদের কাষ্টমার রিভিউ'); ?>
                                </div>
                            </div>
                            
                            <?php if (isset($images_by_section['review_slider']) && !empty($images_by_section['review_slider'])): ?>
                            <div class="col-12">
                                <div class="info-label">Review Screenshots</div>
                                <div class="image-gallery">
                                    <?php foreach ($images_by_section['review_slider'] as $img): ?>
                                    <div class="gallery-item">
                                        <img src="../uploads/templates/<?php echo htmlspecialchars($img['image_path']); ?>" 
                                             alt="Review <?php echo $img['display_order'] + 1; ?>" 
                                             class="gallery-image">
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="col-12">
                                <div class="info-value empty">No review images uploaded</div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Product Selection -->
                <div class="section-divider">
                    <span class="counter">4</span> Product Selection Section
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <?php if (isset($images_by_section['product_slider']) && !empty($images_by_section['product_slider'])): ?>
                            <div class="col-12">
                                <div class="info-label">Product Slider Images</div>
                                <div class="image-gallery">
                                    <?php foreach ($images_by_section['product_slider'] as $img): ?>
                                    <div class="gallery-item">
                                        <img src="../uploads/templates/<?php echo htmlspecialchars($img['image_path']); ?>" 
                                             alt="Product <?php echo $img['display_order'] + 1; ?>" 
                                             class="gallery-image">
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="col-12">
                                <div class="info-label">Order Section Title</div>
                                <div class="info-value <?php echo empty($template['order_title']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($template['order_title']) ? htmlspecialchars($template['order_title']) : 'Not set'; ?>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="info-label">Order Subtitle</div>
                                <div class="info-value <?php echo empty($template['order_subtitle']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($template['order_subtitle']) ? htmlspecialchars($template['order_subtitle']) : 'Not set'; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-label">Order Benefit 1</div>
                                <div class="info-value <?php echo empty($template['order_benefit_1']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($template['order_benefit_1']) ? htmlspecialchars($template['order_benefit_1']) : 'Not set'; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-label">Order Benefit 2</div>
                                <div class="info-value <?php echo empty($template['order_benefit_2']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($template['order_benefit_2']) ? htmlspecialchars($template['order_benefit_2']) : 'Not set'; ?>
                                </div>
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
                        <div class="row">
                            <div class="col-12">
                                <div class="info-label">Contact Title</div>
                                <div class="info-value <?php echo empty($template['contact_title']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($template['contact_title']) ? htmlspecialchars($template['contact_title']) : 'Not set'; ?>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="info-label">Contact Subtitle</div>
                                <div class="info-value <?php echo empty($template['contact_subtitle']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($template['contact_subtitle']) ? htmlspecialchars($template['contact_subtitle']) : 'Not set'; ?>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="info-label">Contact Information</div>
                                
                                <?php if (!empty($template['whatsapp_number'])): ?>
                                <div class="contact-info">
                                    <i class="fab fa-whatsapp"></i>
                                    <div>
                                        <strong>WhatsApp:</strong> <?php echo htmlspecialchars($template['whatsapp_number']); ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($template['whatsapp_link'])): ?>
                                <div class="contact-info">
                                    <i class="fas fa-link"></i>
                                    <div>
                                        <strong>WhatsApp Link:</strong> 
                                        <a href="<?php echo htmlspecialchars($template['whatsapp_link']); ?>" target="_blank">
                                            <?php echo htmlspecialchars($template['whatsapp_link']); ?>
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($template['messenger_link'])): ?>
                                <div class="contact-info">
                                    <i class="fab fa-facebook-messenger"></i>
                                    <div>
                                        <strong>Messenger Link:</strong> 
                                        <a href="<?php echo htmlspecialchars($template['messenger_link']); ?>" target="_blank">
                                            <?php echo htmlspecialchars($template['messenger_link']); ?>
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (empty($template['whatsapp_number']) && empty($template['whatsapp_link']) && empty($template['messenger_link'])): ?>
                                <div class="info-value empty">No contact information set</div>
                                <?php endif; ?>
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
                        <div class="row">
                            <div class="col-12">
                                <div class="info-label">Checkout Title</div>
                                <div class="info-value <?php echo empty($template['checkout_title']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($template['checkout_title']) ? htmlspecialchars($template['checkout_title']) : 'Not set'; ?>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="info-label">Checkout Subtitle</div>
                                <div class="info-value <?php echo empty($template['checkout_subtitle']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($template['checkout_subtitle']) ? htmlspecialchars($template['checkout_subtitle']) : 'Not set'; ?>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="info-label">Shipping Costs</div>
                                <div class="shipping-costs">
                                    <div class="shipping-cost-card">
                                        <div class="label">
                                            <i class="fas fa-city"></i> Dhaka City
                                        </div>
                                        <div class="amount">৳<?php echo number_format($template['shipping_dhaka_cost'] ?? 70, 2); ?></div>
                                    </div>
                                    
                                    <div class="shipping-cost-card">
                                        <div class="label">
                                            <i class="fas fa-building"></i> Urban Areas
                                        </div>
                                        <div class="amount">৳<?php echo number_format($template['shipping_urban_cost'] ?? 100, 2); ?></div>
                                    </div>
                                    
                                    <div class="shipping-cost-card">
                                        <div class="label">
                                            <i class="fas fa-map-marked-alt"></i> Outside Dhaka
                                        </div>
                                        <div class="amount">৳<?php echo number_format($template['shipping_outside_cost'] ?? 130, 2); ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="info-label">Privacy Policy Text</div>
                                <div class="info-value <?php echo empty($template['privacy_text']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($template['privacy_text']) ? nl2br(htmlspecialchars($template['privacy_text'])) : 'Not set'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <!-- Actions -->
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h5 class="card-title text-white">Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="edit-landing-template.php?id=<?php echo $template_id; ?>" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-edit"></i> Edit Template
                        </a>
                        
                        <?php if ($template['is_active']): ?>
                        <button type="button" class="btn btn-warning w-100 mb-2" onclick="toggleStatus(<?php echo $template_id; ?>, 0)">
                            <i class="fas fa-toggle-off"></i> Deactivate
                        </button>
                        <?php else: ?>
                        <button type="button" class="btn btn-success w-100 mb-2" onclick="toggleStatus(<?php echo $template_id; ?>, 1)">
                            <i class="fas fa-toggle-on"></i> Activate
                        </button>
                        <?php endif; ?>
                        
                        <button type="button" class="btn btn-danger w-100 mb-2" onclick="deleteTemplate(<?php echo $template_id; ?>)">
                            <i class="fas fa-trash"></i> Delete Template
                        </button>
                        
                        <a href="manage-landing-templates.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <!-- Template Info -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title text-white">Template Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Template ID</small>
                            <div><strong>#<?php echo $template['id']; ?></strong></div>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">Product ID</small>
                            <div><strong>#<?php echo $template['product_id']; ?></strong></div>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">Created At</small>
                            <div><?php echo date('M d, Y H:i', strtotime($template['created_at'])); ?></div>
                        </div>
                        
                        <?php if ($template['updated_at']): ?>
                        <div class="mb-3">
                            <small class="text-muted">Last Updated</small>
                            <div><?php echo date('M d, Y H:i', strtotime($template['updated_at'])); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <small class="text-muted">Status</small>
                            <div>
                                <?php if ($template['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title text-white">Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Total Images</small>
                            <div><strong><?php echo count($template_images); ?></strong></div>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">Review Images</small>
                            <div><strong><?php echo isset($images_by_section['review_slider']) ? count($images_by_section['review_slider']) : 0; ?></strong></div>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">Product Images</small>
                            <div><strong><?php echo isset($images_by_section['product_slider']) ? count($images_by_section['product_slider']) : 0; ?></strong></div>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">Sections Filled</small>
                            <div>
                                <?php 
                                $filled_sections = 0;
                                if (!empty($template['hero_title'])) $filled_sections++;
                                if (!empty($template['features_title'])) $filled_sections++;
                                if (!empty($template['reviews_title'])) $filled_sections++;
                                if (!empty($template['order_title'])) $filled_sections++;
                                if (!empty($template['contact_title'])) $filled_sections++;
                                if (!empty($template['checkout_title'])) $filled_sections++;
                                ?>
                                <strong><?php echo $filled_sections; ?> / 6</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleStatus(templateId, newStatus) {
    if (confirm('Are you sure you want to ' + (newStatus ? 'activate' : 'deactivate') + ' this template?')) {
        window.location.href = 'toggle-template-status.php?id=' + templateId + '&status=' + newStatus;
    }
}

function deleteTemplate(templateId) {
    if (confirm('Are you sure you want to delete this template? This action cannot be undone!')) {
        window.location.href = 'delete-template.php?id=' + templateId;
    }
}
</script>

<?php require_once './components/footer.php'; ?>