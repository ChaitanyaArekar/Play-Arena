<?php
// verify-otp.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../vendor/autoload.php';

$config = require dirname(__DIR__) . '/config.php';
$client = new MongoDB\Client($config['MONGODB_URI']);
$collection = $client->Play_Arena->users;
$pendingCollection = $client->Play_Arena->pending_registrations;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $email = $_SESSION['pending_email'] ?? '';
    $otp = $_POST['otp'];

    if (empty($email)) {
        $_SESSION['message'] = "Session expired. Please register again.";
        $_SESSION['message_type'] = "error";
        header('Location: login.php');
        exit();
    }

    // Get the current time minus 10 minutes
    $tenMinutesAgo = time() - 600;

    $pendingUser = $pendingCollection->findOne([
        'email' => $email,
        'otp' => $otp,
        'created_at' => ['$gt' => $tenMinutesAgo]
    ]);

    if ($pendingUser) {
        try {
            // Insert verified user into main collection
            $collection->insertOne([
                'full_name' => $pendingUser['full_name'],
                'email' => $pendingUser['email'],
                'password' => $pendingUser['password'],
                'user_type' => 'user',
                'created_at' => time()
            ]);

            // Remove pending registration
            $pendingCollection->deleteOne(['_id' => $pendingUser['_id']]);

            $_SESSION['message'] = "Registration successful! You can now login.";
            $_SESSION['message_type'] = "success";
            unset($_SESSION['pending_email']);
            header('Location: login.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['message'] = "An error occurred during registration. Please try again.";
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Invalid or expired OTP. Please try again.";
        $_SESSION['message_type'] = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Play Arena</title>
    <link rel="stylesheet" href="login.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
</head>

<body>
    <?php if (isset($_SESSION['message'])): ?>
        <div id="message" class="<?php echo $_SESSION['message_type'] === 'success' ? 'success' : 'error'; ?>">
            <?php
            echo $_SESSION['message'];
            unset($_SESSION['message'], $_SESSION['message_type']);
            ?>
        </div>
    <?php endif; ?>

    <div class="container">
        <h2>Verify Email</h2>
        <form action="verify-otp.php" method="POST">
            <div class="form-group">
                <input type="text" name="otp" placeholder="Enter OTP" required>
            </div>
            <button type="submit" name="verify_otp">Verify OTP</button>
        </form>
        <p>Didn't receive OTP? <a href="resend-otp.php">Resend OTP</a></p>
    </div>

    <script>
        const messageDiv = document.getElementById('message');
        if (messageDiv) {
            messageDiv.style.display = 'block';
            gsap.fromTo("#message", {
                opacity: 0,
                y: -30
            }, {
                opacity: 1,
                y: 0,
                duration: 0.5,
                ease: "power3.out"
            });

            setTimeout(() => {
                gsap.to("#message", {
                    opacity: 0,
                    y: -30,
                    duration: 0.5,
                    ease: "power3.out",
                    onComplete: () => messageDiv.style.display = "none"
                });
            }, 3000);
        }
    </script>
</body>

</html>