<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>Play Arena</h1>
        </div>
        <nav class="navbar">
            <div class="menu-icon" id="menu-icon">&#9776;</div> <!-- Hamburger icon -->
            <div class="close-icon" id="close-icon">&#10005;</div> <!-- Close icon -->
            <ul class="nav-list">
                <li><a href="about-us.html">About Us</a></li>
                <li><a href="#contact-us">Contact Us</a></li>
                <li><a href="#book-now">Book Now</a></li>
                <li><a href="#login">Login</a></li>
            </ul>
        </nav>
    </header>

    <section class="home-page">
        <img src="Public/img/Wallpaper.jpg" alt="Background" class="background-image">
        <div class="home-page-content">
            <h1>YOUR<br>NEAREST<br>SPORTS COMMUNITY</h1>
            <p><b>IS JUST A TAP AWAY</b></p>
            <a href="book-now.html">
                <button class="btn">To Book Turf</button>
            </a>
         </div>
    </section>

    <script>
        // JavaScript to toggle the menu visibility on mobile
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');
        const navList = document.querySelector('.nav-list');

        // When the menu icon is clicked, show the menu and hide the menu icon
        menuIcon.addEventListener('click', () => {
            navList.classList.add('active');
            menuIcon.style.display = 'none'; // Hide the menu icon
            closeIcon.style.display = 'block'; // Show the close icon
        });

        // When the close icon is clicked, hide the menu and show the menu icon again
        closeIcon.addEventListener('click', () => {
            navList.classList.remove('active');
            closeIcon.style.display = 'none'; // Hide the close icon
            menuIcon.style.display = 'block'; // Show the menu icon again
        });
    </script>
</body>
</html>