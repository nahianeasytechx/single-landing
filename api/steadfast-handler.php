<?php
// IMPORTANT: This MUST be the very first line - no spaces or text before it
header('Content-Type: application/json');
error_reporting(0); // Suppress errors in JSON output
ini_set('display_errors', 0);

// Start output buffering to catch any stray output
ob_start();

try {
    // Include database connection
    require_once __DIR__ . '/../components/functions.php';

    // Steadfast API Configuration - REPLACE WITH YOUR ACTUAL KEYS
    define('STEADFAST_API_KEY', 'b6lq1nqfash5twezulzbh84xqp451mt7');
    define('STEADFAST_SECRET_KEY', 'maftf1cwhgvs9xlegik2rlvk');
    define('STEADFAST_BASE_URL', 'https://portal.packzy.com/api/v1');

    /**
     * Make request to Steadfast API
     */
    function callSteadfastAPI($endpoint, $method = 'GET', $data = null) {
        $url = STEADFAST_BASE_URL . $endpoint;
        
        $headers = [
            'Api-Key: ' . STEADFAST_API_KEY,
            'Secret-Key: ' . STEADFAST_SECRET_KEY,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        
        curl_close($ch);
        
        if ($curl_error) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $curl_error,
                'http_code' => $http_code
            ];
        }
        
        $decoded = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'message' => 'Invalid API response',
                'http_code' => $http_code
            ];
        }
        
        return [
            'success' => ($http_code >= 200 && $http_code < 300),
            'data' => $decoded,
            'http_code' => $http_code
        ];
    }

    /**
     * Validate Bangladeshi phone number
     */
    function validateBDPhone($phone) {
        $clean = preg_replace('/[\s\-]/', '', $phone);
        $clean = preg_replace('/^(\+88|88)/', '', $clean);
        return preg_match('/^01[3-9]\d{8}$/', $clean) ? $clean : false;
    }

    // Get action parameter
    $action = $_GET['action'] ?? 'create_order';

    // Clear any output buffer before sending JSON
    ob_clean();

    switch ($action) {
        case 'test':
            // Test API connection
            $response = callSteadfastAPI('/get_balance', 'GET');
            
            echo json_encode([
                'success' => $response['success'],
                'service' => 'Steadfast Courier',
                'status' => $response['success'] ? 'Connected' : 'Failed',
                'base_url' => STEADFAST_BASE_URL,
                'api_key_set' => !empty(STEADFAST_API_KEY),
                'balance' => $response['data']['current_balance'] ?? null,
                'message' => $response['success'] ? 'API connection successful' : ($response['message'] ?? 'Connection failed')
            ]);
            break;
            
        case 'track':
            // Track shipment
            $tracking_code = $_GET['tracking_code'] ?? '';
            
            if (empty($tracking_code)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Tracking code is required'
                ]);
                exit;
            }
            
            $response = callSteadfastAPI('/status_by_trackingcode/' . urlencode($tracking_code), 'GET');
            
            echo json_encode([
                'success' => $response['success'],
                'tracking_code' => $tracking_code,
                'delivery_status' => $response['data']['delivery_status'] ?? 'unknown',
                'tracking_info' => $response['data'] ?? null,
                'message' => $response['success'] ? 'Tracking info retrieved' : ($response['message'] ?? 'Failed to fetch tracking')
            ]);
            break;
            
        case 'create_order':
        default:
            // Create order
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid request data'
                ]);
                exit;
            }
            
            // Validate required fields
            $required = ['invoice', 'recipient_name', 'recipient_phone', 'recipient_address', 'cod_amount'];
            $errors = [];
            
            foreach ($required as $field) {
                if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
                    if ($field !== 'cod_amount' || !isset($data[$field])) {
                        $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
                    }
                }
            }
            
            // Validate phone
            $clean_phone = validateBDPhone($data['recipient_phone'] ?? '');
            if (!$clean_phone) {
                $errors[] = 'Invalid phone number format (use 01XXXXXXXXX)';
            }
            
            if (!empty($errors)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors
                ]);
                exit;
            }
            
            // Prepare Steadfast data
            $steadfast_data = [
                'invoice' => $data['invoice'],
                'recipient_name' => trim($data['recipient_name']),
                'recipient_phone' => $clean_phone,
                'recipient_address' => trim($data['recipient_address']),
                'cod_amount' => floatval($data['cod_amount']),
                'note' => $data['note'] ?? 'Please call before delivery',
                'item_description' => $data['item_description'] ?? 'Product',
                'delivery_type' => intval($data['delivery_type'] ?? 0)
            ];
            
            // Call API
            $response = callSteadfastAPI('/create_order', 'POST', $steadfast_data);
            
            if ($response['success'] && isset($response['data']['consignment'])) {
                $consignment = $response['data']['consignment'];
                
                // Update database
                $conn = getDatabaseConnection();
                if ($conn) {
                    $stmt = $conn->prepare("SELECT id FROM orders WHERE order_number = ? LIMIT 1");
                    $stmt->bind_param("s", $data['invoice']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($row = $result->fetch_assoc()) {
                        $order_id = $row['id'];
                        $tracking_code = $consignment['tracking_code'];
                        $consignment_id = $consignment['consignment_id'];
                        
                        $update = $conn->prepare(
                            "UPDATE orders SET 
                            tracking_code = ?, 
                            consignment_id = ?,
                            courier_status = 'sent',
                            order_status = 'processing',
                            sent_to_courier_at = NOW(),
                            updated_at = NOW()
                            WHERE id = ?"
                        );
                        $update->bind_param("sii", $tracking_code, $consignment_id, $order_id);
                        $update->execute();
                        $update->close();
                    }
                    $stmt->close();
                    $conn->close();
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Consignment created successfully',
                    'tracking_code' => $consignment['tracking_code'],
                    'consignment_id' => $consignment['consignment_id'],
                    'invoice' => $consignment['invoice'],
                    'status' => $consignment['status']
                ]);
            } else {
                $error_msg = $response['data']['message'] ?? $response['message'] ?? 'Failed to create consignment';
                
                echo json_encode([
                    'success' => false,
                    'message' => $error_msg,
                    'errors' => $response['data']['errors'] ?? null
                ]);
            }
            break;
    }

} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

// End output buffering and send
ob_end_flush();
// ```

// ## **Critical Fixes:**

// 1. ✅ **Removed test output** - No HTML/debug output
// 2. ✅ **Output buffering** - Captures any stray output
// 3. ✅ **Pure JSON output** - Only JSON responses
// 4. ✅ **Error suppression** - No PHP errors in JSON

// ## **File Structure:**
// ```
// your-project/
// ├── admin/
// │   ├── manage-orders.php
// │   └── components/
// │       └── functions.php
// └── api/
//     ├── steadfast-handler.php  ← This file
//     └── update-tracking.php
// ```

// ## **Test Again:**

// Delete your old test file and access the handler directly:
// ```
// https://your-domain.com/api/steadfast-handler.php?action=test