<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$config = require __DIR__ . '/config.php';
$client = new MongoDB\Client($config['MONGODB_URI']);
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
        $reset_link = $config['HOST'] . "/src/reset-password.php?token=" . $token;
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $config['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['SMTP_USERNAME'];
            $mail->Password   = $config['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $config['SMTP_PORT'];

            // Recipients
            $mail->setFrom($config['SMTP_FROM_ADDRESS'], $config['SMTP_FROM_NAME']);
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Reset Your Play Arena Password";
            $mail->Body = '
<!DOCTYPE html>
<html>
<head>
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }
        .notice {
            font-size: 12px;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello from Play Arena!</h2>
        <p>We received a request to reset your password. If you didn\'t make this request, you can safely ignore this email.</p>
        
        <p>To reset your password, click the button below:</p>
        
        <p style="text-align: center;">
            <a href="' . $reset_link . '" class="button" style="color: white;">Reset My Password</a>
        </p>
        
        <p class="notice">
            ⚠️ This link will expire in 1 hour for security reasons.<br>
            If you need assistance, please contact our support team.
        </p>
        
        <hr>
        <p style="font-size: 12px; color: #666;">
            This email was sent by Play Arena.<br>
            If you didn\'t request a password reset, please ignore this email or contact support if you have concerns.
        </p>
    </div>
</body>
</html>';
            $mail->AltBody = "
Hello from Play Arena!

We received a request to reset your password. If you didn't make this request, you can safely ignore this email.

To reset your password, copy and paste this link into your browser:
" . $reset_link . "

⚠️ This link will expire in 1 hour for security reasons.

If you need assistance, please contact our support team.

---
This email was sent by Play Arena.
If you didn't request a password reset, please ignore this email or contact support if you have concerns.";


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
            <h2>Reset Password</h2>
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