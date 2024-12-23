<?php
// Initialize error array
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic form validation
    if (empty($_POST['email'])) {
        $errors['email'] = "Email is required.";
    }

    if (empty($_POST['password'])) {
        $errors['password'] = "Password is required.";
    }

    // Handle successful form submission (e.g., check credentials in database)
    if (empty($errors)) {
        // Here you would add your login logic: 
        // 1. Check email and password against your database.
        // 2. If valid, log the user in. If not, show an error.
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/src/login.css">
    <script>
        function toggleDepartmentInput() {
            const userTypeSelect = document.getElementById('user_type');
            const departmentInput = document.getElementById('department_name');

            if (userTypeSelect.value === 'department') {
                departmentInput.style.display = 'block';
            } else {
                departmentInput.style.display = 'none';
            }
        }

        window.onload = function() {
            toggleDepartmentInput();
        };
    </script>
</head>
<body>
    <div class="container">
        <h1>Login</h1>

        <!-- Display email error if any -->
        <?php if (isset($errors['email'])): ?>
            <div class="error"><?php echo $errors['email']; ?></div>
        <?php endif; ?>

        <!-- Display password error if any -->
        <?php if (isset($errors['password'])): ?>
            <div class="error"><?php echo $errors['password']; ?></div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" name="email" class="form-control" id="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="user_type">User Type:</label>
                <select name="user_type" class="form-control" id="user_type" required>
                    <option value="user" <?php echo isset($_POST['user_type']) && $_POST['user_type'] == 'user' ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo isset($_POST['user_type']) && $_POST['user_type'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" class="form-control" id="password" required>
            </div>

            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="/src/register.php">Register</a></p>
    </div>
</body>
</html>
