<?php
session_start();  // Start the session at the beginning

require '../vendor/autoload.php';  // Include MongoDB PHP library

// MongoDB connection URI
$uri = "mongodb+srv://chaitanya32:Atlas123@cluster.jx1zl.mongodb.net/?retryWrites=true&w=majority&appName=Cluster";
$client = new MongoDB\Client($uri);
$collection = $client->Play_Arena->users;  // Reference to the 'users' collection

// Initialize form submission flag
$formType = 'login'; // Default form type is login

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Registration logic
    if (isset($_POST['register'])) {
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $user_type = $_POST['user_type'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);  // Hash the password

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
            // Store success message in session
            $_SESSION['success_message'] = "Registration successful! You can now login.";
            // Redirect to the login form after registration
            header('Location: login.php?form=login');
            exit();  // Make sure no further code is executed
        }
    }

    // Login logic
    elseif (isset($_POST['login'])) {
        $email = $_POST['email'];
        $user_type = $_POST['user_type'];
        $password = $_POST['password'];

        // Find the user in the database based on email and user type
        $user = $collection->findOne(['email' => $email, 'user_type' => $user_type]);

        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['login_success_message'] = "Login successful! Welcome, " . $user['full_name'] . ".";  // Store the success message in session
            $_SESSION['user'] = $user;  // Store user session data
            // Redirect to the index page (root directory) after successful login
            header("Location: /index.php");  // Redirect to the root directory's index.php
            exit();  // Ensure the script stops executing after the redirect
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
    <style>
        .form-container { width: 300px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        .btn { padding: 10px; width: 100%; background-color: #4CAF50; color: white; border: none; border-radius: 5px; }
        .btn:hover { background-color: #45a049; }
        .success-message { color: green; margin-bottom: 15px; }
        .error-message { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="form-container">
        <!-- Display Success Message for Registration if available -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?php
                    echo $_SESSION['success_message'];  // Display success message
                    unset($_SESSION['success_message']);  // Clear the message after displaying it
                ?>
            </div>
        <?php endif; ?>


        <!-- Show Login Form if $formType is login -->
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
        // JavaScript function to show the register form
        function showRegisterForm() {
            document.getElementById('loginForm').style.display = 'none'; // Hide login form
            document.getElementById('registerForm').style.display = 'block'; // Show register form
        }

        // JavaScript function to show the login form
        function showLoginForm() {
            document.getElementById('registerForm').style.display = 'none'; // Hide register form
            document.getElementById('loginForm').style.display = 'block'; // Show login form
        }
    </script>

</body>
</html>
