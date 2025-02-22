<?php
// verify-otp.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$config = require dirname(__DIR__) . '/config.php';
$client = new MongoDB\Client($config['MONGODB_URI']);
$collection = $client->Play_Arena->users;
$pendingCollection = $client->Play_Arena->pending_registrations;

$pendingCollection->createIndex([
    'created_at' => 1
], ['expireAfterSeconds' => 600]);

function sendOTPEmail($email, $otp)
{
    global $config;
    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = $config['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['SMTP_USERNAME'];
        $mail->Password = $config['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['SMTP_PORT'];

        $mail->setFrom('your-email@gmail.com', 'Play Arena');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your New OTP for Play Arena';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #333;'>Play Arena - Email Verification</h2>
                <p>Your new OTP is: <strong style='font-size: 20px; color: #4CAF50;'>{$otp}</strong></p>
                <p>This OTP will expire in 10 minutes.</p>
                <p style='color: #666;'>If you didn't request this OTP, please ignore this email.</p>
            </div>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Failed to send email: " . $mail->ErrorInfo);
        return false;
    }
}

// Handle AJAX resend OTP request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'resend') {
    header('Content-Type: application/json');

    $email = $_SESSION['pending_email'] ?? '';

    if (empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired. Please register again.']);
        exit();
    }

    $new_otp = sprintf("%06d", mt_rand(100000, 999999));
    $current_time = time();

    try {
        if (sendOTPEmail($email, $new_otp)) {
            $result = $pendingCollection->updateOne(
                ['email' => $email],
                [
                    '$set' => [
                        'otp' => $new_otp,
                        'created_at' => $current_time
                    ]
                ],
                ['upsert' => true]
            );

            if ($result->getModifiedCount() > 0 || $result->getUpsertedCount() > 0) {
                echo json_encode(['status' => 'success', 'message' => 'New OTP has been sent to your email.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update OTP. Please try again.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send email. Please try again.']);
        }
    } catch (Exception $e) {
        error_log("Error in resend OTP: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An error occurred. Please try again.']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $email = $_SESSION['pending_email'] ?? '';
    $otp = $_POST['otp'];

    if (empty($email)) {
        $_SESSION['message'] = "Session expired. Please register again.";
        $_SESSION['message_type'] = "error";
        header('Location: login.php');
        exit();
    }

    $pendingUser = $pendingCollection->findOne([
        'email' => $email,
        'otp' => $otp
    ]);

    if ($pendingUser) {
        try {
            $collection->insertOne([
                'full_name' => $pendingUser['full_name'],
                'email' => $pendingUser['email'],
                'password' => $pendingUser['password'],
                'user_type' => 'user',
                'created_at' => time()
            ]);

            $pendingCollection->deleteOne(['_id' => $pendingUser['_id']]);

            $_SESSION['message'] = "Registration successful! You can now login.";
            $_SESSION['message_type'] = "success";
            unset($_SESSION['pending_email']);
            header('Location: login.php');
            exit();
        } catch (Exception $e) {
            error_log("Error during user registration: " . $e->getMessage());
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
    <style>
        #resendOtp.disabled {
            pointer-events: none;
            opacity: 0.6;
            cursor: not-allowed;
        }

        .countdown {
            font-size: 0.9em;
            color: #666;
            margin-left: 10px;
        }
    </style>
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
                <input type="text" name="otp" placeholder="Enter OTP" required pattern="\d{6}" maxlength="6">
            </div>
            <button type="submit" name="verify_otp">Verify OTP</button>
        </form>
        <p>
            Didn't receive OTP?
            <a href="#" id="resendOtp">Resend OTP</a>
            <span id="countdown" class="countdown"></span>
        </p>
    </div>

    <script>
        function showMessage(message, type) {
            const existingMessage = document.getElementById('message');
            if (existingMessage) {
                existingMessage.remove();
            }

            const messageDiv = document.createElement('div');
            messageDiv.id = 'message';
            messageDiv.className = type;
            messageDiv.textContent = message;
            document.body.insertBefore(messageDiv, document.body.firstChild);

            gsap.fromTo(messageDiv, {
                opacity: 0,
                y: -30
            }, {
                opacity: 1,
                y: 0,
                duration: 0.5,
                ease: "power3.out"
            });

            setTimeout(() => {
                gsap.to(messageDiv, {
                    opacity: 0,
                    y: -30,
                    duration: 0.5,
                    ease: "power3.out",
                    onComplete: () => messageDiv.remove()
                });
            }, 3000);
        }

        const resendLink = document.getElementById('resendOtp');
        const countdownSpan = document.getElementById('countdown');
        let countdownInterval;

        function startCountdown(seconds) {
            resendLink.classList.add('disabled');
            let remainingTime = seconds;

            countdownInterval = setInterval(() => {
                remainingTime--;
                countdownSpan.textContent = `(${remainingTime}s)`;

                if (remainingTime <= 0) {
                    clearInterval(countdownInterval);
                    resendLink.classList.remove('disabled');
                    countdownSpan.textContent = '';
                }
            }, 1000);
        }

        const initialMessage = document.getElementById('message');
        if (initialMessage) {
            setTimeout(() => {
                gsap.to(initialMessage, {
                    opacity: 0,
                    y: -30,
                    duration: 0.5,
                    ease: "power3.out",
                    onComplete: () => initialMessage.remove()
                });
            }, 3000);
        }

        resendLink.addEventListener('click', function(e) {
            e.preventDefault();

            if (resendLink.classList.contains('disabled')) {
                return;
            }

            fetch('verify-otp.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=resend'
                })
                .then(response => response.json())
                .then(data => {
                    showMessage(data.message, data.status);
                    if (data.status === 'success') {
                        startCountdown(60);
                    }
                })
                .catch(error => {
                    showMessage('An error occurred. Please try again.', 'error');
                });
        });

        startCountdown(60);
    </script>
</body>

</html>