<head>
    <link rel="stylesheet" href="css/userheader.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
</head>
<header id="header">
    <div id="navbar">
        <a href="index.jsp" id="logo">
            <img src="${logo}" alt="Logo" width="60" height="60" />
            <p id="banana">${companyName1}</p>
            <p id="sis">${companyName2}</p>
        </a>
        <a href="index.jsp" class="navlink">Home</a>
        <a href="RetrieveProduct" class="navlink">Shop</a>
        <a href="PromotionServlet?url=promotion" class="navlink">Promotion</a>
        <a href="about.jsp" class="navlink">About</a>
        <a href="contact.jsp" class="navlink">Contact</a>
    </div>
    <div id="user-features">
        <a href="OrderServlet?url=cart"><i class="ti ti-shopping-cart-filled"></i>Cart</a>
        <a href="ProfileDetail"><i class="ti ti-user-filled"></i>Profile</a>
    </div>
    <script src="js/headerAnimation.js"></script>
</header>