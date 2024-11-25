<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="../css/header.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet" />
    <link rel="icon" href="../images/logo.png">
    <link href="<?= $_css ?>" rel="stylesheet" type="text/css">
    <title><?= $_title ?? 'Untitled' ?></title>
</head>
<body>
    <header id="header">
        <div id="navbar">
            <a href="/index.php" id="logo">
                <img src="../images/logo.png" alt="Logo" width="60" height="60" />
                <p id="banana">BANANA</p>
                <p id="sis">SIS</p>
            </a>
            <a href="/index.php" class="navlink">Home</a>
            <a href="/page/shop.php" class="navlink">Shop</a>
            <a href="/page/promotion.php" class="navlink">Promotion</a>
            <a href="/page/about-us.php" class="navlink">About</a>
            <a href="/page/contact.php" class="navlink">Contact</a>
        </div>
        <a href="login.php" id="loginbtn">Login</a>
        <script src="../js/headerAnimation.js"></script>
    </header>