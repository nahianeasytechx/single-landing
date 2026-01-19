<?php



    function getDatabaseConnection() {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'single_landing';
    // $host = 'localhost';
    // $username = 'sidratul';
    // $password = 'L5e567zQnJx.A:';
    // $database = 'sidratul_muntaha';
    
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        return null;
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
        
        // Delete product (cascades to images and colors)
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Product deleted successfully'];
        } else {
            throw new Exception('Failed to delete product');
        }
        
    } catch (Exception $e) {
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