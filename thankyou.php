<?php
require_once __DIR__ . '/./components/functions.php';

$order_number = isset($_GET['order']) ? htmlspecialchars($_GET['order']) : '';

$order_value    = 0;
$order_currency = 'BDT';

if ($order_number) {
    $conn = getDatabaseConnection();
    if ($conn) {
        $stmt = $conn->prepare("SELECT total_amount FROM orders WHERE order_number = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $order_number);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $order_value = floatval($row['total_amount']);
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অর্ডার সম্পন্ন হয়েছে - ধন্যবাদ!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');

    fbq('init', '2232905527196160');
    fbq('track', 'PageView');

    <?php if ($order_number && $order_value > 0): ?>
    fbq('track', 'Purchase', {
        content_type: 'product',
        currency:     '<?php echo $order_currency; ?>',
        value:        <?php echo $order_value; ?>,
        order_id:     '<?php echo addslashes($order_number); ?>'
    });
    <?php elseif ($order_number && $order_value == 0): ?>
    // value=0 fallback: check your DB column name if this keeps happening
    fbq('track', 'Purchase', {
        content_type: 'product',
        currency:     'BDT',
        value:        0,
        order_id:     '<?php echo addslashes($order_number); ?>'
    });
    <?php endif; ?>
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=2232905527196160&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->

    <style>
        :root {
            --primary-color: #E91E63;
            --primary-dark: #C2185B;
            --accent-success: #00C853;
            --text-dark: #212121;
            --text-light: #757575;
            --border-color: #E0E0E0;
            --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', 'Noto Sans Bengali', sans-serif;
            background: linear-gradient(135deg, #FFF5F8 0%, #FFFFFF 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }
        h1, h2, h3 { font-family: 'Playfair Display', serif; }
        .thankyou-card {
            background: white;
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            padding: 60px 50px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.6s ease;
        }
        .thankyou-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes popIn {
            0%   { transform: scale(0); opacity: 0; }
            60%  { transform: scale(1.15); }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes confetti {
            0%   { transform: translateY(-10px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(60px) rotate(360deg); opacity: 0; }
        }
        .success-icon-wrap {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--accent-success) 0%, #00A344 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.3s backwards;
            box-shadow: 0 8px 24px rgba(0, 200, 83, 0.35);
        }
        .success-icon-wrap i { font-size: 44px; color: white; }
        .thankyou-card h1 {
            font-size: 36px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 12px;
        }
        .thankyou-card h1 span {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .tagline {
            font-size: 17px;
            color: var(--text-light);
            margin-bottom: 35px;
            line-height: 1.6;
        }
        .order-box {
            background: linear-gradient(135deg, #FFF5F8 0%, white 100%);
            border: 2px solid var(--border-color);
            border-radius: 16px;
            padding: 24px 30px;
            margin-bottom: 35px;
        }
        .order-box .label {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-light);
            margin-bottom: 8px;
        }
        .order-box .order-num {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .steps { text-align: left; margin-bottom: 35px; }
        .step {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 16px;
            border-radius: 14px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #FAFAFA, white);
            border: 1px solid var(--border-color);
            transition: all 0.2s;
        }
        .step:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.07);
            transform: translateX(4px);
        }
        .step-num {
            width: 36px; height: 36px; min-width: 36px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(233, 30, 99, 0.3);
        }
        .step-text strong {
            display: block;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 3px;
            font-size: 15px;
        }
        .step-text span { font-size: 13px; color: var(--text-light); }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 14px 36px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(233, 30, 99, 0.35);
        }
        .back-btn:hover {
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(233, 30, 99, 0.45);
        }
        .confetti-dot {
            position: absolute;
            width: 10px; height: 10px;
            border-radius: 50%;
            animation: confetti 1.5s ease-out forwards;
        }
        @media (max-width: 480px) {
            .thankyou-card { padding: 40px 24px; }
            .thankyou-card h1 { font-size: 28px; }
            .order-box .order-num { font-size: 22px; }
        }
    </style>
</head>
<body>
    <div class="thankyou-card" id="card">
        <?php
        $colors    = ['#E91E63','#FFB300','#00C853','#2196F3','#9C27B0','#FF5722'];
        $positions = [[10,10],[20,5],[50,8],[75,6],[90,12],[15,20],[80,18],[40,3],[60,15]];
        foreach ($positions as $i => $pos):
            $color = $colors[$i % count($colors)];
        ?>
        <div class="confetti-dot" style="
            left: <?= $pos[0] ?>%;
            top: <?= $pos[1] ?>%;
            background: <?= $color ?>;
            animation-delay: <?= ($i * 0.1) ?>s;
            animation-duration: <?= (1.2 + $i * 0.1) ?>s;
        "></div>
        <?php endforeach; ?>

        <div class="success-icon-wrap">
            <i class="fas fa-check"></i>
        </div>

        <h1>ধন্যবাদ! <span>অর্ডার হয়েছে</span></h1>
        <p class="tagline">
            আপনার অর্ডারটি সফলভাবে গ্রহণ করা হয়েছে।<br>
            আমরা শীঘ্রই আপনার সাথে যোগাযোগ করবো।
        </p>

        <?php if ($order_number): ?>
        <div class="order-box">
            <div class="label">আপনার অর্ডার নম্বর</div>
            <div class="order-num">#<?= $order_number ?></div>
        </div>
        <?php endif; ?>

        <div class="steps">
            <div class="step">
                <div class="step-num">১</div>
                <div class="step-text">
                    <strong>অর্ডার নিশ্চিত করা হয়েছে</strong>
                    <span>আপনার অর্ডারটি আমাদের সিস্টেমে যুক্ত হয়েছে</span>
                </div>
            </div>
            <div class="step">
                <div class="step-num">২</div>
                <div class="step-text">
                    <strong>কল করা হবে</strong>
                    <span>আমাদের টিম শীঘ্রই আপনার নম্বরে কল করে অর্ডার নিশ্চিত করবে</span>
                </div>
            </div>
            <div class="step">
                <div class="step-num">৩</div>
                <div class="step-text">
                    <strong>পণ্য পাঠানো হবে</strong>
                    <span>নিশ্চিতকরণের পর ১-৩ কার্যদিবসের মধ্যে পণ্য পৌঁছাবে</span>
                </div>
            </div>
            <div class="step">
                <div class="step-num">৪</div>
                <div class="step-text">
                    <strong>পণ্য পেয়ে পেমেন্ট করুন</strong>
                    <span>ক্যাশ অন ডেলিভারি — পণ্য হাতে পেয়ে টাকা পরিশোধ করুন</span>
                </div>
            </div>
        </div>

        <a href="index.php" class="back-btn">
            <i class="fas fa-home"></i> হোম পেজে ফিরুন
        </a>
    </div>
</body>
</html>