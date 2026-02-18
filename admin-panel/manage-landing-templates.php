<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Manage Landing Templates';

require_once './components/header.php';

$message = '';
$message_type = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_template'])) {
        $result = deleteTemplate($_POST['template_id']);
        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'danger';
    } elseif (isset($_POST['toggle_status'])) {
        $current_status = (int) $_POST['current_status'];
        $new_status = $current_status ? 0 : 1;
        $result = updateTemplateStatus($_POST['template_id'], $new_status);
        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'danger';
    }
}

// Get all templates
$templates = getAllTemplates();
?>

<style>
    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
    }
    
    .template-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        position: relative;
        transition: all 0.3s;
    }
    
    .template-card:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        transform: translateY(-3px);
    }
    
    .template-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 15px;
    }
    
    .template-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .product-name {
        color: #667eea;
        font-weight: 600;
        font-size: 0.95rem;
    }
    
    .status-badge {
        padding: 6px 15px;
        border-radius: 20px;
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
    
    .template-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 10px;
        margin: 15px 0;
    }
    
    .preview-img {
        width: 100%;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
        border: 2px solid #e0e0e0;
    }
    
    .template-info {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin: 15px 0;
        padding: 15px;
        background: #f5f5f5;
        border-radius: 8px;
    }
    
    .info-item {
        flex: 1;
        min-width: 150px;
    }
    
    .info-label {
        font-size: 12px;
        color: #666;
        margin-bottom: 3px;
    }
    
    .info-value {
        font-weight: 600;
        color: #333;
    }
    
    .template-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .action-btn {
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .btn-view {
        background: #2196f3;
        color: white;
    }
    
    .btn-view:hover {
        background: #1976d2;
        color: white;
    }
    
    .btn-edit {
        background: #ff9800;
        color: white;
    }
    
    .btn-edit:hover {
        background: #f57c00;
        color: white;
    }
    
    .btn-toggle {
        background: #9c27b0;
        color: white;
    }
    
    .btn-toggle:hover {
        background: #7b1fa2;
    }
    
    .btn-delete {
        background: #f44336;
        color: white;
    }
    
    .btn-delete:hover {
        background: #d32f2f;
    }
    
    .empty-state {
        text-align: center;
        padding: 80px 20px;
    }
    
    .empty-state i {
        font-size: 5rem;
        color: #ccc;
        margin-bottom: 20px;
    }
    
    .stats-row {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }
    
    .stat-card {
        flex: 1;
        min-width: 200px;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        font-size: 2rem;
        margin-bottom: 10px;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #667eea;
    }
    
    .stat-label {
        color: #666;
        font-size: 0.9rem;
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
                        <i class="fas fa-file-code"></i> Landing Page Templates
                    </h1>
                    <a href="create-landing-template.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Template
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon text-primary">
                    <i class="fas fa-file-code"></i>
                </div>
                <div class="stat-number"><?php echo count($templates); ?></div>
                <div class="stat-label">Total Templates</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number">
                    <?php echo count(array_filter($templates, fn($t) => $t['is_active'])); ?>
                </div>
                <div class="stat-label">Active Templates</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon text-warning">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-number">
                    <?php echo count(array_unique(array_column($templates, 'product_id'))); ?>
                </div>
                <div class="stat-label">Products with Templates</div>
            </div>
        </div>

        <!-- Templates List -->
        <?php if (empty($templates)): ?>
            <div class="empty-state">
                <i class="fas fa-file-code"></i>
                <h3>No Templates Found</h3>
                <p>Create your first landing page template to get started.</p>
                <a href="create-landing-template.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus"></i> Create First Template
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($templates as $template): ?>
                <div class="template-card">
                    <div class="template-header">
                        <div>
                            <div class="template-title">
                                <?php echo htmlspecialchars($template['template_name']); ?>
                            </div>
                            <div class="product-name">
                                <i class="fas fa-box"></i> <?php echo htmlspecialchars($template['product_name']); ?>
                            </div>
                        </div>
                        <span class="status-badge status-<?php echo $template['is_active'] ? 'active' : 'inactive'; ?>">
                            <?php echo $template['is_active'] ? 'ACTIVE' : 'INACTIVE'; ?>
                        </span>
                    </div>
                    
                    <div class="template-info">
                        <div class="info-item">
                            <div class="info-label">Hero Title</div>
                            <div class="info-value">
                                <?php echo $template['hero_title'] ? mb_substr($template['hero_title'], 0, 30) . '...' : '—'; ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Features Title</div>
                            <div class="info-value">
                                <?php echo $template['features_title'] ? mb_substr($template['features_title'], 0, 30) . '...' : '—'; ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Contact</div>
                            <div class="info-value">
                                <?php echo $template['whatsapp_number'] ?: '—'; ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Created</div>
                            <div class="info-value">
                                <?php echo date('M d, Y', strtotime($template['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php
                    // Get template images
                    $temp_data = getTemplateById($template['id']);
                    $has_images = false;
                    $preview_images = [];
                    
                    if (!empty($template['brand_logo'])) {
                        $preview_images[] = $template['brand_logo'];
                        $has_images = true;
                    }
                    if (!empty($template['hero_comparison_image'])) {
                        $preview_images[] = $template['hero_comparison_image'];
                        $has_images = true;
                    }
                    if (!empty($template['product_showcase_image'])) {
                        $preview_images[] = $template['product_showcase_image'];
                        $has_images = true;
                    }
                    
                    if (isset($temp_data['images']['review_slider'])) {
                        foreach (array_slice($temp_data['images']['review_slider'], 0, 2) as $img) {
                            $preview_images[] = $img['image_path'];
                            $has_images = true;
                        }
                    }
                    ?>
                    
                    <?php if ($has_images): ?>
                        <div class="template-preview">
                            <?php foreach (array_slice($preview_images, 0, 6) as $img): ?>
                                <img src="../uploads/templates/<?php echo $img; ?>" 
                                     alt="Preview" class="preview-img">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="template-actions">
                        <a href="view-landing-page.php?template_id=<?php echo $template['id']; ?>" 
                           target="_blank" class="action-btn btn-view">
                            <i class="fas fa-eye"></i> Preview
                        </a>
                        
                        <a href="edit-landing-template.php?id=<?php echo $template['id']; ?>" 
                           class="action-btn btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="template_id" value="<?php echo $template['id']; ?>">
                            <input type="hidden" name="current_status" value="<?php echo $template['is_active']; ?>">
                            <button type="submit" name="toggle_status" class="action-btn btn-toggle">
                                <i class="fas fa-power-off"></i> 
                                <?php echo $template['is_active'] ? 'Deactivate' : 'Activate'; ?>
                            </button>
                        </form>
                        
                        <form method="POST" style="display: inline;" 
                              onsubmit="return confirm('Are you sure you want to delete this template? This action cannot be undone.');">
                            <input type="hidden" name="template_id" value="<?php echo $template['id']; ?>">
                            <button type="submit" name="delete_template" class="action-btn btn-delete">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require './components/footer.php'; ?>