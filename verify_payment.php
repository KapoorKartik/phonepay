<?php
// Include the Razorpay SDK
require('C:/xampp/htdocs/phonepay/razorpay-php-2.9.0/Razorpay.php'); // Adjust the path as necessary

// require('path/to/razorpay-php/Razorpay.php'); // Adjust the path as necessary

use Razorpay\Api\Api;

$apiKey = 'rzp_live_Mve4wgbJgNAKwD'; // Replace with your Razorpay Key ID
$apiSecret = 'Z1bjRBa2hy721Y1A1yDLUwot'; // Replace with your Razorpay Key Secret

// Create an instance of the Razorpay API
$api = new Api($apiKey, $apiSecret);
$data = json_decode(file_get_contents("php://input"), true);

// print_r($data);
// die("kkk");
function executeQuery($sql_query) {
    // Database connection parameters
    $servername = "localhost";
    $username = "root"; // Default username for XAMPP MySQL
    $password = ""; // Default password for XAMPP MySQL
    $database = "user_db"; // Replace with your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Execute query
    $result = $conn->query($sql_query);

    // Close connection
    $conn->close();

    // Return result
    return $result;
}

$order_id = $data['razorpay_order_id'];
$razorpay_payment_id = $data['razorpay_payment_id'];
$razorpay_signature = $data['razorpay_signature'];
$generated_signature = hash_hmac('sha256', $order_id . '|' . $razorpay_payment_id, $apiSecret);

if ($generated_signature === $razorpay_signature) {
    // print_r($_POST);
    $isSucess = "Successful";
    // echo 'Payment verified successfully';
} else {
    $isSucess = "Failed";
    // echo 'Payment verification failed';
}
$sql = "UPDATE `payment_data` SET `payment_status` = '$isSucess' WHERE `payment_data`.`order_id` = '$order_id'";
$result = executeQuery($sql);
// print_r($result);
echo $isSucess;
?>