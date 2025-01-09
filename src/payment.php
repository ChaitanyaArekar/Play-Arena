<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['payment_data'])) {
    header('Location: book.php');
    exit();
}

$paymentData = $_SESSION['payment_data'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Turf Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <!-- Razorpay SDK -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>

<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-6">Payment Details</h2>

                <div class="space-y-4 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Selected Sport:</span>
                        <span class="font-medium"><?php echo htmlspecialchars($paymentData['sport']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Number of Slots:</span>
                        <span class="font-medium"><?php echo count($paymentData['slots']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Amount:</span>
                        <span class="font-medium">₹<?php echo htmlspecialchars($paymentData['total_amount']); ?></span>
                    </div>
                </div>

                <button id="pay-button" class="w-full bg-green-500 text-white py-4 rounded-lg hover:bg-green-600 transition-all">
                    Pay Now ₹<?php echo htmlspecialchars($paymentData['total_amount']); ?>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('pay-button').addEventListener('click', function() {
            const options = {
                key: '<?php echo RAZORPAY_KEY; ?>',
                amount: <?php echo $paymentData['total_amount'] * 100; ?>,
                currency: 'INR',
                name: 'Turf Booking',
                description: 'Payment for turf booking',
                handler: function(response) {
                    verifyPayment(response.razorpay_payment_id);
                },
                prefill: {
                    name: '<?php echo htmlspecialchars($_SESSION['user']['name'] ?? ''); ?>',
                    email: '<?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?>',
                },
                theme: {
                    color: '#22C55E'
                }
            };

            const rzp = new Razorpay(options);
            rzp.open();
        });

        function verifyPayment(paymentId) {
            fetch('verify_payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        payment_id: paymentId,
                        booking_data: <?php echo json_encode($paymentData); ?>
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'booking_success.php';
                    } else {
                        alert('Payment verification failed. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        }
    </script>
</body>

</html>