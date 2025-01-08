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
    'price' => 800
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
</head>

<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen py-2 px-4">
    <?php $textColor = 'text-black'; ?>
    <header class="<?php echo $textColor; ?>">
        <?php include 'navbar.php'; ?>
    </header>

    <div class="max-h-screen p-8">
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
                                    <span class="text-sm"><?php echo $turf['address']; ?></span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-600">
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
                            <div id="calendar-grid" class="grid grid-cols-7 gap-2"></div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                <i class="far fa-clock mr-2"></i>Select Time Slots
                                <span class="text-sm text-gray-500">(Multiple selection allowed)</span>
                            </label>
                            <div id="time-slots-grid" class="grid grid-cols-4 gap-3"></div>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary Section -->
                <div class="lg:w-1/4">
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
                                <div class="bg-blue-50 p-4 rounded-lg space-y-2">
                                    <div class="flex justify-between text-sm text-blue-600">
                                        <span class="font-medium">Selected Slots</span>
                                        <span id="total-slots" class="font-bold">0</span>
                                    </div>
                                    <div class="flex justify-between text-sm text-blue-600">
                                        <span class="font-medium">Price per Slot</span>
                                        <span class="font-bold">₹<?php echo $turf['price']; ?></span>
                                    </div>
                                    <div class="flex justify-between text-blue-800 border-t border-blue-200 pt-2">
                                        <span class="font-bold">Total Amount</span>
                                        <span id="total-amount" class="font-bold">₹0</span>
                                    </div>
                                </div>

                                <button id="book-slot"
                                    class="w-full bg-green-500 text-white py-4 rounded-lg hover:bg-green-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2 font-medium">
                                    <i class="fas fa-check-circle"></i>
                                    Book Selected Slots
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

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const backendUrl = 'http://localhost:8000/db.php';
                        const sportSelect = document.getElementById('sport-select');
                        const calendarGrid = document.getElementById('calendar-grid');
                        const timeSlotsGrid = document.getElementById('time-slots-grid');
                        const bookSlotButton = document.getElementById('book-slot');
                        const popupMessage = document.getElementById('popup-message');
                        const popupText = document.getElementById('popup-text');
                        const popupClose = document.getElementById('popup-close');
                        const cartItems = document.getElementById('cart-items');
                        const totalSlots = document.getElementById('total-slots');
                        const totalAmount = document.getElementById('total-amount');
                        const PRICE_PER_SLOT = <?php echo $turf['price']; ?>;

                        let selectedDate = null;
                        let selectedTimeSlots = new Set();

                        // Image Slideshow
                        const images = <?php echo json_encode($turf['photos']); ?>;
                        let currentIndex = 0;
                        const imgElement = document.getElementById('turf-image');

                        setInterval(() => {
                            currentIndex = (currentIndex + 1) % images.length;
                            imgElement.src = images[currentIndex];
                        }, 3000);

                        const updateCart = () => {
                            const clearCartButton = document.getElementById('clear-cart');

                            if (selectedTimeSlots.size === 0) {
                                cartItems.innerHTML = `
                <div class="flex flex-col items-center justify-center py-8">
                    <div class="text-gray-400 mb-2">
                        <i class="fas fa-shopping-cart text-3xl"></i>
                    </div>
                    <p class="text-gray-500 text-sm text-center">No slots selected</p>
                </div>`;
                                totalSlots.textContent = '0';
                                totalAmount.textContent = '₹0';
                                bookSlotButton.disabled = true;
                                clearCartButton.disabled = true;
                                return;
                            }

                            cartItems.innerHTML = '';
                            selectedTimeSlots.forEach(slot => {
                                const hour = slot % 12 || 12;
                                const period = slot >= 12 ? 'PM' : 'AM';
                                const itemDiv = document.createElement('div');
                                itemDiv.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all';
                                itemDiv.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="text-blue-500">
                        <i class="far fa-clock"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium">${hour}:00 ${period}</div>
                        <div class="text-xs text-gray-500">${selectedDate}</div>
                    </div>
                </div>
                <button class="text-red-500 hover:text-red-600 p-1 hover:bg-red-50 rounded-full transition-all" 
                    onclick="removeTimeSlot(${slot})" 
                    title="Remove Slot">
                    <i class="fas fa-times"></i>
                </button>
            `;
                                cartItems.appendChild(itemDiv);
                            });

                            totalSlots.textContent = selectedTimeSlots.size;
                            totalAmount.textContent = `₹${selectedTimeSlots.size * PRICE_PER_SLOT}`;
                            bookSlotButton.disabled = false;
                            clearCartButton.disabled = false;
                        };

                        // Add clear cart functionality
                        const clearCart = () => {
                            selectedTimeSlots.clear();
                            // Reset all selected time slots in the grid
                            const timeButtons = timeSlotsGrid.querySelectorAll('button');
                            timeButtons.forEach(btn => {
                                btn.classList.remove('ring-2', 'ring-green-500', 'bg-green-200');
                                btn.classList.add('bg-green-50');
                            });
                            updateCart();
                        };

                        // Add event listener for clear cart button
                        document.getElementById('clear-cart').addEventListener('click', () => {
                            if (selectedTimeSlots.size > 0) {
                                clearCart();
                            }
                        });

                        window.removeTimeSlot = (slot) => {
                            selectedTimeSlots.delete(slot);
                            updateCart();
                            const timeButtons = timeSlotsGrid.querySelectorAll('button');
                            timeButtons.forEach(btn => {
                                if (parseInt(btn.dataset.hour) === slot) {
                                    btn.classList.remove('ring-2', 'ring-green-500', 'bg-green-200');
                                    btn.classList.add('bg-green-50');
                                }
                            });
                        };

                        const populateCalendar = () => {
                            calendarGrid.innerHTML = '';
                            const today = new Date();
                            for (let i = 0; i < 7; i++) {
                                const date = new Date(today.getTime() + i * 24 * 60 * 60 * 1000);
                                const dateStr = date.toISOString().split('T')[0];
                                const isToday = i === 0;

                                const dayElement = document.createElement('button');
                                dayElement.className = `p-2 rounded-lg text-center transition-all ${dateStr === selectedDate ? 'bg-blue-500 text-white' : 'bg-blue-100 hover:bg-blue-200'} ${isToday ? 'ring-2 ring-blue-500' : ''}`;
                                dayElement.textContent = `${date.toLocaleDateString('en-US', { weekday: 'short' })} ${date.getDate()}`;

                                dayElement.addEventListener('click', () => {
                                    document.querySelectorAll('#calendar-grid button').forEach(btn => {
                                        btn.classList.remove('bg-blue-500', 'text-white');
                                    });
                                    dayElement.classList.add('bg-blue-500', 'text-white');
                                    selectedDate = dateStr;
                                    selectedTimeSlots.clear();
                                    updateCart();
                                    populateTimeSlots();
                                });

                                calendarGrid.appendChild(dayElement);
                            }
                        };

                        const populateTimeSlots = async () => {
                            if (!selectedDate) return;

                            timeSlotsGrid.innerHTML = `
                    <div class="col-span-4 flex flex-col items-center justify-center space-y-4">
                        <div class="flex space-x-2">
                            <div class="h-3 w-3 bg-blue-500 rounded-full animate-bounce"></div>
                            <div class="h-3 w-3 bg-blue-500 rounded-full animate-bounce delay-100"></div>
                            <div class="h-3 w-3 bg-blue-500 rounded-full animate-bounce delay-200"></div>
                        </div>
                        <p class="text-gray-500 text-sm">Fetching available time slots...</p>
                    </div>
                `;

                            try {
                                const response = await fetch(`${backendUrl}?sport=${sportSelect.value}&date=${selectedDate}`);
                                const data = await response.json();

                                timeSlotsGrid.innerHTML = '';
                                data.slots.forEach(slot => {
                                    const timeButton = document.createElement('button');
                                    const isBooked = slot.status === 'booked';
                                    const hour = slot.hour % 12 || 12;
                                    const period = slot.hour >= 12 ? 'PM' : 'AM';

                                    timeButton.className = `p-3 rounded-lg text-center transition-all ${
                            isBooked ? 'bg-red-50 text-red-600 cursor-not-allowed' : 'bg-green-50 text-green-600 hover:bg-green-100'
                        }`;
                                    timeButton.textContent = `${hour}:00 ${period}`;
                                    timeButton.dataset.hour = slot.hour;

                                    if (!isBooked) {
                                        timeButton.addEventListener('click', () => {
                                            if (selectedTimeSlots.has(slot.hour)) {
                                                selectedTimeSlots.delete(slot.hour);
                                                timeButton.classList.remove('ring-2', 'ring-green-500', 'bg-green-200');
                                                timeButton.classList.add('bg-green-50');
                                            } else {
                                                selectedTimeSlots.add(slot.hour);
                                                timeButton.classList.add('ring-2', 'ring-green-500', 'bg-green-200');
                                                timeButton.classList.remove('bg-green-50');
                                            }
                                            updateCart();
                                        });
                                    }

                                    timeSlotsGrid.appendChild(timeButton);
                                });
                            } catch (error) {
                                timeSlotsGrid.innerHTML = '<div class="col-span-4 text-center text-red-500">Error loading time slots</div>';
                            }
                        };

                        const bookSlot = async () => {
                            if (!selectedDate || selectedTimeSlots.size === 0) {
                                popupText.textContent = 'Please select at least one time slot';
                                popupMessage.classList.remove('hidden');
                                return;
                            }

                            const isLoggedIn = <?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>;
                            if (!isLoggedIn) {
                                window.location.href = "../src/login.php";
                                return;
                            }

                            try {
                                const bookingPromises = Array.from(selectedTimeSlots).map(hour =>
                                    fetch(backendUrl, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            sport: sportSelect.value,
                                            date: selectedDate,
                                            hour: hour
                                        }),
                                    })
                                );

                                const results = await Promise.all(bookingPromises);
                                const allSuccessful = results.every(r => r.ok);

                                if (allSuccessful) {
                                    popupText.textContent = 'All slots booked successfully!';
                                    selectedTimeSlots.clear();
                                    await populateTimeSlots();
                                    updateCart();
                                } else {
                                    popupText.textContent = 'Some slots could not be booked. Please try again.';
                                }
                            } catch (error) {
                                popupText.textContent = 'Error booking slots. Please try again.';
                            }
                            popupMessage.classList.remove('hidden');
                        };

                        sportSelect.addEventListener('change', () => {
                            if (selectedDate) {
                                selectedTimeSlots.clear();
                                updateCart();
                                populateTimeSlots();
                            }
                        });

                        bookSlotButton.addEventListener('click', bookSlot);

                        popupClose.addEventListener('click', () => {
                            popupMessage.classList.add('hidden');
                        });

                        populateCalendar();
                        updateCart();
                    });
                </script>

</body>

</html>