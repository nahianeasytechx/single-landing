<?php
    function getDatabaseConnection() {
    // $host     = 'localhost';
    // $username = 'techytor';        // ← prefixed with cPanel username
    // $password = 'n76DXUiw:d01(S';
    // $database = 'techytor_single_landing';

    $host     = 'localhost';
    $username = 'root';        
    $password = '';
    $database = 'single_landing';

    
    $conn = new mysqli($host, $username, $password, $database);
    $conn->set_charset("utf8mb4");  // ← ADD THIS LINE
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        exit;
    }
    
    return $conn;
}

function authenticateUser($username, $password)
{
    $conn = getDatabaseConnection();

    if (!$conn) {
        return [
            'success' => false,
            'message' => 'Database connection error.'
        ];
    }

    $stmt = $conn->prepare("SELECT id, username, password_hash, created_at 
                           FROM users 
                           WHERE username = ?");

    if (!$stmt) {
        $conn->close();
        return [
            'success' => false,
            'message' => 'System error.'
        ];
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            $stmt->close();
            $conn->close();

            unset($user['password_hash']);

            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => $user
            ];
        }
    }

    $stmt->close();
    $conn->close();
$password = "admin123";

// Hash the password using BCRYPT
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Store $hashedPassword in the database
echo $hashedPassword;

    return [
        'success' => false,
        'message' => 'Invalid username or password'
    ];
}

/**
 * Register a new user
 */


/**
 * Check if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Logout user
 */
function logoutUser()
{
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

/**
 * Get current logged-in user data
 */
function getCurrentUser()
{
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username']
    ];
}

/**
 * Protect page - redirect to login if not authenticated
 */
function protectPage()
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

/**
 * Change user password
 */
function changePassword($user_id, $old_password, $new_password)
{
    $conn = getDatabaseConnection();

    if (!$conn) {
        return [
            'success' => false,
            'message' => 'Database connection error'
        ];
    }

    // Verify old password
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        $stmt->close();
        $conn->close();
        return [
            'success' => false,
            'message' => 'User not found'
        ];
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    if (!password_verify($old_password, $user['password_hash'])) {
        $conn->close();
        return [
            'success' => false,
            'message' => 'Current password is incorrect'
        ];
    }

    if (strlen($new_password) < 6) {
        $conn->close();
        return [
            'success' => false,
            'message' => 'New password must be at least 6 characters long'
        ];
    }

    // Update password
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $stmt->bind_param("si", $new_hash, $user_id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return [
            'success' => true,
            'message' => 'Password changed successfully'
        ];
    }

    $stmt->close();
    $conn->close();
    return [
        'success' => false,
        'message' => 'Password change failed'
    ];
}




// Create product function
function createProduct($data, $files) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        // Validate required fields
        $required = ['product_name', 'regular_price', 'offer_price', 'quantity', 'stock_quantity'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
        }
        
        // Sanitize inputs
        $product_name = trim($data['product_name']);
        $description = trim($data['description'] ?? '');
        $regular_price = (float) $data['regular_price'];
        $offer_price = (float) $data['offer_price'];
        $quantity = (int) $data['quantity'];
        $stock_quantity = (int) $data['stock_quantity'];
        $status = $data['status'] ?? 'active';
        $is_featured = isset($data['is_featured']) ? 1 : 0;
        $is_special_offer = isset($data['is_special_offer']) ? 1 : 0;
        $category = $data['category'] ?? '';
        $tags = trim($data['tags'] ?? '');
        
        // Validate prices
        if ($offer_price > $regular_price) {
            throw new Exception('Offer price cannot be greater than regular price');
        }
        
        // Check if images are uploaded
        if (empty($files['product_images']['name'][0])) {
            throw new Exception('At least one product image is required');
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        // Insert product
        $stmt = $conn->prepare("
            INSERT INTO products 
            (product_name, description, regular_price, offer_price, quantity, stock_quantity,
             status, is_featured, is_special_offer, category, tags, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->bind_param(
            "ssddiisiiis",
            $product_name,
            $description,
            $regular_price,
            $offer_price,
            $quantity,
            $stock_quantity,
            $status,
            $is_featured,
            $is_special_offer,
            $category,
            $tags
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert product');
        }
        
        $product_id = $conn->insert_id;
        $stmt->close();
        
        // Handle image uploads
        $upload_dir = '../uploads/products/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $uploaded_images = [];
        foreach ($files['product_images']['name'] as $key => $name) {
            if ($files['product_images']['error'][$key] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                
                if (!in_array($ext, $allowed)) {
                    throw new Exception('Invalid image type. Only JPG, PNG, WEBP, GIF allowed');
                }
                
                $filename = 'product_' . $product_id . '_' . uniqid() . '.' . $ext;
                $path = $upload_dir . $filename;
                
                if (move_uploaded_file($files['product_images']['tmp_name'][$key], $path)) {
                    $is_main = ($key === 0) ? 1 : 0;
                    
                    $img_stmt = $conn->prepare("
                        INSERT INTO product_images (product_id, image_path, is_main, display_order)
                        VALUES (?, ?, ?, ?)
                    ");
                    $img_stmt->bind_param("isii", $product_id, $filename, $is_main, $key);
                    $img_stmt->execute();
                    $img_stmt->close();
                    
                    $uploaded_images[] = $filename;
                }
            }
        }
        
        if (empty($uploaded_images)) {
            throw new Exception('No images were uploaded successfully');
        }
        
        // Handle color options if provided
        if (!empty($data['color_name'])) {
            foreach ($data['color_name'] as $index => $color_name) {
                if (!empty($color_name)) {
                    $color_code = $data['color_code'][$index] ?? '#000000';
                    $color_stock = (int) ($data['color_stock'][$index] ?? 0);
                    
                    $color_stmt = $conn->prepare("
                        INSERT INTO product_colors (product_id, color_name, color_code, stock)
                        VALUES (?, ?, ?, ?)
                    ");
                    $color_stmt->bind_param("issi", $product_id, $color_name, $color_code, $color_stock);
                    $color_stmt->execute();
                    $color_stmt->close();
                }
            }
        }
        
        // Commit transaction
        $conn->commit();
        $conn->close();
        
        return [
            'success' => true,
            'message' => 'Product created successfully',
            'product_id' => $product_id
        ];
        
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Get all products
function getAllProducts() {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return [];
    }
    
    $query = "SELECT p.*, 
              (SELECT image_path FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
              FROM products p
              ORDER BY p.created_at DESC";
    
    $result = $conn->query($query);
    $products = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    $conn->close();
    return $products;
}

// Get product by ID
function getProductById($id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return null;
    }
    
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $product = $result->fetch_assoc();
    $stmt->close();
    
    if ($product) {
        // Get images
        $img_stmt = $conn->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order");
        $img_stmt->bind_param("i", $id);
        $img_stmt->execute();
        $img_result = $img_stmt->get_result();
        $product['images'] = $img_result->fetch_all(MYSQLI_ASSOC);
        $img_stmt->close();
        
        // Get colors
        $color_stmt = $conn->prepare("SELECT * FROM product_colors WHERE product_id = ?");
        $color_stmt->bind_param("i", $id);
        $color_stmt->execute();
        $color_result = $color_stmt->get_result();
        $product['colors'] = $color_result->fetch_all(MYSQLI_ASSOC);
        $color_stmt->close();
    }
    
    $conn->close();
    return $product;
}

// Delete product
function deleteProduct($id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        // Get product images to delete from filesystem
        $stmt = $conn->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $images = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        // Delete images from filesystem
        foreach ($images as $img) {
            $file_path = '../uploads/products/' . $img['image_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        // Delete child records first
        $tables = ['product_images', 'product_colors', 'product_sets'];
        foreach ($tables as $table) {
            $del = $conn->prepare("DELETE FROM `$table` WHERE product_id = ?");
            $del->bind_param("i", $id);
            $del->execute();
            $del->close();
        }

        // Nullify order_items instead of deleting (preserve order history)
        $del = $conn->prepare("UPDATE order_items SET product_id = NULL WHERE product_id = ?");
        $del->bind_param("i", $id);
        $del->execute();
        $del->close();

        // Delete landing page templates linked to this product
        $del = $conn->prepare("DELETE FROM landing_page_templates WHERE product_id = ?");
        $del->bind_param("i", $id);
        $del->execute();
        $del->close();
        
        // Now delete the product
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Product deleted successfully'];
        } else {
            throw new Exception('Failed to delete product: ' . $stmt->error);
        }
        
    } catch (Exception $e) {
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
/**
 * Update product
 */
function updateProduct($product_id, $data, $files) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        // Validate required fields
        $required = ['product_name', 'regular_price', 'offer_price', 'quantity', 'stock_quantity'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        // Sanitize inputs
        $product_name = trim($data['product_name']);
        $description = trim($data['description'] ?? '');
        $regular_price = (float) $data['regular_price'];
        $offer_price = (float) $data['offer_price'];
        $quantity = (int) $data['quantity'];
        $stock_quantity = (int) $data['stock_quantity'];
        $status = $data['status'] ?? 'active';
        $is_featured = isset($data['is_featured']) ? 1 : 0;
        $is_special_offer = isset($data['is_special_offer']) ? 1 : 0;
        $category = $data['category'] ?? '';
        $tags = trim($data['tags'] ?? '');
        
        // Validate prices
        if ($offer_price > $regular_price) {
            throw new Exception('Offer price cannot be greater than regular price');
        }
        
        // Update product
        $stmt = $conn->prepare("
            UPDATE products 
            SET product_name = ?, 
                description = ?, 
                regular_price = ?, 
                offer_price = ?, 
                quantity = ?, 
                stock_quantity = ?,
                status = ?, 
                is_featured = ?, 
                is_special_offer = ?, 
                category = ?, 
                tags = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->bind_param(
            "ssddiisiiisi",
            $product_name,
            $description,
            $regular_price,
            $offer_price,
            $quantity,
            $stock_quantity,
            $status,
            $is_featured,
            $is_special_offer,
            $category,
            $tags,
            $product_id
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update product: ' . $stmt->error);
        }
        
        $stmt->close();
        
        // Update main image if specified
        if (!empty($data['main_image'])) {
            // Reset all to not main
            $reset_stmt = $conn->prepare("UPDATE product_images SET is_main = 0 WHERE product_id = ?");
            $reset_stmt->bind_param("i", $product_id);
            $reset_stmt->execute();
            $reset_stmt->close();
            
            // Set new main image
            $main_stmt = $conn->prepare("UPDATE product_images SET is_main = 1 WHERE id = ? AND product_id = ?");
            $main_stmt->bind_param("ii", $data['main_image'], $product_id);
            $main_stmt->execute();
            $main_stmt->close();
        }
        
        // Handle new image uploads
        if (!empty($files['product_images']['name'][0])) {
            $upload_dir = '../uploads/products/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            foreach ($files['product_images']['name'] as $key => $name) {
                if ($files['product_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    
                    if (!in_array($ext, $allowed)) {
                        throw new Exception('Invalid image type. Only JPG, PNG, WEBP, GIF allowed');
                    }
                    
                    $filename = 'product_' . $product_id . '_' . uniqid() . '.' . $ext;
                    $path = $upload_dir . $filename;
                    
                    if (move_uploaded_file($files['product_images']['tmp_name'][$key], $path)) {
                        $is_main = ($key === 0 && empty($data['main_image'])) ? 1 : 0;
                        
                        // Get max display order
                        $order_stmt = $conn->prepare("SELECT COALESCE(MAX(display_order), 0) + 1 as next_order FROM product_images WHERE product_id = ?");
                        $order_stmt->bind_param("i", $product_id);
                        $order_stmt->execute();
                        $order_result = $order_stmt->get_result();
                        $order_row = $order_result->fetch_assoc();
                        $display_order = $order_row['next_order'];
                        $order_stmt->close();
                        
                        $img_stmt = $conn->prepare("
                            INSERT INTO product_images (product_id, image_path, is_main, display_order)
                            VALUES (?, ?, ?, ?)
                        ");
                        $img_stmt->bind_param("isii", $product_id, $filename, $is_main, $display_order);
                        $img_stmt->execute();
                        $img_stmt->close();
                    }
                }
            }
        }
        
        // Handle color options
        // First, delete existing colors
        $delete_colors_stmt = $conn->prepare("DELETE FROM product_colors WHERE product_id = ?");
        $delete_colors_stmt->bind_param("i", $product_id);
        $delete_colors_stmt->execute();
        $delete_colors_stmt->close();
        
        // Then add new colors if provided
        if (!empty($data['color_name'])) {
            foreach ($data['color_name'] as $index => $color_name) {
                if (!empty($color_name)) {
                    $color_code = $data['color_code'][$index] ?? '#000000';
                    $color_stock = (int) ($data['color_stock'][$index] ?? 0);
                    
                    $color_stmt = $conn->prepare("
                        INSERT INTO product_colors (product_id, color_name, color_code, stock)
                        VALUES (?, ?, ?, ?)
                    ");
                    $color_stmt->bind_param("issi", $product_id, $color_name, $color_code, $color_stock);
                    $color_stmt->execute();
                    $color_stmt->close();
                }
            }
        }
        
        // Commit transaction
        $conn->commit();
        $conn->close();
        
        return [
            'success' => true,
            'message' => 'Product updated successfully',
            'product_id' => $product_id
        ];
        
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
// Update product status
function updateProductStatus($id, $status) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    $stmt = $conn->prepare("UPDATE products SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return ['success' => true, 'message' => 'Status updated successfully'];
    }
    
    $stmt->close();
    $conn->close();
    return ['success' => false, 'message' => 'Failed to update status'];
}

// Get dashboard statistics
function getDashboardStats() {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return null;
    }
    
    $stats = [];
    
    // Total products
    $result = $conn->query("SELECT COUNT(*) as count FROM products");
    $stats['total_products'] = $result->fetch_assoc()['count'];
    
    // Active products
    $result = $conn->query("SELECT COUNT(*) as count FROM products WHERE status = 'active'");
    $stats['active_products'] = $result->fetch_assoc()['count'];
    
    // Low stock products (less than 10)
    $result = $conn->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity < 10");
    $stats['low_stock'] = $result->fetch_assoc()['count'];
    
    // Total orders (if orders table exists)
    $result = $conn->query("SELECT COUNT(*) as count FROM orders");
    if ($result) {
        $stats['total_orders'] = $result->fetch_assoc()['count'];
    } else {
        $stats['total_orders'] = 0;
    }
    
    $conn->close();
    return $stats;
}




/**
 * Create a new product set
 */
function createProductSet($data) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        // Validate required fields
        $required = ['product_id', 'set_name', 'pieces_count', 'unit_price'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
        }
        
        // Sanitize inputs
        $product_id = (int) $data['product_id'];
        $set_name = trim($data['set_name']);
        $pieces_count = (int) $data['pieces_count'];
        $unit_price = (float) $data['unit_price'];
        $total_price = $unit_price * $pieces_count;
        $stock_quantity = (int) ($data['stock_quantity'] ?? 0);
        $is_special_offer = isset($data['is_special_offer']) ? 1 : 0;
        $display_order = (int) ($data['display_order'] ?? 0);
        $status = $data['status'] ?? 'active';
        
        // Validate product exists
        $check_stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
        $check_stmt->bind_param("i", $product_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            throw new Exception('Product not found');
        }
        $check_stmt->close();
        
        // Insert product set
        $stmt = $conn->prepare("
            INSERT INTO product_sets 
            (product_id, set_name, pieces_count, unit_price, total_price, stock_quantity,
             is_special_offer, display_order, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param(
            "isiddiiis",
            $product_id,
            $set_name,
            $pieces_count,
            $unit_price,
            $total_price,
            $stock_quantity,
            $is_special_offer,
            $display_order,
            $status
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create product set');
        }
        
        $set_id = $conn->insert_id;
        $stmt->close();
        $conn->close();
        
        return [
            'success' => true,
            'message' => 'Product set created successfully',
            'set_id' => $set_id
        ];
        
    } catch (Exception $e) {
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Get all product sets for a specific product
 */
function getProductSets($product_id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return [];
    }
    
    $stmt = $conn->prepare("
        SELECT * FROM product_sets 
        WHERE product_id = ? 
        ORDER BY display_order ASC, pieces_count ASC
    ");
    
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sets = [];
    while ($row = $result->fetch_assoc()) {
        $sets[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $sets;
}

/**
 * Get all product sets with product information
 */
function getAllProductSets() {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return [];
    }
    
    $query = "
        SELECT 
            ps.*,
            p.product_name,
            p.status as product_status,
            (SELECT image_path FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as product_image
        FROM product_sets ps
        INNER JOIN products p ON ps.product_id = p.id
        ORDER BY p.product_name ASC, ps.display_order ASC
    ";
    
    $result = $conn->query($query);
    $sets = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $sets[] = $row;
        }
    }
    
    $conn->close();
    return $sets;
}

/**
 * Get product set by ID
 */
function getProductSetById($id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return null;
    }
    
    $stmt = $conn->prepare("
        SELECT ps.*, p.product_name 
        FROM product_sets ps
        INNER JOIN products p ON ps.product_id = p.id
        WHERE ps.id = ?
    ");
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $set = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    
    return $set;
}

/**
 * Update product set
 */
function updateProductSet($id, $data) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        // Sanitize inputs
        $set_name = trim($data['set_name']);
        $pieces_count = (int) $data['pieces_count'];
        $unit_price = (float) $data['unit_price'];
        $total_price = $unit_price * $pieces_count;
        $stock_quantity = (int) ($data['stock_quantity'] ?? 0);
        $is_special_offer = isset($data['is_special_offer']) ? 1 : 0;
        $display_order = (int) ($data['display_order'] ?? 0);
        $status = $data['status'] ?? 'active';
        
        $stmt = $conn->prepare("
            UPDATE product_sets 
            SET set_name = ?, pieces_count = ?, unit_price = ?, total_price = ?,
                stock_quantity = ?, is_special_offer = ?, display_order = ?, status = ?
            WHERE id = ?
        ");
        
        $stmt->bind_param(
            "siddiiiis",
            $set_name,
            $pieces_count,
            $unit_price,
            $total_price,
            $stock_quantity,
            $is_special_offer,
            $display_order,
            $status,
            $id
        );
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Product set updated successfully'];
        } else {
            throw new Exception('Failed to update product set');
        }
        
    } catch (Exception $e) {
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Delete product set
 */
function deleteProductSet($id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    $stmt = $conn->prepare("DELETE FROM product_sets WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return ['success' => true, 'message' => 'Product set deleted successfully'];
    }
    
    $stmt->close();
    $conn->close();
    return ['success' => false, 'message' => 'Failed to delete product set'];
}

/**
 * Update product set status
 */
function updateProductSetStatus($id, $status) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    $stmt = $conn->prepare("UPDATE product_sets SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return ['success' => true, 'message' => 'Status updated successfully'];
    }
    
    $stmt->close();
    $conn->close();
    return ['success' => false, 'message' => 'Failed to update status'];
}

/**
 * Bulk create product sets
 */
function bulkCreateProductSets($product_id, $sets_data) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        $conn->begin_transaction();
        
        $created_count = 0;
        
        foreach ($sets_data as $index => $set) {
            if (empty($set['pieces_count']) || empty($set['unit_price'])) {
                continue;
            }
            
            $set_name = trim($set['set_name'] ?? ($set['pieces_count'] . ' pcs'));
            $pieces_count = (int) $set['pieces_count'];
            $unit_price = (float) $set['unit_price'];
            $total_price = $unit_price * $pieces_count;
            $stock_quantity = (int) ($set['stock_quantity'] ?? 0);
            $is_special_offer = isset($set['is_special_offer']) ? 1 : 0;
            $display_order = $index;
            $status = 'active';
            
            $stmt = $conn->prepare("
                INSERT INTO product_sets 
                (product_id, set_name, pieces_count, unit_price, total_price, stock_quantity,
                 is_special_offer, display_order, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->bind_param(
                "isiddiiis",
                $product_id,
                $set_name,
                $pieces_count,
                $unit_price,
                $total_price,
                $stock_quantity,
                $is_special_offer,
                $display_order,
                $status
            );
            
            if ($stmt->execute()) {
                $created_count++;
            }
            
            $stmt->close();
        }
        
        $conn->commit();
        $conn->close();
        
        return [
            'success' => true,
            'message' => "$created_count product sets created successfully",
            'count' => $created_count
        ];
        
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}




/**
 * Create landing page template
 */
/**
 * Create landing page template - FIXED VERSION
 */
/**
 * Create landing page template - FIXED VERSION
 */
function createLandingPageTemplate($data, $files) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        // Validate required fields
        if (empty($data['product_id'])) {
            throw new Exception('Product is required');
        }
        
        if (empty($data['template_name'])) {
            throw new Exception('Template name is required');
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        // Sanitize inputs
        $product_id = (int) $data['product_id'];
        $template_name = trim($data['template_name']);
        $is_active = isset($data['is_active']) ? 1 : 0;
        
        // Section 1: Hero
        $hero_title = trim($data['hero_title'] ?? '');
        $hero_subtitle = trim($data['hero_subtitle'] ?? '');
        $before_label = trim($data['before_label'] ?? 'আগের কবলন');
        $after_label = trim($data['after_label'] ?? 'আজীর কবলন');
        $hero_benefit_text = trim($data['hero_benefit_text'] ?? '');
        
        // Section 2: Features
        $features_title = trim($data['features_title'] ?? '');
        $feature_1_text = trim($data['feature_1_text'] ?? '');
        $feature_2_text = trim($data['feature_2_text'] ?? '');
        $feature_3_text = trim($data['feature_3_text'] ?? '');
        $feature_4_text = trim($data['feature_4_text'] ?? '');
        
        // Section 3: Reviews
        $reviews_title = trim($data['reviews_title'] ?? 'আমাদের কাষ্টমার রিভিউ');
        
        // Section 4: Order
        $order_title = trim($data['order_title'] ?? '');
        $order_subtitle = trim($data['order_subtitle'] ?? '');
        $order_benefit_1 = trim($data['order_benefit_1'] ?? '');
        $order_benefit_2 = trim($data['order_benefit_2'] ?? '');
        
        // Section 5: Contact
        $contact_title = trim($data['contact_title'] ?? '');
        $contact_subtitle = trim($data['contact_subtitle'] ?? '');
        $whatsapp_number = trim($data['whatsapp_number'] ?? '');
        $whatsapp_link = trim($data['whatsapp_link'] ?? '');
        $messenger_link = trim($data['messenger_link'] ?? '');
        
        // Section 6: Checkout
        $checkout_title = trim($data['checkout_title'] ?? '');
        $checkout_subtitle = trim($data['checkout_subtitle'] ?? '');
        $shipping_dhaka_cost = (float) ($data['shipping_dhaka_cost'] ?? 70);
        $shipping_urban_cost = (float) ($data['shipping_urban_cost'] ?? 100);
        $shipping_outside_cost = (float) ($data['shipping_outside_cost'] ?? 130);
        $privacy_text = trim($data['privacy_text'] ?? '');
        
        // Settings
        $brand_name = trim($data['brand_name'] ?? '');
        
        // Insert template
        // Field order matches database schema (29 fields total)
        $stmt = $conn->prepare("
            INSERT INTO landing_page_templates 
            (product_id, template_name, is_active,
             hero_title, hero_subtitle, before_label, after_label, hero_benefit_text,
             features_title, feature_1_text, feature_2_text, feature_3_text, feature_4_text,
             reviews_title,
             order_title, order_subtitle, order_benefit_1, order_benefit_2,
             contact_title, contact_subtitle, whatsapp_number, whatsapp_link, messenger_link,
             checkout_title, checkout_subtitle, shipping_dhaka_cost, shipping_outside_cost, privacy_text,
             brand_name)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        // Type string breakdown (29 parameters):
        // i (product_id) + s (template_name) + i (is_active) = isi
        // 5 strings (hero section) = sssss
        // 5 strings (features section) = sssss
        // 1 string (reviews_title) = s
        // 4 strings (order section) = ssss
        // 5 strings (contact section) = sssss
        // 2 strings + 2 doubles + 1 string (checkout section) = ssdds
        // 1 string (brand_name) = s
        // Total: i + s + i + sssss + sssss + s + ssss + sssss + ss + dd + s + s = "isissssssssssssssssssssssdds"
        // Count: 1+1+1+5+5+1+4+5+2+2+1+1 = 29 ✓
        
        $stmt->bind_param(
            "isiss" .      // 1-5: product_id, template_name, is_active, hero_title, hero_subtitle
            "sssss" .      // 6-10: before_label, after_label, hero_benefit_text, features_title, feature_1_text
            "sssss" .      // 11-15: feature_2_text, feature_3_text, feature_4_text, reviews_title, order_title
            "sssss" .      // 16-20: order_subtitle, order_benefit_1, order_benefit_2, contact_title, contact_subtitle
            "sssss" .      // 21-25: whatsapp_number, whatsapp_link, messenger_link, checkout_title, checkout_subtitle
            "ddss",        // 26-29: shipping_dhaka_cost, shipping_outside_cost, privacy_text, brand_name
            $product_id,           // 1 - i
            $template_name,        // 2 - s
            $is_active,            // 3 - i
            $hero_title,           // 4 - s
            $hero_subtitle,        // 5 - s
            $before_label,         // 6 - s
            $after_label,          // 7 - s
            $hero_benefit_text,    // 8 - s
            $features_title,       // 9 - s
            $feature_1_text,       // 10 - s
            $feature_2_text,       // 11 - s
            $feature_3_text,       // 12 - s
            $feature_4_text,       // 13 - s
            $reviews_title,        // 14 - s
            $order_title,          // 15 - s
            $order_subtitle,       // 16 - s
            $order_benefit_1,      // 17 - s
            $order_benefit_2,      // 18 - s
            $contact_title,        // 19 - s
            $contact_subtitle,     // 20 - s
            $whatsapp_number,      // 21 - s
            $whatsapp_link,        // 22 - s
            $messenger_link,       // 23 - s
            $checkout_title,       // 24 - s
            $checkout_subtitle,    // 25 - s
            $shipping_dhaka_cost,  // 26 - d
            $shipping_outside_cost,// 27 - d
            $privacy_text,         // 28 - s
            $brand_name            // 29 - s
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create template: ' . $stmt->error);
        }
        
        $template_id = $conn->insert_id;
        $stmt->close();
        
        // Handle image uploads
        $upload_dir = '../uploads/templates/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Upload brand logo
        if (!empty($files['brand_logo']['name'])) {
            $logo_result = uploadTemplateImage($conn, $files['brand_logo'], $upload_dir, 'logo_' . $template_id);
            if ($logo_result['success']) {
                $stmt = $conn->prepare("UPDATE landing_page_templates SET brand_logo = ? WHERE id = ?");
                $stmt->bind_param("si", $logo_result['filename'], $template_id);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        // Upload hero comparison image
        if (!empty($files['hero_comparison_image']['name'])) {
            $hero_result = uploadTemplateImage($conn, $files['hero_comparison_image'], $upload_dir, 'hero_' . $template_id);
            if ($hero_result['success']) {
                $stmt = $conn->prepare("UPDATE landing_page_templates SET hero_comparison_image = ? WHERE id = ?");
                $stmt->bind_param("si", $hero_result['filename'], $template_id);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        // Upload product showcase image
        if (!empty($files['product_showcase_image']['name'])) {
            $showcase_result = uploadTemplateImage($conn, $files['product_showcase_image'], $upload_dir, 'showcase_' . $template_id);
            if ($showcase_result['success']) {
                $stmt = $conn->prepare("UPDATE landing_page_templates SET product_showcase_image = ? WHERE id = ?");
                $stmt->bind_param("si", $showcase_result['filename'], $template_id);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        // Upload review slider images
        if (!empty($files['review_images']['name'][0])) {
            foreach ($files['review_images']['name'] as $key => $name) {
                if ($files['review_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $files['review_images']['name'][$key],
                        'type' => $files['review_images']['type'][$key],
                        'tmp_name' => $files['review_images']['tmp_name'][$key],
                        'error' => $files['review_images']['error'][$key],
                        'size' => $files['review_images']['size'][$key]
                    ];
                    
                    $img_result = uploadTemplateImage($conn, $file, $upload_dir, 'review_' . $template_id . '_' . $key);
                    if ($img_result['success']) {
                        $img_stmt = $conn->prepare("
                            INSERT INTO template_images (template_id, section, image_path, display_order)
                            VALUES (?, 'review_slider', ?, ?)
                        ");
                        $img_stmt->bind_param("isi", $template_id, $img_result['filename'], $key);
                        $img_stmt->execute();
                        $img_stmt->close();
                    }
                }
            }
        }
        
        // Upload product slider images
        if (!empty($files['product_slider_images']['name'][0])) {
            foreach ($files['product_slider_images']['name'] as $key => $name) {
                if ($files['product_slider_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $files['product_slider_images']['name'][$key],
                        'type' => $files['product_slider_images']['type'][$key],
                        'tmp_name' => $files['product_slider_images']['tmp_name'][$key],
                        'error' => $files['product_slider_images']['error'][$key],
                        'size' => $files['product_slider_images']['size'][$key]
                    ];
                    
                    $img_result = uploadTemplateImage($conn, $file, $upload_dir, 'product_' . $template_id . '_' . $key);
                    if ($img_result['success']) {
                        $img_stmt = $conn->prepare("
                            INSERT INTO template_images (template_id, section, image_path, display_order)
                            VALUES (?, 'product_slider', ?, ?)
                        ");
                        $img_stmt->bind_param("isi", $template_id, $img_result['filename'], $key);
                        $img_stmt->execute();
                        $img_stmt->close();
                    }
                }
            }
        }
        
        // Commit transaction
        $conn->commit();
        $conn->close();
        
        return [
            'success' => true,
            'message' => 'Template created successfully',
            'template_id' => $template_id
        ];
        
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
/**
 * Helper function to upload template images
 */
function uploadTemplateImage($conn, $file, $upload_dir, $prefix) {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    
    if (!in_array($ext, $allowed)) {
        return ['success' => false, 'message' => 'Invalid image type'];
    }
    
    $filename = $prefix . '_' . uniqid() . '.' . $ext;
    $path = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $path)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'Upload failed'];
}

/**
 * Get all templates
 */
function getAllTemplates() {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return [];
    }
    
    $query = "
        SELECT t.*, p.product_name,
        (SELECT image_path FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as product_image
        FROM landing_page_templates t
        INNER JOIN products p ON t.product_id = p.id
        ORDER BY t.created_at DESC
    ";
    
    $result = $conn->query($query);
    $templates = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $templates[] = $row;
        }
    }
    
    $conn->close();
    return $templates;
}

/**
 * Get template by ID
 */


/**
 * LANDING PAGE TEMPLATE FUNCTIONS - COMPLETE
 * Includes support for urban shipping cost field
 */

/**
 * Get landing page template by ID
 */
function getLandingPageTemplate($id) {
    error_log("=== getLandingPageTemplate($id) START ===");
    
    $conn = getDatabaseConnection();
    if (!$conn) {
        error_log("getLandingPageTemplate: Database connection FAILED");
        return null;
    }
    error_log("getLandingPageTemplate: DB connection OK");

    $sql = "SELECT t.*, p.product_name 
            FROM landing_page_templates t
            LEFT JOIN products p ON t.product_id = p.id
            WHERE t.id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("getLandingPageTemplate: prepare() failed: " . $conn->error);
        $conn->close();
        return null;
    }
    error_log("getLandingPageTemplate: prepare() OK");

    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        error_log("getLandingPageTemplate: execute() failed: " . $stmt->error);
        $stmt->close();
        $conn->close();
        return null;
    }
    error_log("getLandingPageTemplate: execute() OK");

    $result = $stmt->get_result();
    if (!$result) {
        error_log("getLandingPageTemplate: get_result() failed: " . $stmt->error);
        $stmt->close();
        $conn->close();
        return null;
    }
    error_log("getLandingPageTemplate: get_result() OK, num_rows = " . $result->num_rows);

    $template = $result->fetch_assoc();
    $stmt->close();

    if ($template) {
        error_log("getLandingPageTemplate: Template found, ID = " . $template['id']);
        // ... rest of image fetching (you can add similar debug there)
    } else {
        error_log("getLandingPageTemplate: No template found for ID = $id");
    }

    $conn->close();
    return $template;
}
/**
 * Get template images by template ID
 */
function getTemplateImages($template_id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return [];
    }
    
    $stmt = $conn->prepare("
        SELECT * FROM template_images 
        WHERE template_id = ? 
        ORDER BY section, display_order
    ");
    
    $stmt->bind_param("i", $template_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $images;
}

/**
 * Update landing page template - INCLUDES URBAN SHIPPING COST
 */
function updateLandingPageTemplate($template_id, $data, $files) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        // Validate required fields
        if (empty($data['product_id'])) {
            throw new Exception('Product is required');
        }
        
        if (empty($data['template_name'])) {
            throw new Exception('Template name is required');
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        // Sanitize inputs
        $product_id = (int) $data['product_id'];
        $template_name = trim($data['template_name']);
        $is_active = isset($data['is_active']) ? 1 : 0;
        
        // Section 1: Hero
        $hero_title = trim($data['hero_title'] ?? '');
        $hero_subtitle = trim($data['hero_subtitle'] ?? '');
        $before_label = trim($data['before_label'] ?? 'আগের কবলন');
        $after_label = trim($data['after_label'] ?? 'আজীর কবলন');
        $hero_benefit_text = trim($data['hero_benefit_text'] ?? '');
        
        // Section 2: Features
        $features_title = trim($data['features_title'] ?? '');
        $feature_1_text = trim($data['feature_1_text'] ?? '');
        $feature_2_text = trim($data['feature_2_text'] ?? '');
        $feature_3_text = trim($data['feature_3_text'] ?? '');
        $feature_4_text = trim($data['feature_4_text'] ?? '');
        
        // Section 3: Reviews
        $reviews_title = trim($data['reviews_title'] ?? 'আমাদের কাষ্টমার রিভিউ');
        
        // Section 4: Order
        $order_title = trim($data['order_title'] ?? '');
        $order_subtitle = trim($data['order_subtitle'] ?? '');
        $order_benefit_1 = trim($data['order_benefit_1'] ?? '');
        $order_benefit_2 = trim($data['order_benefit_2'] ?? '');
        
        // Section 5: Contact
        $contact_title = trim($data['contact_title'] ?? '');
        $contact_subtitle = trim($data['contact_subtitle'] ?? '');
        $whatsapp_number = trim($data['whatsapp_number'] ?? '');
        $whatsapp_link = trim($data['whatsapp_link'] ?? '');
        $messenger_link = trim($data['messenger_link'] ?? '');
        
        // Section 6: Checkout - INCLUDING URBAN SHIPPING COST
        $checkout_title = trim($data['checkout_title'] ?? '');
        $checkout_subtitle = trim($data['checkout_subtitle'] ?? '');
        $shipping_dhaka_cost = (float) ($data['shipping_dhaka_cost'] ?? 70);
        $shipping_urban_cost = (float) ($data['shipping_urban_cost'] ?? 100);
        $shipping_outside_cost = (float) ($data['shipping_outside_cost'] ?? 130);
        $privacy_text = trim($data['privacy_text'] ?? '');
        
        // Settings
        $brand_name = trim($data['brand_name'] ?? '');
        
        // Update template - 30 fields including shipping_urban_cost
        $stmt = $conn->prepare("
            UPDATE landing_page_templates 
            SET product_id = ?,
                template_name = ?,
                is_active = ?,
                hero_title = ?,
                hero_subtitle = ?,
                before_label = ?,
                after_label = ?,
                hero_benefit_text = ?,
                features_title = ?,
                feature_1_text = ?,
                feature_2_text = ?,
                feature_3_text = ?,
                feature_4_text = ?,
                reviews_title = ?,
                order_title = ?,
                order_subtitle = ?,
                order_benefit_1 = ?,
                order_benefit_2 = ?,
                contact_title = ?,
                contact_subtitle = ?,
                whatsapp_number = ?,
                whatsapp_link = ?,
                messenger_link = ?,
                checkout_title = ?,
                checkout_subtitle = ?,
                shipping_dhaka_cost = ?,
                shipping_urban_cost = ?,
                shipping_outside_cost = ?,
                privacy_text = ?,
                brand_name = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        // CORRECTED: Type string for 31 parameters
        // i + s + i + 22×s + 3×d + s + i = "isi" + "ssssssssssssssssssssss" + "ddd" + "s" + "i"
        $stmt->bind_param(
            "isisssssssssssssssssssssssdddsi",
            $product_id,           // 1 - i
            $template_name,        // 2 - s
            $is_active,            // 3 - i
            $hero_title,           // 4 - s
            $hero_subtitle,        // 5 - s
            $before_label,         // 6 - s
            $after_label,          // 7 - s
            $hero_benefit_text,    // 8 - s
            $features_title,       // 9 - s
            $feature_1_text,       // 10 - s
            $feature_2_text,       // 11 - s
            $feature_3_text,       // 12 - s
            $feature_4_text,       // 13 - s
            $reviews_title,        // 14 - s
            $order_title,          // 15 - s
            $order_subtitle,       // 16 - s
            $order_benefit_1,      // 17 - s
            $order_benefit_2,      // 18 - s
            $contact_title,        // 19 - s
            $contact_subtitle,     // 20 - s
            $whatsapp_number,      // 21 - s
            $whatsapp_link,        // 22 - s
            $messenger_link,       // 23 - s
            $checkout_title,       // 24 - s
            $checkout_subtitle,    // 25 - s
            $shipping_dhaka_cost,  // 26 - d
            $shipping_urban_cost,  // 27 - d (URBAN COST)
            $shipping_outside_cost,// 28 - d
            $privacy_text,         // 29 - s
            $brand_name,           // 30 - s
            $template_id           // 31 - i
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update template: ' . $stmt->error);
        }
        
        $stmt->close();
        
        // Handle image uploads
        $upload_dir = '../uploads/templates/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Upload brand logo
        if (!empty($files['brand_logo']['name'])) {
            $logo_result = uploadTemplateImage($conn, $files['brand_logo'], $upload_dir, 'logo_' . $template_id);
            if ($logo_result['success']) {
                $stmt = $conn->prepare("UPDATE landing_page_templates SET brand_logo = ? WHERE id = ?");
                $stmt->bind_param("si", $logo_result['filename'], $template_id);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        // Upload hero comparison image
        if (!empty($files['hero_comparison_image']['name'])) {
            $hero_result = uploadTemplateImage($conn, $files['hero_comparison_image'], $upload_dir, 'hero_' . $template_id);
            if ($hero_result['success']) {
                $stmt = $conn->prepare("UPDATE landing_page_templates SET hero_comparison_image = ? WHERE id = ?");
                $stmt->bind_param("si", $hero_result['filename'], $template_id);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        // Upload product showcase image
        if (!empty($files['product_showcase_image']['name'])) {
            $showcase_result = uploadTemplateImage($conn, $files['product_showcase_image'], $upload_dir, 'showcase_' . $template_id);
            if ($showcase_result['success']) {
                $stmt = $conn->prepare("UPDATE landing_page_templates SET product_showcase_image = ? WHERE id = ?");
                $stmt->bind_param("si", $showcase_result['filename'], $template_id);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        // Upload review slider images
        if (!empty($files['review_images']['name'][0])) {
            foreach ($files['review_images']['name'] as $key => $name) {
                if ($files['review_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $files['review_images']['name'][$key],
                        'type' => $files['review_images']['type'][$key],
                        'tmp_name' => $files['review_images']['tmp_name'][$key],
                        'error' => $files['review_images']['error'][$key],
                        'size' => $files['review_images']['size'][$key]
                    ];
                    
                    $img_result = uploadTemplateImage($conn, $file, $upload_dir, 'review_' . $template_id . '_' . $key);
                    if ($img_result['success']) {
                        $img_stmt = $conn->prepare("
                            INSERT INTO template_images (template_id, section, image_path, display_order)
                            VALUES (?, 'review_slider', ?, ?)
                        ");
                        $img_stmt->bind_param("isi", $template_id, $img_result['filename'], $key);
                        $img_stmt->execute();
                        $img_stmt->close();
                    }
                }
            }
        }
        
        // Upload product slider images
        if (!empty($files['product_slider_images']['name'][0])) {
            foreach ($files['product_slider_images']['name'] as $key => $name) {
                if ($files['product_slider_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $files['product_slider_images']['name'][$key],
                        'type' => $files['product_slider_images']['type'][$key],
                        'tmp_name' => $files['product_slider_images']['tmp_name'][$key],
                        'error' => $files['product_slider_images']['error'][$key],
                        'size' => $files['product_slider_images']['size'][$key]
                    ];
                    
                    $img_result = uploadTemplateImage($conn, $file, $upload_dir, 'product_' . $template_id . '_' . $key);
                    if ($img_result['success']) {
                        $img_stmt = $conn->prepare("
                            INSERT INTO template_images (template_id, section, image_path, display_order)
                            VALUES (?, 'product_slider', ?, ?)
                        ");
                        $img_stmt->bind_param("isi", $template_id, $img_result['filename'], $key);
                        $img_stmt->execute();
                        $img_stmt->close();
                    }
                }
            }
        }
        
        // Commit transaction
        $conn->commit();
        $conn->close();
        
        return [
            'success' => true,
            'message' => 'Template updated successfully',
            'template_id' => $template_id
        ];
        
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}


/**
 * Remove template image
 */
function removeTemplateImage($image_id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        // Get image details
        $stmt = $conn->prepare("SELECT * FROM template_images WHERE id = ?");
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $image = $result->fetch_assoc();
        $stmt->close();
        
        if (!$image) {
            throw new Exception('Image not found');
        }
        
        // Delete from filesystem
        $file_path = '../uploads/templates/' . $image['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Delete from database
        $delete_stmt = $conn->prepare("DELETE FROM template_images WHERE id = ?");
        $delete_stmt->bind_param("i", $image_id);
        
        if ($delete_stmt->execute()) {
            $delete_stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Image removed successfully'];
        } else {
            throw new Exception('Failed to remove image from database');
        }
        
    } catch (Exception $e) {
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Delete landing page template
 */
function deleteLandingPageTemplate($template_id) {
    return deleteTemplate($template_id);
}


function getTemplateById($id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return null;
    }
    
    $stmt = $conn->prepare("
        SELECT t.*, p.product_name 
FROM landing_page_templates t
LEFT JOIN products p ON t.product_id = p.id
WHERE t.id = ?
    ");
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $template = $result->fetch_assoc();
    $stmt->close();
    
    if ($template) {
        // Get template images
        $img_stmt = $conn->prepare("
            SELECT * FROM template_images 
            WHERE template_id = ? 
            ORDER BY section, display_order
        ");
        $img_stmt->bind_param("i", $id);
        $img_stmt->execute();
        $img_result = $img_stmt->get_result();
        
        $template['images'] = [];
        while ($img = $img_result->fetch_assoc()) {
            $template['images'][$img['section']][] = $img;
        }
        
        $img_stmt->close();
    }
    
    $conn->close();
    return $template;
}

/**
 * Get active template for a product
 */
function getActiveTemplateByProduct($product_id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return null;
    }
    
    $stmt = $conn->prepare("
        SELECT * FROM landing_page_templates 
        WHERE product_id = ? AND is_active = 1 
        LIMIT 1
    ");
    
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $template = $result->fetch_assoc();
    $stmt->close();
    
    if ($template) {
        // Get template images
        $img_stmt = $conn->prepare("
            SELECT * FROM template_images 
            WHERE template_id = ? 
            ORDER BY section, display_order
        ");
        $img_stmt->bind_param("i", $template['id']);
        $img_stmt->execute();
        $img_result = $img_stmt->get_result();
        
        $template['images'] = [];
        while ($img = $img_result->fetch_assoc()) {
            $template['images'][$img['section']][] = $img;
        }
        
        $img_stmt->close();
    }
    
    $conn->close();
    return $template;
}

/**
 * Delete template
 */
function deleteTemplate($id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        // Get template images to delete from filesystem
        $template = getTemplateById($id);
        
        if ($template) {
            // Delete brand logo
            if ($template['brand_logo']) {
                $file_path = '../uploads/templates/' . $template['brand_logo'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            
            // Delete other images
            foreach (['hero_comparison_image', 'product_showcase_image'] as $field) {
                if ($template[$field]) {
                    $file_path = '../uploads/templates/' . $template[$field];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }
            
            // Delete slider images
            if (isset($template['images'])) {
                foreach ($template['images'] as $section => $images) {
                    foreach ($images as $img) {
                        $file_path = '../uploads/templates/' . $img['image_path'];
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }
            }
        }
        
        // Delete template (cascades to images)
        $stmt = $conn->prepare("DELETE FROM landing_page_templates WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Template deleted successfully'];
        } else {
            throw new Exception('Failed to delete template');
        }
        
    } catch (Exception $e) {
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Update template status
 */
function updateTemplateStatus($id, $is_active) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    // If activating, deactivate other templates for same product
    if ($is_active) {
        $stmt = $conn->prepare("
            UPDATE landing_page_templates 
            SET is_active = 0 
            WHERE product_id = (SELECT product_id FROM landing_page_templates WHERE id = ?)
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    
    $stmt = $conn->prepare("UPDATE landing_page_templates SET is_active = ? WHERE id = ?");
    $stmt->bind_param("ii", $is_active, $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return ['success' => true, 'message' => 'Status updated successfully'];
    }
    
    $stmt->close();
    $conn->close();
    return ['success' => false, 'message' => 'Failed to update status'];
}




/**
 * Order Management Functions
 * Complete CRUD operations for orders and order items
 */

/**
 * Generate a unique order number
 */
function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

/**
 * Create a new order
 * @param array $data Order data including customer info and product selection
 * @return array Success status and order details
 */
function createOrder($data) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        // Validate required fields
        $required = ['customer_name', 'customer_phone', 'customer_address', 'shipping_area', 'product_set_id', 'quantity'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        // Get product set details
        $set_id = (int) $data['product_set_id'];
        $quantity = (int) $data['quantity'];
        
        $set_query = "SELECT ps.*, p.product_name, p.id as product_id
                      FROM product_sets ps
                      INNER JOIN products p ON ps.product_id = p.id
                      WHERE ps.id = ? AND ps.status = 'active'";
        $set_stmt = $conn->prepare($set_query);
        $set_stmt->bind_param("i", $set_id);
        $set_stmt->execute();
        $set_result = $set_stmt->get_result();
        $product_set = $set_result->fetch_assoc();
        $set_stmt->close();
        
        if (!$product_set) {
            throw new Exception('Invalid product selection');
        }
        
        // Get shipping cost from active template
        $template_query = "SELECT shipping_dhaka_cost, shipping_outside_cost 
                          FROM landing_page_templates 
                          WHERE product_id = ? AND is_active = 1 
                          LIMIT 1";
        $template_stmt = $conn->prepare($template_query);
        $template_stmt->bind_param("i", $product_set['product_id']);
        $template_stmt->execute();
        $template_result = $template_stmt->get_result();
        $template = $template_result->fetch_assoc();
        $template_stmt->close();
        
        if (!$template) {
            throw new Exception('No active template found for this product');
        }
        
        // Calculate order totals
        $unit_price = floatval($product_set['total_price']);
        $subtotal = $unit_price * $quantity;
        $shipping_cost = ($data['shipping_area'] == 'dhaka') 
            ? floatval($template['shipping_dhaka_cost']) 
            : floatval($template['shipping_outside_cost']);
        $total_amount = $subtotal + $shipping_cost;
        
        // Sanitize customer data
        $customer_name = trim($data['customer_name']);
        $customer_phone = trim($data['customer_phone']);
        $customer_address = trim($data['customer_address']);
        $customer_email = trim($data['customer_email'] ?? '');
        $notes = trim($data['notes'] ?? '');
        $shipping_area = $data['shipping_area'];
        $payment_method = 'cod'; // Default to Cash on Delivery
        
        // Generate order number
        $order_number = generateOrderNumber();
        
        // Insert order
        $order_query = "INSERT INTO orders 
                       (order_number, customer_name, customer_phone, customer_address, customer_email,
                        notes, subtotal, shipping_cost, total_amount, shipping_area, payment_method,
                        payment_status, order_status, created_at)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', NOW())";
        
        $order_stmt = $conn->prepare($order_query);
        $order_stmt->bind_param(
            "ssssssdddss",
            $order_number,
            $customer_name,
            $customer_phone,
            $customer_address,
            $customer_email,
            $notes,
            $subtotal,
            $shipping_cost,
            $total_amount,
            $shipping_area,
            $payment_method
        );
        
        if (!$order_stmt->execute()) {
            throw new Exception('Failed to create order: ' . $order_stmt->error);
        }
        
        $order_id = $conn->insert_id;
        $order_stmt->close();
        
        // Insert order item
        $pieces_per_set = intval($product_set['pieces_count']);
        $total_pieces = $pieces_per_set * $quantity;
        
        $item_query = "INSERT INTO order_items 
                      (order_id, product_id, product_name, quantity, pieces_per_set, 
                       total_pieces, unit_price, total_price, created_at)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $item_stmt = $conn->prepare($item_query);
        $item_stmt->bind_param(
            "iisiiidd",
            $order_id,
            $product_set['product_id'],
            $product_set['product_name'],
            $quantity,
            $pieces_per_set,
            $total_pieces,
            $unit_price,
            $subtotal
        );
        
        if (!$item_stmt->execute()) {
            throw new Exception('Failed to create order item: ' . $item_stmt->error);
        }
        
        $item_stmt->close();
        
        // Commit transaction
        $conn->commit();
        $conn->close();
        
        return [
            'success' => true,
            'message' => 'Order created successfully',
            'order_id' => $order_id,
            'order_number' => $order_number,
            'total_amount' => $total_amount
        ];
        
    } catch (Exception $e) {
        if ($conn) {
            $conn->rollback();
            $conn->close();
        }
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
/**
 * Get order statistics for dashboard
 * 
 * @param mysqli $conn Database connection (optional, will create if not provided)
 * @return array Statistics data
 */
function getOrderStatistics($conn = null) {
    $should_close = false;
    
    if (!$conn) {
        $conn = getDatabaseConnection();
        $should_close = true;
    }
    
    if (!$conn) {
        return [];
    }
    
    $stats = [
        'total_revenue' => 0,
        'total_orders' => 0,
        'pending_orders' => 0,
        'confirmed_orders' => 0,
        'processing_orders' => 0,
        'shipped_orders' => 0,
        'delivered_orders' => 0,
        'cancelled_orders' => 0,
    ];
    
    // Total revenue and orders
    $query = "SELECT 
                COUNT(*) as total_orders,
                COALESCE(SUM(CASE WHEN order_status != 'cancelled' THEN total_amount ELSE 0 END), 0) as total_revenue
              FROM orders";
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $stats['total_orders'] = $row['total_orders'];
        $stats['total_revenue'] = $row['total_revenue'];
    }
    
    // Orders by status
    $status_query = "SELECT order_status, COUNT(*) as count 
                     FROM orders 
                     GROUP BY order_status";
    $status_result = $conn->query($status_query);
    if ($status_result) {
        while ($row = $status_result->fetch_assoc()) {
            $key = $row['order_status'] . '_orders';
            if (isset($stats[$key])) {
                $stats[$key] = $row['count'];
            }
        }
    }
    
    if ($should_close) {
        $conn->close();
    }
    
    return $stats;
}

/**
 * Get all orders with pagination (UPDATED VERSION)
 */
function getAllOrders($page = 1, $per_page = 20, $filters = []) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    $offset = ($page - 1) * $per_page;
    
    // Build WHERE clause based on filters
    $where_conditions = [];
    $params = [];
    $types = '';
    
    if (!empty($filters['status']) && $filters['status'] !== 'all') {
        $where_conditions[] = "o.order_status = ?";
        $params[] = $filters['status'];
        $types .= 's';
    }
    
    if (!empty($filters['payment_status'])) {
        $where_conditions[] = "o.payment_status = ?";
        $params[] = $filters['payment_status'];
        $types .= 's';
    }
    
    if (!empty($filters['date_from'])) {
        $where_conditions[] = "DATE(o.created_at) >= ?";
        $params[] = $filters['date_from'];
        $types .= 's';
    }
    
    if (!empty($filters['date_to'])) {
        $where_conditions[] = "DATE(o.created_at) <= ?";
        $params[] = $filters['date_to'];
        $types .= 's';
    }
    
    if (!empty($filters['search'])) {
        $where_conditions[] = "(o.order_number LIKE ? OR o.customer_name LIKE ? OR o.customer_phone LIKE ?)";
        $search_term = '%' . $filters['search'] . '%';
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= 'sss';
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Get total count
    $count_query = "SELECT COUNT(*) as total FROM orders o $where_clause";
    
    if (!empty($params)) {
        $count_stmt = $conn->prepare($count_query);
        $count_stmt->bind_param($types, ...$params);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $total_orders = $count_result->fetch_assoc()['total'];
        $count_stmt->close();
    } else {
        $count_result = $conn->query($count_query);
        $total_orders = $count_result->fetch_assoc()['total'];
    }
    
    // Get orders
    $query = "SELECT o.* FROM orders o $where_clause ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
    
    $params[] = $per_page;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    $total_pages = ceil($total_orders / $per_page);
    
    return [
        'success' => true,
        'orders' => $orders,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $per_page,
            'total_orders' => $total_orders,
            'total_pages' => $total_pages,
            'has_prev' => $page > 1,
            'has_next' => $page < $total_pages
        ]
    ];
}
/**
 * Get order by ID with items (UPDATED - no duplicate)
 */
function getOrderById($order_id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return null;
    }
    
    // Get order
    $order_query = "SELECT * FROM orders WHERE id = ?";
    $order_stmt = $conn->prepare($order_query);
    $order_stmt->bind_param("i", $order_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
    $order = $order_result->fetch_assoc();
    $order_stmt->close();
    
    if (!$order) {
        $conn->close();
        return null;
    }
    
    // Get order items
    $items_query = "SELECT * FROM order_items WHERE order_id = ?";
    $items_stmt = $conn->prepare($items_query);
    $items_stmt->bind_param("i", $order_id);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    
    $order['items'] = [];
    while ($item = $items_result->fetch_assoc()) {
        $order['items'][] = $item;
    }
    
    $items_stmt->close();
    $conn->close();
    
    return $order;
}
/**
 * Update order status (UPDATED - no duplicate)
 */
function updateOrderStatus($order_id, $order_status, $payment_status = null) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    $valid_statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
    
    if (!in_array($order_status, $valid_statuses)) {
        $conn->close();
        return ['success' => false, 'message' => 'Invalid order status'];
    }
    
    if ($payment_status !== null) {
        $query = "UPDATE orders SET order_status = ?, payment_status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $order_status, $payment_status, $order_id);
    } else {
        $query = "UPDATE orders SET order_status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $order_status, $order_id);
    }
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return ['success' => true, 'message' => 'Order status updated successfully'];
    }
    
    $stmt->close();
    $conn->close();
    return ['success' => false, 'message' => 'Failed to update order status'];
}

/**
 * Delete order (UPDATED - no duplicate)
 */
function deleteOrder($order_id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        $conn->begin_transaction();
        
        // Delete order items first
        $items_query = "DELETE FROM order_items WHERE order_id = ?";
        $items_stmt = $conn->prepare($items_query);
        $items_stmt->bind_param("i", $order_id);
        $items_stmt->execute();
        $items_stmt->close();
        
        // Delete order tracking if exists
        $tracking_query = "DELETE FROM order_tracking WHERE order_id = ?";
        $tracking_stmt = $conn->prepare($tracking_query);
        $tracking_stmt->bind_param("i", $order_id);
        $tracking_stmt->execute();
        $tracking_stmt->close();
        
        // Delete order
        $order_query = "DELETE FROM orders WHERE id = ?";
        $order_stmt = $conn->prepare($order_query);
        $order_stmt->bind_param("i", $order_id);
        $order_stmt->execute();
        $order_stmt->close();
        
        $conn->commit();
        $conn->close();
        
        return ['success' => true, 'message' => 'Order deleted successfully'];
        
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        return ['success' => false, 'message' => 'Failed to delete order: ' . $e->getMessage()];
    }
}
/**
 * Get recent orders
 */
function getRecentOrders($limit = 10) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return [];
    }
    
    $stmt = $conn->prepare("
        SELECT * FROM orders
        ORDER BY created_at DESC
        LIMIT ?
    ");
    
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $orders;
}


/**
 * Steadfast Courier Integration Functions
 */

/**
 * Update order with tracking code from Steadfast
 * 
 * @param mysqli $con Database connection
 * @param int $order_id Order ID
 * @param string $tracking_code Tracking code from Steadfast
 * @param int $consignment_id Consignment ID from Steadfast
 * @return bool Success status
 */
function update_tracking_code($con, $order_id, $tracking_code, $consignment_id = null) {
    $order_id = mysqli_real_escape_string($con, $order_id);
    $tracking_code = mysqli_real_escape_string($con, $tracking_code);
    
    // Build SQL with optional consignment_id
    $sql = "UPDATE orders SET 
            tracking_code = '$tracking_code', ";
    
    if ($consignment_id !== null) {
        $consignment_id = mysqli_real_escape_string($con, $consignment_id);
        $sql .= "consignment_id = '$consignment_id', ";
    }
    
    $sql .= "courier_status = 'sent', 
            sent_to_courier_at = NOW() 
            WHERE id = '$order_id'";
    
    return mysqli_query($con, $sql);
}

/**
 * Get order details from database
 * 
 * @param mysqli $con Database connection
 * @param int $order_id Order ID
 * @return array|null Order details
 */
function get_order_details($con, $order_id) {
    $order_id = mysqli_real_escape_string($con, $order_id);
    
    $sql = "SELECT * FROM orders WHERE id = '$order_id' LIMIT 1";
    $result = mysqli_query($con, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Get multiple orders for bulk sending to courier
 * 
 * @param mysqli $con Database connection
 * @param string $status Order status to filter (default: 'pending')
 * @param int $limit Maximum orders to fetch (default: 500, Steadfast limit)
 * @return array Orders array
 */
function get_orders_for_courier($con, $status = 'pending', $limit = 500) {
    $status = mysqli_real_escape_string($con, $status);
    $limit = (int)$limit;
    
    // Get orders that haven't been sent to courier yet
    $sql = "SELECT * FROM orders 
            WHERE order_status = '$status' 
            AND (courier_status IS NULL OR courier_status = '') 
            ORDER BY id ASC 
            LIMIT $limit";
    
    $result = mysqli_query($con, $sql);
    $orders = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
    }
    
    return $orders;
}

/**
 * Update courier status for an order
 * 
 * @param mysqli $con Database connection
 * @param int $order_id Order ID
 * @param string $status New courier status
 * @return bool Success status
 */
function update_courier_status($con, $order_id, $status) {
    $order_id = mysqli_real_escape_string($con, $order_id);
    $status = mysqli_real_escape_string($con, $status);
    
    $sql = "UPDATE orders SET 
            courier_status = '$status', 
            updated_at = NOW() 
            WHERE id = '$order_id'";
    
    return mysqli_query($con, $sql);
}

/**
 * Log Steadfast API errors
 * 
 * @param mysqli $con Database connection
 * @param int $order_id Order ID
 * @param string $error_message Error message
 * @param string $api_response Full API response
 * @return bool Success status
 */
function log_steadfast_error($con, $order_id, $error_message, $api_response = null) {
    $order_id = mysqli_real_escape_string($con, $order_id);
    $error_message = mysqli_real_escape_string($con, $error_message);
    $api_response = $api_response ? mysqli_real_escape_string($con, $api_response) : null;
    
    // Option 1: If you have a courier_logs table
    $sql = "INSERT INTO courier_logs (order_id, error_message, api_response, created_at) 
            VALUES ('$order_id', '$error_message', '$api_response', NOW())";
    
    // Option 2: If no logs table, update order with error
    // $sql = "UPDATE orders SET courier_error = '$error_message' WHERE id = '$order_id'";
    
    return mysqli_query($con, $sql);
}

/**
 * Get order by tracking code
 * 
 * @param mysqli $con Database connection
 * @param string $tracking_code Tracking code
 * @return array|null Order details
 */
function get_order_by_tracking_code($con, $tracking_code) {
    $tracking_code = mysqli_real_escape_string($con, $tracking_code);
    
    $sql = "SELECT * FROM orders WHERE tracking_code = '$tracking_code' LIMIT 1";
    $result = mysqli_query($con, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Update delivery status from Steadfast webhook/status check
 * 
 * @param mysqli $con Database connection
 * @param string $tracking_code Tracking code
 * @param string $delivery_status Steadfast delivery status
 * @return bool Success status
 */
function update_delivery_status($con, $tracking_code, $delivery_status) {
    $tracking_code = mysqli_real_escape_string($con, $tracking_code);
    $delivery_status = mysqli_real_escape_string($con, $delivery_status);
    
    // Map Steadfast statuses to your order statuses
    $order_status_map = [
        'delivered' => 'delivered',
        'partial_delivered' => 'partial_delivered',
        'cancelled' => 'cancelled',
        'hold' => 'hold',
        'in_review' => 'processing',
        'pending' => 'shipped'
    ];
    
    $order_status = isset($order_status_map[$delivery_status]) 
        ? $order_status_map[$delivery_status] 
        : 'shipped';
    
    $sql = "UPDATE orders SET 
            courier_status = '$delivery_status',
            order_status = '$order_status',
            updated_at = NOW()";
    
    // If delivered, set delivery date
    if ($delivery_status === 'delivered') {
        $sql .= ", delivered_at = NOW()";
    }
    
    $sql .= " WHERE tracking_code = '$tracking_code'";
    
    return mysqli_query($con, $sql);
}

/**
 * Check if order already sent to courier
 * 
 * @param mysqli $con Database connection
 * @param int $order_id Order ID
 * @return bool True if already sent
 */
function is_order_sent_to_courier($con, $order_id) {
    $order_id = mysqli_real_escape_string($con, $order_id);
    
    $sql = "SELECT tracking_code FROM orders 
            WHERE id = '$order_id' 
            AND tracking_code IS NOT NULL 
            AND tracking_code != '' 
            LIMIT 1";
    
    $result = mysqli_query($con, $sql);
    
    return ($result && mysqli_num_rows($result) > 0);
}





// deepseek functions of orders 
/**
 * Get complete order details with customer info and items
 */
function getOrderWithDetails($order_id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return null;
    }
    
    // Get order with customer details
    $query = "
        SELECT o.*, 
               GROUP_CONCAT(oi.product_name SEPARATOR ', ') as products,
               COUNT(oi.id) as item_count
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.id = ?
        GROUP BY o.id
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();
    
    if ($order) {
        // Get order items
        $items_query = "SELECT * FROM order_items WHERE order_id = ?";
        $items_stmt = $conn->prepare($items_query);
        $items_stmt->bind_param("i", $order_id);
        $items_stmt->execute();
        $items_result = $items_stmt->get_result();
        
        $order['items'] = [];
        while ($item = $items_result->fetch_assoc()) {
            $order['items'][] = $item;
        }
        $items_stmt->close();
        
        // Get tracking info
        $tracking_query = "SELECT * FROM order_tracking WHERE order_id = ? ORDER BY created_at DESC";
        $tracking_stmt = $conn->prepare($tracking_query);
        $tracking_stmt->bind_param("i", $order_id);
        $tracking_stmt->execute();
        $tracking_result = $tracking_stmt->get_result();
        
        $order['tracking'] = [];
        while ($track = $tracking_result->fetch_assoc()) {
            $order['tracking'][] = $track;
        }
        $tracking_stmt->close();
    }
    
    $conn->close();
    return $order;
}

/**
 * Get recent orders
 * 
 * @param int $limit Number of orders to retrieve
 * @return array Recent orders
 */



/**
 * Update order customer details
 */
function updateOrderCustomerDetails($order_id, $data) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        // Validate required fields
        $required = ['customer_name', 'customer_phone', 'customer_address'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
        }
        
        // Sanitize inputs
        $customer_name = trim($data['customer_name']);
        $customer_phone = trim($data['customer_phone']);
        $customer_address = trim($data['customer_address']);
        $customer_email = trim($data['customer_email'] ?? '');
        $notes = trim($data['notes'] ?? '');
        
        // Update query
        $stmt = $conn->prepare("
            UPDATE orders 
            SET customer_name = ?, 
                customer_phone = ?, 
                customer_address = ?, 
                customer_email = ?, 
                notes = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->bind_param(
            "sssssi",
            $customer_name,
            $customer_phone,
            $customer_address,
            $customer_email,
            $notes,
            $order_id
        );
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Customer details updated successfully'];
        } else {
            throw new Exception('Failed to update customer details');
        }
        
    } catch (Exception $e) {
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
/**
 * Update order shipping and payment details
 */
function updateOrderShippingDetails($order_id, $data) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        // Get current order to validate shipping cost
        $current_order = getOrderById($order_id);
        if (!$current_order) {
            throw new Exception('Order not found');
        }
        
        // Calculate new totals
        $subtotal = floatval($data['subtotal']);
        $shipping_cost = floatval($data['shipping_cost']);
        $total_amount = $subtotal + $shipping_cost;
        
        // Update query
        $stmt = $conn->prepare("
            UPDATE orders 
            SET shipping_area = ?, 
                shipping_cost = ?, 
                subtotal = ?, 
                total_amount = ?,
                payment_method = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->bind_param(
            "sdddsi",
            $data['shipping_area'],
            $shipping_cost,
            $subtotal,
            $total_amount,
            $data['payment_method'],
            $order_id
        );
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return [
                'success' => true, 
                'message' => 'Shipping details updated successfully',
                'new_total' => $total_amount
            ];
        } else {
            throw new Exception('Failed to update shipping details');
        }
        
    } catch (Exception $e) {
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Update order item
 */
function updateOrderItem($item_id, $data) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        $quantity = intval($data['quantity']);
        $pieces_per_set = intval($data['pieces_per_set'] ?? 1);
        $total_pieces = $quantity * $pieces_per_set;
        $unit_price = floatval($data['unit_price']);
        $total_price = $quantity * $unit_price;
        
        // Update item
        $stmt = $conn->prepare("
            UPDATE order_items 
            SET quantity = ?, 
                pieces_per_set = ?,
                total_pieces = ?,
                unit_price = ?,
                total_price = ?
            WHERE id = ?
        ");
        
        $stmt->bind_param(
            "iiiddi",
            $quantity,
            $pieces_per_set,
            $total_pieces,
            $unit_price,
            $total_price,
            $item_id
        );
        
        if ($stmt->execute()) {
            // Get order_id to update order total
            $order_stmt = $conn->prepare("SELECT order_id FROM order_items WHERE id = ?");
            $order_stmt->bind_param("i", $item_id);
            $order_stmt->execute();
            $order_result = $order_stmt->get_result();
            $item = $order_result->fetch_assoc();
            $order_stmt->close();
            
            if ($item) {
                // Recalculate order totals
                $total_stmt = $conn->prepare("
                    UPDATE orders o
                    SET o.subtotal = (
                        SELECT SUM(total_price) 
                        FROM order_items 
                        WHERE order_id = o.id
                    ),
                    o.total_amount = o.subtotal + o.shipping_cost,
                    o.updated_at = NOW()
                    WHERE o.id = ?
                ");
                $total_stmt->bind_param("i", $item['order_id']);
                $total_stmt->execute();
                $total_stmt->close();
            }
            
            $stmt->close();
            $conn->close();
            
            return ['success' => true, 'message' => 'Order item updated successfully'];
        } else {
            throw new Exception('Failed to update order item');
        }
        
    } catch (Exception $e) {
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Add new item to order
 */
function addOrderItem($order_id, $data) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        $required = ['product_name', 'quantity', 'unit_price'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
        }
        
        $product_name = trim($data['product_name']);
        $quantity = intval($data['quantity']);
        $pieces_per_set = intval($data['pieces_per_set'] ?? 1);
        $total_pieces = $quantity * $pieces_per_set;
        $unit_price = floatval($data['unit_price']);
        $total_price = $quantity * $unit_price;
        $product_id = intval($data['product_id'] ?? 0);
        
        // Insert new item
        $stmt = $conn->prepare("
            INSERT INTO order_items 
            (order_id, product_id, product_name, quantity, pieces_per_set, total_pieces, unit_price, total_price)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param(
            "iisiiidd",
            $order_id,
            $product_id,
            $product_name,
            $quantity,
            $pieces_per_set,
            $total_pieces,
            $unit_price,
            $total_price
        );
        
        if ($stmt->execute()) {
            $item_id = $conn->insert_id;
            
            // Update order totals
            $update_stmt = $conn->prepare("
                UPDATE orders 
                SET subtotal = (
                    SELECT SUM(total_price) 
                    FROM order_items 
                    WHERE order_id = ?
                ),
                total_amount = subtotal + shipping_cost,
                updated_at = NOW()
                WHERE id = ?
            ");
            $update_stmt->bind_param("ii", $order_id, $order_id);
            $update_stmt->execute();
            $update_stmt->close();
            
            $stmt->close();
            $conn->close();
            
            return [
                'success' => true, 
                'message' => 'Item added successfully',
                'item_id' => $item_id
            ];
        } else {
            throw new Exception('Failed to add item');
        }
        
    } catch (Exception $e) {
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Delete order item
 */
function deleteOrderItem($item_id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        // Get order_id before deletion
        $stmt = $conn->prepare("SELECT order_id FROM order_items WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        $stmt->close();
        
        if (!$item) {
            throw new Exception('Item not found');
        }
        
        $order_id = $item['order_id'];
        
        // Delete item
        $delete_stmt = $conn->prepare("DELETE FROM order_items WHERE id = ?");
        $delete_stmt->bind_param("i", $item_id);
        
        if ($delete_stmt->execute()) {
            // Update order totals
            $update_stmt = $conn->prepare("
                UPDATE orders 
                SET subtotal = COALESCE((
                    SELECT SUM(total_price) 
                    FROM order_items 
                    WHERE order_id = ?
                ), 0),
                total_amount = subtotal + shipping_cost,
                updated_at = NOW()
                WHERE id = ?
            ");
            $update_stmt->bind_param("ii", $order_id, $order_id);
            $update_stmt->execute();
            $update_stmt->close();
            
            $delete_stmt->close();
            $conn->close();
            
            return ['success' => true, 'message' => 'Item deleted successfully'];
        } else {
            throw new Exception('Failed to delete item');
        }
        
    } catch (Exception $e) {
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Get all customers
 */
function getAllCustomers($page = 1, $per_page = 20, $filters = []) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    $offset = ($page - 1) * $per_page;
    
    // Build WHERE clause
    $where_conditions = [];
    $params = [];
    $types = '';
    
    if (!empty($filters['search'])) {
        $where_conditions[] = "(customer_name LIKE ? OR customer_phone LIKE ? OR customer_email LIKE ?)";
        $search_term = '%' . $filters['search'] . '%';
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= 'sss';
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Get total count
    $count_query = "SELECT COUNT(DISTINCT customer_phone) as total FROM orders $where_clause";
    if (!empty($params)) {
        $count_stmt = $conn->prepare($count_query);
        $count_stmt->bind_param($types, ...$params);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $total_customers = $count_result->fetch_assoc()['total'];
        $count_stmt->close();
    } else {
        $count_result = $conn->query($count_query);
        $total_customers = $count_result->fetch_assoc()['total'];
    }
    
    // Get customers with their latest order and order count
    $query = "
        SELECT 
            customer_phone,
            customer_name,
            customer_email,
            MAX(customer_address) as latest_address,
            COUNT(*) as order_count,
            SUM(total_amount) as total_spent,
            MAX(created_at) as last_order_date
        FROM orders
        $where_clause
        GROUP BY customer_phone, customer_name, customer_email
        ORDER BY last_order_date DESC
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $per_page;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    $total_pages = ceil($total_customers / $per_page);
    
    return [
        'success' => true,
        'customers' => $customers,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $per_page,
            'total_customers' => $total_customers,
            'total_pages' => $total_pages
        ]
    ];
}
/**
 * Get customer details with all orders
 */
function getCustomerDetails($customer_phone) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return null;
    }
    
    $stmt = $conn->prepare("
        SELECT 
            customer_phone,
            customer_name,
            customer_email,
            GROUP_CONCAT(DISTINCT customer_address SEPARATOR ' | ') as all_addresses,
            COUNT(*) as total_orders,
            SUM(total_amount) as total_spent,
            MIN(created_at) as first_order_date,
            MAX(created_at) as last_order_date
        FROM orders
        WHERE customer_phone = ?
        GROUP BY customer_phone, customer_name, customer_email
    ");
    
    $stmt->bind_param("s", $customer_phone);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
    $stmt->close();
    
    if ($customer) {
        // Get all orders for this customer
        $orders_stmt = $conn->prepare("
            SELECT * FROM orders 
            WHERE customer_phone = ? 
            ORDER BY created_at DESC
        ");
        $orders_stmt->bind_param("s", $customer_phone);
        $orders_stmt->execute();
        $orders_result = $orders_stmt->get_result();
        
        $customer['orders'] = [];
        while ($order = $orders_result->fetch_assoc()) {
            $customer['orders'][] = $order;
        }
        $orders_stmt->close();
    }
    
    $conn->close();
    return $customer;
}

/**
 * Update courier tracking information
 */
function updateOrderTracking($order_id, $data) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        $required = ['consignment_id', 'courier', 'tracking_status'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
        }
        
        $consignment_id = trim($data['consignment_id']);
        $courier = trim($data['courier']);
        $tracking_status = trim($data['tracking_status']);
        $tracking_note = trim($data['tracking_note'] ?? '');
        
        // Check if tracking already exists
        $check_stmt = $conn->prepare("SELECT id FROM order_tracking WHERE order_id = ?");
        $check_stmt->bind_param("i", $order_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $check_stmt->close();
        
        if ($check_result->num_rows > 0) {
            // Update existing
            $stmt = $conn->prepare("
                UPDATE order_tracking 
                SET consignment_id = ?, 
                    courier = ?, 
                    tracking_status = ?, 
                    tracking_note = ?,
                    updated_at = NOW()
                WHERE order_id = ?
            ");
            $stmt->bind_param("ssssi", $consignment_id, $courier, $tracking_status, $tracking_note, $order_id);
        } else {
            // Insert new
            $stmt = $conn->prepare("
                INSERT INTO order_tracking 
                (order_id, consignment_id, courier, tracking_status, tracking_note)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("issss", $order_id, $consignment_id, $courier, $tracking_status, $tracking_note);
        }
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Tracking information updated successfully'];
        } else {
            throw new Exception('Failed to update tracking information');
        }
        
    } catch (Exception $e) {
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}


function getOrderStatisticsByDateRange($date_from, $date_to) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return [];
    }
    
    $stats = [];
    
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_orders,
            SUM(CASE WHEN order_status != 'cancelled' THEN total_amount ELSE 0 END) as total_revenue,
            SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
            SUM(CASE WHEN order_status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_orders,
            SUM(CASE WHEN order_status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
            SUM(CASE WHEN order_status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders,
            SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
            SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders
        FROM orders
        WHERE DATE(created_at) BETWEEN ? AND ?
    ");
    
    $stmt->bind_param("ss", $date_from, $date_to);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    
    return $stats;
}



/**
 * ENHANCED ORDER MANAGEMENT FUNCTIONS
 * Complete analytics and comprehensive status tracking
 */

/**
 * Get comprehensive order statistics with all statuses
 */
function getEnhancedOrderStatistics($conn = null) {
    $should_close = false;
    
    if (!$conn) {
        $conn = getDatabaseConnection();
        $should_close = true;
    }
    
    if (!$conn) {
        return [];
    }
    
    $stats = [
        // Revenue metrics
        'total_revenue' => 0,
        'today_revenue' => 0,
        'month_revenue' => 0,
        'pending_revenue' => 0,
        
        // Order counts
        'total_orders' => 0,
        'today_orders' => 0,
        'month_orders' => 0,
        
        // Order status breakdown
        'pending_orders' => 0,
        'confirmed_orders' => 0,
        'processing_orders' => 0,
        'packed_orders' => 0,
        'shipped_orders' => 0,
        'out_for_delivery_orders' => 0,
        'delivered_orders' => 0,
        'cancelled_orders' => 0,
        'returned_orders' => 0,
        
        // Payment status breakdown
        'payment_pending' => 0,
        'payment_partial' => 0,
        'payment_paid' => 0,
        'payment_refunded' => 0,
        'payment_failed' => 0,
        
        // Courier integration
        'sent_to_courier' => 0,
        'in_transit' => 0,
        
        // Additional metrics
        'average_order_value' => 0,
        'conversion_rate' => 0,
    ];
    
    // Total revenue and orders (excluding cancelled)
    $query = "SELECT 
                COUNT(*) as total_orders,
                COALESCE(SUM(CASE WHEN order_status != 'cancelled' THEN total_amount ELSE 0 END), 0) as total_revenue,
                COALESCE(AVG(CASE WHEN order_status != 'cancelled' THEN total_amount ELSE NULL END), 0) as avg_order_value
              FROM orders";
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $stats['total_orders'] = $row['total_orders'];
        $stats['total_revenue'] = $row['total_revenue'];
        $stats['average_order_value'] = $row['avg_order_value'];
    }
    
    // Today's stats
    $today_query = "SELECT 
                      COUNT(*) as count,
                      COALESCE(SUM(total_amount), 0) as revenue
                    FROM orders 
                    WHERE DATE(created_at) = CURDATE()";
    $today_result = $conn->query($today_query);
    if ($today_result && $row = $today_result->fetch_assoc()) {
        $stats['today_orders'] = $row['count'];
        $stats['today_revenue'] = $row['revenue'];
    }
    
    // This month's stats
    $month_query = "SELECT 
                      COUNT(*) as count,
                      COALESCE(SUM(total_amount), 0) as revenue
                    FROM orders 
                    WHERE YEAR(created_at) = YEAR(CURDATE()) 
                    AND MONTH(created_at) = MONTH(CURDATE())";
    $month_result = $conn->query($month_query);
    if ($month_result && $row = $month_result->fetch_assoc()) {
        $stats['month_orders'] = $row['count'];
        $stats['month_revenue'] = $row['revenue'];
    }
    
    // Orders by status
    $status_query = "SELECT order_status, COUNT(*) as count 
                     FROM orders 
                     GROUP BY order_status";
    $status_result = $conn->query($status_query);
    if ($status_result) {
        while ($row = $status_result->fetch_assoc()) {
            $key = $row['order_status'] . '_orders';
            if (isset($stats[$key])) {
                $stats[$key] = $row['count'];
            }
        }
    }
    
    // Payment status breakdown
    $payment_query = "SELECT payment_status, COUNT(*) as count 
                      FROM orders 
                      GROUP BY payment_status";
    $payment_result = $conn->query($payment_query);
    if ($payment_result) {
        while ($row = $payment_result->fetch_assoc()) {
            $key = 'payment_' . $row['payment_status'];
            if (isset($stats[$key])) {
                $stats[$key] = $row['count'];
            }
        }
    }
    
    // Courier statistics
    $courier_stats = "SELECT 
                        SUM(CASE WHEN tracking_code IS NOT NULL AND tracking_code != '' THEN 1 ELSE 0 END) as sent_to_courier,
                        SUM(CASE WHEN courier_status IN ('in_transit', 'out_for_delivery') THEN 1 ELSE 0 END) as in_transit
                      FROM orders";
    $courier_result = $conn->query($courier_stats);
    if ($courier_result && $row = $courier_result->fetch_assoc()) {
        $stats['sent_to_courier'] = $row['sent_to_courier'];
        $stats['in_transit'] = $row['in_transit'];
    }
    
    // Pending revenue (orders not yet paid)
    $pending_rev = "SELECT COALESCE(SUM(total_amount), 0) as pending_revenue
                    FROM orders 
                    WHERE payment_status IN ('pending', 'partial')
                    AND order_status NOT IN ('cancelled', 'refunded')";
    $pending_result = $conn->query($pending_rev);
    if ($pending_result && $row = $pending_result->fetch_assoc()) {
        $stats['pending_revenue'] = $row['pending_revenue'];
    }
    
    // Conversion rate (delivered / total)
    if ($stats['total_orders'] > 0) {
        $stats['conversion_rate'] = ($stats['delivered_orders'] / $stats['total_orders']) * 100;
    }
    
    if ($should_close) {
        $conn->close();
    }
    
    return $stats;
}

/**
 * Get daily analytics for charts
 */
function getDailyAnalytics($days = 30) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return [];
    }
    
    $query = "SELECT 
                DATE(created_at) as date,
                COUNT(*) as total_orders,
                SUM(CASE WHEN order_status != 'cancelled' THEN total_amount ELSE 0 END) as revenue,
                SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid
              FROM orders
              WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
              GROUP BY DATE(created_at)
              ORDER BY date DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $days);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $analytics = [];
    while ($row = $result->fetch_assoc()) {
        $analytics[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $analytics;
}

/**
 * Record partial payment
 */
function recordPartialPayment($order_id, $amount, $method = 'cash', $note = '') {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        $conn->begin_transaction();
        
        // Get order details
        $order = getOrderById($order_id);
        if (!$order) {
            throw new Exception('Order not found');
        }
        
        $current_partial = floatval($order['partial_payment_amount'] ?? 0);
        $new_partial = $current_partial + floatval($amount);
        $total = floatval($order['total_amount']);
        $remaining = $total - $new_partial;
        
        // Determine new payment status
        if ($remaining <= 0) {
            $payment_status = 'paid';
            $remaining = 0;
        } elseif ($new_partial > 0) {
            $payment_status = 'partial';
        } else {
            $payment_status = 'pending';
        }
        
        // Update order
        $stmt = $conn->prepare("
            UPDATE orders 
            SET partial_payment_amount = ?,
                remaining_amount = ?,
                payment_status = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param("ddsi", $new_partial, $remaining, $payment_status, $order_id);
        $stmt->execute();
        $stmt->close();
        
        // Record payment history
        $history_stmt = $conn->prepare("
            INSERT INTO payment_history 
            (order_id, payment_amount, payment_method, payment_status, payment_note)
            VALUES (?, ?, ?, ?, ?)
        ");
        $history_stmt->bind_param("idsss", $order_id, $amount, $method, $payment_status, $note);
        $history_stmt->execute();
        $history_stmt->close();
        
        $conn->commit();
        $conn->close();
        
        return [
            'success' => true,
            'message' => 'Payment recorded successfully',
            'new_total_paid' => $new_partial,
            'remaining' => $remaining,
            'payment_status' => $payment_status
        ];
        
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Enhanced update order status with history tracking
 */
function updateOrderStatusWithHistory($order_id, $new_status, $note = '', $changed_by = 'admin') {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection error'];
    }
    
    try {
        $conn->begin_transaction();
        
        // Get current status
        $order = getOrderById($order_id);
        if (!$order) {
            throw new Exception('Order not found');
        }
        
        $old_status = $order['order_status'];
        
        // Update order status
        $update_query = "UPDATE orders SET order_status = ?, updated_at = NOW()";
        $params = [$new_status];
        $types = 's';
        
        // Add specific timestamps based on status
        if ($new_status === 'delivered') {
            $update_query .= ", delivered_at = NOW()";
        } elseif ($new_status === 'cancelled') {
            $update_query .= ", cancelled_at = NOW(), cancelled_reason = ?";
            $params[] = $note;
            $types .= 's';
        }
        
        $update_query .= " WHERE id = ?";
        $params[] = $order_id;
        $types .= 'i';
        
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->close();
        
        // Record status change history
        $history_stmt = $conn->prepare("
            INSERT INTO order_status_history 
            (order_id, old_status, new_status, changed_by, note)
            VALUES (?, ?, ?, ?, ?)
        ");
        $history_stmt->bind_param("issss", $order_id, $old_status, $new_status, $changed_by, $note);
        $history_stmt->execute();
        $history_stmt->close();
        
        $conn->commit();
        $conn->close();
        
        return ['success' => true, 'message' => 'Order status updated successfully'];
        
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Get payment history for an order
 */
function getOrderPaymentHistory($order_id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return [];
    }
    
    $stmt = $conn->prepare("
        SELECT * FROM payment_history 
        WHERE order_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $history;
}

/**
 * Get status change history for an order
 */
function getOrderStatusHistory($order_id) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return [];
    }
    
    $stmt = $conn->prepare("
        SELECT * FROM order_status_history 
        WHERE order_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $history;
}

/**
 * Get top selling products
 */
function getTopSellingProducts($limit = 10, $days = 30) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return [];
    }
    
    $query = "SELECT 
                oi.product_name,
                SUM(oi.quantity) as total_quantity,
                SUM(oi.total_price) as total_revenue,
                COUNT(DISTINCT oi.order_id) as order_count
              FROM order_items oi
              INNER JOIN orders o ON oi.order_id = o.id
              WHERE o.created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
              AND o.order_status != 'cancelled'
              GROUP BY oi.product_name
              ORDER BY total_quantity DESC
              LIMIT ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $days, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $products;
}

/**
 * Update daily analytics (run via cron job)
 */
function updateDailyAnalytics($date = null) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return false;
    }
    
    if (!$date) {
        $date = date('Y-m-d');
    }
    
    $stats_query = "SELECT 
                      COUNT(*) as total_orders,
                      COALESCE(SUM(CASE WHEN order_status != 'cancelled' THEN total_amount ELSE 0 END), 0) as total_revenue,
                      COALESCE(AVG(CASE WHEN order_status != 'cancelled' THEN total_amount ELSE NULL END), 0) as avg_order_value,
                      SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) as pending,
                      SUM(CASE WHEN order_status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                      SUM(CASE WHEN order_status = 'processing' THEN 1 ELSE 0 END) as processing,
                      SUM(CASE WHEN order_status = 'shipped' THEN 1 ELSE 0 END) as shipped,
                      SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                      SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                      SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as payment_pending,
                      SUM(CASE WHEN payment_status = 'partial' THEN 1 ELSE 0 END) as payment_partial,
                      SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as payment_paid
                    FROM orders
                    WHERE DATE(created_at) = ?";
    
    $stmt = $conn->prepare($stats_query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();
    $stmt->close();
    
    // Insert or update analytics
    $insert_stmt = $conn->prepare("
        INSERT INTO order_analytics 
        (date, total_orders, total_revenue, average_order_value,
         pending_orders, confirmed_orders, processing_orders, shipped_orders,
         delivered_orders, cancelled_orders, payment_pending, payment_partial, payment_paid)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        total_orders = VALUES(total_orders),
        total_revenue = VALUES(total_revenue),
        average_order_value = VALUES(average_order_value),
        pending_orders = VALUES(pending_orders),
        confirmed_orders = VALUES(confirmed_orders),
        processing_orders = VALUES(processing_orders),
        shipped_orders = VALUES(shipped_orders),
        delivered_orders = VALUES(delivered_orders),
        cancelled_orders = VALUES(cancelled_orders),
        payment_pending = VALUES(payment_pending),
        payment_partial = VALUES(payment_partial),
        payment_paid = VALUES(payment_paid),
        updated_at = NOW()
    ");
    
    $insert_stmt->bind_param(
        "siddiiiiiiiii",
        $date,
        $stats['total_orders'],
        $stats['total_revenue'],
        $stats['avg_order_value'],
        $stats['pending'],
        $stats['confirmed'],
        $stats['processing'],
        $stats['shipped'],
        $stats['delivered'],
        $stats['cancelled'],
        $stats['payment_pending'],
        $stats['payment_partial'],
        $stats['payment_paid']
    );
    
    $result = $insert_stmt->execute();
    $insert_stmt->close();
    $conn->close();
    
    return $result;
}

/**
 * ENHANCED ANALYTICS FUNCTIONS
 * Additional helper functions for advanced order analytics
 */

/**
 * Get weekly performance comparison
 */
function getWeeklyPerformance($conn = null) {
    $should_close = false;
    
    if (!$conn) {
        $conn = getDatabaseConnection();
        $should_close = true;
    }
    
    if (!$conn) {
        return [];
    }
    
    $query = "SELECT 
                WEEK(created_at) as week_number,
                COUNT(*) as orders,
                SUM(CASE WHEN order_status != 'cancelled' THEN total_amount ELSE 0 END) as revenue,
                AVG(CASE WHEN order_status != 'cancelled' THEN total_amount ELSE NULL END) as avg_order
              FROM orders
              WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 8 WEEK)
              GROUP BY WEEK(created_at)
              ORDER BY week_number DESC";
    
    $result = $conn->query($query);
    $data = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    if ($should_close) {
        $conn->close();
    }
    
    return $data;
}

/**
 * Get hourly order distribution
 */
function getHourlyDistribution($conn = null, $days = 7) {
    $should_close = false;
    
    if (!$conn) {
        $conn = getDatabaseConnection();
        $should_close = true;
    }
    
    if (!$conn) {
        return [];
    }
    
    $query = "SELECT 
                HOUR(created_at) as hour,
                COUNT(*) as order_count
              FROM orders
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
              GROUP BY HOUR(created_at)
              ORDER BY hour";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $days);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = array_fill(0, 24, 0); // Initialize 24 hours with 0
    
    while ($row = $result->fetch_assoc()) {
        $data[$row['hour']] = $row['order_count'];
    }
    
    $stmt->close();
    
    if ($should_close) {
        $conn->close();
    }
    
    return $data;
}

/**
 * Get conversion funnel data
 */
function getConversionFunnel($conn = null) {
    $should_close = false;
    
    if (!$conn) {
        $conn = getDatabaseConnection();
        $should_close = true;
    }
    
    if (!$conn) {
        return [];
    }
    
    $query = "SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN order_status IN ('confirmed', 'processing', 'packed', 'shipped', 'out_for_delivery', 'delivered') THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN order_status IN ('processing', 'packed', 'shipped', 'out_for_delivery', 'delivered') THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN order_status IN ('shipped', 'out_for_delivery', 'delivered') THEN 1 ELSE 0 END) as shipped,
                SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
              FROM orders";
    
    $result = $conn->query($query);
    $data = $result->fetch_assoc();
    
    if ($should_close) {
        $conn->close();
    }
    
    // Calculate conversion rates
    if ($data['total_orders'] > 0) {
        $data['confirm_rate'] = ($data['confirmed'] / $data['total_orders']) * 100;
        $data['process_rate'] = ($data['processing'] / $data['total_orders']) * 100;
        $data['ship_rate'] = ($data['shipped'] / $data['total_orders']) * 100;
        $data['delivery_rate'] = ($data['delivered'] / $data['total_orders']) * 100;
        $data['cancel_rate'] = ($data['cancelled'] / $data['total_orders']) * 100;
    }
    
    return $data;
}

/**
 * Get revenue by shipping area
 */
function getRevenueByShippingArea($conn = null) {
    $should_close = false;
    
    if (!$conn) {
        $conn = getDatabaseConnection();
        $should_close = true;
    }
    
    if (!$conn) {
        return [];
    }
    
    $query = "SELECT 
                shipping_area,
                COUNT(*) as order_count,
                SUM(total_amount) as revenue,
                AVG(total_amount) as avg_order_value
              FROM orders
              WHERE order_status != 'cancelled'
              GROUP BY shipping_area";
    
    $result = $conn->query($query);
    $data = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    if ($should_close) {
        $conn->close();
    }
    
    return $data;
}

/**
 * Get payment method statistics
 */
function getPaymentMethodStats($conn = null) {
    $should_close = false;
    
    if (!$conn) {
        $conn = getDatabaseConnection();
        $should_close = true;
    }
    
    if (!$conn) {
        return [];
    }
    
    $query = "SELECT 
                payment_method,
                COUNT(*) as order_count,
                SUM(total_amount) as total_revenue,
                SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END) as collected_revenue,
                SUM(CASE WHEN payment_status = 'pending' THEN total_amount ELSE 0 END) as pending_revenue
              FROM orders
              WHERE order_status != 'cancelled'
              GROUP BY payment_method";
    
    $result = $conn->query($query);
    $data = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    if ($should_close) {
        $conn->close();
    }
    
    return $data;
}

/**
 * Get customer lifetime value distribution
 */
function getCustomerLTVDistribution($conn = null) {
    $should_close = false;
    
    if (!$conn) {
        $conn = getDatabaseConnection();
        $should_close = true;
    }
    
    if (!$conn) {
        return [];
    }
    
    $query = "SELECT 
                CASE 
                    WHEN customer_value <= 500 THEN '0-500'
                    WHEN customer_value <= 1000 THEN '501-1000'
                    WHEN customer_value <= 2000 THEN '1001-2000'
                    WHEN customer_value <= 5000 THEN '2001-5000'
                    ELSE '5000+'
                END as value_range,
                COUNT(*) as customer_count
              FROM (
                  SELECT 
                      customer_phone,
                      SUM(total_amount) as customer_value
                  FROM orders
                  WHERE order_status != 'cancelled'
                  GROUP BY customer_phone
              ) as customer_values
              GROUP BY value_range
              ORDER BY 
                  CASE value_range
                      WHEN '0-500' THEN 1
                      WHEN '501-1000' THEN 2
                      WHEN '1001-2000' THEN 3
                      WHEN '2001-5000' THEN 4
                      ELSE 5
                  END";
    
    $result = $conn->query($query);
    $data = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    if ($should_close) {
        $conn->close();
    }
    
    return $data;
}

/**
 * Get repeat customer rate
 */
function getRepeatCustomerRate($conn = null) {
    $should_close = false;
    
    if (!$conn) {
        $conn = getDatabaseConnection();
        $should_close = true;
    }
    
    if (!$conn) {
        return [];
    }
    
    $query = "SELECT 
                COUNT(DISTINCT customer_phone) as total_customers,
                SUM(CASE WHEN order_count > 1 THEN 1 ELSE 0 END) as repeat_customers,
                AVG(order_count) as avg_orders_per_customer
              FROM (
                  SELECT 
                      customer_phone,
                      COUNT(*) as order_count
                  FROM orders
                  GROUP BY customer_phone
              ) as customer_orders";
    
    $result = $conn->query($query);
    $data = $result->fetch_assoc();
    
    if ($data['total_customers'] > 0) {
        $data['repeat_rate'] = ($data['repeat_customers'] / $data['total_customers']) * 100;
    } else {
        $data['repeat_rate'] = 0;
    }
    
    if ($should_close) {
        $conn->close();
    }
    
    return $data;
}

/**
 * Get order processing time statistics
 */
function getProcessingTimeStats($conn = null) {
    $should_close = false;
    
    if (!$conn) {
        $conn = getDatabaseConnection();
        $should_close = true;
    }
    
    if (!$conn) {
        return [];
    }
    
    $query = "SELECT 
                AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_processing_hours,
                MIN(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as min_processing_hours,
                MAX(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as max_processing_hours
              FROM orders
              WHERE order_status IN ('delivered', 'shipped')
              AND updated_at > created_at";
    
    $result = $conn->query($query);
    $data = $result->fetch_assoc();
    
    if ($should_close) {
        $conn->close();
    }
    
    return $data;
}

/**
 * Get monthly comparison
 */
function getMonthlyComparison($conn = null) {
    $should_close = false;
    
    if (!$conn) {
        $conn = getDatabaseConnection();
        $should_close = true;
    }
    
    if (!$conn) {
        return [];
    }
    
    $query = "SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as orders,
                SUM(CASE WHEN order_status != 'cancelled' THEN total_amount ELSE 0 END) as revenue,
                AVG(CASE WHEN order_status != 'cancelled' THEN total_amount ELSE NULL END) as avg_order,
                SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
              FROM orders
              WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
              GROUP BY DATE_FORMAT(created_at, '%Y-%m')
              ORDER BY month DESC";
    
    $result = $conn->query($query);
    $data = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    if ($should_close) {
        $conn->close();
    }
    
    return $data;
}

/**
 * Export orders to CSV
 */
function exportOrdersToCSV($filters = []) {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        return false;
    }
    
    // Build query with filters
    $where_clauses = [];
    $params = [];
    $types = '';
    
    if (!empty($filters['status']) && $filters['status'] !== 'all') {
        $where_clauses[] = "order_status = ?";
        $params[] = $filters['status'];
        $types .= 's';
    }
    
    if (!empty($filters['date_from'])) {
        $where_clauses[] = "DATE(created_at) >= ?";
        $params[] = $filters['date_from'];
        $types .= 's';
    }
    
    if (!empty($filters['date_to'])) {
        $where_clauses[] = "DATE(created_at) <= ?";
        $params[] = $filters['date_to'];
        $types .= 's';
    }
    
    $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';
    
    $query = "SELECT 
                order_number,
                customer_name,
                customer_phone,
                customer_email,
                customer_address,
                total_amount,
                order_status,
                payment_status,
                payment_method,
                shipping_area,
                tracking_code,
                created_at,
                updated_at
              FROM orders 
              $where_sql 
              ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Generate CSV
    $filename = 'orders_export_' . date('Y-m-d_His') . '.csv';
    $filepath = '/home/claude/' . $filename;
    
    $fp = fopen($filepath, 'w');
    
    // Write headers
    fputcsv($fp, [
        'Order Number',
        'Customer Name',
        'Phone',
        'Email',
        'Address',
        'Amount',
        'Order Status',
        'Payment Status',
        'Payment Method',
        'Shipping Area',
        'Tracking Code',
        'Created At',
        'Updated At'
    ]);
    
    // Write data
    while ($row = $result->fetch_assoc()) {
        fputcsv($fp, $row);
    }
    
    fclose($fp);
    $stmt->close();
    $conn->close();
    
    return $filename;
}
?>
