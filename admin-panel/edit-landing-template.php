<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Edit Landing Page Template';

require_once './components/header.php';

$message = '';
$message_type = '';

// ------------------------------------------------------------------
// 1. GET TEMPLATE ID & LOAD DATA
// ------------------------------------------------------------------
$template_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$template_id) {
    $_SESSION['message'] = 'Invalid template ID.';
    $_SESSION['message_type'] = 'danger';
    header('Location: manage-landing-templates.php');
    exit;
}

$template = getLandingPageTemplate($template_id);
if (!$template) {
    $_SESSION['message'] = "Template #$template_id not found.";
    $_SESSION['message_type'] = 'danger';
    header('Location: manage-landing-templates.php');
    exit;
}

// Get all images for this template (also inside $template['images'], but we keep separate)
$template_images = getTemplateImages($template_id);
$images_by_section = [];
foreach ($template_images as $img) {
    $images_by_section[$img['section']][] = $img;
}

// ------------------------------------------------------------------
// 2. GET ALL PRODUCTS FOR DROPDOWN
// ------------------------------------------------------------------
// (assumes you have a function getAllProducts(); if not, define it once)
if (!function_exists('getAllProducts')) {
    function getAllProducts() {
        $conn = getDatabaseConnection();
        if (!$conn) return [];
        $result = $conn->query("SELECT id, product_name FROM products ORDER BY product_name");
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $conn->close();
        return $products;
    }
}
$products = getAllProducts();

// ------------------------------------------------------------------
// 3. HELPER: DELETE SINGLE-IMAGE FILE AND NULLIFY COLUMN
// ------------------------------------------------------------------
function deleteSingleImage($conn, $template_id, $column_name, $current_file) {
    if ($current_file) {
        $file_path = '../uploads/templates/' . $current_file;
        if (file_exists($file_path)) unlink($file_path);
    }
    $stmt = $conn->prepare("UPDATE landing_page_templates SET $column_name = NULL WHERE id = ?");
    $stmt->bind_param('i', $template_id);
    $stmt->execute();
    $stmt->close();
}

// ------------------------------------------------------------------
// 4. HANDLE FORM SUBMISSION
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ----- 4.1 Delete selected slider images (review / product) -----
    if (isset($_POST['delete_review_images']) && is_array($_POST['delete_review_images'])) {
        foreach ($_POST['delete_review_images'] as $img_id) {
            removeTemplateImage((int)$img_id);
        }
    }
    if (isset($_POST['delete_product_images']) && is_array($_POST['delete_product_images'])) {
        foreach ($_POST['delete_product_images'] as $img_id) {
            removeTemplateImage((int)$img_id);
        }
    }

    // ----- 4.2 Handle removal of single images (brand_logo, hero_comparison, product_showcase) -----
    $conn = getDatabaseConnection();
    if ($conn) {
        if (isset($_POST['remove_brand_logo'])) {
            deleteSingleImage($conn, $template_id, 'brand_logo', $template['brand_logo']);
            $template['brand_logo'] = null; // update local for display
        }
        if (isset($_POST['remove_hero_comparison'])) {
            deleteSingleImage($conn, $template_id, 'hero_comparison_image', $template['hero_comparison_image']);
            $template['hero_comparison_image'] = null;
        }
        if (isset($_POST['remove_product_showcase'])) {
            deleteSingleImage($conn, $template_id, 'product_showcase_image', $template['product_showcase_image']);
            $template['product_showcase_image'] = null;
        }
        $conn->close();
    }

    // ----- 4.3 Prepare $data array from $_POST -----
    $data = [
        'product_id'            => (int)$_POST['product_id'],
        'template_name'         => trim($_POST['template_name']),
        'is_active'            => isset($_POST['is_active']) ? 1 : 0,
        'hero_title'           => trim($_POST['hero_title'] ?? ''),
        'hero_subtitle'        => trim($_POST['hero_subtitle'] ?? ''),
        'before_label'         => trim($_POST['before_label'] ?? 'আগের কবলন'),
        'after_label'          => trim($_POST['after_label'] ?? 'আজীর কবলন'),
        'hero_benefit_text'    => trim($_POST['hero_benefit_text'] ?? ''),
        'features_title'       => trim($_POST['features_title'] ?? ''),
        'feature_1_text'       => trim($_POST['feature_1_text'] ?? ''),
        'feature_2_text'       => trim($_POST['feature_2_text'] ?? ''),
        'feature_3_text'       => trim($_POST['feature_3_text'] ?? ''),
        'feature_4_text'       => trim($_POST['feature_4_text'] ?? ''),
        'reviews_title'        => trim($_POST['reviews_title'] ?? 'আমাদের কাষ্টমার রিভিউ'),
        'order_title'          => trim($_POST['order_title'] ?? ''),
        'order_subtitle'       => trim($_POST['order_subtitle'] ?? ''),
        'order_benefit_1'      => trim($_POST['order_benefit_1'] ?? ''),
        'order_benefit_2'      => trim($_POST['order_benefit_2'] ?? ''),
        'contact_title'        => trim($_POST['contact_title'] ?? ''),
        'contact_subtitle'     => trim($_POST['contact_subtitle'] ?? ''),
        'whatsapp_number'      => trim($_POST['whatsapp_number'] ?? ''),
        'whatsapp_link'        => trim($_POST['whatsapp_link'] ?? ''),
        'messenger_link'       => trim($_POST['messenger_link'] ?? ''),
        'checkout_title'       => trim($_POST['checkout_title'] ?? ''),
        'checkout_subtitle'    => trim($_POST['checkout_subtitle'] ?? ''),
        'shipping_dhaka_cost'  => (float)($_POST['shipping_dhaka_cost'] ?? 70),
        'shipping_urban_cost'  => (float)($_POST['shipping_urban_cost'] ?? 100),
        'shipping_outside_cost' => (float)($_POST['shipping_outside_cost'] ?? 130),
        'privacy_text'         => trim($_POST['privacy_text'] ?? ''),
        'brand_name'           => trim($_POST['brand_name'] ?? '')
    ];

    // ----- 4.4 Prepare $files array from $_FILES -----
    $files = [
        'brand_logo'              => $_FILES['brand_logo'] ?? null,
        'hero_comparison_image'   => $_FILES['hero_comparison_image'] ?? null,
        'product_showcase_image'  => $_FILES['product_showcase_image'] ?? null,
        'review_images'           => $_FILES['review_images'] ?? null,
        'product_slider_images'   => $_FILES['product_slider_images'] ?? null
    ];

    // ----- 4.5 Call update function -----
    $result = updateLandingPageTemplate($template_id, $data, $files);

    if ($result['success']) {
        $_SESSION['message'] = 'Template updated successfully!';
        $_SESSION['message_type'] = 'success';
        // Reload template data to reflect changes (new images, etc.)
        $template = getLandingPageTemplate($template_id);
        $template_images = getTemplateImages($template_id);
        $images_by_section = [];
        foreach ($template_images as $img) {
            $images_by_section[$img['section']][] = $img;
        }
    } else {
        $_SESSION['message'] = 'Update failed: ' . $result['message'];
        $_SESSION['message_type'] = 'danger';
    }


    echo "<script>window.location.href = 'edit-landing-template.php?id=$template_id';</script>"; // fallback for non-JS
    exit;
}

// ------------------------------------------------------------------
// 5. DISPLAY SESSION MESSAGES (if any)
// ------------------------------------------------------------------
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<!-- ============================================================== -->
<!-- HTML FORM (same styling as view page)                          -->
<!-- ============================================================== -->
<style>
    /* reuse all CSS from view-landing-page.php – copy it here or include via external file */
    .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-radius: 10px; margin-bottom: 20px; }
    .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px 10px 0 0 !important; padding: 15px 20px; }
    .card-title { margin: 0; font-size: 1.1rem; font-weight: 600; color: #fff; }
    .page-title { font-size: 1.8rem; font-weight: 700; color: #333; margin-bottom: 30px; }
    .section-divider { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 20px; border-radius: 8px; margin: 30px 0 20px; font-weight: 700; font-size: 1.1rem; }
    .info-label { font-weight: 600; color: #555; margin-bottom: 5px; font-size: 0.9rem; }
    .form-control, .form-select { border-radius: 5px; border: 1px solid #ddd; padding: 10px; margin-bottom: 15px; }
    .btn-action { margin: 5px; }
    .image-gallery { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 10px; }
    .gallery-item { flex: 0 0 calc(33.333% - 10px); position: relative; }
    .gallery-image { width: 100%; height: 200px; object-fit: cover; border-radius: 8px; border: 2px solid #ddd; }
    .delete-checkbox { position: absolute; top: 10px; left: 10px; background: white; padding: 5px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    .current-single-image { max-width: 200px; margin-bottom: 10px; border: 2px solid #ddd; border-radius: 8px; padding: 5px; }
    @media (max-width: 768px) { .gallery-item { flex: 0 0 calc(50% - 8px); } }
</style>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Alert Messages -->
        <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
            <strong><?= $message_type === 'success' ? 'Success!' : 'Error!' ?></strong> <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="page-title">
                        <i class="fas fa-edit"></i> Edit Template: <?= htmlspecialchars($template['template_name']) ?>
                    </h1>
                    <div>
                        <a href="view-landing-page.php?template_id=<?= $template_id ?>" class="btn btn-info btn-action" target="_blank">
                            <i class="fas fa-eye"></i> Preview
                        </a>
                        <a href="manage-landing-templates.php" class="btn btn-secondary btn-action">
                            <i class="fas fa-list"></i> All Templates
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="template_id" value="<?= $template_id ?>">

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-9">
                    <!-- ========== BASIC INFORMATION ========== -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title text-white">Basic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">Template Name *</div>
                                    <input type="text" name="template_name" class="form-control" 
                                           value="<?= htmlspecialchars($template['template_name']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Product *</div>
                                    <select name="product_id" class="form-select" required>
                                        <option value="">-- Select Product --</option>
                                        <?php foreach ($products as $p): ?>
                                            <option value="<?= $p['id'] ?>" <?= $p['id'] == $template['product_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($p['product_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Brand Name</div>
                                    <input type="text" name="brand_name" class="form-control" 
                                           value="<?= htmlspecialchars($template['brand_name'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Status</div>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" 
                                               <?= $template['is_active'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="isActive">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========== SECTION 1: HERO ========== -->
                    <div class="section-divider"><span class="counter">1</span> Hero Section</div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="info-label">Hero Title</div>
                                    <input type="text" name="hero_title" class="form-control" 
                                           value="<?= htmlspecialchars($template['hero_title'] ?? '') ?>">
                                </div>
                                <div class="col-12">
                                    <div class="info-label">Hero Subtitle</div>
                                    <input type="text" name="hero_subtitle" class="form-control" 
                                           value="<?= htmlspecialchars($template['hero_subtitle'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Before Label</div>
                                    <input type="text" name="before_label" class="form-control" 
                                           value="<?= htmlspecialchars($template['before_label'] ?? 'আগের কবলন') ?>">
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">After Label</div>
                                    <input type="text" name="after_label" class="form-control" 
                                           value="<?= htmlspecialchars($template['after_label'] ?? 'আজীর কবলন') ?>">
                                </div>
                                <div class="col-12">
                                    <div class="info-label">Hero Benefit Text</div>
                                    <textarea name="hero_benefit_text" class="form-control" rows="3"><?= htmlspecialchars($template['hero_benefit_text'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <!-- Hero Comparison Image -->
                            <div class="mt-3">
                                <div class="info-label">Hero Comparison Image</div>
                                <?php if (!empty($template['hero_comparison_image'])): ?>
                                    <div class="current-single-image">
                                        <img src="../uploads/templates/<?= $template['hero_comparison_image'] ?>" class="img-fluid" style="max-height:150px">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="remove_hero_comparison" id="removeHeroComp">
                                            <label class="form-check-label text-danger" for="removeHeroComp">Remove current image</label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="hero_comparison_image" class="form-control" accept="image/*">
                                <small class="text-muted">Leave empty to keep current image (if not removed).</small>
                            </div>
                        </div>
                    </div>

                    <!-- ========== SECTION 2: FEATURES ========== -->
                    <div class="section-divider"><span class="counter">2</span> Features Section</div>
                    <div class="card">
                        <div class="card-body">
                            <div class="info-label">Features Section Title</div>
                            <input type="text" name="features_title" class="form-control" 
                                   value="<?= htmlspecialchars($template['features_title'] ?? '') ?>">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="info-label">Feature 1</div>
                                    <input type="text" name="feature_1_text" class="form-control" 
                                           value="<?= htmlspecialchars($template['feature_1_text'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Feature 2</div>
                                    <input type="text" name="feature_2_text" class="form-control" 
                                           value="<?= htmlspecialchars($template['feature_2_text'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Feature 3</div>
                                    <input type="text" name="feature_3_text" class="form-control" 
                                           value="<?= htmlspecialchars($template['feature_3_text'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Feature 4</div>
                                    <input type="text" name="feature_4_text" class="form-control" 
                                           value="<?= htmlspecialchars($template['feature_4_text'] ?? '') ?>">
                                </div>
                            </div>

                            <!-- Product Showcase Image -->
                            <div class="mt-3">
                                <div class="info-label">Product Showcase Image</div>
                                <?php if (!empty($template['product_showcase_image'])): ?>
                                    <div class="current-single-image">
                                        <img src="../uploads/templates/<?= $template['product_showcase_image'] ?>" class="img-fluid" style="max-height:150px">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="remove_product_showcase" id="removeShowcase">
                                            <label class="form-check-label text-danger" for="removeShowcase">Remove current image</label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="product_showcase_image" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <!-- ========== SECTION 3: REVIEWS ========== -->
                    <div class="section-divider"><span class="counter">3</span> Customer Reviews</div>
                    <div class="card">
                        <div class="card-body">
                            <div class="info-label">Reviews Section Title</div>
                            <input type="text" name="reviews_title" class="form-control" 
                                   value="<?= htmlspecialchars($template['reviews_title'] ?? 'আমাদের কাষ্টমার রিভিউ') ?>">

                            <!-- Existing Review Images with Delete Checkboxes -->
                            <?php if (!empty($images_by_section['review_slider'])): ?>
                                <div class="mt-3">
                                    <div class="info-label">Current Review Images (check to delete)</div>
                                    <div class="image-gallery">
                                        <?php foreach ($images_by_section['review_slider'] as $img): ?>
                                            <div class="gallery-item">
                                                <img src="../uploads/templates/<?= $img['image_path'] ?>" class="gallery-image">
                                                <div class="delete-checkbox">
                                                    <input type="checkbox" name="delete_review_images[]" value="<?= $img['id'] ?>" id="del_rev_<?= $img['id'] ?>">
                                                    <label for="del_rev_<?= $img['id'] ?>">Delete</label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Upload New Review Images -->
                            <div class="mt-3">
                                <div class="info-label">Add New Review Images</div>
                                <input type="file" name="review_images[]" class="form-control" multiple accept="image/*">
                                <small class="text-muted">You can select multiple images.</small>
                            </div>
                        </div>
                    </div>

                    <!-- ========== SECTION 4: ORDER / PRODUCT SELECTION ========== -->
                    <div class="section-divider"><span class="counter">4</span> Order Section</div>
                    <div class="card">
                        <div class="card-body">
                            <div class="info-label">Order Section Title</div>
                            <input type="text" name="order_title" class="form-control" 
                                   value="<?= htmlspecialchars($template['order_title'] ?? '') ?>">
                            <div class="info-label mt-3">Order Subtitle</div>
                            <textarea name="order_subtitle" class="form-control" rows="2"><?= htmlspecialchars($template['order_subtitle'] ?? '') ?></textarea>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="info-label">Order Benefit 1</div>
                                    <input type="text" name="order_benefit_1" class="form-control" 
                                           value="<?= htmlspecialchars($template['order_benefit_1'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Order Benefit 2</div>
                                    <input type="text" name="order_benefit_2" class="form-control" 
                                           value="<?= htmlspecialchars($template['order_benefit_2'] ?? '') ?>">
                                </div>
                            </div>

                            <!-- Product Slider Images -->
                            <?php if (!empty($images_by_section['product_slider'])): ?>
                                <div class="mt-3">
                                    <div class="info-label">Current Product Slider Images (check to delete)</div>
                                    <div class="image-gallery">
                                        <?php foreach ($images_by_section['product_slider'] as $img): ?>
                                            <div class="gallery-item">
                                                <img src="../uploads/templates/<?= $img['image_path'] ?>" class="gallery-image">
                                                <div class="delete-checkbox">
                                                    <input type="checkbox" name="delete_product_images[]" value="<?= $img['id'] ?>" id="del_prod_<?= $img['id'] ?>">
                                                    <label for="del_prod_<?= $img['id'] ?>">Delete</label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mt-3">
                                <div class="info-label">Add New Product Slider Images</div>
                                <input type="file" name="product_slider_images[]" class="form-control" multiple accept="image/*">
                            </div>
                        </div>
                    </div>

                    <!-- ========== SECTION 5: CONTACT ========== -->
                    <div class="section-divider"><span class="counter">5</span> Contact Section</div>
                    <div class="card">
                        <div class="card-body">
                            <div class="info-label">Contact Title</div>
                            <input type="text" name="contact_title" class="form-control" 
                                   value="<?= htmlspecialchars($template['contact_title'] ?? '') ?>">
                            <div class="info-label mt-3">Contact Subtitle</div>
                            <textarea name="contact_subtitle" class="form-control" rows="2"><?= htmlspecialchars($template['contact_subtitle'] ?? '') ?></textarea>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="info-label">WhatsApp Number</div>
                                    <input type="text" name="whatsapp_number" class="form-control" 
                                           value="<?= htmlspecialchars($template['whatsapp_number'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">WhatsApp Link</div>
                                    <input type="text" name="whatsapp_link" class="form-control" 
                                           value="<?= htmlspecialchars($template['whatsapp_link'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Messenger Link</div>
                                    <input type="text" name="messenger_link" class="form-control" 
                                           value="<?= htmlspecialchars($template['messenger_link'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========== SECTION 6: CHECKOUT ========== -->
                    <div class="section-divider"><span class="counter">6</span> Checkout Section</div>
                    <div class="card">
                        <div class="card-body">
                            <div class="info-label">Checkout Title</div>
                            <input type="text" name="checkout_title" class="form-control" 
                                   value="<?= htmlspecialchars($template['checkout_title'] ?? '') ?>">
                            <div class="info-label mt-3">Checkout Subtitle</div>
                            <textarea name="checkout_subtitle" class="form-control" rows="2"><?= htmlspecialchars($template['checkout_subtitle'] ?? '') ?></textarea>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="info-label">Shipping Cost – Dhaka</div>
                                    <input type="number" step="0.01" name="shipping_dhaka_cost" class="form-control" 
                                           value="<?= $template['shipping_dhaka_cost'] ?? 70 ?>">
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Shipping Cost – Urban</div>
                                    <input type="number" step="0.01" name="shipping_urban_cost" class="form-control" 
                                           value="<?= $template['shipping_urban_cost'] ?? 100 ?>">
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Shipping Cost – Outside</div>
                                    <input type="number" step="0.01" name="shipping_outside_cost" class="form-control" 
                                           value="<?= $template['shipping_outside_cost'] ?? 130 ?>">
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="info-label">Privacy Policy Text</div>
                                <textarea name="privacy_text" class="form-control" rows="4"><?= htmlspecialchars($template['privacy_text'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Brand Logo (separate, often in footer) -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title text-white">Brand Logo</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($template['brand_logo'])): ?>
                                <div class="current-single-image">
                                    <img src="../uploads/templates/<?= $template['brand_logo'] ?>" style="max-width:200px; max-height:100px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_brand_logo" id="removeBrandLogo">
                                        <label class="form-check-label text-danger" for="removeBrandLogo">Remove current logo</label>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="brand_logo" class="form-control" accept="image/*">
                        </div>
                    </div>

                </div> <!-- col-lg-9 -->

                <!-- ========== SIDEBAR ========== -->
                <div class="col-lg-3">
                    <div class="card sticky-top" style="top: 20px;">
                        <div class="card-header">
                            <h5 class="card-title text-white">Actions</h5>
                        </div>
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-save"></i> Update Template
                            </button>
                            <a href="view-landing-page.php?template_id=<?= $template_id ?>" class="btn btn-info w-100 mb-2" target="_blank">
                                <i class="fas fa-eye"></i> Preview
                            </a>
                            <a href="manage-landing-templates.php" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </div>

                    <!-- Template Info Card -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title text-white">Template Info</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2"><small>ID</small><br><strong>#<?= $template['id'] ?></strong></div>
                            <div class="mb-2"><small>Created</small><br><?= date('M d, Y H:i', strtotime($template['created_at'])) ?></div>
                            <div class="mb-2"><small>Last Updated</small><br><?= $template['updated_at'] ? date('M d, Y H:i', strtotime($template['updated_at'])) : 'Never' ?></div>
                        </div>
                    </div>
                </div> <!-- col-lg-3 -->
            </div> <!-- row -->
        </form>
    </div> <!-- container-fluid -->
</div> <!-- content-wrapper -->

<?php require_once './components/footer.php'; ?>