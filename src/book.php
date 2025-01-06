<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turf Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<section id="book">
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen py-10 px-4">
    <div class="container mx-auto max-w-4xl">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-baseball text-white text-3xl"></i>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-2">Turf Booking</h1>
            <p class="text-center text-gray-600">Book your favorite sport slot today</p>
        </div>

        <!-- Booking Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Sport selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-running mr-2"></i>Select Sport
                </label>
                <select id="sport-select" class="block w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    <option value="cricket">🏏 Cricket</option>
                    <option value="football">⚽ Football</option>
                    <option value="tennis">🎾 Tennis</option>
                </select>
            </div>

            <!-- 7-Day Date Selector -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-4">
                    <i class="far fa-calendar mr-2"></i>Select Date
                </label>
                <div id="calendar-grid" class="grid grid-cols-7 gap-2">
                    <!-- Next 7 days will be populated dynamically -->
                </div>
            </div>

            <!-- Time Slots Grid -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-4">
                    <i class="far fa-clock mr-2"></i>Select Time Slot
                </label>
                <div id="time-slots-grid" class="grid grid-cols-4 gap-3">
                    <!-- Time slots will be populated dynamically -->
                </div>
            </div>

            <!-- Book button -->
            <button id="book-slot" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition-all transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                <i class="fas fa-check-circle mr-2"></i>Book Selected Slot
            </button>
        </div>
    </div>

    <!-- Pop-up message -->
    <div id="popup-message" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full transform transition-all">
            <p id="popup-text" class="text-lg font-semibold text-gray-800 text-center mb-6"></p>
            <button id="popup-close" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition-all">
                Close
            </button>
        </div>
    </div>
</section>

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

            let selectedDate = null;
            let selectedTime = null;

            const populateCalendar = () => {
                calendarGrid.innerHTML = '';
                const today = new Date();
                for (let i = 0; i < 7; i++) {
                    const date = new Date(today.getTime() + i * 24 * 60 * 60 * 1000);
                    const dateStr = date.toISOString().split('T')[0];
                    const isToday = i === 0;

                    const dayElement = document.createElement('button');
                    dayElement.className = `p-2 rounded-lg text-center transition-all ${
                        dateStr === selectedDate ? 'bg-blue-500 text-white' : 'bg-blue-100 hover:bg-blue-200'
                    } ${isToday ? 'ring-2 ring-blue-500' : ''}`;
                    dayElement.textContent = `${date.toLocaleDateString('en-US', { weekday: 'short' })} ${date.getDate()}`;

                    dayElement.addEventListener('click', () => {
                        document.querySelectorAll('#calendar-grid button').forEach(btn => {
                            btn.classList.remove('bg-blue-500', 'text-white');
                        });
                        dayElement.classList.add('bg-blue-500', 'text-white');
                        selectedDate = dateStr;
                        populateTimeSlots();
                    });

                    calendarGrid.appendChild(dayElement);
                }
            };

            const populateTimeSlots = async () => {
                if (!selectedDate) return;

                timeSlotsGrid.innerHTML = '<div class="col-span-4 text-center">Loading...</div>';

                try {
                    const response = await fetch(`${backendUrl}?sport=${sportSelect.value}&date=${selectedDate}`);
                    const data = await response.json();

                    timeSlotsGrid.innerHTML = '';
                    data.slots.forEach(slot => {
                        const timeButton = document.createElement('button');
                        const isBooked = slot.status === 'booked';

                        // Convert the hour to AM/PM format
                        let hour = slot.hour;
                        const period = hour >= 12 ? 'PM' : 'AM';
                        hour = hour % 12 || 12;

                        timeButton.className = `p-3 rounded-lg text-center transition-all ${
                isBooked ? 
                'bg-red-50 text-red-600 cursor-not-allowed' : 
                'bg-green-50 text-green-600 hover:bg-green-100'
            }`;
                        timeButton.textContent = `${hour}:00 ${period}`;

                        if (!isBooked) {
                            timeButton.addEventListener('click', () => {
                                document.querySelectorAll('#time-slots-grid button').forEach(btn => {
                                    btn.classList.remove('ring-2', 'ring-green-500');
                                });
                                timeButton.classList.add('ring-2', 'ring-green-500');
                                selectedTime = slot.hour;
                            });
                        }

                        timeSlotsGrid.appendChild(timeButton);
                    });
                } catch (error) {
                    timeSlotsGrid.innerHTML = '<div class="col-span-4 text-center text-red-500">Error loading time slots</div>';
                }
            };


            const bookSlot = async () => {
                if (!selectedDate || selectedTime === null) {
                    popupText.textContent = 'Please select both date and time slot';
                    popupMessage.classList.remove('hidden');
                    return;
                }

                const response = await fetch(backendUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        sport: sportSelect.value,
                        date: selectedDate,
                        hour: selectedTime
                    }),
                });
                const result = await response.json();

                popupText.textContent = result.message;
                popupMessage.classList.remove('hidden');

                if (result.success) {
                    await populateTimeSlots();
                }
            };

            popupClose.addEventListener('click', () => {
                popupMessage.classList.add('hidden');
            });

            sportSelect.addEventListener('change', () => {
                if (selectedDate) populateTimeSlots();
            });
            bookSlotButton.addEventListener('click', bookSlot);

            // Initial population
            populateCalendar();
        });
    </script>
</body>

</html>