<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
// Include the Razorpay SDK
require('C:/xampp/htdocs/phonepay/razorpay-php-2.9.0/Razorpay.php'); // Adjust the path as necessary

// C:\xampp\htdocs\phonepay\razorpay-php-2.9.0
use Razorpay\Api\Api;

$apiKey = 'rzp_live_Mve4wgbJgNAKwD'; // Replace with your Razorpay Key ID
$apiSecret = 'Z1bjRBa2hy721Y1A1yDLUwot'; // Replace with your Razorpay Key Secret

// Create an instance of the Razorpay API
$api = new Api($apiKey, $apiSecret);

// Create an order
$orderData = [
    'receipt'         => 'rcptid_12',
    'amount'          => 100, // amount in the smallest currency unit (e.g., paise)
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];

try {
    $order = $api->order->create($orderData); // Creates order
    print_r($order);
    echo "kkkk";
    // Redirect to Razorpay Checkout
    $checkoutUrl = 'https://checkout.razorpay.com/v1/checkout.js';
    ?>
    <html>
    <head>
        <title>Razorpay Checkout</title>
        <script src="<?php echo $checkoutUrl; ?>"></script>
    </head>
    <body>
        <form action="verify_payment.php" method="POST">
            <input type="text" name="mobile_number" placeholder="Enter your mobile number">
            <br><br>
            <script
                src="<?php echo $checkoutUrl; ?>"
                data-key="<?php echo $apiKey; ?>" // Enter the Key ID generated from the Dashboard
                data-amount="<?php echo $order['amount']; ?>" // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
                data-currency="INR"
                data-order_id="<?php echo $order['id']; ?>" // Pass the order ID obtained in Step 3
                data-buttontext="Pay with Razorpay"
                data-name="pi web tech"
                data-description="Test Transaction"
                data-image="https://cdn.razorpay.com/logos/NIebO22WUklO0B_large.png"
                data-prefill.name="John Doe"
                data-prefill.email="john.doe@example.com"
                data-prefill.contact="8626866293" // Pass the mobile number entered by the user
            ></script>
            <input type="text" name="order_id" value="<?php echo $order['id']; ?>">
            <input type="text" name="amount" value="<?php echo $order['amount']; ?>">
        </form>
    </body>
    </html>
    <?php
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

