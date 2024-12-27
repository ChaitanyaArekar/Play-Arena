<?php
session_start();
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');

$dotenv->load();

$uri = $_ENV['MONGODB_URI'];
$client = new MongoDB\Client($uri);
$collection = $client->Play_Arena->users;

$formType = 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $user_type = $_POST['user_type'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $existingUser = $collection->findOne(['email' => $email]);

        if ($existingUser) {
            $_SESSION['message'] = "Email already exists. Please choose another.";
            $_SESSION['message_type'] = "error";
        } else {
            $collection->insertOne([
                'full_name' => $full_name,
                'email' => $email,
                'user_type' => $user_type,
                'password' => $password
            ]);
            $_SESSION['message'] = "Registration successful! You can now login.";
            $_SESSION['message_type'] = "success";
            header('Location: login.php?form=login');
            exit();
        }
    }

    // Login logic
    elseif (isset($_POST['login'])) {
        $email = $_POST['email'];
        $user_type = $_POST['user_type'];
        $password = $_POST['password'];
        $user = $collection->findOne(['email' => $email, 'user_type' => $user_type]);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            $_SESSION['user_full_name'] = $user['full_name'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['message'] = "Login successful! Welcome back.";
            $_SESSION['message_type'] = "success";
            header("Location: /index.php");
            exit();
        } else {
            $_SESSION['message'] = "Invalid credentials or user not found.";
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
        <div id="loginForm">
            <h2>Login</h2>
            <form action="login.php" method="POST">
                <input type="email" name="email" placeholder="Email" required><br><br>
                <select name="user_type" required>
                    <option value="user">User</option>
                    <option value="owner">Owner</option>
                </select><br><br>
                <input type="password" name="password" placeholder="Password" required><br><br>
                <button type="submit" name="login" class="btn">Login</button>
            </form>
            <p>Don't have an account? <a href="javascript:void(0);" onclick="showRegisterForm()">Register here</a></p>
        </div>

        <div id="registerForm" style="display: none;">
            <h2>Register</h2>
            <form action="login.php" method="POST">
                <input type="text" name="full_name" placeholder="Full Name" required><br><br>
                <input type="email" name="email" placeholder="Email" required><br><br>
                <select name="user_type" required>
                    <option value="user">User</option>
                    <option value="owner">Owner</option>
                </select><br><br>
                <input type="password" name="password" placeholder="Password" required><br><br>
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
    </script>
</body>

</html>