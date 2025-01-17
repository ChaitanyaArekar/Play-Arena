<?php
                            ini_set('session.cookie_lifetime', 0);
                            ini_set('session.use_strict_mode', 1);
                            ini_set('session.use_only_cookies', 1);
                            ini_set('session.cookie_httponly', 1);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Error: Composer autoload file not found. Please run 'composer install' in the project root directory.");
}

require $autoloadPath;
\Stripe\Stripe::setApiKey('sk_test_51Qi7qaHgsEGcE4nXK3Q29MHEqn9kfR2EgUBCk9mA94uX5t8qrmsDJASrTIBnWH8MOgRvCBoIC39Km9llQ1zQo3tE00GtZk6cOC');


$amount = $_POST['amount'] ?? 0;
$productInfo = $_POST['product_info'] ?? '';
$sport = $_POST['sport'] ?? '';
$date = $_POST['date'] ?? '';
$slots = json_decode($_POST['slots'] ?? '[]', true);


$_SESSION['pending_booking'] = [
    'sport' => $sport,
    'date' => $date,
    'slots' => $slots,
    'amount' => $amount,
    'product_info' => $productInfo
];

try {
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount * 100, // Convert to cents
        'currency' => 'inr',
        'metadata' => [
            'product_info' => $productInfo
        ]
    ]);
} catch (Exception $e) {
    $_SESSION['payment_error'] = $e->getMessage();
    header('Location: error.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Turf Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://js.stripe.com/v3/"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Complete Your Payment</h2>

            <div class="mb-6">
                <h3 class="font-semibold mb-2">Booking Details</h3>
                <p class="text-gray-600"><?php echo htmlspecialchars($productInfo); ?></p>
                <p class="text-xl font-bold mt-2">Total: ₹<?php echo number_format($amount, 2); ?></p>
            </div>

            <form id="payment-form" class="space-y-4">
                <div id="payment-element"></div>
                <button id="submit-button" class="w-full bg-blue-500 text-white py-3 px-4 rounded-lg hover:bg-blue-600">
                    Pay Now
                </button>
                <div id="payment-message" class="hidden text-center text-red-500"></div>
            </form>
        </div>
    </div>

    <script>
        const stripe = Stripe('pk_test_51Qi7qaHgsEGcE4nXSY6yxLOxIwzgeVpaj0Ep50VdxhhRLMKaRu9zP4DgXv3nlCaiedpj1myamctga3haK7jtqQHb006wD2Lgsw');
        const elements = stripe.elements({
            clientSecret: '<?php echo $paymentIntent->client_secret; ?>'
        });

        const paymentElement = elements.create('payment');
        paymentElement.mount('#payment-element');

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const messageDiv = document.getElementById('payment-message');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';

            try {
                const {
                    error
                } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: 'http://localhost:8000/payment_success.php'
                    }
                });

                if (error) {
                    messageDiv.textContent = error.message;
                    messageDiv.classList.remove('hidden');
                    submitButton.disabled = false;
                    submitButton.textContent = 'Pay Now';
                }
            } catch (e) {
                messageDiv.textContent = 'An unexpected error occurred.';
                messageDiv.classList.remove('hidden');
                submitButton.disabled = false;
                submitButton.textContent = 'Pay Now';
            }
        });
    </script>
</body>

</html>