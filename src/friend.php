<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.10.0/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.10.0/ScrollTrigger.min.js"></script>
</head>
<body class="bg-gray-100">

<section class="flex justify-center items-center m-10 my-20 py-10 px-6 h-72 sm:flex-row flex-col bg-[linear-gradient(to_right,_#000000,_#434343)] rounded-2xl shadow-lg relative" id="bookSection">
    <div class="flex-1 flex-col ml-10" id="textSection">
        <h2 class="text-3xl sm:text-5xl font-bold bg-gradient-to-r from-[#1d976c] to-[#93f9b9] bg-clip-text text-transparent">
            Book Your Turf, Own the Game!
        </h2>
        <p class="text-white mt-5 max-w-lg" id="textParagraph">
            Step onto the field with confidence — book your turf effortlessly, play at your convenience, and create unforgettable moments with every match!
        </p>
    </div>

    <div class="sm:mt-10 items-center">
        <img src="public/img/friend.png" alt="friend" class="absolute w-[250px] sm:w-[450px] h-[200px] sm:h-[371px] -top-32 right-[32px]" id="friendImage">
    </div>
</section>

<script>

    gsap.registerPlugin(ScrollTrigger);

    gsap.fromTo('#textSection', 
        { opacity: 0, x: -100 },
        { 
            opacity: 1, 
            x: 0, 
            duration: 1.5,
            ease: 'power3.out', 
            scrollTrigger: {
                trigger: '#bookSection',
                start: 'top 80%',
                toggleActions: 'play none none none'
            }
        });
    gsap.fromTo('#textParagraph', 
        { opacity: 0, x: -50 }, 
        { 
            opacity: 1, 
            x: 0, 
            duration: 1.5, 
            ease: 'power3.out', 
            delay: 0.5,
            scrollTrigger: {
                trigger: '#bookSection',
                start: 'top 80%',
                toggleActions: 'play none none none'
            }
        });

    gsap.fromTo('#friendImage', 
        { opacity: 0, x: 100, scale: 0.8 },
        { 
            opacity: 1, 
            x: 0, 
            scale: 1, 
            duration: 1.5, 
            ease: 'power3.out', 
            scrollTrigger: {
                trigger: '#bookSection',
                start: 'top 80%',
                toggleActions: 'play none none none'
            }
        });
</script>

</body>
</html>