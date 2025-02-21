<?php
// reset-password.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

$client = new MongoDB\Client($config['MONGODB_URI']);
$collection = $client->Play_Arena->users;

if (!isset($_GET['token'])) {
    header('Location: login.php');
    exit();
}

$token = $_GET['token'];
$currentTime = time();

$user = $collection->findOne([
    'reset_token' => $token,
    'reset_expires' => ['$gt' => $currentTime]
]);

if (!$user) {
    $_SESSION['message'] = "Invalid or expired reset token.";
    $_SESSION['message_type'] = "error";
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $_SESSION['message'] = "Passwords do not match.";
        $_SESSION['message_type'] = "error";
    } else {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $collection->updateOne(
            ['reset_token' => $token],
            [
                '$set' => ['password' => $password],
                '$unset' => ['reset_token' => "", 'reset_expires' => ""]
            ]
        );

        $_SESSION['message'] = "Password has been successfully reset. You can now login with your new password.";
        $_SESSION['message_type'] = "success";
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Play Arena</title>
    <link rel="stylesheet" href="login.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        <div id="resetPasswordForm">
            <h2>Reset Password</h2>
            <form action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                <div class="password-field">
                    <input type="password" name="password" id="newPassword" placeholder="New Password" required><br><br>
                    <i class="fas fa-eye" id="newPasswordEye" onclick="togglePasswordVisibility('newPassword', 'newPasswordEye')"></i>
                </div>
                <div class="password-field">
                    <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm Password" required><br><br>
                    <i class="fas fa-eye" id="confirmPasswordEye" onclick="togglePasswordVisibility('confirmPassword', 'confirmPasswordEye')"></i>
                </div>
                <button type="submit" class="btn">Set New Password</button>
            </form>
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

        function togglePasswordVisibility(passwordFieldId, eyeIconId) {
            const passwordField = document.getElementById(passwordFieldId);
            const eyeIcon = document.getElementById(eyeIconId);
            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            }
        }
    </script>
</body>

</html>