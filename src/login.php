<?php
session_start();
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$uri = $_ENV['MONGODB_URI'];
$client = new MongoDB\Client($uri);
$collection = $client->Play_Arena->users;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Registration functionality
    if (isset($_POST['register'])) {
        $full_name = $_POST['full_name'];
        $email = strtolower($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $existingUser = $collection->findOne(['email' => $email]);

        if ($existingUser) {
            $_SESSION['message'] = "Email already exists. Please choose another.";
            $_SESSION['message_type'] = "error";
        } else {
            $collection->insertOne([
                'full_name' => $full_name,
                'email' => $email,
                'user_type' => 'user',
                'password' => $password
            ]);
            $_SESSION['message'] = "Registration successful! You can now login.";
            $_SESSION['message_type'] = "success";
            header('Location: login.php');
            exit();
        }
    }

    // Login functionality
    elseif (isset($_POST['login'])) {
        $email = strtolower($_POST['email']);
        $user_type = $_POST['user_type'];
        $password = $_POST['password'];

        $user = $collection->findOne(['email' => $email, 'user_type' => $user_type]);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_full_name'] = $user['full_name'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['message'] = "Login successful! Welcome back, " . ucfirst($user['user_type']) . ".";
            $_SESSION['message_type'] = "success";
            header("Location: /index.php");
            exit();
        } else {
            $_SESSION['message'] = "Invalid credentials or user type mismatch.";
            $_SESSION['message_type'] = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register - Play Arena</title>
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
        <!-- Login Form -->
        <div id="loginForm">
            <h2>Login</h2>
            <form action="login.php" method="POST">
                <input type="email" name="email" placeholder="Email" required><br><br>
                <select name="user_type" required>
                    <option value="user">User</option>
                    <option value="owner">Owner</option>
                </select><br><br>
                <div class="password-field">
                    <input type="password" name="password" id="loginPassword" placeholder="Password" required><br><br>
                    <i class="fas fa-eye" id="loginEye" onclick="togglePasswordVisibility('loginPassword', 'loginEye')"></i>
                </div>
                <button type="submit" name="login" class="btn">Login</button>
            </form>
            <p>Don't have an account? <a href="javascript:void(0);" onclick="showRegisterForm()">Register here</a></p>
            <div class="forgot-password">
                <p><a href="forgot-password.php">Forgot your password?</a></p>
            </div>
        </div>

        <!-- Registration Form -->
        <div id="registerForm" style="display: none;">
            <h2>Register</h2>
            <form action="login.php" method="POST">
                <input type="text" name="full_name" placeholder="Full Name" required><br><br>
                <input type="email" name="email" placeholder="Email" required><br><br>
                <div class="password-field">
                    <input type="password" name="password" id="registerPassword" placeholder="Password" required><br><br>
                    <i class="fas fa-eye" id="registerEye" onclick="togglePasswordVisibility('registerPassword', 'registerEye')"></i>
                </div>
                <button type="submit" name="register" class="btn">Register</button>
            </form>
            <p>Already have an account? <a href="javascript:void(0);" onclick="showLoginForm()">Login here</a></p>
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

        function showRegisterForm() {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
        }

        function showLoginForm() {
            document.getElementById('registerForm').style.display = 'none';
            document.getElementById('loginForm').style.display = 'block';
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