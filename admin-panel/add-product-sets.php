<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Add Product Sets';

require_once './components/header.php';

$message = '';
$message_type = '';
$selected_product_id = $_GET['product_id'] ?? '';

// Get all products for dropdown
$products = getAllProducts();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['bulk_create'])) {
        // Bulk create sets
        $product_id = (int) $_POST['product_id'];
        $sets_data = [];

        if (!empty($_POST['set_pieces'])) {
            foreach ($_POST['set_pieces'] as $index => $pieces) {
                if (!empty($pieces) && !empty($_POST['set_unit_price'][$index])) {
                    $sets_data[] = [
                        'set_name' => $_POST['set_name'][$index] ?? '',
                        'pieces_count' => $pieces,
                        'unit_price' => $_POST['set_unit_price'][$index],
                        'stock_quantity' => $_POST['set_stock'][$index] ?? 0,
                        'is_special_offer' => isset($_POST['set_special'][$index]) ? 1 : 0
                    ];
                }
            }
        }

        $result = bulkCreateProductSets($product_id, $sets_data);
        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'danger';

        if ($result['success']) {
            echo '<script>setTimeout(function(){ window.location.href = "manage-product-sets.php?product_id=' . $product_id . '"; }, 2000);</script>';
        }
    }
}
?>

<style>
    .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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

    .set-item {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid #667eea;
        position: relative;
    }

    .set-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .set-number {
        background: #667eea;
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
    }

    .remove-set-btn {
        background: #dc3545;
        border: none;
        color: white;
        padding: 6px 12px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .remove-set-btn:hover {
        background: #c82333;
    }

    .special-offer-badge {
        background: #ff5722;
        color: white;
        padding: 4px 12px;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        margin-left: 10px;
    }

    .calculated-price {
        background: #e3f2fd;
        padding: 10px 15px;
        border-radius: 5px;
        margin-top: 10px;
        font-weight: 600;
        color: #1976d2;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .product-preview {
        background: white;
        padding: 15px;
        border-radius: 8px;
        border: 2px solid #e0e0e0;
        margin-bottom: 20px;
    }

    .product-preview img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 15px;
    }

    .product-info h5 {
        color: #333;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .product-info p {
        color: #666;
        margin: 0;
        font-size: 14px;
    }

    .add-set-btn {
        background: white;
        border: 2px dashed #667eea;
        color: #667eea;
        padding: 12px 25px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s;
    }

    .add-set-btn:hover {
        background: #667eea;
        color: white;
    }

    .quick-templates {
        background: #fff3cd;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .template-btn {
        background: white;
        border: 1px solid #ffc107;
        color: #856404;
        padding: 8px 15px;
        border-radius: 5px;
        margin: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .template-btn:hover {
        background: #ffc107;
        color: white;
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
                    <div class="page-title-section mt-3">
                        <div class="icon-box">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <h1> Add Product Sets</h1>
                    </div>

                    <a href="manage-product-sets.php" class="btn btn-secondary">
                        <i class="fas fa-list"></i> View All Sets
                    </a>
                </div>
            </div>
        </div>

        <!-- Product Sets Form -->
        <form method="POST" id="productSetsForm">
            <input type="hidden" name="bulk_create" value="1">

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Select Product -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title text-white">Select Product</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Choose Product <span class="required">*</span></label>
                                <select class="form-select" name="product_id" id="productSelect" required onchange="loadProductPreview()">
                                    <option value="">-- Select a Product --</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                                            data-price="<?php echo $product['offer_price']; ?>"
                                            data-image="<?php echo $product['main_image']; ?>"
                                            <?php echo $selected_product_id == $product['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($product['product_name']); ?>
                                            (৳<?php echo number_format($product['offer_price'], 2); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Product Preview -->
                            <div id="productPreview" style="display: none;" class="product-preview">
                                <div class="d-flex align-items-center">
                                    <img id="previewImage" src="" alt="Product">
                                    <div class="product-info">
                                        <h5 id="previewName"></h5>
                                        <p>Unit Price: <strong>৳<span id="previewPrice"></span></strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Templates -->
                    <div class="quick-templates">
                        <strong><i class="fas fa-magic"></i> Quick Templates:</strong>
                        <button type="button" class="template-btn" onclick="applyTemplate('basic')">
                            Basic (1, 2, 3 pcs)
                        </button>
                        <button type="button" class="template-btn" onclick="applyTemplate('standard')">
                            Standard (1, 2, 3, 6 pcs)
                        </button>
                        <button type="button" class="template-btn" onclick="applyTemplate('combo')">
                            Combo (2, 4, 6, 12 pcs)
                        </button>
                    </div>

                    <!-- Product Sets -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title text-white mb-0">Configure Sets</h5>
                            <button type="button" class="btn btn-sm btn-light text-dark" onclick="addSet()">
                                <i class="fas fa-plus"></i> Add Set
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="setsContainer">
                                <!-- Sets will be added here -->
                            </div>

                            <button type="button" class="add-set-btn" onclick="addSet()">
                                <i class="fas fa-plus-circle"></i> Add Another Set
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Instructions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title text-white">Instructions</h5>
                        </div>
                        <div class="card-body">
                            <ol style="padding-left: 20px; line-height: 1.8;">
                                <li>Select the product you want to create sets for</li>
                                <li>Add different set configurations (1pc, 2pcs, 3pcs, etc.)</li>
                                <li>Set the unit price for each piece in the set</li>
                                <li>Total price will be calculated automatically</li>
                                <li>Mark special offers for discounted bundles</li>
                                <li>Save to create all sets at once</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title text-white">Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Sets:</span>
                                <strong id="totalSets">0</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Special Offers:</span>
                                <strong id="specialOffers">0</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Total Stock:</span>
                                <strong id="totalStock">0</strong>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-save"></i> Create Sets
                            </button>
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="resetForm()">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let setCounter = 0;

    // Load product preview
    function loadProductPreview() {
        const select = document.getElementById('productSelect');
        const option = select.options[select.selectedIndex];

        if (option.value) {
            document.getElementById('productPreview').style.display = 'block';
            document.getElementById('previewName').textContent = option.dataset.name;
            document.getElementById('previewPrice').textContent = parseFloat(option.dataset.price).toFixed(2);

            const imagePath = option.dataset.image ? '../uploads/products/' + option.dataset.image : 'https://via.placeholder.com/80';
            document.getElementById('previewImage').src = imagePath;
        } else {
            document.getElementById('productPreview').style.display = 'none';
        }
    }

    // Add new set
    function addSet(pieces = '', unitPrice = '', stock = '', isSpecial = false, setName = '') {
        const select = document.getElementById('productSelect');
        if (!select.value) {
            alert('Please select a product first!');
            select.focus();
            return;
        }

        setCounter++;
        const container = document.getElementById('setsContainer');

        const defaultPrice = select.options[select.selectedIndex].dataset.price;
        unitPrice = unitPrice || defaultPrice;

        const setHTML = `
        <div class="set-item" id="set-${setCounter}">
            <div class="set-item-header">
                <div class="set-number">${setCounter}</div>
                <button type="button" class="remove-set-btn" onclick="removeSet(${setCounter})">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Set Name</label>
                    <input type="text" class="form-control" name="set_name[]" 
                           value="${setName}" placeholder="e.g., Single Pack">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Pieces in Set <span class="required">*</span></label>
                    <input type="number" class="form-control pieces-input" name="set_pieces[]" 
                           value="${pieces}" min="1" required onchange="calculatePrice(${setCounter})" 
                           placeholder="e.g., 1">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Unit Price (per piece) <span class="required">*</span></label>
                    <input type="number" class="form-control unit-price-input" name="set_unit_price[]" 
                           value="${unitPrice}" step="0.01" min="0" required 
                           onchange="calculatePrice(${setCounter})" placeholder="e.g., 290">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" class="form-control stock-input" name="set_stock[]" 
                           value="${stock}" min="0" onchange="updateSummary()" placeholder="e.g., 100">
                </div>
                
                <div class="col-12 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input special-input" type="checkbox" 
                               name="set_special[${setCounter}]" ${isSpecial ? 'checked' : ''} 
                               onchange="updateSummary()">
                        <label class="form-check-label">
                            Mark as Special Offer <span class="special-offer-badge">HOT DEAL</span>
                        </label>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="calculated-price" id="price-${setCounter}">
                        Total Price: ৳0.00
                    </div>
                </div>
            </div>
        </div>
    `;

        container.insertAdjacentHTML('beforeend', setHTML);
        calculatePrice(setCounter);
        updateSummary();
    }

    // Remove set
    function removeSet(id) {
        if (confirm('Are you sure you want to remove this set?')) {
            document.getElementById('set-' + id).remove();
            updateSummary();
        }
    }

    // Calculate total price for a set
    function calculatePrice(setId) {
        const setElement = document.getElementById('set-' + setId);
        const pieces = parseFloat(setElement.querySelector('.pieces-input').value) || 0;
        const unitPrice = parseFloat(setElement.querySelector('.unit-price-input').value) || 0;
        const totalPrice = pieces * unitPrice;

        document.getElementById('price-' + setId).textContent =
            `Total Price: ৳${totalPrice.toFixed(2)} (${pieces} × ৳${unitPrice.toFixed(2)})`;
    }

    // Update summary
    function updateSummary() {
        const sets = document.querySelectorAll('.set-item');
        let totalSets = sets.length;
        let specialOffers = 0;
        let totalStock = 0;

        sets.forEach(set => {
            if (set.querySelector('.special-input')?.checked) {
                specialOffers++;
            }
            totalStock += parseInt(set.querySelector('.stock-input')?.value || 0);
        });

        document.getElementById('totalSets').textContent = totalSets;
        document.getElementById('specialOffers').textContent = specialOffers;
        document.getElementById('totalStock').textContent = totalStock;
    }

    // Apply template
    function applyTemplate(type) {
        const select = document.getElementById('productSelect');
        if (!select.value) {
            alert('Please select a product first!');
            select.focus();
            return;
        }

        if (document.querySelectorAll('.set-item').length > 0) {
            if (!confirm('This will clear existing sets. Continue?')) {
                return;
            }
        }

        document.getElementById('setsContainer').innerHTML = '';
        setCounter = 0;

        const basePrice = parseFloat(select.options[select.selectedIndex].dataset.price);

        if (type === 'basic') {
            addSet(1, basePrice, 100, false, '1 Piece');
            addSet(2, basePrice, 80, false, '2 Pieces');
            addSet(3, basePrice, 60, false, '3 Pieces');
        } else if (type === 'standard') {
            addSet(1, basePrice, 100, false, '1 Piece');
            addSet(2, basePrice, 80, false, '2 Pieces');
            addSet(3, basePrice, 60, false, '3 Pieces');
            addSet(6, basePrice * 0.85, 40, true, '6 Pieces Bundle');
        } else if (type === 'combo') {
            addSet(2, basePrice, 80, false, '2 Pieces Combo');
            addSet(4, basePrice * 0.9, 60, false, '4 Pieces Combo');
            addSet(6, basePrice * 0.85, 40, true, '6 Pieces Special');
            addSet(12, basePrice * 0.8, 20, true, '12 Pieces Mega Deal');
        }
    }

    // Reset form
    function resetForm() {
        if (confirm('Are you sure you want to reset the form?')) {
            document.getElementById('productSetsForm').reset();
            document.getElementById('setsContainer').innerHTML = '';
            document.getElementById('productPreview').style.display = 'none';
            setCounter = 0;
            updateSummary();
        }
    }

    // Form validation
    document.getElementById('productSetsForm').addEventListener('submit', function(e) {
        const sets = document.querySelectorAll('.set-item');

        if (sets.length === 0) {
            e.preventDefault();
            alert('Please add at least one product set!');
            return false;
        }

        let hasError = false;
        sets.forEach(set => {
            const pieces = set.querySelector('.pieces-input').value;
            const unitPrice = set.querySelector('.unit-price-input').value;

            if (!pieces || !unitPrice || pieces <= 0 || unitPrice <= 0) {
                hasError = true;
            }
        });

        if (hasError) {
            e.preventDefault();
            alert('Please fill in all required fields with valid values!');
            return false;
        }
    });

    // Load product preview on page load if product is selected
    window.addEventListener('load', function() {
        loadProductPreview();
        if (document.getElementById('productSelect').value) {
            addSet(1, '', 100, false, '1 Piece');
        }
    });
</script>

<?php require './components/footer.php'; ?>