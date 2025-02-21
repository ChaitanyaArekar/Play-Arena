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
    <title>Home Page</title>
    <link rel="stylesheet" href="/src/home.css">
</head>

<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>

    <section class="home-page">
        <img src="Public/img/Wallpaper.jpg" alt="Background" class="background-image">
        <div class="home-page-content">
            <h1>YOUR<br>NEAREST<br>SPORTS COMMUNITY</h1>
            <p><b>IS JUST A TAP AWAY</b></p>
            <a href="#booking">
                <button class="btn">To Book Turf</button>
            </a>
        </div>
    </section>

</body>

</html>