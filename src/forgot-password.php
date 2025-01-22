<?php
session_start();
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$uri = $_ENV['MONGODB_URI'];
$client = new MongoDB\Client($uri);
$collection = $client->Play_Arena->users;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower($_POST['email']);
    $user = $collection->findOne(['email' => $email]);

    if ($user) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires = time() + 3600;

        // Store token in database
        $collection->updateOne(
            ['email' => $email],
            ['$set' => [
                'reset_token' => $token,
                'reset_expires' => $expires
            ]]
        );

        // Send reset email
        $reset_link = "http://localhost:8000/src/reset-password.php?token=" . $token;
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USERNAME'];
            $mail->Password   = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $_ENV['SMTP_PORT'];

            // Recipients
            $mail->setFrom($_ENV['SMTP_FROM_ADDRESS'], $_ENV['SMTP_FROM_NAME']);
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Password Reset - Play Arena";
            $mail->Body    = "Click the following link to reset your password: <br><br><a href='" . $reset_link . "'>" . $reset_link . "</a><br><br>This link will expire in 1 hour.";
            $mail->AltBody = "Click the following link to reset your password: \n\n" . $reset_link . "\n\nThis link will expire in 1 hour.";

            $mail->send();
            $_SESSION['message'] = "Password reset instructions have been sent to your email.";
            $_SESSION['message_type'] = "success";
        } catch (Exception $e) {
            $_SESSION['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Email address not found.";
        $_SESSION['message_type'] = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Play Arena</title>
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
        <div id="forgotPasswordForm">
            <h2>Forgot Password</h2>
            <form action="forgot-password.php" method="POST">
                <input type="email" name="email" placeholder="Enter your email" required><br><br>
                <button type="submit" class="btn">Reset Password</button>
            </form>
            <p><a href="login.php">Back to Login</a></p>
        </div>
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