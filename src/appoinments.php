<?php
date_default_timezone_set('Asia/Kolkata');

$turf = [
    'name' => 'City Sports Turf',
    'photo' => '/public/img/football.jpg',
    'address' => '123 Turf Lane, Vadodara',
    'sports' => 'Cricket',
    'price' => 800
];

$dates = [];
for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime("+$i days"));
    $dates[] = [
        'date' => $date,
        'day' => date('d', strtotime($date)),
        'dayName' => date('D', strtotime($date))
    ];
}

$slots = [];
for ($hour = 10; $hour <= 24; $hour++) {
    if ($hour == 12) {
        $slots[] = "12:00 PM";
    } elseif ($hour > 12 && $hour < 24) {
        $slots[] = ($hour - 12) . ":00 PM";
    } elseif ($hour == 24) {
        $slots[] = "12:00 AM";
    } else {
        $slots[] = $hour . ":00 AM";
    }
}

$bookedSlots = ['12:00 PM', '3:00 PM', '6:00 PM'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turf Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100">
    <?php $textColor = 'text-black'; ?>
    <header>
        <div class="container mx-auto px-6 py-4">
            <?php include 'navbar.php'; ?>
        </div>
    </header>
    <div class="min-h-screen p-8">
        <div class="container mx-auto">
            <div class="flex flex-col lg:flex-row gap-6">
                <div class="lg:w-1/4">
                    <div class="bg-white rounded-lg shadow-lg min-h-[630px]">
                        <img
                            src="<?php echo $turf['photo']; ?>"
                            alt="<?php echo $turf['name']; ?>"
                            class="w-full h-64 object-cover rounded-t-lg" />
                        <div class="p-4">
                            <h2 class="text-xl font-bold mb-3"><?php echo $turf['name']; ?></h2>
                            <div class="space-y-3">
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    <span class="text-sm"><?php echo $turf['address']; ?></span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm">10:00 AM - 12:00 AM</span>
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
                        </div>
                        <h3 class="font-semibold text-sm mb-1 ml-4">Cancellation</h3>
                        <p class="text-sm text-gray-600 space-y-1 ml-4">
                            Cancellation of Bookings is allowed as per the cancellation policy.
                            <a href="/src/cancellation_policy.php" class="text-blue-600" target="_blank" rel="noopener noreferrer">View Cancellation Policy</a>
                        </p>

                    </div>
                </div>
                <div class="lg:w-2/4">
                    <div class="bg-white rounded-lg shadow-lg h-full p-6">
                        <div class="space-y-6">
                            <h2 class="text-xl font-bold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Select Date & Time
                            </h2>

                            <div>
                                <h3 class="font-semibold mb-3">Select Date</h3>
                                <div class="grid grid-cols-7 gap-2">
                                    <?php foreach ($dates as $date): ?>
                                        <button
                                            data-date="<?php echo $date['date']; ?>"
                                            class="date-btn p-2 rounded-lg text-center bg-gray-100 hover:bg-blue-600">
                                            <div class="text-sm font-semibold"><?php echo $date['day']; ?></div>
                                            <div class="text-xs"><?php echo $date['dayName']; ?></div>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div id="time-slots" class="hidden">
                                <h3 class="font-semibold mb-3">Select Time Slot</h3>
                                <div class="grid grid-cols-4 gap-2">
                                    <?php foreach ($slots as $slot): ?>
                                        <?php $isBooked = in_array($slot, $bookedSlots); ?>
                                        <button
                                            data-slot="<?php echo $slot; ?>"
                                            class="slot-btn p-2 rounded-lg text-center text-sm <?php echo $isBooked ? 'bg-red-100 text-red-500 cursor-not-allowed' : 'bg-gray-100 hover:bg-gray-200'; ?>"
                                            <?php if ($isBooked) echo 'disabled'; ?>>
                                            <?php echo $slot; ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <button
                                id="add-to-cart"
                                class="hidden w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                                Confirm Booking
                            </button>
                        </div>
                    </div>
                </div>

                <div class="lg:w-1/4">
                    <div class="bg-white rounded-lg shadow-lg h-full p-6">
                        <h2 class="text-xl font-bold mb-4">Cart Summary</h2>
                        <div id="cart-items" class="space-y-4">
                            <p class="text-gray-500 text-sm">No items in cart</p>
                        </div>
                        <div id="cart-total" class="hidden border-t pt-4 mt-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span>Total Slots</span>
                                <span id="total-slots">0</span>
                            </div>
                            <div class="flex justify-between font-bold mb-4">
                                <span>Total Amount</span>
                                <span id="total-amount">₹0</span>
                            </div>
                            <button class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600">
                                Proceed to Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let selectedDate = null;
            let selectedSlots = [];
            let cartItems = [];
            const PRICE_PER_SLOT = <?php echo $turf['price']; ?>;

            $('.date-btn').click(function() {
                $('.date-btn').removeClass('bg-blue-500 text-white').addClass('bg-gray-100');
                $(this).removeClass('bg-gray-100').addClass('bg-blue-500 text-white');
                selectedDate = $(this).data('date');
                selectedSlots = [];
                $('#time-slots').removeClass('hidden');
                updateAddToCartButton();
            });

            $('.slot-btn').click(function() {
                if (!$(this).prop('disabled')) {
                    const slot = $(this).data('slot');
                    if (selectedSlots.includes(slot)) {
                        selectedSlots = selectedSlots.filter(s => s !== slot);
                        $(this).removeClass('bg-blue-500 text-white').addClass('bg-blue-500');
                    } else {
                        selectedSlots.push(slot);
                        $(this).removeClass('bg-gray-100').addClass('bg-blue-500 text-white');
                    }
                    updateAddToCartButton();
                }
            });

            $('#add-to-cart').click(function() {
                if (selectedDate && selectedSlots.length > 0) {
                    const newItems = selectedSlots.map(slot => ({
                        id: Date.now() + Math.random(),
                        date: selectedDate,
                        slot: slot,
                        price: PRICE_PER_SLOT
                    }));
                    cartItems = [...cartItems, ...newItems];
                    updateCart();
                    selectedSlots = [];
                    $('.slot-btn').removeClass('bg-blue-500 text-white').addClass('bg-gray-100');
                    updateAddToCartButton();
                }
            });

            $(document).on('click', '.remove-item', function() {
                const itemId = $(this).data('id');
                cartItems = cartItems.filter(item => item.id !== itemId);
                updateCart();
            });

            function updateAddToCartButton() {
                if (selectedSlots.length > 0) {
                    $('#add-to-cart').removeClass('hidden');
                } else {
                    $('#add-to-cart').addClass('hidden');
                }
            }

            function updateCart() {
                if (cartItems.length === 0) {
                    $('#cart-items').html('<p class="text-gray-500 text-sm">No items in cart</p>');
                    $('#cart-total').addClass('hidden');
                } else {
                    let cartHtml = '<div class="space-y-3">';
                    cartItems.forEach(item => {
                        cartHtml += `
                            <div class="p-3 bg-gray-50 rounded-lg relative">
                                <button class="remove-item absolute top-2 right-2 text-gray-400 hover:text-red-500" data-id="${item.id}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                <div class="text-sm">Date: ${item.date}</div>
                                <div class="text-sm">Time: ${item.slot}</div>
                                <div class="font-semibold mt-1">₹${item.price}</div>
                            </div>
                        `;
                    });
                    cartHtml += '</div>';
                    $('#cart-items').html(cartHtml);
                    $('#cart-total').removeClass('hidden');
                    $('#total-slots').text(cartItems.length);
                    $('#total-amount').text(`₹${cartItems.length * PRICE_PER_SLOT}`);
                }
            }
        });

        // Add this to your existing script section
$(document).ready(function() {
    // ... (keep existing code) ...

    // Add payment mode selection
    let paymentMode = 'full';
    const paymentModeHtml = `
        <div class="mb-4">
            <h3 class="font-semibold mb-2">Select Payment Mode</h3>
            <div class="space-y-2">
                <label class="flex items-center space-x-2">
                    <input type="radio" name="payment_mode" value="full" checked>
                    <span>Full Payment Now</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="radio" name="payment_mode" value="advance">
                    <span>40% Advance (60% at venue)</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="radio" name="payment_mode" value="venue">
                    <span>Full Payment at Venue</span>
                </label>
            </div>
        </div>
    `;
    
    // Add payment mode selection before the proceed button
    $('#cart-total').append(paymentModeHtml);
    
    // Update payment mode when selection changes
    $('input[name="payment_mode"]').change(function() {
        paymentMode = $(this).val();
        updatePaymentSummary();
    });
    
    function updatePaymentSummary() {
        const totalAmount = cartItems.length * PRICE_PER_SLOT;
        let summaryHtml = '';
        
        switch (paymentMode) {
            case 'full':
                summaryHtml = `
                    <div class="text-sm mb-2">Pay now: ₹${totalAmount}</div>
                `;
                break;
            case 'advance':
                const advanceAmount = totalAmount * 0.4;
                const remainingAmount = totalAmount * 0.6;
                summaryHtml = `
                    <div class="text-sm mb-2">Pay now (40%): ₹${advanceAmount.toFixed(2)}</div>
                    <div class="text-sm mb-2">Pay at venue (60%): ₹${remainingAmount.toFixed(2)}</div>
                `;
                break;
            case 'venue':
                summaryHtml = `
                    <div class="text-sm mb-2">Pay at venue: ₹${totalAmount}</div>
                `;
                break;
        }
        
        $('#payment-summary').html(summaryHtml);
    }

    // Handle booking submission
    $('.proceed-payment').click(function(e) {
        e.preventDefault();
        
        // Show loading state
        $(this).prop('disabled', true).html('Processing...');
        
        const bookingData = {
            action: 'create_booking',
            userId: currentUserId, // Make sure this is set somewhere in your app
            name: currentUserName, // Make sure this is set somewhere in your app
            email: currentUserEmail, // Make sure this is set somewhere in your app
            phone: currentUserPhone, // Make sure this is set somewhere in your app
            slots: JSON.stringify(cartItems),
            totalAmount: cartItems.length * PRICE_PER_SLOT,
            paymentMode: paymentMode
        };
        
        $.post('/backend/booking.php', bookingData)
            .done(function(response) {
                if (response.success) {
                    showSuccessMessage('Booking confirmed! Check your email for details.');
                    clearCart();
                } else {
                    showErrorMessage(response.message || 'Booking failed. Please try again.');
                }
            })
            .fail(function() {
                showErrorMessage('Network error. Please try again.');
            })
            .always(function() {
                $('.proceed-payment').prop('disabled', false).html('Proceed to Payment');
            });
    });

    function showSuccessMessage(message) {
        const alert = `
            <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
                ${message}
            </div>
        `;
        $('body').append(alert);
        setTimeout(() => $('.fixed').remove(), 5000);
    }

    function showErrorMessage(message) {
        const alert = `
            <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
                ${message}
            </div>
        `;
        $('body').append(alert);
        setTimeout(() => $('.fixed').remove(), 5000);
    }

    function clearCart() {
        cartItems = [];
        updateCart();
        $('#time-slots').addClass('hidden');
        $('.date-btn').removeClass('bg-blue-500 text-white').addClass('bg-gray-100');
    }
});
    </script>
</body>

</html>