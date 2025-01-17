<?php
session_start();
$error_message = $_SESSION['payment_error'] ?? 'An unknown error occurred';
unset($_SESSION['payment_error']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Turf Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto p-6">
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <div class="text-red-500 text-5xl mb-4">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h1 class="text-2xl font-bold mb-4">Payment Error</h1>
            <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($error_message); ?></p>
            <a href="/src/book.php" class="inline-block bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600">
                Go Back
            </a>
        </div>
    </div>
</body>

</html>