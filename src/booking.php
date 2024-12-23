<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adventure Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <h1 class="text-xl font-semibold m-10 ml-20 mt-16">Start Your Adventure →</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 px-16 py-12">
        <a href="box_football.php" class="block">
            <div class="relative">
                <img src="public/img/Football.jpg" alt="Box Football" class="w-full h-64 object-cover rounded-lg shadow-md">
                <div class="absolute bottom-4 left-4">
                    <p class="text-white text-lg font-bold shadow-lg">Box Football</p>
                    <p class="text-gray-300 text-sm shadow-lg">United Sports Arena</p>
                    <p class="text-gray-300 text-sm shadow-lg">Playground in Vadodara</p>
                </div>
            </div>
        </a>

        <a href="box_cricket.php" class="block">
            <div class="relative">
                <img src="public/img/cricket.jpg" alt="Box Cricket" class="w-full h-64 object-cover rounded-lg shadow-md">
                <div class="absolute bottom-4 left-4">
                    <p class="text-white text-lg font-bold shadow-lg">Box Cricket</p>
                    <p class="text-white text-sm shadow-lg">United Sports Arena</p>
                    <p class="text-white text-sm shadow-lg">Playground in Vadodara</p>
                </div>
            </div>
        </a>

        <a href="tennis_turf.php" class="block">
            <div class="relative">
                <img src="public/img/tennis.jpg" alt="Tennis Turf" class="w-full h-64 object-cover rounded-lg shadow-md">
                <div class="absolute bottom-4 left-4">
                    <p class="text-white text-lg font-bold shadow-lg">Tennis Turf</p>
                    <p class="text-white text-sm shadow-lg">United Sports Arena</p>
                    <p class="text-white text-sm shadow-lg">Playground in Vadodara</p>
                </div>
            </div>
        </a>
    </div>

    <div class="flex justify-center items-center my-20 p-10 h-96 sm:flex-row flex-col bg-black shadow-lg relative">
        <div class="flex-1 flex flex-col ml-10">
            <h2 class="text-5xl font-bold pb-2 bg-gradient-to-r from-[#1d976c] to-[#93f9b9] bg-clip-text text-transparent">
                Where Friends Reunite, Memories <br> Are Made, and Laughter Never Ends
            </h2>
            <p class="text-white text-lg mt-5 max-w-lg text-wrap">
                Reconnect, laugh, and create unforgettable moments with the people who matter most—because every meeting with friends is a celebration of life.
            </p>
        </div>

        <div class="relative sm:mt-10 items-center">
            <img src="public/img/booking.png" alt="Children playing" class="w-[600px] h-[516px] p-16">
        </div>
    </div>
</body>
</html>
