<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer - PlayArena</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Font Awesome CDN -->
    <style>
        .footer-link {
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .footer-link:hover {
            transform: translateY(-5px);
            color: #ffffff;
        }

        .footer-link:active {
            transform: translateY(0);
            color: #bbb;
        }

        .icon {
            width: 24px;
            height: 24px;
            cursor: pointer;
            transition: transform 0.3s ease, filter 0.3s ease;
        }

        .icon:hover {
            transform: scale(1.2) rotate(360deg);
            filter: brightness(1.2);
        }

        .icon:active {
            transform: scale(0.9);
            filter: brightness(0.8);
        }

        .footer-section {
            opacity: 0;
            transition: opacity 1s ease;
        }

        .footer-section.show {
            opacity: 1;
        }

        .footer-title {
            animation: slideIn 1s ease-out;
        }

        @keyframes slideIn {
            0% {
                transform: translateX(-30px);
                opacity: 0;
            }

            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .footer-text {
            animation: fadeInText 1.5s ease-out;
        }

        @keyframes fadeInText {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
</head>

<body class="bg-gray-100">

    <section class="text-white bg-gradient-to-r" style="background: linear-gradient(to right, #000000, #434343);">
        <div class="flex flex-col md:flex-row w-full mb-6 footer-section">
            <div class="flex-1 flex flex-col justify-start mr-8 m-6 p-10 text-center sm:text-left">
                <span class="text-4xl font-bold footer-title">PlayArena</span>
                <p class="text-gray-400 mt-4 max-w-[310px] footer-text">
                    Find your perfect turf with ease,
                    Play, compete, and create memories that please!
                </p>
            </div>
            <div class="flex flex-wrap justify-center md:justify-between mt-6 md:mt-0 text-center sm:text-left">
                <?php
                $footerLinks = [
                    [
                        'title' => 'Company',
                        'links' => [
                            ['name' => 'About Us', 'url' => '../src/about-us.php'],
                            ['name' => 'Contact', 'url' => '../index.php#Contactus']
                        ]
                    ],
                    [
                        'title' => 'Community',
                        'links' => [
                            ['name' => 'Terms & Conditions', 'url' => '#'],
                            ['name' => 'Privacy Policy', 'url' => '#']
                        ]
                    ],
                    [
                        'title' => 'Help',
                        'links' => [
                            ['name' => 'FAQ', 'url' => '../index.php#faq'],
                            ['name' => 'Cancellation Policy', 'url' => '../src/cancellation_policy.php']
                        ]
                    ]
                ];

                foreach ($footerLinks as $link) {
                    echo '<div class="flex flex-col ss:my-0 my-2 min-w-[150px] m-6 pt-10 sm:my-2">';
                    echo '<h4 class="font-poppins font-medium text-[18px] leading-[27px] text-white">' . $link['title'] . '</h4>';
                    echo '<ul class="list-none mt-4">';
                    foreach ($link['links'] as $item) {
                        echo '<li class="font-poppins font-normal text-[16px] leading-[24px] text-gray-400 hover:text-white cursor-pointer footer-link mb-2">';
                        echo '<a href="' . $item['url'] . '">' . $item['name'] . '</a>';
                        echo '</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <div class="w-full flex justify-between items-center md:flex-row flex-col pt-4 border-t-[1px] p-4 sm:p-8 border-t-[#9d9ca3] footer-text text-center sm:text-left">
            <p class="font-poppins font-normal text-center text-[18px] sm:text-[18px] leading-[27px] text-white mb-0 sm:mb-5">
                &#169; 2024 PlayArena. All Rights Reserved.
            </p>
            <div class="flex justify-center space-x-6 mt-4 sm:mt-0 sm:mr-16 mr-0 text-xl mb-5">
                <a href="https://linkedin.com" class="icon text-white" target="_blank"><i class="fab fa-linkedin"></i></a>
                <a href="https://twitter.com" class="icon text-white" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="https://www.instagram.com/my_shitstagram/" class="icon text-white" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="https://youtube.com" class="icon text-white" target="_blank"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </section>

    <script>
        window.addEventListener('scroll', () => {
            const footer = document.querySelector('.footer-section');
            const windowHeight = window.innerHeight;
            const footerPosition = footer.getBoundingClientRect().top;
            if (footerPosition < windowHeight) {
                footer.classList.add('show');
            }
        });
    </script>

</body>

</html>