<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

require '../vendor/autoload.php';
$config = require dirname(__DIR__) . '/config.php';
$client = new MongoDB\Client($config['MONGODB_URI']);
$collection = $client->Play_Arena->users;

$user = $collection->findOne(['email' => $_SESSION['email']]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];
    $message = '';

    $bookingsCollection = $client->turf->bookings;
    $cancelRequestsCollection = $client->turf->cancel_requests;
    $pendingCollection = $client->Play_Arena->pending_registrations;

    if (!empty($_POST['full_name']) && $_POST['full_name'] !== $user['full_name']) {
        $updates['full_name'] = $_POST['full_name'];
        $_SESSION['user_full_name'] = $_POST['full_name'];
        
        $bookingsCollection->updateMany(
            ['email' => $_SESSION['email']], 
            ['$set' => ['full_name' => $_POST['full_name']]]
        );
        
        $cancelRequestsCollection->updateMany(
            ['email' => $_SESSION['email']], 
            ['$set' => ['full_name' => $_POST['full_name']]]
        );
        
        $pendingCollection->updateMany(
            ['email' => $_SESSION['email']], 
            ['$set' => ['full_name' => $_POST['full_name']]]
        );
        
        $message .= 'Name updated successfully. ';
    }

    if (!empty($_POST['current_password']) && !empty($_POST['new_password'])) {
        if (!password_verify($_POST['current_password'], $user['password'])) {
            $_SESSION['message'] = 'Current password is incorrect.';
            $_SESSION['message_type'] = 'error';
        } elseif ($_POST['new_password'] !== $_POST['confirm_password']) {
            $_SESSION['message'] = 'New passwords do not match.';
            $_SESSION['message_type'] = 'error';
        } else {
            $updates['password'] = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
            $message .= 'Password updated successfully. ';
        }
    }

    if (!empty($updates)) {
        $collection->updateOne(
            ['email' => $_SESSION['email']],
            ['$set' => $updates]
        );
        $_SESSION['message'] = trim($message);
        $_SESSION['message_type'] = 'success';
        header('Location: edit-profile.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Play Arena</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/lucide.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
</head>

<body class="min-h-screen bg-slate-100">
    <?php $textColor = 'text-black'; ?>
    <header class="<?php echo $textColor; ?>">
        <?php include 'navbar.php'; ?>
    </header>

    <?php if (isset($_SESSION['message'])): ?>
        <div id="message" class="fixed top-20 right-4 p-4 rounded-lg shadow-lg bg-white flex items-center gap-2 z-50 border-l-4 <?php echo $_SESSION['message_type'] === 'success' ? 'border-green-500' : 'border-red-500'; ?>">
            <i data-lucide="<?php echo $_SESSION['message_type'] === 'success' ? 'check' : 'alert-circle'; ?>"
                class="w-5 h-5 <?php echo $_SESSION['message_type'] === 'success' ? 'text-green-500' : 'text-red-500'; ?>"></i>
            <?php
            echo $_SESSION['message'];
            unset($_SESSION['message'], $_SESSION['message_type']);
            ?>
        </div>
    <?php endif; ?>

    <div class="container mx-auto max-w-8xl px-4 pb-8 mt-8">
        <!-- <div class="bg-white rounded-2xl p-8 mb-8 shadow-sm grid grid-cols-12 items-center gap-6">
            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center">
                <div class="profile-picture text-3xl font-bold text-slate-900">
                    <?php echo strtoupper(substr($_SESSION['user_full_name'], 0, 1)); ?>
                </div>
            </div>
            <div class="col-span-2">
                <h1 class="text-2xl font-semibold text-slate-900"><?php echo htmlspecialchars($user['full_name']); ?></h1>
                <p class="text-slate-600 mt-1"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div> -->


        <form action="edit-profile.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Personal Information -->
            <div class="bg-white rounded-2xl p-8 shadow-sm">
                <h2 class="text-xl font-semibold mb-6 flex items-center gap-2">
                    <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                    Personal Information
                </h2>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm text-slate-600 mb-2">Full Name</label>
                        <input
                            type="text"
                            name="full_name"
                            value="<?php echo htmlspecialchars($user['full_name']); ?>"
                            class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:border-blue-600 focus:ring-3 focus:ring-blue-100"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-600 mb-2">Email Address</label>
                        <input
                            type="email"
                            value="<?php echo htmlspecialchars($user['email']); ?>"
                            class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-100"
                            disabled>
                    </div>
                </div>
            </div>

            <!-- Security -->
            <div class="bg-white rounded-2xl p-8 shadow-sm">
                <h2 class="text-xl font-semibold mb-6 flex items-center gap-2">
                    <i data-lucide="lock" class="w-5 h-5 text-blue-600"></i>
                    Security
                </h2>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm text-slate-600 mb-2">Current Password</label>
                        <div class="relative">
                            <input
                                type="password"
                                name="current_password"
                                id="currentPassword"
                                class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:border-blue-600 focus:ring-3 focus:ring-blue-100"
                                placeholder="Enter current password">
                            <button
                                type="button"
                                class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-slate-600">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-600 mb-2">New Password</label>
                        <div class="relative">
                            <input
                                type="password"
                                name="new_password"
                                id="newPassword"
                                class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:border-blue-600 focus:ring-3 focus:ring-blue-100"
                                placeholder="Enter new password">
                            <button
                                type="button"
                                class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-slate-600">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-600 mb-2">Confirm New Password</label>
                        <div class="relative">
                            <input
                                type="password"
                                name="confirm_password"
                                id="confirmPassword"
                                class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:outline-none focus:border-blue-600 focus:ring-3 focus:ring-blue-100"
                                placeholder="Confirm new password">
                            <button
                                type="button"
                                class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-slate-600">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <div class="pt-4 mt-2 text-sm hover:text-blue-500">
                            <p><a href="forgot-password.php">Forgot your current password?</a></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="max-w-xs mx-auto">
                    <button
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/lucide.min.js"></script>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Handle message animation
        const messageDiv = document.getElementById('message');
        if (messageDiv) {
            messageDiv.style.display = 'block';
            gsap.fromTo("#message", {
                opacity: 0,
                y: -30,
                x: 30
            }, {
                opacity: 1,
                y: 0,
                x: 0,
                duration: 0.5,
                ease: "power3.out"
            });

            setTimeout(() => {
                gsap.to("#message", {
                    opacity: 0,
                    y: -30,
                    x: 30,
                    duration: 0.5,
                    ease: "power3.out",
                    onComplete: () => messageDiv.style.display = "none"
                });
            }, 3000);
        }

        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input');
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.setAttribute('data-lucide', 'eye-off');
                } else {
                    input.type = 'password';
                    icon.setAttribute('data-lucide', 'eye');
                }
                lucide.createIcons();
            });
        });
    </script>
</body>

</html>