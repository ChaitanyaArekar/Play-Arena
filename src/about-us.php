<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Play Arena</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
</head>

<body class="bg-gradient-to-b from-white to-gray-50">
    <?php $textColor = 'text-black'; ?>
    <header class="<?php echo $textColor; ?>">
        <?php include 'navbar.php'; ?>
    </header>

    <section class="about-us pt-20">
        <div class="mx-10 px-6 py-16">
            <!-- About Card -->
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8 mt-20 pb-20">

                    <div class="absolute right-8 w-96 mt-40 pt-20 h-96 overflow-hidden transform">
                        <img src="../Public/img/pngegg.png" alt="Adventure Bridge" class="object-cover h-96 w-64">
                    </div>
                </div>

                <!-- Text Content Section -->
                <div class="w-full md:w-1/2 space-y-6 p-4">
                    <h1 class="text-4xl md:text-6xl font-bold text-gray-900 leading-tight">
                        Unleash the game on top-quality turf!
                    </h1>

                    <p class="text-xl text-gray-700 leading-relaxed">
                        Dive into the world of sports with seamless bookings, premium turfs, active communities, and more!
                    </p>

                    <button onclick="window.location.href='http://localhost:8000/index.php#booking'" class="bg-gray-900 text-white px-8 py-3 rounded-full text-lg font-semibold hover:bg-gray-800 transition-colors duration-300 shadow-lg hover:shadow-xl">
                        Book Now!
                    </button>


                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 my-28">
                <!-- Search Section -->
                <div class="text-center p-6 bg-white">
                    <div class="flex justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-black">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold mb-4">Search</h2>
                    <p class="text-gray-600">
                        Are you looking to play after work, organize your Sunday Five's football match? Explore the largest network of sports facilities whole over the India
                    </p>
                </div>

                <!-- Book Section -->
                <div class="text-center p-6 bg-white">
                    <div class="flex justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-black">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                            <line x1="16" x2="16" y1="2" y2="6" />
                            <line x1="8" x2="8" y1="2" y2="6" />
                            <line x1="3" x2="21" y1="10" y2="10" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold mb-4">Book</h2>
                    <p class="text-gray-600">
                        Once you've found the perfect ground, court or gym, Connect with the venue through the Book Now Button to make online booking & secure easier payment
                    </p>
                </div>

                <!-- Play Section -->
                <div class="text-center p-6 bg-white">
                    <div class="flex justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-black">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M4.5 4.5c5 5 10 5 15 0" />
                            <path d="M4.5 19.5c5-5 10-5 15 0" />
                            <path d="M12 2v20" />
                            <path d="M2 12h20" />
                        </svg>


                    </div>
                    <h2 class="text-xl font-semibold mb-4">Play</h2>
                    <p class="text-gray-600">
                        You're the hero, you've found a stunning turf or court, booked with ease and now its time to play. The scene is set for your epic match.
                    </p>
                </div>
            </div>


            <!-- Features Grid -->
            <div class="mx-8 p-2 grid md:grid-cols-3 gap-6 mt-12">
                <div class="bg-white rounded-2xl shadow-xl p-6" data-aos="fade-up">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Why Choose Us?</h3>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">User-Friendly Interface</h4>
                                <p class="text-gray-600">Simple, fast, and intuitive booking system</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">Secure Payments</h4>
                                <p class="text-gray-600">Multiple payment options with top-tier security</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl p-6" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">What We Offer</h3>
                    <ul class="space-y-4 text-gray-600">
                        <li class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>24/7 online booking availability</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Premium sports facilities</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Group & corporate bookings</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-2xl shadow-xl p-6" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Our Mission</h3>
                    <ul class="space-y-4 text-gray-600">
                        <li class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Make sports accessible to everyone.</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Provide easy-to-use and reliable turf booking services.</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Promote an active lifestyle and community spirit.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        </div>

        <!-- Team Section -->
        <div class="text-center mt-8" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-8 mt-20">Meet Our Team</h2>
            <div class="relative overflow-hidden shadow-xl">
                <img src="../Public/img/Ourteam.png" alt="Our Team" class="w-full">
            </div>
        </div>

    </section>

    <footer class="bg-gray-900 text-white">
        <?php include 'footer.php'; ?>
    </footer>

    <script>
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });

        const counters = document.querySelectorAll('.counter');
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 2000;
            const increment = target / (duration / 16);

            let current = 0;
            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.textContent = Math.ceil(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            updateCounter();
        });
    </script>
</body>

</html>