<?php
// test-stealfast-api.php
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Steadfast API Debug Test ===\n\n";

// Test different API key formats
$test_keys = [
    [
        'name' => 'Current Keys',
        'api_key' => 'b6lq1nqfash5twezulzbh84xqp451mt7',
        'secret_key' => 'maftf1cwhgvs9xlegik2rlvk'
    ],
    [
        'name' => 'Test with sf- format (if you have)',
        'api_key' => 'sf-xxxxxxxxxxxxxxxxxxxx',
        'secret_key' => 'sk-xxxxxxxxxxxxxxxxxxxx'
    ]
];

foreach ($test_keys as $test) {
    echo "Testing with: {$test['name']}\n";
    echo str_repeat("-", 50) . "\n";
    
    $url = 'https://portal.steadfast.com.bd/api/v1/get_balance';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Api-Key: ' . $test['api_key'],
        'Secret-Key: ' . $test['secret_key'],
        'Content-Type: application/json'
    ]);
    
    // For debugging
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    rewind($verbose);
    $verbose_log = stream_get_contents($verbose);
    fclose($verbose);
    
    echo "HTTP Code: $http_code\n";
    echo "Error: " . ($error ?: 'None') . "\n";
    echo "Response Length: " . strlen($response) . " bytes\n";
    
    if (strlen($response) > 0) {
        echo "First 500 chars of response:\n";
        echo substr($response, 0, 500) . "\n";
        
        // Try to decode JSON
        $json = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "JSON Decoded Successfully:\n";
            print_r($json);
        } else {
            echo "JSON Error: " . json_last_error_msg() . "\n";
            echo "Raw Response (full):\n";
            echo $response . "\n";
        }
    }
    
    curl_close($ch);
    echo "\n\n";
}

// Test without SSL verification (in case that's the issue)
echo "Testing without SSL verification:\n";
echo str_repeat("-", 50) . "\n";

$url = 'https://portal.steadfast.com.bd/api/v1/get_balance';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verify
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Api-Key: ' . $test_keys[0]['api_key'],
    'Secret-Key: ' . $test_keys[0]['secret_key'],
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

echo "HTTP Code: $http_code\n";
echo "Error: " . ($error ?: 'None') . "\n";
if ($response) {
    echo "Response: " . substr($response, 0, 500) . "\n";
}
curl_close($ch);
?>