<?php


// Include database functions
require_once './components/header.php';

// Check if user is logged in and is admin
if (!isLoggedIn()) {
    $_SESSION['error_message'] = 'Please login first';
    header('Location: login.php');
    exit();
}

// Check if product ID is provided
if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    $_SESSION['error_message'] = 'Product ID is required';
   echo '<script>window.location.href = "products.php";</script>';
    exit();
}

$product_id = intval($_POST['product_id']);

// Get product details before deletion (for logging/messages)
$product = getProductById($product_id);

if (!$product) {
    $_SESSION['error_message'] = 'Product not found';
    header('Location: products.php');
    exit();
}

// Delete the product
$result = deleteProduct($product_id);

if ($result['success']) {
    $_SESSION['success_message'] = 'Product "' . htmlspecialchars($product['product_name']) . '" deleted successfully';
} else {
    $_SESSION['error_message'] = 'Failed to delete product: ' . $result['message'];
}

// Redirect back to products page
echo '<script>window.location.href = "products.php";</script>';
exit();
?>