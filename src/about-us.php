<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Play Arena</title>
    <link rel="stylesheet" href="about-us.css">
</head>
<body>
<?php $textColor = 'text-black'; ?>
<header class="<?php echo $textColor; ?>">
    <?php include 'navbar.php'; ?>
</header>
    <section class="about-us">
        <div class="about-us-container">
            <div class="about-card">
                <h3>About Play Arena</h3>
                <p>Welcome to <strong>Play Arena</strong>, your one-stop platform for booking sports turf and facilities for a variety of sports. Whether you're a casual player looking to enjoy a friendly match or a team looking for regular practice sessions, Play Arena has got you covered.</p>
            </div>

            <div class="mission-card">
                <div class="image">
                    <img src="/Public/img/Wallpaper.jpg" alt="Background" class="background-image">
                </div>
                <div class="details">
                    <h3><strong>Our Mission</strong></h3>
                    <p>Our mission is to make sports accessible to everyone by providing easy-to-use and reliable turf booking services. We aim to promote an active lifestyle, foster community spirit, and offer seamless turf booking experiences for individuals, teams, and organizations alike.</p>
                </div>
            </div>

            <div class="choose-card">
                <h3>Why Choose Us?</h3>
                <p>With Play Arena, you can book your next turf in just a few clicks, save time, and enjoy your favorite sports without any hassle. Our platform offers:</p>
                <ul>
                    <li><strong>User-Friendly Interface:</strong><br>Simple, fast, and intuitive booking system.</li>
                    <li><strong>Trusted Turf Providers:</strong><br>We partner with only the best turf providers in your area to ensure a high-quality experience.</li>
                    <li><strong>Customer Support:</strong><br>Our support team is always available to assist you with any queries or issues.</li>
                    <li><strong>Flexible Payment Options:</strong><br>Pay online securely with a variety of payment methods.</li>
                </ul>
            </div>

            <div class="offer-card">
                <div class="details">
                    <h3>What We Offer</h3>
                    <ul>
                        <li>Easy online turf booking for football, cricket, and other sports.</li>
                        <li>24/7 availability for booking, so you can play whenever you want.</li>
                        <li>Access to premium and well-maintained sports facilities near you.</li>
                        <li>Group bookings for teams, parties, or corporate events.</li>
                    </ul>
                </div>
                <div class="image">
                <img src="/Public/img/Wallpaper.jpg" alt="Background" class="background-image">
                </div>
            </div>
        </div>
    </section>

    <footer>
    <?php include 'footer.php'; ?>
    </footer>

</body>
</html>