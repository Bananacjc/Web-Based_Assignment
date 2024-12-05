<?php
// Handle logout directly
if (isset($_GET['logout'])) {
    logout('/page/login.php'); // Call the logout function and redirect to the homepage
}

$userLoggedIn = isset($_SESSION['user']); // Check if user session exists
?>

<head>
    <link rel="stylesheet" href="../css/header.css" />
</head>

<body>
    <header id="header">
        <div class="navbar d-flex">
            <a href="/index.php" class="logo">
                <img src="../images/logo.png" alt="Logo" width="60" height="60" />
                <p class="text-yellow-light">BANANA</p>
                <p class="text-green-light">SIS</p>
            </a>
            <a href="/index.php" class="navlink hover-underline-anim">Home</a>
            <a href="/page/shop.php" class="navlink hover-underline-anim">Shop</a>
            <a href="/page/promotion.php" class="navlink hover-underline-anim">Promotion</a>
            <a href="/page/about-us.php" class="navlink hover-underline-anim">About</a>
            <a href="/page/contact.php" class="navlink hover-underline-anim">Contact</a>
        </div>

        <!-- User Features or Login Button -->
        <?php if ($userLoggedIn): ?>
            <div id="user-features">
                <a href="/page/cart.php"><i class="ti ti-shopping-cart-filled"></i> Cart</a>
                <a href="/page/profile.php"><i class="ti ti-user-filled"></i> Profile</a>
                <a href="?logout=true"><i class="ti ti-logout"></i> Logout</a>
            </div>
        <?php else: ?>
            <a href="/page/login.php" id="loginbtn">Login</a>
        <?php endif; ?>

        <script src="../js/headerAnimation.js"></script>
    </header>
