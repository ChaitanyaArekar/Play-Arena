document.addEventListener("DOMContentLoaded", () => {
  const backendUrl = "http://localhost:8000/db.php";
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
    const timeButtons = timeSlotsGrid.querySelectorAll("button");
    timeButtons.forEach((btn) => {
      btn.classList.remove("ring-2", "ring-green-500", "bg-green-200");
      btn.classList.add("bg-green-50");
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
      const today = now.toISOString().split('T')[0];

      data.slots.forEach((slot) => {
        if (selectedDate === today && slot.hour <= currentHour) {
          return;
        }

        const timeButton = document.createElement("div");
        const isBooked = slot.status === "booked";
        const hour = slot.hour % 12 || 12;
        const period = slot.hour >= 12 ? "PM" : "AM";

        timeButton.className = `relative p-3 rounded-lg text-center transition-all ${
          isBooked
            ? "bg-red-50 text-red-600"
            : "bg-green-50 text-green-600 hover:bg-green-100"
        }`;

        // Main time display
        const timeDisplay = document.createElement("div");
        timeDisplay.textContent = `${hour}:00 ${period}`;
        timeButton.appendChild(timeDisplay);

        // Add booking info for owner
        if (isBooked && isOwner && slot.booking_info) {
          const bookingInfo = document.createElement("div");
          bookingInfo.className = "text-xs text-gray-600 mt-1 ";
          bookingInfo.textContent = `Booked by: ${slot.booking_info.full_name}`;
          timeButton.appendChild(bookingInfo);
          
          // Cancel button for owner
          const cancelBtn = document.createElement("button");
          cancelBtn.className = "absolute top-1 right-1 bg-red-500 text-white text-xs px-1 py-1 rounded hover:bg-red-600 transition-all";
          cancelBtn.textContent = "Cancel";
          cancelBtn.onclick = async (e) => {
            e.stopPropagation();
            try {
              const response = await fetch(backendUrl, {
                method: "DELETE",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                  sport: sportSelect.value,
                  date: selectedDate,
                  hour: slot.hour
                })
              });
              
              const result = await response.json();
              if (result.success) {
                popupText.textContent = "Booking cancelled successfully!";
                await populateTimeSlots();
              } else {
                popupText.textContent = result.message || "Failed to cancel booking";
              }
              popupMessage.classList.remove("hidden");
            } catch (error) {
              popupText.textContent = "Error cancelling booking";
              popupMessage.classList.remove("hidden");
            }
          };
          timeButton.appendChild(cancelBtn);
        }

        if (!isBooked) {
          timeButton.dataset.hour = slot.hour;
          timeButton.style.cursor = "pointer";
          timeButton.onclick = () => {
            if (selectedTimeSlots.has(slot.hour)) {
              selectedTimeSlots.delete(slot.hour);
              timeButton.classList.remove("ring-2", "ring-green-500", "bg-green-200");
              timeButton.classList.add("bg-green-50");
            } else {
              selectedTimeSlots.add(slot.hour);
              timeButton.classList.add("ring-2", "ring-green-500", "bg-green-200");
              timeButton.classList.remove("bg-green-50");
            }
            updateCart();
          };
        }

        timeSlotsGrid.appendChild(timeButton);
      });

      if (timeSlotsGrid.children.length === 0) {
        timeSlotsGrid.innerHTML = '<div class="col-span-4 text-center text-gray-500">No slots available for this day</div>';
      }
    } catch (error) {
      timeSlotsGrid.innerHTML = '<div class="col-span-4 text-center text-red-500">Error loading slots</div>';
    }
  };

  const bookSlot = async () => {
    if (!selectedDate || selectedTimeSlots.size === 0) {
      popupText.textContent = "Please select at least one time slot";
      popupMessage.classList.remove("hidden");
      return;
    }

    const isLoggedIn = document.getElementById("user-logged-in").value === "true";
    if (!isLoggedIn) {
      loginPopup.classList.remove("hidden");
      return;
    }

    try {
      const bookingPromises = Array.from(selectedTimeSlots).map((hour) =>
        fetch(backendUrl, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            sport: sportSelect.value,
            date: selectedDate,
            hour: hour,
          }),
        })
      );

      const results = await Promise.all(bookingPromises);
      const allSuccessful = results.every((r) => r.ok);

      if (allSuccessful) {
        popupText.textContent = "Slots booked successfully!";
        selectedTimeSlots.clear();
        await populateTimeSlots();
        updateCart();
      } else {
        popupText.textContent = "Some slots could not be booked. Please try again.";
      }
    } catch (error) {
      popupText.textContent = "Error booking slots. Please try again.";
    }
    popupMessage.classList.remove("hidden");
  };

  // Event Listeners
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

  // Initialize calendar and cart
  populateCalendar();
  updateCart();
});