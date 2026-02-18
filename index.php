<?php
// Set current page for header navigation
$current_page = basename($_SERVER['PHP_SELF']);

// Include functions file which contains database connection
require_once __DIR__ . '/./components/functions.php';

// Get database connection
$conn = getDatabaseConnection();

if (!$conn) {
    die('Database connection failed. Please check your database configuration.');
}

// Handle order submission
$order_success = false;
$order_message = '';
$order_number = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    $order_data = [
        'customer_name' => $_POST['name'] ?? '',
        'customer_phone' => $_POST['phone'] ?? '',
        'customer_address' => $_POST['address'] ?? '',
        'customer_email' => $_POST['email'] ?? '',
        'notes' => $_POST['notes'] ?? '',
        'shipping_area' => $_POST['shipping'] ?? '',
        'product_set_id' => $_POST['product_variation'] ?? 0,
        'quantity' => intval($_POST['quantity'] ?? 1)
    ];

    $result = createOrder($order_data);

    if ($result['success']) {
        $order_success = true;
        $order_number = $result['order_number'];
        $order_message = 'আপনার অর্ডারটি সফলভাবে সম্পন্ন হয়েছে! অর্ডার নম্বর: #' . $order_number . '\n\nআমরা শীঘ্রই আপনার সাথে যোগাযোগ করব।';
    } else {
        $order_message = $result['message'] ?? 'অর্ডার সম্পন্ন করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।';
    }
}

require_once './components/header.php';

// Get product ID from URL parameter
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

// If no product_id provided, try to load the first active template
if (!$product_id) {
    $auto_query = "SELECT product_id FROM landing_page_templates WHERE is_active = 1 ORDER BY id DESC LIMIT 1";
    $auto_result = $conn->query($auto_query);

    if ($auto_result && $auto_result->num_rows > 0) {
        $auto_row = $auto_result->fetch_assoc();
        $product_id = $auto_row['product_id'];
    } else {
        die('No active landing page template found. Please create a template first or access with ?product_id=X');
    }
}

// Fetch active landing page template for this product
$query = "SELECT * FROM landing_page_templates WHERE product_id = ? AND is_active = 1 LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$template = $result->fetch_assoc();

if (!$template) {
    die('No active landing page template found for product ID: ' . $product_id . '. Please make sure the template is set as active.');
}

// Fetch product details WITH MAIN IMAGE
$product_query = "SELECT p.*, 
                  (SELECT image_path FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                  FROM products p 
                  WHERE p.id = ?";
$product_stmt = $conn->prepare($product_query);
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();
$product = $product_result->fetch_assoc();

if (!$product) {
    die('Product not found.');
}

// Fetch product sets (variations)
$variations_query = "SELECT * FROM product_sets WHERE product_id = ? AND status = 'active' ORDER BY display_order ASC, pieces_count ASC";
$variations_stmt = $conn->prepare($variations_query);

if (!$variations_stmt) {
    die('Error preparing statement: ' . $conn->error);
}

$variations_stmt->bind_param("i", $product_id);
$variations_stmt->execute();
$variations_result = $variations_stmt->get_result();
$variations = [];
while ($row = $variations_result->fetch_assoc()) {
    $variations[] = $row;
}

// Fetch template images from template_images table
$review_images = [];
$product_slider_images = [];

$img_query = "SELECT * FROM template_images WHERE template_id = ? ORDER BY section, display_order";
$img_stmt = $conn->prepare($img_query);

if ($img_stmt) {
    $img_stmt->bind_param("i", $template['id']);
    $img_stmt->execute();
    $img_result = $img_stmt->get_result();

    while ($img_row = $img_result->fetch_assoc()) {
        if ($img_row['section'] === 'review_slider') {
            $review_images[] = $img_row['image_path'];
        } elseif ($img_row['section'] === 'product_slider') {
            $product_slider_images[] = $img_row['image_path'];
        }
    }
    $img_stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($template['brand_name'] ?: 'Beauty & Mine'); ?> - <?php echo htmlspecialchars($product['product_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #E91E63;
            --primary-dark: #C2185B;
            --primary-light: #F48FB1;
            --secondary-color: #1a1a1a;
            --accent-gold: #FFB300;
            --accent-success: #00C853;
            --text-dark: #212121;
            --text-light: #757575;
            --text-muted: #9E9E9E;
            --bg-light: #FAFAFA;
            --bg-gradient: linear-gradient(135deg, #FFF5F8 0%, #FFFFFF 100%);
            --bg-white: #FFFFFF;
            --border-color: #E0E0E0;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12);
            --shadow-xl: 0 12px 40px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Noto Sans Bengali', 'Hind Siliguri', sans-serif;
            overflow-x: hidden;
            background: var(--bg-white);
            color: var(--text-dark);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            line-height: 1.2;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }

            100% {
                background-position: 1000px 0;
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            50% {
                transform: translate(-30px, 30px) rotate(2deg);
            }
        }

        /* Sticky Header */
        .sticky-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            z-index: 1000;
            padding: 10px 0;
            box-shadow: var(--shadow-sm);
            transform: translateY(-100%);
            transition: transform 0.3s ease;
        }

        .sticky-header.visible {
            transform: translateY(0);
        }

        .sticky-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            padding: 0 20px;
        }

        .sticky-header .brand {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .sticky-header .cta-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 10px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            transition: var(--transition);
            box-shadow: 0 2px 8px rgba(233, 30, 99, 0.3);
        }

        .sticky-header .cta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(233, 30, 99, 0.4);
        }

        /* Section 1 - Hero Section */
        .section-hero {
            min-height: 100vh;
            background: var(--bg-gradient);
            padding: 15px 15px 60px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .section-hero::before {
            content: '';
            position: absolute;
            top: -200px;
            right: -200px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(233, 30, 99, 0.06) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s ease-in-out infinite;
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            max-width: 160px;
            height: auto;
            margin: 0 auto 30px;
            display: block;
            animation: fadeInUp 0.8s ease;
        }

        .hero-title {
            font-size: 56px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            margin-bottom: 20px;
            line-height: 1.1;
            letter-spacing: -1px;
            animation: fadeInUp 0.8s ease 0.2s backwards;
        }

        .hero-subtitle {
            text-align: center;
            font-size: 18px;
            color: var(--text-light);
            margin-bottom: 40px;
            font-weight: 400;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 0.8s ease 0.4s backwards;
        }

        .comparison-wrapper {
            max-width: 900px;
            margin: 0 auto 35px;
            animation: scaleIn 0.8s ease 0.6s backwards;
        }

        .comparison-container {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-xl);
            background: white;
        }

        .comparison-image {
            width: 100%;
            display: block;
        }

        .comparison-label {
            position: absolute;
            background: linear-gradient(135deg, rgba(233, 30, 99, 0.95) 0%, rgba(194, 24, 91, 0.95) 100%);
            color: white;
            padding: 12px 28px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            top: 50%;
            transform: translateY(-50%);
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 16px rgba(233, 30, 99, 0.3);
        }

        .comparison-label.before {
            left: 0;
            border-radius: 0 20px 20px 0;
        }

        .comparison-label.after {
            right: 0;
            border-radius: 20px 0 0 20px;
        }

        .benefit-highlight {
            background: white;
            padding: 28px 35px;
            border-radius: 16px;
            font-size: 20px;
            font-weight: 500;
            color: var(--text-dark);
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            border: 2px solid var(--border-color);
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease 0.8s backwards;
        }

        .benefit-highlight::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(233, 30, 99, 0.04), transparent);
            animation: shimmer 3s infinite;
        }

        /* Section 2 - Product Showcase */
        .section-showcase {
            background: white;
            padding: 70px 15px;
            position: relative;
        }

        .showcase-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .product-visual {
            position: relative;
        }


        .product-image {
            width: 100%;
            height: auto;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.12));
            transition: var(--transition);
        }

        .product-image:hover {
            transform: scale(1.02) rotate(-1deg);
        }

        .product-content h2 {
            font-size: 42px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 30px;
            line-height: 1.2;
        }

        .product-content h2 .highlight {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }

        .product-content h2 .highlight::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), transparent);
            border-radius: 2px;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 18px;
            background: linear-gradient(135deg, #FAFAFA 0%, white 100%);
            border-radius: 14px;
            border-left: 3px solid var(--primary-color);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .feature-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--accent-gold) 100%);
            transition: var(--transition);
        }

        .feature-item:hover {
            background: white;
            box-shadow: var(--shadow-md);
            transform: translateX(8px);
        }

        .feature-item:hover::before {
            width: 100%;
            opacity: 0.04;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(233, 30, 99, 0.3);
        }

        .feature-text {
            font-size: 16px;
            color: var(--text-dark);
            line-height: 1.6;
            font-weight: 400;
        }

        /* Section 3 - Social Proof */
        .section-reviews {
            background: var(--bg-gradient);
            padding: 70px 15px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-header h2 {
            font-size: 42px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 12px;
        }

        .section-header h2 .highlight {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-header p {
            font-size: 17px;
            color: var(--text-light);
            max-width: 500px;
            margin: 0 auto;
        }

        .reviews-container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
        }

        .review-swiper {
            padding: 15px 0 60px;
        }

        .review-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            height: 100%;
        }

        .review-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }

        .review-card img {
            width: 100%;
            height: auto;
            display: block;
        }

        .swiper-button-next,
        .swiper-button-prev {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: 50%;
            color: white !important;
            box-shadow: 0 4px 12px rgba(233, 30, 99, 0.3);
            transition: var(--transition);
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(233, 30, 99, 0.4);
        }

        .swiper-button-next:after,
        .swiper-button-prev:after {
            font-size: 18px;
            font-weight: bold;
        }

        .swiper-pagination-bullet {
            width: 10px;
            height: 10px;
            background: var(--border-color);
            opacity: 1;
            transition: var(--transition);
        }

        .swiper-pagination-bullet-active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            width: 32px;
            border-radius: 8px;
        }

        /* Section 4 - Product Selection CTA */
        .section-cta {
            background: white;
            padding: 70px 15px;
        }

        .cta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 45px;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .product-gallery {
            position: relative;
        }

        .gallery-main {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-xl);
            background: white;
        }

        .gallery-main img {
            width: 100%;
            display: block;
        }

        .cta-content {
            padding: 25px;
        }

        .cta-content h3 {
            font-size: 36px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 24px;
        }

        .cta-content h3 .highlight {
            color: var(--primary-color);
        }

        .benefits-list {
            list-style: none;
            margin-bottom: 30px;
        }

        .benefits-list li {
            font-size: 16px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            background: linear-gradient(135deg, #FAFAFA 0%, white 100%);
            border-radius: 12px;
            font-weight: 500;
            transition: var(--transition);
        }

        .benefits-list li:hover {
            background: white;
            box-shadow: var(--shadow-sm);
            transform: translateX(4px);
        }

        .benefits-list li::before {
            content: '✓';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 50%;
            font-size: 12px;
            font-weight: bold;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(233, 30, 99, 0.25);
        }

        .price-box {
            background: linear-gradient(135deg, #FFF5F8 0%, white 100%);
            padding: 24px;
            border-radius: 16px;
            text-align: center;
            margin-bottom: 24px;
            border: 2px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .price-box::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(233, 30, 99, 0.03) 0%, transparent 70%);
        }

        .price-original {
            font-size: 18px;
            color: var(--text-light);
            text-decoration: line-through;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .price-current {
            font-size: 44px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }

        .price-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--accent-success) 0%, #00A344 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cta-button {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 18px 40px;
            font-size: 18px;
            font-weight: 700;
            border-radius: 50px;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
            box-shadow: 0 8px 24px rgba(233, 30, 99, 0.35);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .cta-button:hover::before {
            left: 100%;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(233, 30, 99, 0.45);
        }

        .trust-badges {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .trust-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--text-light);
            font-weight: 500;
        }

        .trust-badge i {
            color: var(--primary-color);
            font-size: 16px;
        }

        /* Section 5 - Contact */
        .section-contact {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            padding: 60px 15px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .section-contact::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(233, 30, 99, 0.08) 0%, transparent 70%);
            border-radius: 50%;
        }

        .section-contact h2 {
            font-size: 38px;
            font-weight: 800;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .section-contact p {
            font-size: 17px;
            margin-bottom: 35px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .contact-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .contact-btn {
            padding: 14px 32px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            color: white;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .whatsapp-btn {
            background: #13AD4D;
        }

        .messenger-btn {
            background: linear-gradient(135deg, #0084FF 0%, #0066CC 100%);
        }

        .contact-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.4);
            color: white;
        }

        /* Section 6 - Checkout Form */
        .section-checkout {
            background: white;
            padding: 70px 15px;
        }

        .checkout-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .checkout-header h2 {
            font-size: 42px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 12px;
        }

        .checkout-header p {
            font-size: 17px;
            color: var(--text-light);
        }

        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 480px;
            gap: 40px;
        }

        .checkout-form {
            background: white;
            padding: 35px;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            border: 2px solid var(--border-color);
        }

        .checkout-sidebar {
            position: sticky;
            top: 100px;
            align-self: start;
        }

        .form-section-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 24px;
            padding-bottom: 14px;
            border-bottom: 2px solid var(--primary-color);
            position: relative;
        }

        .form-section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 80px;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-gold));
        }

        .form-field {
            margin-bottom: 20px;
        }

        .form-field label {
            display: block;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-field input,
        .form-field textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 15px;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
            background: white;
        }

        .form-field input:focus,
        .form-field textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.1);
        }

        .shipping-section,
        .payment-section {
            margin-top: 32px;
        }

        .option-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            margin-bottom: 12px;
            border: 2px solid var(--border-color);
            border-radius: 14px;
            cursor: pointer;
            transition: var(--transition);
            background: white;
        }

        .option-card:hover {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, #FFF5F8 0%, white 100%);
            transform: translateX(6px);
        }

        .option-card:has(input[type="radio"]:checked) {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, #FFF5F8 0%, white 100%);
            box-shadow: 0 4px 16px rgba(233, 30, 99, 0.15);
        }

        .option-card input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary-color);
        }

        .option-card label {
            cursor: pointer;
            margin: 0;
            flex: 1;
            font-weight: 500;
        }

        .product-selector {
            background: white;
            padding: 28px;
            border-radius: 20px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-lg);
            border: 2px solid var(--border-color);
        }

        .product-option {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            margin-bottom: 14px;
            border: 2px solid var(--border-color);
            border-radius: 16px;
            transition: var(--transition);
            cursor: pointer;
            background: white;
        }

        .product-option:hover {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, #FFF5F8 0%, white 100%);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .product-option:has(input[type="radio"]:checked) {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, #FFF5F8 0%, white 100%);
            box-shadow: 0 6px 20px rgba(233, 30, 99, 0.2);
        }

        .product-option input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary-color);
        }

        .product-option img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid var(--border-color);
        }

        .product-details {
            flex: 1;
        }

        .product-name {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 6px;
            font-size: 15px;
        }

        .product-meta {
            font-size: 13px;
            color: var(--text-light);
            font-weight: 500;
        }

        .product-price {
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 16px;
        }

        .special-offer-badge {
            background: linear-gradient(135deg, #FF5722 0%, #E64A19 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 10px;
            font-weight: 700;
            margin-left: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qty-button {
            width: 32px;
            height: 32px;
            border: 2px solid var(--primary-color);
            background: white;
            color: var(--primary-color);
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .qty-button:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .qty-button:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .qty-display {
            width: 50px;
            height: 32px;
            text-align: center;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-weight: 700;
            font-size: 15px;
            background: #FAFAFA;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .order-summary {
            background: white;
            padding: 28px;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            border: 2px solid var(--border-color);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 14px 0;
            border-bottom: 1px solid var(--border-color);
            font-size: 15px;
        }

        .summary-row.total {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            border-bottom: none;
            margin-top: 16px;
            padding-top: 20px;
            border-top: 2px solid var(--primary-color);
        }

        .submit-button {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 18px;
            width: 100%;
            border-radius: 50px;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 24px;
            transition: var(--transition);
            box-shadow: 0 8px 24px rgba(233, 30, 99, 0.35);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        .submit-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .submit-button:hover::before {
            left: 100%;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(233, 30, 99, 0.45);
        }

        .privacy-notice {
            font-size: 12px;
            color: var(--text-light);
            margin-top: 20px;
            line-height: 1.7;
            text-align: center;
        }

        /* Footer */
        footer {
            color: white;
            text-align: center;
            padding: 35px 15px;
        }

        footer p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }

        /* Carousel Controls */
        .carousel-control-prev,
        .carousel-control-next {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.95;
            box-shadow: 0 4px 12px rgba(233, 30, 99, 0.35);
            transition: var(--transition);
        }

        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-indicators button {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--border-color);
            opacity: 1;
            transition: var(--transition);
        }

        .carousel-indicators button.active {
            background: var(--primary-color);
            width: 32px;
            border-radius: 8px;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .checkout-grid {
                display: flex;
                flex-direction: column;
            }

            .checkout-sidebar {
                position: static;
                display: contents; /* This makes the sidebar's children direct children of checkout-grid */
            }

            .product-selector {
                order: 1; /* Appears first */
                margin-bottom: 20px;
            }

            .checkout-form {
                order: 2; /* Appears second */
            }

            .order-summary {
                order: 3; /* Appears third (last) */
            }

            .showcase-grid,
            .cta-grid {
                grid-template-columns: 1fr;
                gap: 45px;
            }

            .hero-title {
                font-size: 48px;
            }

            .product-content h2,
            .section-header h2 {
                font-size: 36px;
            }
        }

        @media (max-width: 768px) {

            .section-hero,
            .section-showcase,
            .section-reviews,
            .section-cta,
            .section-contact,
            .section-checkout {
                padding: 15px 15px;
            }

            .hero-title {
                font-size: 36px;
            }

            .hero-subtitle {
                font-size: 16px;
            }

            .comparison-label {
                padding: 10px 20px;
                font-size: 12px;
            }

            .benefit-highlight {
                font-size: 17px;
                padding: 24px 28px;
            }

            .product-content h2,
            .section-header h2,
            .checkout-header h2 {
                font-size: 30px;
            }

            .section-contact h2 {
                font-size: 32px;
            }

            .feature-text {
                font-size: 15px;
            }

            .cta-content {
                padding: 15px;
            }

            .cta-content h3 {
                font-size: 28px;
            }

            .price-current {
                font-size: 36px;
            }

            .checkout-form,
            .product-selector,
            .order-summary {
                padding: 24px;
            }

            .sticky-header {
                padding: 8px 0;
            }

            .sticky-header .brand {
                font-size: 18px;
            }

            .sticky-header .cta-btn {
                padding: 8px 18px;
                font-size: 12px;
            }

            .product-visual::before {
                top: -20px;
                left: -20px;
                right: -20px;
                bottom: -20px;
            }

            .contact-buttons {
                gap: 15px;
            }

            .contact-btn {
                font-size: 14px;
                padding: 12px 24px;
            }
        }

        @media (max-width: 480px) {

            .section-hero,
            .section-showcase,
            .section-reviews,
            .section-cta,
            .section-contact,
            .section-checkout {
                padding: 12px 12px;
            }

            .hero-title {
                font-size: 28px;
                margin-bottom: 16px;
            }

            .hero-subtitle {
                font-size: 15px;
                margin-bottom: 30px;
            }

            .comparison-wrapper {
                margin-bottom: 25px;
            }

            .benefit-highlight {
                font-size: 16px;
                padding: 20px 24px;
            }
.benefits-list{
    padding:0;
}
            .product-content h2,
            .section-header h2,
            .checkout-header h2 {
                font-size: 20px;
                margin-top: 10px;
            }

            .section-contact h2 {
                font-size: 20px;
            }

            .cta-content h3 {
                font-size: 24px;
                margin-bottom: 20px;
            }

            .benefits-list li {
                font-size: 14px;
                padding: 12px;
            }

            .price-current {
                font-size: 32px;
            }

            .cta-button {
                padding: 16px 32px;
                font-size: 16px;
            }

            .carousel-control-prev,
            .carousel-control-next {
                display: none;
            }

            .product-option {
                flex-wrap: wrap;
                padding: 14px;
            }

            .quantity-selector {
                margin-left: 0;
                margin-top: 12px;
                width: 100%;
                justify-content: center;
            }

            .checkout-form,
            .product-selector,
            .order-summary {
                padding: 20px;
            }

            .form-section-title {
                font-size: 19px;
            }

            .summary-row.total {
                font-size: 24px;
            }

            .brand-logo {
                max-width: 140px;
                margin-bottom: 24px;
            }

            .feature-item {
                padding: 14px;
            }

            .feature-icon {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }

            .trust-badges {
                gap: 15px;
                margin-top: 20px;
            }

            .trust-badge {
                font-size: 12px;
            }

            .contact-btn {
                font-size: 13px;
                padding: 11px 20px;
            }

            .swiper-button-next,
            .swiper-button-prev {
                width: 40px;
                height: 40px;
            }

            .swiper-button-next:after,
            .swiper-button-prev:after {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <!-- Sticky Header -->
    <div class="sticky-header" id="stickyHeader">
        <div class="container">
            <div class="brand"><?php echo htmlspecialchars($template['brand_name'] ?: 'Beauty & Mine'); ?></div>
            <a href="#checkoutSection" class="cta-btn">অৰ্ডার করুন</a>
        </div>
    </div>

    <?php if ($order_success): ?>
        <script>
            alert('<?php echo $order_message; ?>');
        </script>
    <?php elseif (!empty($order_message)): ?>
        <script>
            alert('<?php echo $order_message; ?>');
        </script>
    <?php endif; ?>

    <!-- Section 1: Hero -->
    <section class="section-hero">
        <div class="hero-container">
            <?php if ($template['brand_logo']): ?>
                <img src="./uploads/templates/<?php echo htmlspecialchars($template['brand_logo']); ?>"
                    alt="<?php echo htmlspecialchars($template['brand_name']); ?>"
                    class="brand-logo">
            <?php endif; ?>

            <?php if ($template['hero_title']): ?>
                <h1 class="hero-title"><?php echo nl2br(htmlspecialchars($template['hero_title'])); ?></h1>
            <?php endif; ?>

            <?php if ($template['hero_subtitle']): ?>
                <p class="hero-subtitle"><?php echo nl2br(htmlspecialchars($template['hero_subtitle'])); ?></p>
            <?php endif; ?>

            <?php if ($template['hero_comparison_image']): ?>
                <div class="comparison-wrapper">
                    <div class="comparison-container">
                        <img src="./uploads/templates/<?php echo htmlspecialchars($template['hero_comparison_image']); ?>"
                            alt="Before After Comparison" class="comparison-image">
                    </div>
                    <button class="cta-button mt-5" onclick="scrollToCheckout()">অৰ্ডার করুন</button>
                </div>
            <?php endif; ?>

            <?php if ($template['hero_benefit_text']): ?>
                <div class="benefit-highlight">
                    <?php echo nl2br(htmlspecialchars($template['hero_benefit_text'])); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Section 2: Product Showcase -->
    <section class="section-showcase">
        <div class="showcase-grid">
            <?php if ($template['product_showcase_image']): ?>
                <div class="product-visual">
                    <img src="./uploads/templates/<?php echo htmlspecialchars($template['product_showcase_image']); ?>"
                        alt="Product Showcase" class="product-image">
                </div>
            <?php endif; ?>

            <div class="product-content">
                <?php if ($template['features_title']): ?>
                    <h2><?php
                        $title = htmlspecialchars($template['features_title']);
                        $words = explode(' ', $title);
                        if (count($words) > 1) {
                            $lastWord = array_pop($words);
                            echo implode(' ', $words) . ' <span class="highlight">' . $lastWord . '</span>';
                        } else {
                            echo '<span class="highlight">' . $title . '</span>';
                        }
                        ?></h2>
                <?php endif; ?>

                <div class="feature-list">
                    <?php if ($template['feature_1_text']): ?>
                        <div class="feature-item">
                            <div class="feature-icon">✓</div>
                            <div class="feature-text"><?php echo nl2br(htmlspecialchars($template['feature_1_text'])); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($template['feature_2_text']): ?>
                        <div class="feature-item">
                            <div class="feature-icon">✓</div>
                            <div class="feature-text"><?php echo nl2br(htmlspecialchars($template['feature_2_text'])); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($template['feature_3_text']): ?>
                        <div class="feature-item">
                            <div class="feature-icon">✓</div>
                            <div class="feature-text"><?php echo nl2br(htmlspecialchars($template['feature_3_text'])); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($template['feature_4_text']): ?>
                        <div class="feature-item">
                            <div class="feature-icon">✓</div>
                            <div class="feature-text"><?php echo nl2br(htmlspecialchars($template['feature_4_text'])); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: Customer Reviews -->
    <?php if (!empty($review_images)): ?>
        <section class="section-reviews">
            <div class="section-header">
                <?php if ($template['reviews_title']): ?>
                    <h2><?php
                        $reviewTitle = htmlspecialchars($template['reviews_title']);
                        $words = explode(' ', $reviewTitle);
                        if (count($words) > 1) {
                            $lastWord = array_pop($words);
                            echo implode(' ', $words) . ' <span class="highlight">' . $lastWord . '</span>';
                        } else {
                            echo '<span class="highlight">' . $reviewTitle . '</span>';
                        }
                        ?></h2>
                <?php endif; ?>
                <p>দেখুন আমাদের সন্তুষ্ট গ্রাহকদের মতামত</p>
            </div>

            <div class="reviews-container">
                <div class="swiper review-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($review_images as $index => $image): ?>
                            <div class="swiper-slide">
                                <div class="review-card">
                                    <img src="./uploads/templates/<?php echo htmlspecialchars($image); ?>"
                                        alt="Customer Review <?php echo $index + 1; ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Section 4: Product Selection CTA -->
    <section class="section-cta">
        <div class="cta-grid">
            <?php if (!empty($product_slider_images)): ?>
                <div class="product-gallery">
                    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <?php foreach ($product_slider_images as $index => $image): ?>
                                <button type="button" data-bs-target="#productCarousel"
                                    data-bs-slide-to="<?php echo $index; ?>"
                                    <?php echo $index === 0 ? 'class="active"' : ''; ?>></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="carousel-inner gallery-main">
                            <?php foreach ($product_slider_images as $index => $image): ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="./uploads/templates/<?php echo htmlspecialchars($image); ?>"
                                        alt="Product Gallery <?php echo $index + 1; ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($product_slider_images) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="cta-content">
                <?php if ($template['order_title']): ?>
                    <h3><?php
                        $orderTitle = htmlspecialchars($template['order_title']);
                        $words = explode(' ', $orderTitle);
                        if (count($words) > 1) {
                            $lastWord = array_pop($words);
                            echo implode(' ', $words) . ' <span class="highlight">' . $lastWord . '</span>';
                        } else {
                            echo $orderTitle;
                        }
                        ?></h3>
                <?php endif; ?>

                <ul class="benefits-list">
                    <?php if ($template['order_benefit_1']): ?>
                        <li><?php echo htmlspecialchars($template['order_benefit_1']); ?></li>
                    <?php endif; ?>
                    <?php if ($template['order_benefit_2']): ?>
                        <li><?php echo htmlspecialchars($template['order_benefit_2']); ?></li>
                    <?php endif; ?>
                </ul>

                <?php if ($template['order_subtitle']): ?>
                    <p style="text-align: center; margin-bottom: 24px; color: var(--text-light); font-size: 15px;">
                        <?php echo nl2br(htmlspecialchars($template['order_subtitle'])); ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($variations) && isset($variations[0])): ?>
                    <div class="price-box">
                        <div class="price-original">রেগুলার মূল্য: ৳<?php echo number_format($variations[0]['unit_price'] * $variations[0]['pieces_count'], 0); ?></div>
                        <div class="price-current">৳<?php echo number_format($variations[0]['total_price'], 0); ?></div>
                        <span class="price-badge">স্পেশাল অফার</span>
                    </div>
                <?php endif; ?>

                <button class="cta-button" onclick="scrollToCheckout()">অৰ্ডার করুন</button>

                <div class="trust-badges">
                    <div class="trust-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>নিরাপদ পেমেন্ট</span>
                    </div>
                    <div class="trust-badge">
                        <i class="fas fa-truck"></i>
                        <span>ফ্রি ডেলিভারি</span>
                    </div>
                    <div class="trust-badge">
                        <i class="fas fa-check-circle"></i>
                        <span>অরিজিনাল প্রোডাক্ট</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 5: Contact -->
    <section class="section-contact">
        <?php if ($template['contact_title']): ?>
            <h2><?php echo htmlspecialchars($template['contact_title']); ?></h2>
        <?php endif; ?>

        <?php if ($template['contact_subtitle']): ?>
            <p><?php echo nl2br(htmlspecialchars($template['contact_subtitle'])); ?></p>
        <?php endif; ?>

        <div class="contact-buttons">
            <?php if (!empty($template['whatsapp_link'])): ?>
                <a href="<?php echo htmlspecialchars($template['whatsapp_link']); ?>" class="contact-btn whatsapp-btn" target="_blank">
                    <i class="fab fa-whatsapp"></i>
                    <?php echo !empty($template['whatsapp_number']) ? htmlspecialchars($template['whatsapp_number']) : 'WhatsApp এ যোগাযোগ করুন'; ?>
                </a>
            <?php endif; ?>

            <?php if (!empty($template['messenger_link'])): ?>
                <a href="<?php echo htmlspecialchars($template['messenger_link']); ?>" class="contact-btn messenger-btn" target="_blank">
                    <i class="fab fa-facebook-messenger"></i> Messenger
                </a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Section 6: Checkout -->
    <section class="section-checkout" id="checkoutSection">
        <div class="checkout-header">
            <h2><?php echo htmlspecialchars($template['checkout_title'] ?: 'অৰ্ডারটি সম্পূর্ণ করুন'); ?></h2>
            <p><?php echo htmlspecialchars($template['checkout_subtitle'] ?: 'আমরা শীঘ্রই আপনার সাথে যোগাযোগ করবো'); ?></p>
        </div>

        <div class="checkout-container">
            <form id="checkoutForm" method="POST" action="">
                <input type="hidden" name="submit_order" value="1">
                <input type="hidden" name="quantity" id="formQuantity" value="1">

                <div class="checkout-grid">
                    <!-- Left Column: Form -->
                    <div class="checkout-form">
                        <div class="form-section-title">Customer Information</div>

                        <div class="form-field">
                            <label>আপনার নাম *</label>
                            <input type="text" name="name" placeholder="পুরো নাম লিখুন" required>
                        </div>

                        <div class="form-field">
                            <label>সম্পূর্ণ ঠিকানা *</label>
                            <input type="text" name="address" placeholder="বাসা নং, রোড, এলাকা, থানা, জেলা" required>
                        </div>

                        <div class="form-field">
                            <label>মোবাইল নম্বর *</label>
                            <input type="tel" name="phone" placeholder="01XXXXXXXXX" required>
                        </div>
<!-- 
                        <div class="form-field">
                            <label>ইমেইল (ঐচ্ছিক)</label>
                            <input type="email" name="email" placeholder="your@email.com">
                        </div> -->

                        <div class="form-field">
                            <label>বিশেষ নির্দেশনা (ঐচ্ছিক)</label>
                            <textarea name="notes" placeholder="ডেলিভারি সম্পর্কিত কোন বিশেষ নির্দেশনা থাকলে লিখুন" rows="3"></textarea>
                        </div>

                        <div class="shipping-section">
                            <div class="form-section-title">Shipping Method</div>

                            <div class="option-card" onclick="selectShippingOption('dhaka')">
                                <input type="radio" name="shipping" id="dhaka" value="dhaka" required onchange="updateTotal()">
                                <label for="dhaka">
                                    <strong>ঢাকা সিটির ভিতরে</strong><br>
                                    <small>ডেলিভারি চার্জ: <?php echo number_format($template['shipping_dhaka_cost'], 0); ?>৳</small>
                                </label>
                            </div>

                            <div class="option-card" onclick="selectShippingOption('urban')">
                                <input type="radio" name="shipping" id="urban" value="urban" required onchange="updateTotal()">
                                <label for="urban">
                                    <strong>ঢাকার আশেপাশে (শহরতলী)</strong><br>
                                    <small>ডেলিভারি চার্জ: <?php echo number_format($template['shipping_urban_cost'], 0); ?>৳</small>
                                </label>
                            </div>

                            <div class="option-card" onclick="selectShippingOption('outside')">
                                <input type="radio" name="shipping" id="outside" value="outside" required onchange="updateTotal()">
                                <label for="outside">
                                    <strong>ঢাকার বাইরে</strong><br>
                                    <small>ডেলিভারি চার্জ: <?php echo number_format($template['shipping_outside_cost'], 0); ?>৳</small>
                                </label>
                            </div>
                        </div>

                        <div class="payment-section">
                            <div class="form-section-title">Payment Method</div>

                            <div class="option-card">
                                <input type="radio" name="payment" id="cod" checked>
                                <label for="cod">
                                    <strong>ক্যাশ অন ডেলিভারি</strong><br>
                                    <small>পণ্য হাতে পেয়ে টাকা পরিশোধ করুন</small>
                                </label>
                            </div>
                        </div>

                        <?php if ($template['privacy_text']): ?>
                            <p class="privacy-notice">
                                <?php echo nl2br(htmlspecialchars($template['privacy_text'])); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Right Column: Sidebar -->
                    <div class="checkout-sidebar">
                        <div class="product-selector">
                            <div class="form-section-title">Select Package</div>

                            <?php foreach ($variations as $index => $variation): ?>
                                <div class="product-option">
                                    <input type="radio"
                                        name="product_variation"
                                        id="variation<?php echo $variation['id']; ?>"
                                        value="<?php echo $variation['id']; ?>"
                                        data-price="<?php echo $variation['total_price']; ?>"
                                        data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                                        data-qty="<?php echo $variation['pieces_count']; ?>"
                                        data-label="<?php echo htmlspecialchars($variation['set_name']); ?>"
                                        <?php echo $index === 0 ? 'checked' : ''; ?>
                                        onchange="selectProductOption('variation<?php echo $variation['id']; ?>')">
                                    <?php if ($product['main_image']): ?>
                                        <img src="./uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>"
                                            alt="Product"
                                            onclick="selectProductOption('variation<?php echo $variation['id']; ?>')">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 60px; background: #f5f5f5; border-radius: 12px;"></div>
                                    <?php endif; ?>
                                    <div class="product-details" onclick="selectProductOption('variation<?php echo $variation['id']; ?>')" style="cursor: pointer;">
                                        <div class="product-name">
                                            <?php echo htmlspecialchars($product['product_name']); ?>
                                            <?php if ($variation['is_special_offer']): ?>
                                                <span class="special-offer-badge">Special</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="product-meta">
                                            <?php echo htmlspecialchars($variation['set_name']); ?> |
                                            <span class="product-price" id="price_var<?php echo $variation['id']; ?>"><?php echo number_format($variation['total_price'], 0); ?>৳</span>
                                        </div>
                                    </div>
                                    <div class="quantity-selector">
                                        <button type="button" class="qty-button" onclick="decreaseQty('variation<?php echo $variation['id']; ?>')">−</button>
                                        <div class="qty-display" id="qty_var<?php echo $variation['id']; ?>">1</div>
                                        <button type="button" class="qty-button" onclick="increaseQty('variation<?php echo $variation['id']; ?>')">+</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="order-summary">
                            <div class="form-section-title">Order Summary</div>

                            <div class="summary-row">
                                <span>Product</span>
                                <span>Subtotal</span>
                            </div>

                            <div class="summary-row" id="productSummary">
                                <span id="productName">-</span>
                                <span id="productSubtotal">-</span>
                            </div>

                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span id="subtotalAmount">-</span>
                            </div>

                            <div class="summary-row">
                                <span>Shipping</span>
                                <span id="shippingAmount">—</span>
                            </div>

                            <div class="summary-row total">
                                <span>Total</span>
                                <span id="totalAmount">-</span>
                            </div>

                            <button type="submit" class="submit-button" id="submitBtn">অৰ্ডার সম্পন্ন করুন</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <footer>
        <p>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($template['brand_name'] ?: 'Beauty & Mine'); ?>. All rights reserved. </p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // Sticky Header on Scroll
        window.addEventListener('scroll', function() {
            const stickyHeader = document.getElementById('stickyHeader');
            if (window.scrollY > 400) {
                stickyHeader.classList.add('visible');
            } else {
                stickyHeader.classList.remove('visible');
            }
        });

        // Initialize Swiper for Review Slider
        const reviewSwiper = new Swiper('.review-swiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 25,
                }
            }
        });

        const shippingCosts = {
            dhaka: <?php echo $template['shipping_dhaka_cost']; ?>,
            urban: <?php echo $template['shipping_urban_cost']; ?>,
            outside: <?php echo $template['shipping_outside_cost']; ?>
        };

        const variationData = <?php echo json_encode(array_map(function ($v) use ($product) {
                                    return [
                                        'id' => $v['id'],
                                        'price' => floatval($v['total_price']),
                                        'name' => $product['product_name'],
                                        'qty' => intval($v['pieces_count']),
                                        'label' => $v['set_name']
                                    ];
                                }, $variations)); ?>;

        function selectProductOption(variationId) {
            document.getElementById(variationId).checked = true;
            updateTotal();
        }

        function selectShippingOption(shippingId) {
            document.getElementById(shippingId).checked = true;
            updateTotal();
        }

        function increaseQty(variationId) {
            const varId = variationId.replace('variation', '');
            const qtyDisplay = document.getElementById('qty_var' + varId);
            let currentQty = parseInt(qtyDisplay.textContent);
            if (currentQty < 99) {
                qtyDisplay.textContent = currentQty + 1;
                updateProductPrice(variationId);
                if (document.getElementById(variationId).checked) {
                    updateTotal();
                }
            }
        }

        function decreaseQty(variationId) {
            const varId = variationId.replace('variation', '');
            const qtyDisplay = document.getElementById('qty_var' + varId);
            let currentQty = parseInt(qtyDisplay.textContent);
            if (currentQty > 1) {
                qtyDisplay.textContent = currentQty - 1;
                updateProductPrice(variationId);
                if (document.getElementById(variationId).checked) {
                    updateTotal();
                }
            }
        }

        function updateProductPrice(variationId) {
            const varId = variationId.replace('variation', '');
            const productRadio = document.getElementById(variationId);
            const basePrice = parseFloat(productRadio.dataset.price);
            const qtyDisplay = document.getElementById('qty_var' + varId);
            const quantity = parseInt(qtyDisplay.textContent);

            const totalPrice = basePrice * quantity;

            const priceSpan = document.getElementById('price_var' + varId);
            priceSpan.textContent = totalPrice.toFixed(0) + '৳';
        }

        function updateTotal() {
            const selectedProduct = document.querySelector('input[name="product_variation"]:checked');
            if (!selectedProduct) return;

            const varId = selectedProduct.value;
            const basePrice = parseFloat(selectedProduct.dataset.price);
            const productName = selectedProduct.dataset.name;
            const qtyLabel = selectedProduct.dataset.label;

            const qtyDisplay = document.getElementById('qty_var' + varId);
            const sets = parseInt(qtyDisplay.textContent);

            // Update hidden quantity field
            document.getElementById('formQuantity').value = sets;

            const productPrice = basePrice * sets;

            document.getElementById('productName').textContent = `${productName} × ${sets}`;
            document.getElementById('productSubtotal').textContent = `${productPrice.toFixed(0)}৳`;
            document.getElementById('subtotalAmount').textContent = `${productPrice.toFixed(0)}৳`;

            const selectedShipping = document.querySelector('input[name="shipping"]:checked');

            if (selectedShipping) {
                const shippingType = selectedShipping.value;
                const shippingPrice = shippingCosts[shippingType];

                document.getElementById('shippingAmount').textContent = `${shippingPrice.toFixed(0)}৳`;

                const total = productPrice + shippingPrice;
                document.getElementById('totalAmount').textContent = `${total.toFixed(0)}৳`;
                document.getElementById('submitBtn').textContent = `অৰ্ডার সম্পন্ন করুন - ${total.toFixed(0)}৳`;
            } else {
                document.getElementById('shippingAmount').textContent = '—';
                document.getElementById('totalAmount').textContent = `${productPrice.toFixed(0)}৳`;
                document.getElementById('submitBtn').textContent = `অৰ্ডার সম্পন্ন করুন - ${productPrice.toFixed(0)}৳`;
            }
        }

        function scrollToCheckout() {
            document.getElementById('checkoutSection').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const selectedShipping = document.querySelector('input[name="shipping"]:checked');
            if (!selectedShipping) {
                e.preventDefault();
                alert('অনুগ্রহ করে শিপিং অপশন নির্বাচন করুন!');
                document.querySelector('.shipping-section').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return false;
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateTotal();
        });
    </script>
</body>

</html>