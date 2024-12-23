<?php
$errors = [];
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic form validation
    if (empty($_POST['name'])) {
        $errors['name'] = "Name is required.";
    }

    if (empty($_POST['email'])) {
        $errors['email'] = "Email is required.";
    }

    if (empty($_POST['mobile'])) {
        $errors['mobile'] = "Mobile number is required.";
    }

    if (empty($_POST['user_type'])) {
        $errors['user_type'] = "User type is required.";
    }

    if (empty($_POST['password'])) {
        $errors['password'] = "Password is required.";
    }

    // Handle successful form submission (e.g., save to the database)
    if (empty($errors)) {
        // Here you would add your registration logic:
        // 1. Insert data into the database (e.g., user name, email, mobile, user type, password).
        // 2. If successful, you can display a success message.
        
        // For demonstration purposes, we are just displaying a success message.
        $successMessage = "Registration successful! Please log in.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="/src/login.css">
</head>
<body>
    
    <div class="container">
        <h1>Register</h1>

        <!-- Display success message if any -->
        <?php if (!empty($successMessage)): ?>
            <div class="success"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <!-- Display error messages if any -->
        <?php if (isset($errors['name'])): ?>
            <div class="error"><?php echo $errors['name']; ?></div>
        <?php endif; ?>

        <?php if (isset($errors['email'])): ?>
            <div class="error"><?php echo $errors['email']; ?></div>
        <?php endif; ?>

        <?php if (isset($errors['mobile'])): ?>
            <div class="error"><?php echo $errors['mobile']; ?></div>
        <?php endif; ?>

        <?php if (isset($errors['user_type'])): ?>
            <div class="error"><?php echo $errors['user_type']; ?></div>
        <?php endif; ?>

        <?php if (isset($errors['password'])): ?>
            <div class="error"><?php echo $errors['password']; ?></div>
        <?php endif; ?>

        <!-- Registration Form -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" name="name" class="form-control" id="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" name="email" class="form-control" id="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="mobile">Mobile Number:</label>
                <input type="text" name="mobile" class="form-control" id="mobile" value="<?php echo isset($_POST['mobile']) ? $_POST['mobile'] : ''; ?>" required>
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
                <input type="password" name="password" class="form-control" id="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>" required>
            </div>

            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="/src/login.php">Login</a></p>
    </div>
</body>
</html>
