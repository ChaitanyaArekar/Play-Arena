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
  const cancelConfirmDetails = document.getElementById(
    "cancel-confirm-details"
  );
  const cancelConfirmYes = document.getElementById("cancel-confirm-yes");
  const cancelConfirmNo = document.getElementById("cancel-confirm-no");
  const urlParams = new URLSearchParams(window.location.search);
  const paymentStatus = urlParams.get("payment_status");

  // Add loading state variables
  let isBookingInProgress = false;
  let isCancellingInProgress = false;

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

  // Loading animation template
  const getLoadingAnimation = (text) => `
    <div class="flex items-center justify-center gap-3">
      <div class="flex space-x-1">
        <div class="h-2 w-2 bg-white rounded-full animate-[bounce_0.7s_infinite]"></div>
        <div class="h-2 w-2 bg-white rounded-full animate-[bounce_0.7s_infinite_0.2s]"></div>
        <div class="h-2 w-2 bg-white rounded-full animate-[bounce_0.7s_infinite_0.4s]"></div>
      </div>
      <span>${text}</span>
    </div>
  `;

  // Image slider
  setInterval(() => {
    currentIndex = (currentIndex + 1) % images.length;
    imgElement.src = images[currentIndex];
  }, 3000);

  // Cart updates
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

  // Calendar functionality
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

  // Time slots functionality with loading animation
  const populateTimeSlots = async () => {
    if (!selectedDate) return;

    timeSlotsGrid.innerHTML = `
      <div class="col-span-4 flex flex-col items-center justify-center space-y-4 py-8">
        <div class="flex space-x-2">
          <div class="w-3 h-3 bg-blue-500 rounded-full animate-[bounce_0.7s_infinite]"></div>
          <div class="w-3 h-3 bg-blue-500 rounded-full animate-[bounce_0.7s_infinite_0.2s]"></div>
          <div class="w-3 h-3 bg-blue-500 rounded-full animate-[bounce_0.7s_infinite_0.4s]"></div>
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
            ? "bg-red-50 text-red-600 cursor-not-allowed" +
              (isOwner ? "cursor-pointer hover:bg-red-100" : "")
            : isRestricted
            ? "bg-gray-50 text-gray-600 cursor-not-allowed"
            : "bg-green-50 text-green-600 hover:bg-green-100"
        }`;

        if (isOwner && !isBooked) {
          timeButton.dataset.hour = slot.hour;
          timeButton.dataset.status = isRestricted ? "restricted" : "available";
          timeButton.style.cursor = "pointer";

          timeButton.onclick = () => {
            const hour = parseInt(timeButton.dataset.hour);
            if (selectedTimeSlots.has(hour)) {
              selectedTimeSlots.delete(hour);
              timeButton.classList.remove(
                "ring-2",
                "ring-green-500",
                "bg-green-200"
              );
              timeButton.classList.add(
                isRestricted ? "bg-gray-50" : "bg-green-50"
              );
            } else {
              selectedTimeSlots.add(hour);
              timeButton.classList.add(
                "ring-2",
                "ring-green-500",
                "bg-green-200"
              );
              timeButton.classList.remove(
                isRestricted ? "bg-gray-50" : "bg-green-50"
              );
            }
          };
        }

        const timeDisplay = document.createElement("div");
        timeDisplay.textContent = `${hour}:00 ${period}`;
        timeButton.appendChild(timeDisplay);

        if (isBooked && isOwner && slot.booking_info) {
          timeButton.style.cursor = "pointer";
          const bookingInfo = document.createElement("div");
          bookingInfo.className = "text-xs text-gray-600 mt-1";
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
              </div>`;

            cancelConfirmPopup.classList.remove("hidden");

            cancelConfirmYes.onclick = async () => {
              if (isCancellingInProgress) return;

              try {
                isCancellingInProgress = true;
                cancelConfirmYes.innerHTML = getLoadingAnimation("Cancelling");
                cancelConfirmYes.disabled = true;
                cancelConfirmNo.disabled = true;

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
              } finally {
                isCancellingInProgress = false;
                cancelConfirmYes.innerHTML = "Yes";
                cancelConfirmYes.disabled = false;
                cancelConfirmNo.disabled = false;
              }
            };

            cancelConfirmNo.onclick = () => {
              cancelConfirmPopup.classList.add("hidden");
            };
          };
        }

        if (!isBooked && !isRestricted && !isOwner) {
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

  const handleMultipleSlotAction = async (action) => {
    if (isCancellingInProgress) return;

    if (selectedTimeSlots.size === 0) {
      popupText.textContent = "Please select at least one slot";
      popupMessage.classList.remove("hidden");
      return;
    }

    const slots = Array.from(selectedTimeSlots).map((hour) => ({
      hour: hour,
      status: document.querySelector(`[data-hour="${hour}"]`).dataset.status,
    }));

    const validSlots = slots.filter(
      (slot) =>
        (action === "restrict" && slot.status === "available") ||
        (action === "unrestrict" && slot.status === "restricted")
    );

    if (validSlots.length === 0) {
      popupText.textContent = `No slots available to ${action}`;
      popupMessage.classList.remove("hidden");
      return;
    }

    const formattedDate = new Date(selectedDate).toLocaleDateString("en-US", {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
    });

    cancelConfirmDetails.innerHTML = `
      <div class="space-y-3">
        <div class="text-lg font-semibold ${
          action === "restrict" ? "text-red-600" : "text-green-600"
        } mb-4">
          Confirm ${
            action === "restrict" ? "Restriction" : "Unrestriction"
          } of Multiple Slots
        </div>
        <div class="space-y-2 text-gray-700">
          <p><span class="font-medium">Sport:</span> ${sportSelect.value}</p>
          <p><span class="font-medium">Date:</span> ${formattedDate}</p>
          <p><span class="font-medium">Selected Slots:</span> ${
            validSlots.length
          }</p>
        </div>
        <p class="text-md text-gray-600 mt-2">
          Are you sure you want to ${action} the selected slots?
        </p>
      </div>
    `;

    cancelConfirmPopup.classList.remove("hidden");

    cancelConfirmYes.onclick = async () => {
      if (isCancellingInProgress) return;

      try {
        isCancellingInProgress = true;
        cancelConfirmYes.innerHTML = getLoadingAnimation("Processing");
        cancelConfirmYes.disabled = true;
        cancelConfirmNo.disabled = true;

        const response = await fetch(backendUrl, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            sport: sportSelect.value,
            date: selectedDate,
            hours: validSlots.map((s) => s.hour),
            action: action,
          }),
        });

        const result = await response.json();

        if (result.success) {
          // Get current URL and sport
          const currentUrl = new URL(window.location.href);
          const currentSport = sportSelect.value;

          // Update the sport parameter and reload
          currentUrl.searchParams.set("sport", currentSport);
          window.location.href = currentUrl.toString();
        } else {
          cancelConfirmPopup.classList.add("hidden");
          popupText.textContent = result.message || `Failed to ${action} slots`;
          popupMessage.classList.remove("hidden");
        }
      } catch (error) {
        cancelConfirmPopup.classList.add("hidden");
        popupText.textContent = `Error ${action}ing slots`;
        popupMessage.classList.remove("hidden");
      } finally {
        isCancellingInProgress = false;
        cancelConfirmYes.innerHTML = "Yes";
        cancelConfirmYes.disabled = false;
        cancelConfirmNo.disabled = false;
      }
    };

    cancelConfirmNo.onclick = () => {
      cancelConfirmPopup.classList.add("hidden");
    };
  };

  // Similarly update the cancellation handler for single slots
  const handleSingleSlotCancellation = async (slot) => {
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

      if (result.success) {
        // Get current URL and sport
        const currentUrl = new URL(window.location.href);
        const currentSport = sportSelect.value;

        // Update the sport parameter and reload
        currentUrl.searchParams.set("sport", currentSport);
        window.location.href = currentUrl.toString();
      } else {
        popupText.textContent = result.message || "Failed to cancel booking";
        popupMessage.classList.remove("hidden");
      }
    } catch (error) {
      popupText.textContent = "Error cancelling booking";
      popupMessage.classList.remove("hidden");
    }
  };

  const stripe = Stripe(stripePublicKey);

  // Slot booking with loading state
  const bookSlot = async () => {
    if (isBookingInProgress) return;

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
      isBookingInProgress = true;
      // Update button UI to show loading state
      bookSlotButton.innerHTML = getLoadingAnimation("Processing");
      bookSlotButton.disabled = true;

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
    } finally {
      isBookingInProgress = false;
      // Restore button UI
      bookSlotButton.innerHTML = `
        <i class="fas fa-check-circle"></i>
        Book Now
      `;
      bookSlotButton.disabled = false;
    }
  };

  const showPaymentStatusMessage = () => {
    if (paymentStatus) {
      const messages = {
        success: {
          text: "Payment successful! Your slots have been booked.",
          icon: '<i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>',
        },
        error: {
          text: "Payment processing failed. Please try again.",
          icon: '<i class="fas fa-times-circle text-red-500 text-4xl mb-4"></i>',
        },
        booking_failed: {
          text: "Payment was successful but booking failed. A refund has been initiated.",
          icon: '<i class="fas fa-exclamation-circle text-yellow-500 text-4xl mb-4"></i>',
        },
      };

      const message = messages[paymentStatus];
      if (message) {
        const messageDiv = document.createElement("div");
        messageDiv.className = "text-center";
        messageDiv.innerHTML = `
          ${message.icon}
          <p class="text-lg font-semibold text-gray-800">${message.text}</p>
        `;

        popupText.innerHTML = "";
        popupText.appendChild(messageDiv);
        popupMessage.classList.remove("hidden");

        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
      }
    }
  };

  // Initialize and set up event listeners
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
    const newUrl = new URL(window.location.href);
    newUrl.searchParams.set("sport", sportSelect.value);
    window.history.pushState({}, "", newUrl);

    if (selectedDate) {
      selectedTimeSlots.clear();
      updateCart();
      populateTimeSlots();
    }
  });

  bookSlotButton.addEventListener("click", bookSlot);

  if (isOwner) {
    document
      .getElementById("restrict-selected")
      .addEventListener("click", () => handleMultipleSlotAction("restrict"));
    document
      .getElementById("unrestrict-selected")
      .addEventListener("click", () => handleMultipleSlotAction("unrestrict"));
  }

  populateCalendar();
  updateCart();
});