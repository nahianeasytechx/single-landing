<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty & Mine - Lip Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/font-awesome.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Noto Sans Bengali', 'Hind Siliguri', sans-serif;
            overflow-x: hidden;
        }
        
        /* Section 1 - Hero */
        .section-1 {
            background: linear-gradient(180deg, #f5e6e6 0%, #e8d4d4 100%);
            padding: 60px 20px;
            text-align: center;
        }
        
        .brand-logo {
            width: 200px;
            margin: 0 auto 20px;
        }
        
        .hero-title {
            font-size: 28px;
            font-weight: 700;
            color: #e91e63;
            margin-bottom: 40px;
        }
        
        .hero-title .lips-icon {
            color: #e91e63;
        }
        
        .lip-comparison-container {
            max-width: 600px;
            margin: 0 auto 30px;
            position: relative;
        }
        
        .lip-image {
            width: 100%;
            border-radius: 15px;
        }
        
        .before-label, .after-label {
            position: absolute;
            background: #c62828;
            color: white;
            padding: 10px 25px;
            font-weight: 600;
            font-size: 16px;
            border-radius: 5px;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .before-label {
            left: 30px;
        }
        
        .after-label {
            right: 30px;
        }
        
        .benefit-box {
            background: linear-gradient(135deg, #ffe0e0 0%, #ffd4d4 100%);
            padding: 25px;
            border-radius: 15px;
            font-size: 22px;
            font-weight: 600;
            color: #333;
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* Section 2 - Product Details */
        .section-2 {
            background: linear-gradient(180deg, #ffc4b8 0%, #ffb8b8 100%);
            padding: 60px 20px;
        }
        
        .product-showcase {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 50px;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .product-image-box {
            background: linear-gradient(135deg, #d4a03a 0%, #c89635 100%);
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .product-img {
            width: 280px;
            height: auto;
        }
        
        .product-features {
            background: transparent;
            max-width: 550px;
        }
        
        .product-features h2 {
            font-size: 26px;
            font-weight: 700;
            color: #c62828;
            margin-bottom: 30px;
        }
        
        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            gap: 15px;
        }
        
        .check-icon {
            width: 30px;
            height: 30px;
            background: #000;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 3px;
        }
        
        .feature-text {
            font-size: 17px;
            color: #333;
            line-height: 1.6;
        }
        
        /* Section 3 - Customer Reviews */
        .section-3 {
            background: linear-gradient(180deg, #ffb8a0 0%, #ffa890 100%);
            padding: 60px 20px;
        }
        
        .section-3 h2 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 40px;
        }
        
        .review-slider {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .review-slide img {
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        /* Section 4 - Product Selection */
        .section-4 {
            background: linear-gradient(180deg, #ffb8a0 0%, #ffc4b8 100%);
            padding: 60px 20px;
        }
        
        .product-order-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .product-slider-box {
            flex: 1;
            min-width: 300px;
            max-width: 550px;
        }
        
        .product-slide img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .order-details-box {
            flex: 1;
            min-width: 300px;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.5);
            padding: 30px;
            border-radius: 20px;
        }
        
        .order-details-box h3 {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }
        
        .order-details-box ul {
            list-style: none;
            margin-bottom: 25px;
        }
        
        .order-details-box ul li {
            font-size: 18px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .price-info-box {
            margin: 25px 0;
        }
        
        .price-original-text {
            font-size: 18px;
            color: #666;
            text-decoration: line-through;
        }
        
        .price-offer-text {
            font-size: 26px;
            font-weight: 700;
            color: #c62828;
        }
        
        .order-now-btn {
            background: white;
            color: #333;
            border: 3px solid #333;
            padding: 15px 40px;
            font-size: 20px;
            font-weight: 700;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        
        .order-now-btn:hover {
            background: #333;
            color: white;
        }
        
        /* Section 5 - Contact */
        .section-5 {
            background: linear-gradient(180deg, #ffc4b8 0%, #ffd4d4 100%);
            padding: 50px 20px;
            text-align: center;
        }
        
        .section-5 h2 {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        
        .section-5 p {
            font-size: 18px;
            color: #333;
            margin-bottom: 30px;
        }
        
        .contact-btns {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .whatsapp-btn, .messenger-btn {
            padding: 15px 40px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            color: white;
        }
        
        .whatsapp-btn {
            background: #25D366;
        }
        
        .messenger-btn {
            background: #0084FF;
        }
        
        .whatsapp-btn:hover, .messenger-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            color: white;
        }
        
        /* Section 6 - Checkout Form */
        .section-6 {
            background: linear-gradient(180deg, #ffd4d4 0%, #ffe8e8 100%);
            padding: 60px 20px;
        }
        
        .section-6 h2 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        
        .section-6 .subtitle {
            text-align: center;
            font-size: 16px;
            color: #666;
            margin-bottom: 50px;
        }
        
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .checkout-left {
            flex: 1;
            min-width: 300px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .checkout-right {
            flex: 0 0 400px;
            min-width: 300px;
        }
        
        .form-title {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #e91e63;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
        }
        
        .shipping-options,
        .payment-options {
            margin-top: 30px;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            margin-bottom: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .radio-option:hover {
            border-color: #e91e63;
            background: #fff5f8;
        }
        
        .radio-option:has(input[type="radio"]:checked) {
            border-color: #e91e63;
            background: #fff5f8;
        }
        
        .radio-option input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .radio-option label {
            cursor: pointer;
            margin: 0;
            flex: 1;
        }
        
        .product-selection-box {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .product-radio-option {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            margin-bottom: 12px;
            border: 2px solid #ddd;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .product-radio-option:hover {
            border-color: #e91e63;
            background: #fff5f8;
        }
        
        .product-radio-option input[type="radio"]:checked ~ * {
            font-weight: 600;
        }
        
        .product-radio-option:has(input[type="radio"]:checked) {
            border-color: #e91e63;
            background: #fff5f8;
        }
        
        .product-radio-option input[type="radio"] {
            cursor: pointer;
        }
        
        .product-radio-option img {
            cursor: pointer;
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }
        
        .qty-btn {
            width: 30px;
            height: 30px;
            border: 2px solid #e91e63;
            background: white;
            color: #e91e63;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .qty-btn:hover {
            background: #e91e63;
            color: white;
        }
        
        .qty-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        
        .qty-input {
            width: 45px;
            height: 30px;
            text-align: center;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        }
        
        .product-radio-option input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .product-radio-option img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .product-info {
            flex: 1;
        }
        
        .product-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .product-qty {
            font-size: 14px;
            color: #666;
        }
        
        .product-price {
            font-weight: 700;
            color: #e91e63;
            font-size: 16px;
        }
        
        .special-badge {
            background: #ff5722;
            color: white;
            padding: 3px 10px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 700;
            margin-left: 10px;
        }
        
        .order-summary-box {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .summary-row.total {
            font-size: 20px;
            font-weight: 700;
            color: #e91e63;
            border-bottom: none;
            margin-top: 10px;
        }
        
        .submit-btn {
            background: #c62828;
            color: white;
            border: none;
            padding: 18px;
            width: 100%;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s;
        }
        
        .submit-btn:hover {
            background: #a32020;
            transform: translateY(-2px);
        }
        
        .privacy-text {
            font-size: 12px;
            color: #666;
            margin-top: 20px;
            line-height: 1.6;
        }
        
        .carousel-control-prev,
        .carousel-control-next {
            width: 45px;
            height: 45px;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #333;
        }
        
        @media (max-width: 768px) {
            .checkout-right {
                flex: 1;
            }
            
            .product-showcase {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Section 1: Hero -->
    <section class="section-1">
        <div class="container">
            <img src="https://via.placeholder.com/200x80/FFF/333?text=BEAUTY+%26+MINE" alt="Beauty & Mine" class="brand-logo">
            <h1 class="hero-title">💋 ঠোঁটের তাৎক্ষণাৎ মুখ ঘর বদলাও instantly 💋</h1>
            
            <div class="lip-comparison-container">
                <img src="https://via.placeholder.com/600x300/FFD4E5/333?text=Lip+Before+%26+After" alt="Lip Comparison" class="lip-image">
                <div class="before-label">আগের কবলন</div>
                <div class="after-label">আজীর কবলন</div>
            </div>
            
            <div class="benefit-box">
                ❤️ নোট নানথ নেতন, মসুম, আনৰ সনতজ সানৰানদন😊
            </div>
        </div>
    </section>

    <!-- Section 2: Product Features -->
    <section class="section-2">
        <div class="container">
            <div class="product-showcase">
                <div class="product-image-box">
                    <img src="https://via.placeholder.com/280x340/333/FFF?text=Mirsist+Jelly+Lipstick" alt="Product" class="product-img">
                </div>
                
                <div class="product-features">
                    <h2>কেন Mirsist Jelly Lipstick আপনার ঠোঁটের বন্ধু?</h2>
                    
                    <div class="feature-item">
                        <div class="check-icon">✓</div>
                        <div class="feature-text">pH অনুযায়ী নিজের রঙ: আপনার ঠোঁটের জনা তৈনৰ ইউনিক শেড!</div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="check-icon">✓</div>
                        <div class="feature-text">দীৰ্ঘস্থায়ী উজ্জ্বলতা: সারাদিন গ্লাসবৰ, সতেজ ঠোঁট💋</div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="check-icon">✓</div>
                        <div class="feature-text">দুষি নকল ও উপকাৰেৰ সেবা: ভেতেৰ ফুল মন কেডে নেয় 🌹</div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="check-icon">✓</div>
                        <div class="feature-text">স্বাভাকৰ ঠোঁটের যত্ন:: প্রাকৃতিক উপাদান ঠোঁট থাকে সুস্থ 👑</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: Customer Reviews -->
    <section class="section-3">
        <div class="container">
            <h2>আমাদের কাষ্টমার রিভিউ</h2>
            
            <div id="reviewCarousel" class="carousel slide review-slider" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#reviewCarousel" data-bs-slide-to="0" class="active"></button>
                    <button type="button" data-bs-target="#reviewCarousel" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#reviewCarousel" data-bs-slide-to="2"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="review-slide">
                            <img src="https://via.placeholder.com/1000x700/1a1a1a/4a9eff?text=Customer+Review+Screenshot+1" alt="Review 1">
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="review-slide">
                            <img src="https://via.placeholder.com/1000x700/1a1a1a/4a9eff?text=Customer+Review+Screenshot+2" alt="Review 2">
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="review-slide">
                            <img src="https://via.placeholder.com/1000x700/1a1a1a/4a9eff?text=Customer+Review+Screenshot+3" alt="Review 3">
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#reviewCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#reviewCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
    </section>

    <!-- Section 4: Product Selection -->
    <section class="section-4">
        <div class="container">
            <div class="product-order-container">
                <div class="product-slider-box">
                    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="0" class="active"></button>
                            <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="1"></button>
                            <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="2"></button>
                            <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="3"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="product-slide">
                                    <img src="https://via.placeholder.com/550x420/D485A8/FFF?text=Mirsist+Jelly+Crystal+Lipstick+Collection" alt="Product Image 1">
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="product-slide">
                                    <img src="https://via.placeholder.com/550x420/D485A8/FFF?text=Limited+Stock+Available" alt="Product Image 2">
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="product-slide">
                                    <img src="https://via.placeholder.com/550x420/D485A8/FFF?text=Order+Now" alt="Product Image 3">
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="product-slide">
                                    <img src="https://via.placeholder.com/550x420/D485A8/FFF?text=Get+Discount+22%+Off" alt="Product Image 4">
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>
                
                <div class="order-details-box">
                    <h3>অৰ্ডার করলে যা যা পাচ্ছন:</h3>
                    <ul>
                        <li>✓ <strong>Mirsist jelly Lipstick</strong></li>
                        <li>✓ <strong>প্রিমিয়াম প্যাকেজিং বক্স</strong></li>
                    </ul>
                    
                    <div class="price-info-box">
                        <div class="price-original-text">রেগুলার মূলা: ৳450</div>
                        <div class="price-offer-text">অফার মূলা: ৳290</div>
                    </div>
                    
                    <button class="order-now-btn" onclick="scrollToCheckout()">অৰ্ডার করুন</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 5: Contact -->
    <section class="section-5">
        <div class="container">
            <h2>অৰ্ডার বা প্ৰশ্ন?</h2>
            <p>কল করুন বা WhatsApp / Messenger-এ মেসেজ দিন<br>আপনার ঠিকানা প্ৰেক্ষণ জনা আমৰা দোজি</p>
            
            <div class="contact-btns">
                <a href="https://wa.me/1234567890" class="whatsapp-btn">
                    <i class="fa fa-whatsapp"></i> Whatsapp
                </a>
                <a href="https://m.me/yourusername" class="messenger-btn">
                    <i class="fa fa-facebook-messenger"></i> Messenger
                </a>
            </div>
        </div>
    </section>

    <!-- Section 6: Checkout Form -->
    <section class="section-6" id="checkoutSection">
        <div class="container">
            <h2>অৰ্ডারটি সম্পূর্ণ করুন</h2>
            <p class="subtitle">আমরা শীঘ্রই আপনার সাথে যোগাযোগ করবো</p>
            
            <form id="checkoutForm">
                <div class="checkout-container">
                    <div class="checkout-left">
                        <div class="form-title">Customer information</div>
                        
                        <div class="form-group">
                            <label>আপনার নাম , ঠিকানা ও নম্বর দিয়ে অৰ্ডার করুন</label>
                            <input type="text" name="name" placeholder="নাম *" required>
                        </div>
                        
                        <div class="form-group">
                            <input type="text" name="address" placeholder="ঠিকানা *" required>
                        </div>
                        
                        <div class="form-group">
                            <input type="tel" name="phone" placeholder="যোগাযোগ নাম *" required>
                        </div>
                        
                        <div class="form-group">
                            <textarea name="notes" placeholder="Notes (Optional)" rows="3"></textarea>
                        </div>
                        
                        <div class="shipping-options">
                            <div class="form-title">Shipping</div>
                            
                            <div class="radio-option" onclick="selectShippingOption('dhaka')">
                                <input type="radio" name="shipping" id="dhaka" value="70" required onchange="updateTotal()">
                                <label for="dhaka">ঢাকা জিলায় - 70.00৳</label>
                            </div>
                            
                            <div class="radio-option" onclick="selectShippingOption('outside')">
                                <input type="radio" name="shipping" id="outside" value="130" required onchange="updateTotal()">
                                <label for="outside">ঢাকা বাহিরে - 130.00৳</label>
                            </div>
                        </div>
                        
                        <div class="payment-options">
                            <div class="form-title">Payment</div>
                            
                            <div class="radio-option">
                                <input type="radio" name="payment" id="cod" checked>
                                <label for="cod">
                                    <strong>Cash on delivery</strong><br>
                                    <small>Pay with cash upon delivery.</small>
                                </label>
                            </div>
                        </div>
                        
                        <p class="privacy-text">
                            Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our privacy policy.
                        </p>
                    </div>
                    
                    <div class="checkout-right">
                        <div class="product-selection-box">
                            <div class="form-title">Select An Option</div>
                            
                            <div class="product-radio-option">
                                <input type="radio" name="product" id="product1" value="290" data-name="Mirsist jelly lipstick" data-qty="1pc" data-unit-price="290" checked onchange="selectProductOption('product1')">
                                <img src="https://via.placeholder.com/50x50/D485A8/FFF?text=1" alt="Product" onclick="selectProductOption('product1')">
                                <div class="product-info" onclick="selectProductOption('product1')" style="cursor: pointer;">
                                    <div class="product-name">Mirsist jelly lipstick</div>
                                    <div class="product-qty">Qty: 1 | <span id="price1">290.00</span>৳</div>
                                </div>
                                <div class="quantity-control">
                                    <button type="button" class="qty-btn" onclick="decreaseQty('product1')">−</button>
                                    <input type="number" class="qty-input" id="qty1" value="1" min="1" max="99" readonly>
                                    <button type="button" class="qty-btn" onclick="increaseQty('product1')">+</button>
                                </div>
                            </div>
                            
                            <div class="product-radio-option">
                                <input type="radio" name="product" id="product2" value="580" data-name="Mirsist jelly lipstick" data-qty="2pcs" data-unit-price="290" onchange="selectProductOption('product2')">
                                <img src="https://via.placeholder.com/50x50/D485A8/FFF?text=2" alt="Product" onclick="selectProductOption('product2')">
                                <div class="product-info" onclick="selectProductOption('product2')" style="cursor: pointer;">
                                    <div class="product-name">Mirsist jelly lipstick</div>
                                    <div class="product-qty">Qty: 2 | <span id="price2">580.00</span>৳</div>
                                </div>
                                <div class="quantity-control">
                                    <button type="button" class="qty-btn" onclick="decreaseQty('product2')">−</button>
                                    <input type="number" class="qty-input" id="qty2" value="1" min="1" max="99" readonly>
                                    <button type="button" class="qty-btn" onclick="increaseQty('product2')">+</button>
                                </div>
                            </div>
                            
                            <div class="product-radio-option">
                                <input type="radio" name="product" id="product3" value="870" data-name="Mirsist jelly lipstick" data-qty="3pcs" data-unit-price="290" onchange="selectProductOption('product3')">
                                <img src="https://via.placeholder.com/50x50/D485A8/FFF?text=3" alt="Product" onclick="selectProductOption('product3')">
                                <div class="product-info" onclick="selectProductOption('product3')" style="cursor: pointer;">
                                    <div class="product-name">Mirsist jelly lipstick</div>
                                    <div class="product-qty">Qty: 3 | <span id="price3">870.00</span>৳</div>
                                </div>
                                <div class="quantity-control">
                                    <button type="button" class="qty-btn" onclick="decreaseQty('product3')">−</button>
                                    <input type="number" class="qty-input" id="qty3" value="1" min="1" max="99" readonly>
                                    <button type="button" class="qty-btn" onclick="increaseQty('product3')">+</button>
                                </div>
                            </div>
                            
                            <div class="product-radio-option">
                                <input type="radio" name="product" id="product4" value="1400" data-name="Mirsist jelly lipstick" data-qty="6pcs" data-unit-price="233.33" onchange="selectProductOption('product4')">
                                <img src="https://via.placeholder.com/50x50/D485A8/FFF?text=6" alt="Product" onclick="selectProductOption('product4')">
                                <div class="product-info" onclick="selectProductOption('product4')" style="cursor: pointer;">
                                    <div class="product-name">Mirsist jelly lipstick
                                        <span class="special-badge">বিশেষ অফার</span>
                                    </div>
                                    <div class="product-qty">Qty: 6 | <span id="price4">1,400.00</span>৳</div>
                                </div>
                                <div class="quantity-control">
                                    <button type="button" class="qty-btn" onclick="decreaseQty('product4')">−</button>
                                    <input type="number" class="qty-input" id="qty4" value="1" min="1" max="99" readonly>
                                    <button type="button" class="qty-btn" onclick="increaseQty('product4')">+</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="order-summary-box">
                            <div class="form-title">Your order</div>
                            
                            <div class="summary-row">
                                <span>Product</span>
                                <span>Subtotal</span>
                            </div>
                            
                            <div class="summary-row" id="productSummary">
                                <span id="productName">Mirsist jelly lipstick × 1</span>
                                <span id="productSubtotal">290.00৳</span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span id="subtotalAmount">290.00৳</span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span id="shippingAmount">—</span>
                            </div>
                            
                            <div class="summary-row total">
                                <span>Total</span>
                                <span id="totalAmount">290.00৳</span>
                            </div>
                            
                            <button type="submit" class="submit-btn">অৰ্ডার সম্পন্ন হলে ক্লিক করুন 290.00৳</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <footer style="background: #333; color: white; text-align: center; padding: 20px;">
        <p style="margin: 0;">© 2025 Beautyandmine. All rights reserved</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectProductOption(productId) {
            document.getElementById(productId).checked = true;
            updateTotal();
        }
        
        function selectShippingOption(shippingId) {
            document.getElementById(shippingId).checked = true;
            updateTotal();
        }
        
        function increaseQty(productId) {
            const qtyInput = document.getElementById('qty' + productId.replace('product', ''));
            let currentQty = parseInt(qtyInput.value);
            if (currentQty < 99) {
                qtyInput.value = currentQty + 1;
                updateProductPrice(productId);
                if (document.getElementById(productId).checked) {
                    updateTotal();
                }
            }
        }
        
        function decreaseQty(productId) {
            const qtyInput = document.getElementById('qty' + productId.replace('product', ''));
            let currentQty = parseInt(qtyInput.value);
            if (currentQty > 1) {
                qtyInput.value = currentQty - 1;
                updateProductPrice(productId);
                if (document.getElementById(productId).checked) {
                    updateTotal();
                }
            }
        }
        
        function updateProductPrice(productId) {
            const productRadio = document.getElementById(productId);
            const basePrice = parseFloat(productRadio.dataset.unitPrice);
            const qtyInput = document.getElementById('qty' + productId.replace('product', ''));
            const quantity = parseInt(qtyInput.value);
            
            // Get the base quantity (how many pieces in this pack)
            const baseQty = parseInt(productRadio.dataset.qty);
            
            // Calculate total price
            const totalPrice = basePrice * baseQty * quantity;
            
            // Update the displayed price
            const priceSpan = document.getElementById('price' + productId.replace('product', ''));
            priceSpan.textContent = totalPrice.toFixed(2);
            
            // Update the radio value
            productRadio.value = totalPrice;
        }
        
        function updateTotal() {
            // Get selected product
            const selectedProduct = document.querySelector('input[name="product"]:checked');
            const productPrice = parseFloat(selectedProduct.value);
            const productQty = selectedProduct.dataset.qty;
            const productName = selectedProduct.dataset.name;
            const productId = selectedProduct.id;
            const qtyInput = document.getElementById('qty' + productId.replace('product', ''));
            const sets = parseInt(qtyInput.value);
            
            // Update product summary
            const totalPieces = parseInt(productQty) * sets;
            document.getElementById('productName').textContent = `${productName} × ${totalPieces}pcs (${sets} set${sets > 1 ? 's' : ''})`;
            document.getElementById('productSubtotal').textContent = `${productPrice.toFixed(2)}৳`;
            
            // Update subtotal
            document.getElementById('subtotalAmount').textContent = `${productPrice.toFixed(2)}৳`;
            
            // Get selected shipping
            const selectedShipping = document.querySelector('input[name="shipping"]:checked');
            
            if (selectedShipping) {
                const shippingPrice = parseInt(selectedShipping.value);
                
                // Update shipping
                document.getElementById('shippingAmount').textContent = `${shippingPrice.toFixed(2)}৳`;
                
                // Calculate and update total
                const total = productPrice + shippingPrice;
                document.getElementById('totalAmount').textContent = `${total.toFixed(2)}৳`;
                
                // Update submit button text
                document.querySelector('.submit-btn').textContent = `অৰ্ডার সম্পন্ন হলে ক্লিক করুন ${total.toFixed(2)}৳`;
            } else {
                // No shipping selected yet
                document.getElementById('shippingAmount').textContent = '—';
                document.getElementById('totalAmount').textContent = `${productPrice.toFixed(2)}৳`;
                document.querySelector('.submit-btn').textContent = `অৰ্ডার সম্পন্ন হলে ক্লিক করুন ${productPrice.toFixed(2)}৳`;
            }
        }
        
        function scrollToCheckout() {
            document.getElementById('checkoutSection').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }
        
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Check if all required fields are filled
            const name = document.querySelector('input[name="name"]').value.trim();
            const address = document.querySelector('input[name="address"]').value.trim();
            const phone = document.querySelector('input[name="phone"]').value.trim();
            
            if (!name) {
                alert('অনুগ্রহ করে আপনার নাম লিখুন!');
                document.querySelector('input[name="name"]').focus();
                return;
            }
            
            if (!address) {
                alert('অনুগ্রহ করে আপনার ঠিকানা লিখুন!');
                document.querySelector('input[name="address"]').focus();
                return;
            }
            
            if (!phone) {
                alert('অনুগ্রহ করে আপনার ফোন নম্বর লিখুন!');
                document.querySelector('input[name="phone"]').focus();
                return;
            }
            
            // Check if shipping is selected
            const selectedShipping = document.querySelector('input[name="shipping"]:checked');
            if (!selectedShipping) {
                alert('অনুগ্রহ করে শিপিং অপশন নির্বাচন করুন!');
                document.querySelector('.shipping-options').scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            
            const selectedProduct = document.querySelector('input[name="product"]:checked');
            const productName = selectedProduct.dataset.name;
            const productQty = selectedProduct.dataset.qty;
            const productId = selectedProduct.id;
            const qtyInput = document.getElementById('qty' + productId.replace('product', ''));
            const sets = parseInt(qtyInput.value);
            const totalPieces = parseInt(productQty) * sets;
            const total = document.getElementById('totalAmount').textContent;
            
            alert(`আপনার অর্ডারটি সফলভাবে সম্পন্ন হয়েছে!\n\nপণ্য: ${productName}\nপরিমাণ: ${totalPieces} pieces (${sets} set${sets > 1 ? 's' : ''})\nমোট: ${total}\n\nআমরা শীঘ্রই আপনার সাথে যোগাযোগ করব।`);
            
            // Reset form
            this.reset();
            updateTotal();
        });
        
        // Initialize on page load
        updateTotal();
    </script>
</body>
</html>