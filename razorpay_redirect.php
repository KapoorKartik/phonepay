<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
// header('Content-Type: application/json');

require('C:/xampp/htdocs/phonepay/razorpay-php-2.9.0/Razorpay.php'); // Adjust the path as necessary

use Razorpay\Api\Api;


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

function sendOrderDetails($orderData,$mobile){
    $apiKey = 'rzp_live_Mve4wgbJgNAKwD'; 
    $apiSecret = 'Z1bjRBa2hy721Y1A1yDLUwot'; 

    $api = new Api($apiKey, $apiSecret);

    

    try {
        $order = $api->order->create($orderData); // Creates order
        $checkoutUrl = 'https://checkout.razorpay.com/v1/checkout.js';
        
        $response = [
            'success' => true,
            'checkoutUrl' => $checkoutUrl,
            'orderId' => $order['id'],
            'amount' => $order['amount'],
            'mobileNumber' => $mobile
        ];
        
        return $response;
    } catch (\Exception $e) {
        $response = [
            'success' => false,
            'error' => $e->getMessage()
        ];
        
        return $response;
    }
}


function addPaymentData($data, $reciptId ,$orderId) {
    // Prepare INSERT statement
    $amount = $data['amount'] * 100;
    $mobile = $data['mobile'];
    $name = $data['name'];
    $email = $data['email'];
    $address = $data['address'];
    $pincode = $data['pincode'];
    $state = $data['state'];
    $product = $data['product'];
    $sql = "INSERT INTO payment_data (name, mobile, email, address, pincode, state, amount, order_id,receipt_id,product) 
            VALUES ('$name', '$mobile', '$email', '$address', '$pincode', '$state', '$amount' , '$orderId' , '$reciptId' , '$product')";

    // Execute the query using executeQuery function
    $result = executeQuery($sql);

    // Check if query was successful
    if ($result === TRUE) {
        // echo "New record created successfully";
        return $result;
    } else {
        return $result->error;
        // echo "Error: " . $sql . "<br>" . $result->error;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Capture data from the frontend
    $data = json_decode(file_get_contents("php://input"), true);
    // print_r($data);die();
    $amount = $data['amount'] * 100;
    $mobile = $data['mobile'];
    $reciptId = 'rcptid_'. uniqid();

    $orderData = [
        'receipt'         => $reciptId,
        'amount'          => $amount, // amount in the smallest currency unit (e.g., paise)
        'currency'        => 'INR',
        'payment_capture' => 1 // auto capture
    ];
    $res = sendOrderDetails($orderData,$mobile);
    $idAddedToDb = addPaymentData($data,$reciptId,$res['orderId']);
    // print_r($idAddedToDb);
    // if ($idAddedToDb === TRUE){
        $res['email'] = $data['email'];
        echo json_encode($res);
    // }else {
        // echo json_encode($idAddedToDb);
    // }
}

?>
