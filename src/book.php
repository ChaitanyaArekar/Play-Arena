<?php
session_start();


$turf = [
    'name' => 'City Sports Turf',
    'photos' => [
        '/public/img/football.jpg',
        '/public/img/cricket.jpg',
        '/public/img/tennis.jpg'
    ],
    'address' => '123 Turf Lane, Vadodara',
    'sports' => ['Cricket', 'Football', 'Tennis'],
    'prices' => [
        'cricket' => 1300,
        'football' => 1500,
        'tennis' => 800
    ]
];

$selectedSport = isset($_GET['sport']) ? $_GET['sport'] : 'cricket';
$isLoggedIn = isset($_SESSION['user']);
$userType = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'customer';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turf Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        header {
            position: sticky;
            z-index: 1000;
        }

        @media (max-width: 768px) {
            .navbar ul li a {
                color: white;
            }
        }
    </style>

</head>

<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen py-2 px-4">
    <?php $textColor = 'text-black'; ?>
    <header class="<?php echo $textColor; ?>">
        <?php include 'navbar.php'; ?>
        <script src="https://js.stripe.com/v3/"></script>
    </header>

    <div class="max-h-screen pt-7">
        <div class="container mx-auto">
            <div class="flex flex-col lg:flex-row gap-6">
                <div class="lg:w-1/4">
                    <div class="bg-white rounded-lg shadow-lg">
                        <div class="relative h-64">
                            <img id="turf-image" src="<?php echo $turf['photos'][0]; ?>"
                                alt="<?php echo $turf['name']; ?>"
                                class="w-full h-full object-cover rounded-t-lg" />
                        </div>
                        <div class="p-4">
                            <h2 class="text-xl font-bold mb-3"><?php echo $turf['name']; ?></h2>
                            <div class="space-y-3">
                                <div class="flex items-center gap-2 text-gray-600">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <a href="https://maps.app.goo.gl/TptvLJdU6ETJYhKR7"
                                        target="_blank"
                                        class="text-sm hover:text-blue-500">
                                        <?php echo $turf['address']; ?>
                                    </a>
                                </div>
                                <div class="flex items-center gap-2 text-gray-600">
                                    <i class="far fa-clock"></i>
                                    <span class="text-sm">08:00 AM - 11:59 PM</span>
                                </div>
                                <div class="grid grid-cols-2 bg-gray-50 gap-4 mt-4">
                                    <div class="p-3 rounded-lg">
                                        <h3 class="font-semibold text-sm mb-2">Amenities</h3>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            <li>• Floodlights</li>
                                            <li>• Changing Rooms</li>
                                            <li>• Parking Available</li>
                                            <li>• Water Facility</li>
                                        </ul>
                                    </div>
                                    <div class="p-3 rounded-lg">
                                        <h3 class="font-semibold text-sm mb-2">Rules</h3>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            <li>• Respect Time</li>
                                            <li>• No Littering</li>
                                            <li>• No Spitting</li>
                                            <li>• No Smoking</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <h3 class="font-semibold text-sm mb-1">Cancellation</h3>
                                    <p class="text-sm text-gray-600">
                                        Cancellation of Bookings is allowed as per the policy.
                                        <a href="/src/cancellation_policy.php"
                                            class="text-blue-600 hover:underline"
                                            target="_blank">
                                            View Policy
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Section -->
                <div class="lg:w-2/4 ">
                    <div class="bg-white rounded-lg shadow-lg p-6 relative flex-grow h-full">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-running mr-2"></i>Select Sport
                            </label>
                            <select id="sport-select"
                                class="block w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <?php foreach ($turf['sports'] as $sport): ?>
                                    <option value="<?php echo strtolower($sport); ?>"
                                        <?php echo (strtolower($sport) === $selectedSport) ? 'selected' : ''; ?>>
                                        <?php echo $sport; ?> - ₹<?php echo $turf['prices'][strtolower($sport)]; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                <i class="far fa-calendar mr-2"></i>Select Date
                            </label>
                            <div id="calendar-grid" class="grid sm:grid-cols-7 grid-cols-3 gap-2"></div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                <i class="far fa-clock mr-2"></i>Select Time Slots
                            </label>
                            <div id="time-slots-grid" class="grid grid-cols-4 gap-3"></div>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="lg:w-1/4">
                    <div class="bg-white rounded-lg shadow-lg  flex-grow h-full mb-12">
                        <div class="p-4 border-b bg-gray-50 rounded-t-lg">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold text-gray-800">Cart Summary</h2>
                                <button id="clear-cart"
                                    class="text-red-500 hover:text-red-600 text-sm font-medium flex items-center gap-2 px-3 py-1 rounded-md hover:bg-red-50 disabled:opacity-50">
                                    <i class="fas fa-trash-alt"></i>
                                    Clear All
                                </button>
                            </div>
                        </div>

                        <div class="p-4">
                            <div id="cart-items" class="space-y-3 max-h-[300px] overflow-y-auto mb-4">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <div class="text-gray-400 mb-2">
                                        <i class="fas fa-shopping-cart text-3xl"></i>
                                    </div>
                                    <p class="text-gray-500 text-sm">No slots selected</p>
                                </div>
                            </div>

                            <div class="border-t pt-4 space-y-3">
                                <div class="p-4 rounded-lg space-y-2">
                                    <div class="flex justify-between text-sm text-black">
                                        <span class="font-medium">Selected Slots</span>
                                        <span id="total-slots" class="font-bold">0</span>
                                    </div>
                                    <div class="flex justify-between text-black pt-2">
                                        <span class="font-bold">Total Amount</span>
                                        <span id="total-amount" class="font-bold">₹0</span>
                                    </div>
                                </div>

                                <button id="book-slot"
                                    class="w-full bg-green-500 text-white py-4 rounded-lg hover:bg-green-600 disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center justify-center gap-2 font-medium">
                                    <i class="fas fa-check-circle"></i>
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="popup-message" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full">
            <p id="popup-text" class="text-lg font-semibold text-gray-800 text-center mb-6"></p>
            <button id="popup-close"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">
                Close
            </button>
        </div>
    </div>

    <div id="login-popup" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full">
            <p class="text-lg font-semibold text-gray-800 text-center mb-6">Please login to book slots</p>
            <div class="flex gap-4">
                <button id="login-cancel"
                    class="flex-1 border border-gray-300 hover:bg-gray-100 text-gray-700 font-semibold py-2 px-4 rounded-lg">
                    Cancel
                </button>
                <button id="login-confirm"
                    class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg">
                    Login
                </button>
            </div>
        </div>
    </div>
    <div id="cancel-confirm-popup" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full">
            <div id="cancel-confirm-details" class="mb-6">
            </div>
            <div class="flex gap-4">
                <button id="cancel-confirm-no" class="flex-1 border border-gray-300 hover:bg-gray-100 text-gray-700 font-semibold py-2 px-4 rounded-lg">
                    Keep Booking
                </button>
                <button id="cancel-confirm-yes" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg">
                    Cancel Booking
                </button>
            </div>
        </div>
    </div>
    <input type="hidden" id="user-logged-in" value="<?php echo $isLoggedIn ? 'true' : 'false'; ?>">
    <input type="hidden" id="user-type" value="<?php echo $userType; ?>">
    <input type="hidden" id="turf-images" value='<?php echo json_encode($turf['photos']); ?>'>

    <script src="../src/book.js"></script>
</body>

</html>