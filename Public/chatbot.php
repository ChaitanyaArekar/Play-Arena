<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href='https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap'>
    <link rel="stylesheet" href="/Public/chatbot.css">
</head>

<body>
    <button id="chat-button" class="chat-button" onclick="toggleChat()" data-title="How can I help you?">
        🤖
    </button>

    <div class=" chat-container" id="chatContainer">
        <div class="chat-header">
            How can we help you?
            <button class="close-chat" onclick="toggleChat()">x</button>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="message bot-message">
                👋 Hi there! I'm your virtual assistant. How can I help you today?
            </div>
            <div class="suggestion-chips">
                <button class="suggestion-chip" onclick="sendSuggestion('How to book?')">How to book?</button>
                <button class="suggestion-chip" onclick="sendSuggestion('Payment methods')">Payment methods</button>
                <button class="suggestion-chip" onclick="sendSuggestion('Operating hours')">Operating hours</button>
            </div>
            <div class="typing-indicator" id="typingIndicator">
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
            </div>
        </div>
        <div class="chat-input">
            <input type="text" id="userInput" placeholder="Type your question here..." onkeypress="handleKeyPress(event)">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        const chatBtn = document.getElementById('chat-button');

        function updateChatButtonVisibility() {
            if (window.scrollY > 200) {
                chatBtn.style.opacity = '1';
                chatBtn.style.visibility = 'visible';
            } else {
                chatBtn.style.opacity = '0';
                chatBtn.style.visibility = 'hidden';
            }
        }

        window.addEventListener('scroll', updateChatButtonVisibility);

        chatBtn.style.opacity = '0';
        chatBtn.style.visibility = 'hidden';



        const faqData = {
            'greeting': {
                patterns: [
                    /^(?:hi|hey|hello|howdy|hola)(?:\s+there)?[!.?]*$/i,
                    /^good\s*(?:morning|afternoon|evening)[!.?]*$/i,
                    /^(?:help|assist)(?:\s+me)?[!.?]*$/i
                ],
                response: "👋 Hello! I'm here to help you with any questions about our turf booking services. What would you like to know?"
            },
            'booking': {
                patterns: [
                    /(?:how|can|want)\s+to\s+book/i,
                    /book(?:ing)?\s+(?:process|procedure|steps)/i,
                    /make\s+(?:a\s+)?(?:book|reservation)/i,
                    /available\s+(?:slot|time)/i
                ],
                response: "To book a turf:\n1. Click the 'Book Now' button on our homepage\n2. Select your preferred sports\n3. Select your preferred date and time slot\n4. Complete the payment\n\nYou'll receive a confirmation email with your booking details. 🎯"
            },
            'cancellation': {
                patterns: [
                    /cancel(?:lation)?\s+(?:process|policy|booking)/i,
                    /how\s+to\s+cancel/i,
                    /refund\s+policy/i,
                    /reschedule\s+booking/i
                ],
                response: "First of all, read the cancellation policy on the bookings page and to continue to cancel the booked slot, log in to your account and in profile tab go to 'My Bookings', where you will find all your booking , find the slot you wan to cancel and click on the cancel button to send a cancelation request to the Owner. 💫"
            },
            'timing': {
                patterns: [
                    /\b(what|your)\s*(are\s*)?(the\s*)?(timing|hours)\b/i,
                    /\bwhen\s*(do\s*you\s*)?(open|close)\b/i,
                    /\boperating\s*hours\b/i
                ],
                response: "⏰ Operating Hours: 8 AM - 11 PM, Daily."
            },
            'payment': {
                patterns: [
                    /\b(payment|pay)\s*(method|option)s?\b/i,
                    /\bhow\s*(to|do\s*i)\s*pay\b/i,
                    /\b(cost|price|rate|fee|charge)s?\b/i,
                    /\bhow\s*much\b/i
                ],
                response: "💳 We accept multiple payment methods:\n• Credit/Debit Cards\n• UPI (Google Pay, PhonePe, etc.)\n• Net Banking\n• Digital Wallets\n\nRates vary by sport field:\nCricket: ₹1300/hour\nFootball: ₹1500/hour\nTennis: ₹800/hour"
            },
            'facilities': {
                patterns: [
                    /\b(what|which)\s*(facilities|amenities)\b/i,
                    /\bdo\s*you\s*(have|provide)\b/i,
                    /\b(available|included)\s*(facilities|services|equipment)\b/i
                ],
                response: "🏟️ Our facilities include:\n• Best artificial turf\n• Changing rooms with lockers\n• Clean washrooms\n• Drinking water stations\n• First aid kit\n• Basic sports equipment\n• Floodlights for night games\n• Parking space"
            },
            'rules': {
                patterns: [
                    /\b(what|any)\s*(are\s*the\s*)?(rules|regulations)\b/i,
                    /\bwhat('s|\s*is)\s*(allowed|permitted)\b/i,
                    /\b(dress|clothing)\s*code\b/i
                ],
                response: "📋 Important rules:\n• Wear proper sports shoes\n• No food/drinks on the turf\n• Arrive 15 mins before booking\n• Maximum 14 players per turf\n• Follow fair play guidelines"
            },
            'location': {
                patterns: [
                    /\bwhere\s*(are|is|you)\b/i,
                    /\b(location|address|direction)s?\b/i,
                    /\bhow\s*to\s*(reach|get\s*there)\b/i
                ],
                response: "📍 We're located at: 123 Turf Lane, Vadodara"
            },
            'contact': {
                patterns: [
                    /\b(contact|support)\s*(number|detail)s?\b/i,
                    /\b(phone|email|whatsapp)\s*(number)?\b/i,
                    /\bhow\s*(to\s*)?contact\b/i
                ],
                response: "📞 Contact us:\nPhone: +91-111111111\nEmail: support@turfbooking.com\nWhatsApp: +91-2222222222\n\nSupport hours: 9 AM - 8 PM daily"
            }
        };

        let isTyping = false;

        function toggleChat() {
            const chatContainer = document.getElementById('chatContainer');
            chatContainer.style.display = chatContainer.style.display === 'none' ? 'flex' : 'none';
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }

        function showTypingIndicator() {
            const indicator = document.getElementById('typingIndicator');
            indicator.style.display = 'block';
            isTyping = true;
        }

        function hideTypingIndicator() {
            const indicator = document.getElementById('typingIndicator');
            indicator.style.display = 'none';
            isTyping = false;
        }

        function sendSuggestion(text) {
            const input = document.getElementById('userInput');
            input.value = text;
            sendMessage();
        }

        function sendMessage() {
            const input = document.getElementById('userInput');
            const message = input.value.trim();

            if (message === '') return;
            if (isTyping) return;

            addMessage(message, 'user');

            showTypingIndicator();

            const response = getBotResponse(message);
            setTimeout(() => {
                hideTypingIndicator();
                addMessage(response, 'bot');
            }, 1000);

            input.value = '';
        }

        function addMessage(message, sender) {
            const chatMessages = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', `${sender}-message`);
            messageDiv.innerHTML = message.replace(/\n/g, '<br>');
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function getBotResponse(message) {
            const cleanMessage = message.trim().toLowerCase();

            const matches = new Map();

            for (const [category, data] of Object.entries(faqData)) {
                let confidence = 0;

                data.patterns.forEach(pattern => {
                    if (pattern.test(cleanMessage)) {
                        confidence += 2;
                    }
                });

                const words = cleanMessage.split(/\s+/);
                data.patterns.forEach(pattern => {
                    const patternStr = pattern.toString().slice(2, -2).toLowerCase();
                    words.forEach(word => {
                        if (word.length > 3 && patternStr.includes(word)) {
                            confidence += 0.5;
                        }
                    });
                });

                if (confidence > 0) {
                    matches.set(category, confidence);
                }
            }

            if (matches.size > 0) {
                const bestMatch = [...matches.entries()].reduce((a, b) => b[1] > a[1] ? b : a);
                if (bestMatch[1] >= 1) {
                    return faqData[bestMatch[0]].response;
                }
            }

            return "I'm not sure about that. You can ask me about bookings, timings, facilities, rules, payments, or contact information. How can I help you with these topics? 🤔";
        }

        document.getElementById('chatContainer').style.display = 'none';
    </script>
</body>

</html>