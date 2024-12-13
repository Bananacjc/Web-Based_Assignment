<head>
    <link rel="stylesheet" href="../css/footer.css" />
</head>
<footer class="w-100">
    <svg
        id="footer-wave-svg"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 1200 100"
        preserveAspectRatio="none">
        <path
            id="footer-wave-path"
            d="M851.8,100c125,0,288.3-45,348.2-64V100H0v-44c3.7-1,7.3-1.9,11-2.9C80.7,22,151.7,10.8,223.5,6.3C276.7,2.9,330,4,383,9.8 c52.2,5.7,103.3,16.2,153.4,32.8C623.9,71.3,726.8,100,851.8,100z"></path>

    </svg>
    <div id="footer-container">
        <div id="footer-top" class="d-flex justify-content-space-evenly">
            <div class="d-flex justify-content-center align-items-center"> 
                <?= html_logo(80, 80);?>
            </div>
            <div class="d-flex h-100 flex-direction-column justify-content-flex-start align-items-flex-start">
                <p class="footer-contact-heading">Useful Links</p>
                <div class="menu-item"><a class="menu-link" href="index.php">Home</a></div>
                <div class="menu-item"><a class="menu-link" href="shop.php">Shop</a></div>
                <div class="menu-item"><a class="menu-link" href="promotion.php">Promotion</a></div>
                <div class="menu-item"><a class="menu-link" href="about.php">About</a></div>
                <div class="menu-item"><a class="menu-link" href="contact.php">Contact</a></div>
            </div>
            <div id="footer-contact-info">
                <p class="footer-contact-heading">Contact Info</p>
                <div class="contact-info-item">
                    <div class="footer-contact-icon"><i class="ti ti-map-pin"></i></div>
                    <div>
                        <p class="footer-contact-title">Address:</p>
                        <p class="footer-contact-content">
                            Jalan Genting Kelang, Setapak, 53300 Kuala Lumpur, Federal Territory of Kuala Lumpur
                        </p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="footer-contact-icon"><i class="ti ti-phone"></i></div>
                    <div>
                        <p class="footer-contact-title">Phone:</p>
                        <p class="footer-contact-content">+601163985186</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="footer-contact-icon"><i class="ti ti-mail"></i></div>
                    <div>
                        <p class="footer-contact-title">Email:</p>
                        <p class="footer-contact-content">bananasis@gmail.com</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="divider"></div>
        <div id="footer-bottom">
            <div id="copyright">
                <p>&copy;2024</p>
                <p class="text-yellow-light">&nbsp;Banana</p>
                <p class="text-green-light">SIS</p>
                <p>&nbsp;All rights reserved</p>
            </div>
            <div id="payment-service">
                <img src="../images/visa.png" alt="Visa" width="50" height="50" />
                <img src="../images/mastercard.svg" alt="MasterCard" width="50" height="50" />
                <img src="../images/tng.webp" alt="TouchNGo" width="50" height="50" />
            </div>
        </div>
    </div>
</footer>
</body>

</html>