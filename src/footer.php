<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer - PlayArena</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Add animation for footer links */
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

        /* Hover effect for social media icons */
        .icon {
            width: 21px;
            height: 21px;
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

        /* Animation for the footer section */
        .footer-section {
            opacity: 0;
            transition: opacity 1s ease;
        }

        .footer-section.show {
            opacity: 1;
        }

        /* Smooth animation for the footer title */
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

        /* Animation for the footer text */
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
        <div class="flex-1 flex flex-col justify-start mr-8 m-6 p-10">
            <span class="text-4xl font-bold footer-title">PlayArena</span>
            <p class="text-dimblack mt-4 max-w-[310px] footer-text">
                Find your perfect turf with ease,  
                Play, compete, and create memories that please!
            </p>
        </div>
        <div class="flex-[1.5] w-full flex flex-row justify-between flex-wrap mt-6 md:mt-0">
            <?php
            $footerLinks = [
                [
                    'title' => 'Company',
                    'links' => [
                        ['name' => 'About Us', 'url' => 'www.ritesh.com'],
                        ['name' => 'Careers', 'url' => '/careers'],
                        ['name' => 'Contact', 'url' => '/contact']
                    ]
                ],
                [
                    'title' => 'Community',
                    'links' => [
                        ['name' => 'Terms & Conditions'],
                        ['name' => 'Privacy Policy'],
                        ['name' => 'Newsletters']
                    ]
                ],
                [
                    'title' => 'Help',
                    'links' => [
                        ['name' => 'FAQ'],
                        ['name' => 'Support'],
                        ['name' => 'Privacy Policy']
                    ]
                ]
            ];

            foreach ($footerLinks as $link) {
                echo '<div class="flex flex-col ss:my-0 my-4 min-w-[150px] m-6 pt-10">';
                echo '<h4 class="font-poppins font-medium text-[18px] leading-[27px] text-white">' . $link['title'] . '</h4>';
                echo '<ul class="list-none mt-4">';
                foreach ($link['links'] as $item) {
                    echo '<li class="font-poppins font-normal text-[16px] leading-[24px] text-dimblack hover:text-white cursor-pointer footer-link mb-4">';
                    echo $item['name'];
                    echo '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <div class="w-full flex justify-between items-center md:flex-row flex-col pt-4 border-t-[1px] p-14 border-t-[#9d9ca3] footer-text">
        <p class="font-poppins font-normal text-center text-[18px] leading-[27px] text-white">
            &#169; 2024 PlayArena. All Rights Reserved.
        </p>
    </div>
</section>

<script>

    window.addEventListener('scroll', () => {
        const footer = document.querySelector('.footer-section');
        const windowHeight = window.innerHeight;
        const footerPosition = footer.getBoundingClientRect().top;

        // If the footer is within the viewport, add the 'show' class
        if (footerPosition < windowHeight) {
            footer.classList.add('show');
        }
    });
</script>

</body>
</html>
