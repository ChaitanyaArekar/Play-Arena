<?php
$turf = [
    'name' => 'City Sports Turf',
    'photos' => [
        '/public/img/football.jpg',
        '/public/img/cricket.jpg',
        '/public/img/tennis.jpg'
    ],
    'address' => '123 Turf Lane, Vadodara',
    'sports' => 'Cricket',
    'prices' => [
        'cricket' => 1300,
        'football' => 1500,
        'tennis' => 800
    ]
];

if (!empty($turf['photos'])) {
    $firstPhoto = $turf['photos'][0];
} else {
    $firstPhoto =  '/public/img/football.jpg';
}

$selectedSport = isset($_GET['sport']) ? $_GET['sport'] : '';
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
    </header>

    <div class="max-h-screen pt-7">
        <div class="container mx-auto">
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Turf Details Section -->
                <div class="lg:w-1/4">
                    <div class="bg-white rounded-lg shadow-lg min-h-[630px]">
                        <img id="turf-image" src="<?php echo $firstPhoto; ?>" alt="<?php echo $turf['name']; ?>"
                            class="w-full h-64 object-cover rounded-t-lg" />
                        <div class="p-4">
                            <h2 class="text-xl font-bold mb-3"><?php echo $turf['name']; ?></h2>
                            <div class="space-y-3">
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    <span class="text-sm">
                                        <a href="https://maps.app.goo.gl/TptvLJdU6ETJYhKR7" target="_blank" class="hover:text-blue-500">
                                            <?php echo $turf['address']; ?>
                                        </a>
                                    </span>
                                </div>
                                <div class=" flex items-center gap-2 text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm">08:00 AM - 11:59 PM</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2">
                                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                    <h3 class="font-semibold text-sm mb-2">Amenities</h3>
                                    <ul class="text-sm text-gray-600 space-y-1">
                                        <li>• Floodlights</li>
                                        <li>• Changing Rooms</li>
                                        <li>• Parking Available</li>
                                        <li>• Water Facility</li>
                                    </ul>
                                </div>
                                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                    <h3 class="font-semibold text-sm mb-2">Rules & Guidelines</h3>
                                    <ul class="text-sm text-gray-600 space-y-1">
                                        <li>• Respect Time</li>
                                        <li>• No Littering</li>
                                        <li>• No Spitting</li>
                                        <li>• No Smoking</li>
                                    </ul>
                                </div>
                            </div>
                            <h3 class="font-semibold text-sm mb-1 mt-4">Cancellation</h3>
                            <p class="text-sm text-gray-600 space-y-1">
                                Cancellation of Bookings is allowed as per the cancellation policy.
                                <a href="/src/cancellation_policy.php" class="text-blue-600" target="_blank"
                                    rel="noopener noreferrer">View Cancellation Policy</a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Booking Section -->
                <div class="lg:w-2/4">
                    <div class="bg-white rounded-lg shadow-lg p-6 min-h-[630px]">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-running mr-2"></i>Select Sport
                            </label>
                            <select id="sport-select"
                                class="block w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                <option value="cricket" <?php echo ($selectedSport === 'cricket') ? 'selected' : ''; ?>>🏏
                                    Cricket</option>
                                <option value="football" <?php echo ($selectedSport === 'football') ? 'selected' : ''; ?>>⚽
                                    Football</option>
                                <option value="tennis" <?php echo ($selectedSport === 'tennis') ? 'selected' : ''; ?>>🎾
                                    Tennis</option>
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

                <!-- Cart Summary Section -->
                <div class="lg:w-1/4 lg:m-0 lg:p-0 mb-10">
                    <div class="bg-white rounded-lg shadow-lg h-full">
                        <div class="p-4 border-b bg-gray-50 rounded-t-lg">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold text-gray-800">Cart Summary</h2>
                                <button id="clear-cart"
                                    class="text-red-500 hover:text-red-600 text-sm font-medium flex items-center gap-2 px-3 py-1 rounded-md hover:bg-red-50 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-trash-alt"></i>
                                    Clear All
                                </button>
                            </div>
                        </div>

                        <div class="p-4">
                            <div id="cart-items" class="space-y-3 max-h-[300px] overflow-y-auto mb-4">
                                <p class="text-gray-500 text-sm text-center py-8">No slots selected</p>
                            </div>

                            <div id="cart-total" class="border-t pt-4 space-y-3">
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
                                    class="w-full bg-green-500 text-white py-4 rounded-lg hover:bg-green-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2 font-medium">
                                    <i class="fas fa-check-circle"></i>
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="popup-message" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full transform transition-all">
                        <p id="popup-text" class="text-lg font-semibold text-gray-800 text-center mb-6"></p>
                        <button id="popup-close"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition-all">
                            Close
                        </button>
                    </div>
                </div>
                <div id="login-popup" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full transform transition-all">
                        <p class="text-lg font-semibold text-gray-800 text-center mb-6">Please login to book slots</p>
                        <div class="flex gap-4">
                            <button id="login-cancel" class="flex-1 border border-gray-300 hover:bg-gray-100 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-all">
                                Cancel
                            </button>
                            <button id="login-confirm" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition-all">
                                Login
                            </button>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="user-logged-in" value="<?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>">
                <input type="hidden" id="turf-images" value='<?php echo json_encode($turf['photos']); ?>'>
            </div>
        </div>
    </div>
    <script src="../src/book.js"></script>
</body>

</html>