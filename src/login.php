<?php
session_start();
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__. '/../');
$dotenv->load();

$uri = $_ENV['MONGODB_URI'];
$client = new MongoDB\Client($uri);
$collection = $client->Play_Arena->users;

$formType = 'login'; // Default form type is login

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Registration logic
    if (isset($_POST['register'])) {
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $user_type = $_POST['user_type'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // Check if the email already exists in the database
        $existingUser = $collection->findOne(['email' => $email]);

        if ($existingUser) {
            echo "Email already exists. Please choose another.";
        } else {
            // Insert new user data into MongoDB
            $collection->insertOne([
                'full_name' => $full_name,
                'email' => $email,
                'user_type' => $user_type,
                'password' => $password
            ]);
            $_SESSION['success_message'] = "Registration successful! You can now login.";
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
            // Redirect to the index page (root directory) after successful login
            header("Location: /index.php");
            exit();
        } else {
            echo "Invalid credentials or user not found.";
        }
    }
} else {
    $formType = 'login'; // Show login form by default
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register - Play Arena</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <!-- Display Success Message for Registration if available -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success-message">
            <?php
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Show Login Form if $formType is login -->
    <div class="container">
        <div id="loginForm" style="<?php echo ($formType == 'login') ? '' : 'display:none;'; ?>">
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

        <!-- Show Register Form if $formType is register -->
        <div id="registerForm" style="<?php echo ($formType == 'register') ? '' : 'display:none;'; ?>">
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