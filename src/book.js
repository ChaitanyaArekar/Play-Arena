document.addEventListener("DOMContentLoaded", () => {
  const backendUrl = window.appConfig.backendUrl;
  const stripePublicKey = window.appConfig.stripePublicKey;
  const sportSelect = document.getElementById("sport-select");
  const calendarGrid = document.getElementById("calendar-grid");
  const timeSlotsGrid = document.getElementById("time-slots-grid");
  const bookSlotButton = document.getElementById("book-slot");
  const popupMessage = document.getElementById("popup-message");
  const popupText = document.getElementById("popup-text");
  const popupClose = document.getElementById("popup-close");
  const cartItems = document.getElementById("cart-items");
  const totalSlots = document.getElementById("total-slots");
  const totalAmount = document.getElementById("total-amount");
  const loginPopup = document.getElementById("login-popup");
  const loginConfirm = document.getElementById("login-confirm");
  const loginCancel = document.getElementById("login-cancel");
  const isOwner = document.getElementById("user-type").value === "owner";
  const cancelConfirmPopup = document.getElementById("cancel-confirm-popup");
  const cancelConfirmDetails = document.getElementById("cancel-confirm-details");
  const cancelConfirmYes = document.getElementById("cancel-confirm-yes");
  const cancelConfirmNo = document.getElementById("cancel-confirm-no");
  const urlParams = new URLSearchParams(window.location.search);
  const paymentStatus = urlParams.get('payment_status');

  const PRICES = {
    cricket: 1300,
    football: 1500,
    tennis: 800,
  };

  let selectedDate = null;
  let selectedTimeSlots = new Set();
  const images = JSON.parse(document.getElementById("turf-images").value);
  let currentIndex = 0;
  const imgElement = document.getElementById("turf-image");

  setInterval(() => {
    currentIndex = (currentIndex + 1) % images.length;
    imgElement.src = images[currentIndex];
  }, 3000);

  const updateCart = () => {
    const clearCartButton = document.getElementById("clear-cart");
    const currentPrice = PRICES[sportSelect.value];

    if (selectedTimeSlots.size === 0) {
      cartItems.innerHTML = `
        <div class="flex flex-col items-center justify-center py-8">
            <div class="text-gray-400 mb-2">
                <i class="fas fa-shopping-cart text-3xl"></i>
            </div>
            <p class="text-gray-500 text-sm text-center">No slots selected</p>
        </div>`;
      totalSlots.textContent = "0";
      totalAmount.textContent = "₹0";
      bookSlotButton.disabled = true;
      clearCartButton.disabled = true;
      return;
    }

    const formattedDate = new Date(selectedDate).toLocaleDateString("en-US", {
      month: "short",
      day: "numeric",
    });

    cartItems.innerHTML = "";
    selectedTimeSlots.forEach((slot) => {
      const hour = slot % 12 || 12;
      const period = slot >= 12 ? "PM" : "AM";
      const itemDiv = document.createElement("div");
      itemDiv.className =
        "flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all";
      itemDiv.innerHTML = `
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <span class="font-medium capitalize">${sportSelect.value}</span>
                <span class="text-sm text-gray-500">|</span>
                <span class="text-sm text-gray-600">${formattedDate}</span>
                <span class="text-sm text-gray-500">|</span>
                <div class="text-xs text-gray-600">${hour}:00 ${period}</div>
            </div>
            <div class="text-sm text-gray-500">₹${currentPrice}</div>
        </div>
        <button class="text-red-500 hover:text-red-600 p-1 hover:bg-red-50 rounded-full transition-all" 
            onclick="removeTimeSlot(${slot})" 
            title="Remove Slot">
            <i class="fas fa-times"></i>
        </button>`;
      cartItems.appendChild(itemDiv);
    });

    totalSlots.textContent = selectedTimeSlots.size;
    totalAmount.textContent = `₹${selectedTimeSlots.size * currentPrice}`;
    bookSlotButton.disabled = false;
    clearCartButton.disabled = false;
  };

  const clearCart = () => {
    selectedTimeSlots.clear();
    const timeButtons = timeSlotsGrid.querySelectorAll(
      "button, div[data-hour]"
    );
    timeButtons.forEach((btn) => {
      if (btn.dataset.hour) {
        btn.classList.remove("ring-2", "ring-green-500", "bg-green-200");
        btn.classList.add("bg-green-50", "hover:bg-green-100");
      }
    });
    updateCart();
  };

  document.getElementById("clear-cart").addEventListener("click", () => {
    if (selectedTimeSlots.size > 0) {
      clearCart();
    }
  });

  window.removeTimeSlot = (slot) => {
    selectedTimeSlots.delete(slot);
    updateCart();
    const timeButtons = timeSlotsGrid.querySelectorAll("button");
    timeButtons.forEach((btn) => {
      if (parseInt(btn.dataset.hour) === slot) {
        btn.classList.remove("ring-2", "ring-green-500", "bg-green-200");
        btn.classList.add("bg-green-50");
      }
    });
  };

  window.removeTimeSlot = (slot) => {
    selectedTimeSlots.delete(slot);
    updateCart();

    const timeButtons = timeSlotsGrid.querySelectorAll(
      "button, div[data-hour]"
    );
    timeButtons.forEach((btn) => {
      if (parseInt(btn.dataset.hour) === slot) {
        btn.classList.remove("ring-2", "ring-green-500", "bg-green-200");
        btn.classList.add("bg-green-50", "hover:bg-green-100");
      }
    });
  };

  const populateCalendar = () => {
    calendarGrid.innerHTML = "";
    const today = new Date();
    selectedDate = today.toISOString().split("T")[0];

    for (let i = 0; i < 7; i++) {
      const date = new Date(today.getTime() + i * 24 * 60 * 60 * 1000);
      const dateStr = date.toISOString().split("T")[0];
      const isToday = i === 0;

      const dayElement = document.createElement("button");
      dayElement.className = `p-2 rounded-lg text-center transition-all ${
        dateStr === selectedDate
          ? "bg-blue-500 text-white"
          : "bg-blue-100 hover:bg-blue-200"
      } ${isToday ? "" : ""}`;
      dayElement.textContent = `${date.toLocaleDateString("en-US", {
        weekday: "short",
      })} ${date.getDate()}`;

      dayElement.addEventListener("click", () => {
        document.querySelectorAll("#calendar-grid button").forEach((btn) => {
          btn.classList.remove("bg-blue-500", "text-white");
          btn.classList.add("bg-blue-100");
        });
        dayElement.classList.add("bg-blue-500", "text-white");
        dayElement.classList.remove("bg-blue-100");
        selectedDate = dateStr;
        selectedTimeSlots.clear();
        updateCart();
        populateTimeSlots();
      });

      calendarGrid.appendChild(dayElement);
    }
    populateTimeSlots();
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
            <p class="text-gray-500 text-sm">Loading available slots...</p>
        </div>`;

    try {
      const response = await fetch(
        `${backendUrl}?sport=${sportSelect.value}&date=${selectedDate}`
      );
      const data = await response.json();

      timeSlotsGrid.innerHTML = "";

      const now = new Date();
      const currentHour = now.getHours();
      const today = now.toISOString().split("T")[0];

      data.slots.forEach((slot) => {
        if (selectedDate === today && slot.hour <= currentHour) {
          return;
        }

        const timeButton = document.createElement("div");
        const isBooked = slot.status === "booked";
        const isRestricted = slot.status === "restricted";
        const hour = slot.hour % 12 || 12;
        const period = slot.hour >= 12 ? "PM" : "AM";

        timeButton.className = `relative p-3 rounded-lg text-center transition-all ${
          isBooked
            ? "bg-red-50 text-red-600 " +
              (isOwner ? "cursor-pointer hover:bg-red-100" : "")
            : isRestricted
            ? "bg-gray-50 text-gray-600 cursor-not-allowed"
            : "bg-green-50 text-green-600 hover:bg-green-100"
        }`;

        const timeDisplay = document.createElement("div");
        timeDisplay.textContent = `${hour}:00 ${period}`;
        timeButton.appendChild(timeDisplay);

        if (isBooked && isOwner && slot.booking_info) {
          const bookingInfo = document.createElement("div");
          bookingInfo.className = "text-xs text-gray-600 mt-1";
          // bookingInfo.textContent = `Booked by: ${slot.booking_info.full_name}`;
          timeButton.appendChild(bookingInfo);

          timeButton.onclick = () => {
            const formattedDate = new Date(selectedDate).toLocaleDateString(
              "en-US",
              {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
              }
            );

            cancelConfirmDetails.innerHTML = `
              <div class="space-y-3">
                <div class="text-lg font-semibold text-red-600 mb-4">Are you sure you want to cancel this booking?</div>
                <div class="space-y-2 text-gray-700">
                  <p><span class="font-medium">Sport:</span> ${
                    sportSelect.value
                  }</p>
                  <p><span class="font-medium">Date:</span> ${formattedDate}</p>
                  <p><span class="font-medium">Time:</span> ${hour}:00 ${period}</p>
                  <p><span class="font-medium">Customer Name:</span> ${
                    slot.booking_info.full_name
                  }</p>
                  <p><span class="font-medium">Email:</span> ${
                    slot.booking_info.email || "N/A"
                  }</p>
                </div>
              </div>
            `;

            cancelConfirmPopup.classList.remove("hidden");

            cancelConfirmYes.onclick = async () => {
              try {
                const response = await fetch(backendUrl, {
                  method: "DELETE",
                  headers: { "Content-Type": "application/json" },
                  body: JSON.stringify({
                    sport: sportSelect.value,
                    date: selectedDate,
                    hour: slot.hour,
                  }),
                });

                const result = await response.json();
                cancelConfirmPopup.classList.add("hidden");

                if (result.success) {
                  popupText.textContent = "Booking cancelled successfully!";
                  await populateTimeSlots();
                  // Remove associated cancellation request
                  try {
                    await fetch("process_cancel_request.php", {
                      method: "POST",
                      headers: { "Content-Type": "application/json" },
                      body: JSON.stringify({
                        sport: sportSelect.value,
                        date: selectedDate,
                        hour: slot.hour,
                      }),
                    });
                  } catch (removeError) {
                    console.error(
                      "Error removing cancellation request:",
                      removeError
                    );
                  }
                } else {
                  popupText.textContent =
                    result.message || "Failed to cancel booking";
                }
                popupMessage.classList.remove("hidden");
              } catch (error) {
                cancelConfirmPopup.classList.add("hidden");
                popupText.textContent = "Error cancelling booking";
                popupMessage.classList.remove("hidden");
              }
            };

            cancelConfirmNo.onclick = () => {
              cancelConfirmPopup.classList.add("hidden");
            };
          };
        }

        if (!isBooked && !isRestricted) {
          timeButton.dataset.hour = slot.hour;
          timeButton.style.cursor = "pointer";
          timeButton.onclick = () => {
            if (selectedTimeSlots.has(slot.hour)) {
              selectedTimeSlots.delete(slot.hour);
              timeButton.classList.remove(
                "ring-2",
                "ring-green-500",
                "bg-green-200"
              );
              timeButton.classList.add("bg-green-50");
            } else {
              selectedTimeSlots.add(slot.hour);
              timeButton.classList.add(
                "ring-2",
                "ring-green-500",
                "bg-green-200"
              );
              timeButton.classList.remove("bg-green-50");
            }
            updateCart();
          };
        }

        if (isOwner && !isBooked && !isRestricted) {
          timeButton.onclick = () => {
            const formattedDate = new Date(selectedDate).toLocaleDateString(
              "en-US",
              {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
              }
            );

            const confirmDetails = `
      <div class="space-y-3">
        <div class="text-lg font-semibold text-red-600 mb-4">Confirm Slot Restriction</div>
        <div class="space-y-2 text-gray-700">
          <p><span class="font-medium">Sport:</span> ${sportSelect.value}</p>
          <p><span class="font-medium">Date:</span> ${formattedDate}</p>
          <p><span class="font-medium">Time:</span> ${hour}:00 ${period}</p>
        </div>
        <p class="text-sm text-gray-600 mt-2">
          This slot will be unavailable for booking. Are you sure?
        </p>
      </div>
    `;

            cancelConfirmDetails.innerHTML = confirmDetails;
            cancelConfirmPopup.classList.remove("hidden");

            cancelConfirmYes.onclick = async () => {
              try {
                const response = await fetch(backendUrl, {
                  method: "POST",
                  headers: { "Content-Type": "application/json" },
                  body: JSON.stringify({
                    sport: sportSelect.value,
                    date: selectedDate,
                    hour: slot.hour,
                    action: "restrict",
                  }),
                });

                const result = await response.json();
                cancelConfirmPopup.classList.add("hidden");

                if (result.success) {
                  await populateTimeSlots();
                  popupText.textContent = "Slot restricted successfully";
                  popupMessage.classList.remove("hidden");
                } else {
                  popupText.textContent =
                    result.message || "Failed to restrict slot";
                  popupMessage.classList.remove("hidden");
                }
              } catch (error) {
                cancelConfirmPopup.classList.add("hidden");
                popupText.textContent = "Error restricting slot";
                popupMessage.classList.remove("hidden");
              }
            };

            cancelConfirmNo.onclick = () => {
              cancelConfirmPopup.classList.add("hidden");
            };
          };
        }

        timeSlotsGrid.appendChild(timeButton);
      });

      if (timeSlotsGrid.children.length === 0) {
        timeSlotsGrid.innerHTML =
          '<div class="col-span-4 text-center text-gray-500">No slots available for this day</div>';
      }
    } catch (error) {
      timeSlotsGrid.innerHTML =
        '<div class="col-span-4 text-center text-red-500">Error loading slots</div>';
    }
  };
  const stripe = Stripe(stripePublicKey);

  const bookSlot = async () => {
    if (!selectedDate || selectedTimeSlots.size === 0) {
      popupText.textContent = "Please select at least one time slot";
      popupMessage.classList.remove("hidden");
      return;
    }

    const isLoggedIn =
      document.getElementById("user-logged-in").value === "true";
    if (!isLoggedIn) {
      loginPopup.classList.remove("hidden");
      return;
    }

    try {
      const formData = new FormData();
      formData.append("sport", sportSelect.value);
      formData.append("date", selectedDate);
      formData.append("slots", JSON.stringify(Array.from(selectedTimeSlots)));
      formData.append(
        "amount",
        selectedTimeSlots.size * PRICES[sportSelect.value]
      );

      const response = await fetch("payment.php", {
        method: "POST",
        body: formData,
      });

      const session = await response.json();

      if (session.error) {
        throw new Error(session.error);
      }

      const result = await stripe.redirectToCheckout({
        sessionId: session.id,
      });

      if (result.error) {
        throw new Error(result.error.message);
      }
    } catch (error) {
      popupText.textContent = "Error processing payment. Please try again.";
      popupMessage.classList.remove("hidden");
    }
  };

  const showPaymentStatusMessage = () => {
    if (paymentStatus) {
      const messages = {
        'success': {
          text: 'Payment successful! Your slots have been booked.',
          icon: '<i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>'
        },
        'error': {
          text: 'Payment processing failed. Please try again.',
          icon: '<i class="fas fa-times-circle text-red-500 text-4xl mb-4"></i>'
        },
        'booking_failed': {
          text: 'Payment was successful but booking failed. A refund has been initiated.',
          icon: '<i class="fas fa-exclamation-circle text-yellow-500 text-4xl mb-4"></i>'
        }
      };

      const message = messages[paymentStatus];
      if (message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'text-center';
        messageDiv.innerHTML = `
          ${message.icon}
          <p class="text-lg font-semibold text-gray-800">${message.text}</p>
        `;
        
        popupText.innerHTML = '';
        popupText.appendChild(messageDiv);
        popupMessage.classList.remove('hidden');
        
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
      }
    }
  };

  showPaymentStatusMessage();

  popupClose.addEventListener("click", () => {
    popupMessage.classList.add("hidden");
  });

  loginConfirm.addEventListener("click", () => {
    window.location.href = "../src/login.php";
  });

  loginCancel.addEventListener("click", () => {
    loginPopup.classList.add("hidden");
  });

  sportSelect.addEventListener("change", () => {
    if (selectedDate) {
      selectedTimeSlots.clear();
      updateCart();
      populateTimeSlots();
    }
  });

  bookSlotButton.addEventListener("click", bookSlot);

  popupClose.addEventListener("click", () => {
    popupMessage.classList.add("hidden");
  });
  populateCalendar();
  updateCart();
});