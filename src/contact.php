<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script>
        emailjs.init("IpqCjFQL0NXP14LQ7");
    </script>
    <style>
        body {
            overflow-x: hidden;
        }
        #message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px;
            border-radius: 8px;
            z-index: 9999;
            max-width: 90%;
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100">
<section id="Contactus" class="relative">
    <div class="max-w-6xl mx-auto py-12 mt-4 mb-4 sm:mb-8 md:mb-16 px-5 flex lg:items-center flex-col lg:flex-row lg:gap-16 gap-8">
        <div class="max-w-[300px]">
            <h2 class="text-4xl font-bold opacity-0" id="contact-title">Contact Us</h2>
            <p class="py-5 text-lg opacity-0" id="contact-desc">We’d love your feedback, ideas, or anything else you’d like to share! Let us know!</p>
        </div>
        <form id="contactForm" class="w-full lg:w-[600px] py-10 grid grid-cols-1 md:grid-cols-2 gap-5 pb-4 sm:pb-8 md:pb-12">
            <div id="message" class="hidden"></div>

            <input type="text" id="name" name="name" placeholder="Your name" class="bg-black text-white p-4 rounded-md transform scale-0" required>
            <input type="email" id="email" name="email" placeholder="Your email" class="bg-black text-white p-4 rounded-md transform scale-0" required onblur="validateEmail()">
            <textarea id="messageText" name="message" placeholder="Message" class="bg-black text-white p-4 rounded-md h-40 sm:col-span-2 transform scale-0" required></textarea>

            <button type="button" onclick="sendEmail()" class="bg-black hover:bg-gray-800 text-white h-[45px] flex items-center justify-center gap-2 transition-all rounded-md transform scale-0">
                <span>Send</span>
            </button>
        </form>
    </div>
</section>



    <script>
        gsap.registerPlugin(ScrollTrigger);

        gsap.fromTo(
            "#contact-title", 
            { opacity: 0, x: -50 }, 
            { opacity: 1, x: 0, duration: 1, ease: "power3.out", delay: 0.2 }
        );

        gsap.fromTo(
            "#contact-desc", 
            { opacity: 0, x: -50 }, 
            { opacity: 1, x: 0, duration: 1, ease: "power3.out", delay: 0.4 }
        );

        gsap.fromTo(
            "#contactForm input, #contactForm textarea, #contactForm button", 
            { scale: 0, opacity: 0 }, 
            {
                scale: 1,
                opacity: 1,
                duration: 0.8,
                ease: "elastic.out(1, 0.75)",
                stagger: 0.1,
                scrollTrigger: {
                    trigger: "#contactForm",
                    start: "top 80%",
                    toggleActions: "play none none none"
                }
            }
        );


        function validateEmail() {
            const email = document.getElementById('email').value;
            const messageDiv = document.getElementById('message');

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!email.match(emailPattern)) {
                messageDiv.textContent = "Please enter a valid email address.";
                messageDiv.className = "bg-red-600 text-white mb-4";
                messageDiv.style.display = "block";

                gsap.fromTo(
                    "#message", 
                    { opacity: 0, y: -30 }, 
                    { opacity: 1, y: 0, duration: 0.5, ease: "power3.out" }
                );

                setTimeout(() => {
                    gsap.to("#message", { opacity: 0, y: -30, duration: 0.5, ease: "power3.out", onComplete: () => messageDiv.style.display = "none" });
                }, 3000);
                return false;
            }

            return true;
        }

        function sendEmail() {
            const isEmailValid = validateEmail();
            if (!isEmailValid) return;

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const message = document.getElementById('messageText').value;
            const messageDiv = document.getElementById('message');

            if (!name || !message) {
                messageDiv.textContent = "Please fill all the fields 😒";
                messageDiv.className = "bg-red-600 text-white mb-4";
                messageDiv.style.display = "block";

                gsap.fromTo(
                    "#message", 
                    { opacity: 0, y: -30 }, 
                    { opacity: 1, y: 0, duration: 0.5, ease: "power3.out" }
                );

                setTimeout(() => {
                    gsap.to("#message", { opacity: 0, y: -30, duration: 0.5, ease: "power3.out", onComplete: () => messageDiv.style.display = "none" });
                }, 3000);
                return;
            }

            const templateParams = {
                name: name,
                email: email,
                message: message
            };

            emailjs.send("service_l052khs", "template_0mr0ckf", templateParams)
                .then(response => {
                    messageDiv.textContent = "Thank you so much! 😊";
                    messageDiv.className = "bg-green-600 text-white mb-4";
                    messageDiv.style.display = "block";

                    gsap.fromTo(
                        "#message", 
                        { opacity: 0, y: -30 }, 
                        { opacity: 1, y: 0, duration: 0.5, ease: "power3.out" }
                    );

                    setTimeout(() => {
                        gsap.to("#message", { opacity: 0, y: -30, duration: 0.5, ease: "power3.out", onComplete: () => messageDiv.style.display = "none" });
                    }, 3000);

                    document.getElementById('contactForm').reset();
                })
                .catch(error => {
                    messageDiv.textContent = "An error occurred. Please try again later.";
                    messageDiv.className = "bg-red-600 text-white mb-4";
                    messageDiv.style.display = "block";

                    gsap.fromTo(
                        "#message", 
                        { opacity: 0, y: -30 }, 
                        { opacity: 1, y: 0, duration: 0.5, ease: "power3.out" }
                    );

                    setTimeout(() => {
                        gsap.to("#message", { opacity: 0, y: -30, duration: 0.5, ease: "power3.out", onComplete: () => messageDiv.style.display = "none" });
                    }, 3000);
                });
        }
    </script>
</body>
</html>
