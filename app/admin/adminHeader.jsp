
<head>
    <link rel="stylesheet" href="css/adminHeader.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet"/>
</head>
<style>
   
</style>
<header id="header">
    <div id="navbar">
        <a href="adminPage.jsp" id="logo">
             <img src="../${logo}" alt="Logo" width="60" height="60" />
            <p id="banana">${companyName1}</p>
            <p id="sis">${companyName2}</p>
        </a>
        <a href="adminPage.jsp" class="navlink">Home</a>
        <a href="product.jsp" class="navlink">Product</a>
        <a href="adminPromotion.jsp" class="navlink">Promotion</a>
        <a href="staff.jsp" class="navlink">Staff</a>
        <a href="customer.jsp" class="navlink">Customer</a>
        <a href="adminContact.jsp" class="navlink">Contact</a>
        <a href="adminSalesReport.jsp" class="navlink">Sales</a>
    </div>
    <div class="profile-menu">
       
        <div class="profile-dropdown">
            <button href="../LogoutServlet">Logout</button>
        </div>
       
    </div>
    <script src="../js/adminHeaderAnimation.js"></script>
</header>

<script>
        
    function toggleDropdown() {
    var dropdown = document.querySelector('.profile-dropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

</script>