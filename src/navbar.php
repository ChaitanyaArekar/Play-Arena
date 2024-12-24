<?php
$navLinks = [
    ["id" => "about", "title" => "About", "url" => "/src/about-us.php"],
    ["id" => "contact", "title" => "Contact", "url" => "#Contactus"],
    ["id" => "book", "title" => "Book Now", "url" => "#booking"],
    ["id" => "login", "title" => "Login", "url" => "contact.php"]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlayArena Navbar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .rotate-90 { transform: rotate(90deg); }
        .transition-transform { transition: transform 0.4s ease-in-out; }
        .icon { transition: transform 0.4s ease-in-out; }
        .icon:hover { transform: scale(1.2); }
        #sidebar {
            transition: transform 0.3s ease-in-out;
        }
        .sidebar.show {
            transform: translateX(0);
        }
        .sidebar.hidden {
            transform: translateX(100%);
        }
        html {
        scroll-behavior: smooth;
    }
    </style>
</head>
<body>
    <nav class="w-full flex py-3 sm:py-6 justify-between items-center navbar">
        <span 
            class="text-2xl sm:text-3xl ml-2 sm:ml-10 font-bold <?php echo isset($textColor) ? $textColor : 'text-white'; ?> cursor-pointer"
            onclick="window.location.href='/'"
        >
            PlayArena
        </span>
        <ul class="list-none sm:flex hidden justify-end items-center flex-1">
            <?php foreach ($navLinks as $index => $nav): ?>
                <li 
    class="font-poppins font-normal cursor-pointer text-[16px] mr-10 <?php echo isset($textColor) ? $textColor : 'text-white'; ?> hover:text-gray-500 transition-transform"
>

                    <a href="<?= $nav['url'] ?>"><?= $nav['title'] ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="sm:hidden flex flex-1 justify-end items-center relative">
            <button 
                id="menu-toggle" 
                class="<?php echo isset($textColor) ? $textColor : 'text-white'; ?>  focus:outline-none"
            >
                <svg id="menu-icon" class="h-6 w-6 icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path id="menu-path" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg id="close-icon" class="h-6 w-6 icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div 
                id="sidebar" 
                class="p-6 bg-black absolute top-10 right-0 mx-4 my-2 min-w-[140px] rounded-xl shadow-lg border border-gray-300 sidebar hidden"
            >
                <ul class="list-none flex flex-col justify-end items-center flex-1">
                    <?php foreach ($navLinks as $index => $nav): ?>
                        <li 
                            class="font-poppins font-normal cursor-pointer text-[16px] <?= $index === count($navLinks) - 1 ? 'mr-10' : 'mb-4' ?> <?php echo isset($textColor) ? $textColor : 'text-white'; ?> hover:text-gray-500 transition-transform"
                        >
                            <a href="<?= $nav['url'] ?>"><?= $nav['title'] ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>

    <script>
        const toggleButton = document.getElementById('menu-toggle');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');
        const sidebar = document.getElementById('sidebar');
        
        toggleButton.addEventListener('click', () => {
            const isVisible = sidebar.classList.contains('hidden');
            sidebar.classList.toggle('hidden', !isVisible);
            sidebar.classList.toggle('show', isVisible);
            
            menuIcon.classList.toggle('hidden', !isVisible);
            closeIcon.classList.toggle('hidden', isVisible);
        });
    </script>
</body>
</html>
