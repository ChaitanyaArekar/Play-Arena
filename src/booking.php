<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.10.0/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.10.0/ScrollTrigger.min.js"></script>
    <style>
        .card:hover .card-overlay {
            background: linear-gradient(to top, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.2));
        }

        .card:hover img {
            transform: scale(1.1);
            filter: brightness(0.9);
        }

        .card-overlay {
            background: linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.3));
        }
    </style>
</head>

<body class="bg-gray-100">
    <section id="booking">
        <h2 class="text-4xl font-bold text-center m-2 my-14" id="h2">Start Your Adventure</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 px-5 sm:px-16 py-6">
            <a href="/src/book.php?sport=football" class="block card">
                <div class="relative overflow-hidden rounded-lg shadow-md">
                    <img src="public/img/Football.jpg" alt="Box Football" class="w-full h-64 object-cover transition-transform duration-300">
                    <div class="absolute inset-0 card-overlay transition-opacity duration-300 flex flex-col justify-end p-4">
                        <p class="text-white text-lg font-bold">Football</p>
                        <p class="text-gray-300 text-sm">United Sports Arena</p>
                        <p class="text-gray-300 text-sm">Playground in Vadodara</p>
                    </div>
                </div>
            </a>


            <a href="/src/book.php?sport=cricket" class="block card">
                <div class="relative overflow-hidden rounded-lg shadow-md">
                    <img src="public/img/cricket.jpg" alt="Box Cricket" class="w-full h-64 object-cover transition-transform duration-300">
                    <div class="absolute inset-0 card-overlay transition-opacity duration-300 flex flex-col justify-end p-4">
                        <p class="text-white text-lg font-bold">Cricket</p>
                        <p class="text-white text-sm">United Sports Arena</p>
                        <p class="text-white text-sm">Playground in Vadodara</p>
                    </div>
                </div>
            </a>

            <a href="/src/book.php?sport=tennis" class="block card">
                <div class="relative overflow-hidden rounded-lg shadow-md">
                    <img src="public/img/tennis.jpg" alt="Tennis Turf" class="w-full h-64 object-cover transition-transform duration-300">
                    <div class="absolute inset-0 card-overlay transition-opacity duration-300 flex flex-col justify-end p-4">
                        <p class="text-white text-lg font-bold">Tennis Turf</p>
                        <p class="text-white text-sm">United Sports Arena</p>
                        <p class="text-white text-sm">Playground in Vadodara</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="flex justify-center items-center my-10 sm:my-20 p-6 sm:p-6 h-auto sm:h-96 sm:flex-row flex-col bg-black shadow-lg relative" id="heroSection">
            <div class="flex-1 flex flex-col ml-6 sm:ml-10" id="textSection">
                <h2 class="text-4xl sm:text-5xl font-bold pb-2 bg-gradient-to-r from-[#1d976c] to-[#93f9b9] bg-clip-text text-transparent">
                    Where Friends Reunite, Memories <br> Are Made, and Laughter Never Ends
                </h2>
                <p class="text-white text-lg mt-4 sm:mt-5 max-w-lg text-wrap" id="textParagraph">
                    Reconnect, laugh, and create unforgettable moments with the people who matter most—because every meeting with friends is a celebration of life.
                </p>
            </div>

            <div class="booking flex flex-row flex-wrap sm:mt-10 mt-4 items-center justify-center sm:w-auto w-full">
                <img src="public/img/booking.png" alt="Children playing" class="w-[250px] sm:w-[500px] h-auto object-contain mr-5 m-2" id="heroImage">
            </div>
        </div>
    </section>

    <script>
        gsap.registerPlugin(ScrollTrigger);

        window.addEventListener('DOMContentLoaded', () => {
            // Hero section text animations
            gsap.fromTo(
                '#textSection', {
                    opacity: 0,
                    x: -100,
                }, {
                    opacity: 1,
                    x: 0,
                    duration: 1.5,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: '#heroSection',
                        start: 'top 100%',
                        scrub: true,
                        toggleActions: 'play none none none',
                    },
                }
            );

            gsap.fromTo(
                '#h2', {
                    opacity: 0,
                    y: 50,
                }, {
                    opacity: 1,
                    y: 0,
                    duration: 1,
                    ease: 'power3.out',
                    stagger: 0.2,
                    scrollTrigger: {
                        trigger: '.grid',
                        start: 'top 100%',
                        scrub: true,
                        toggleActions: 'play none none none',
                    },
                }
            );
            gsap.fromTo(
                '#textParagraph', {
                    opacity: 0,
                    x: -50,
                }, {
                    opacity: 1,
                    x: 0,
                    duration: 1.5,
                    ease: 'power3.out',
                    delay: 0.5,
                    scrollTrigger: {
                        trigger: '#heroSection',
                        start: 'top 100%',
                        scrub: true,
                        toggleActions: 'play none none none',
                    },
                }
            );

            gsap.fromTo(
                '#heroImage', {
                    opacity: 0,
                    x: 100,
                    scale: 0.8,
                }, {
                    opacity: 1,
                    x: 0,
                    scale: 1,
                    duration: 1,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: '#heroSection',
                        start: 'top 100%',
                        scrub: true,
                        toggleActions: 'play none none none',
                    },
                }
            );

            gsap.fromTo(
                '.card', {
                    opacity: 0,
                    y: 50,
                }, {
                    opacity: 1,
                    y: 0,
                    duration: 1,
                    ease: 'power3.out',
                    stagger: 0.2,
                    scrollTrigger: {
                        trigger: '.grid',
                        start: 'top 80%',
                        scrub: true,
                        toggleActions: 'play none none none',
                    },
                }
            );
            gsap.to('#heroSection', {
                backgroundPosition: '50% 100%',
                ease: 'none',
                scrollTrigger: {
                    trigger: '#heroSection',
                    start: 'top bottom',
                    end: 'bottom top',
                    scrub: true,
                },
            });
        });
    </script>

</body>

</html>