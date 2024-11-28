<?php
$_css = '../css/contact.css';
$_title = 'Contact Us';
require '../_base.php';
include '../_head.php';
?>
    <body>
        <section class="blog">
            <div class="container">
                <div>
                    <h1 class="text-center h-1">Contact</h1>
                </div>
            </div>
        </section>
        <section class="d-flex">
            <div class="container2">
                <div class="b1">
                    <h1 class="b1b">Contact Information</h1>
                    <span class="parag">Fill the form below or write us .We will help you as soon as possible.</span>
                    <div class="d-flex">
                        <div class="b2b1">
                            <div class="iPhone">
                                <i class="ti ti-phone" style="color:#34a853;"></i>
                            </div>
                            <span class="bb1">Phone</span>
                            <span style="
                            font-size: 15px;
                                  color: #797979;
                                  font-weight: 500;
                                  font-family: 'Inter', sans-serif;">+601163985186</span>
                        </div>
                        <div class="b2b2">
                            <div class="iEmail">
                                <i class="ti ti-mail" style="color:#34a853;"></i>
                            </div>
                            <span class="bb1">Email</span>
                            <span style="    font-size: 15px;
                                  color: #797979;
                                  font-weight: 500;
                                  font-family: 'Inter', sans-serif;">tanjc@gmail.com</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="bb2">
                        <span class="iPins">
                            <i class="ti ti-map-pins" style="color:#34a853"></i>
                        </span>
                    </div>
                    <div class="bb3">
                        <h5 class="add1">Address</h5>

                        <p class="pp1">Jalan Genting Kelang, Setapak, 53300 Kuala Lumpur, Federal Territory of Kuala Lumpur</p>
                    </div>
                </div>
                <iframe style="height:280px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15934.14306094369!2d101.71941264735288!3d3.215779248149603!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc3843bfb6a031%3A0x2dc5e067aae3ab84!2sTunku%20Abdul%20Rahman%20University%20of%20Management%20and%20Technology%20(TAR%20UMT)!5e0!3m2!1sen!2smy!4v1710395879605!5m2!1sen!2smy" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div class="container3">

                <div class="align-items-center justify-content-center position-relative p-0 flex-direction-column">
                    <h5 class="comment-title">Get In Touch</h5>
                    <div id="vector-line">
                        <img src="../images/vector-line.png" width="354" height="30" />
                    </div>
                </div>
                <div class="c1">
                    <form action="SubmitContactServlet" method="POST">
                        <div class=" account-inner-form">

                            <div class="review-form-name">
                                <label for="fname" class="form-label">Name*</label>
                                <input type="text" id="fname" name="name" class="form-control" placeholder="Name" required>
                            </div>
                            <div class="review-form-name">
                                <label for="email" class="form-label">Email*</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="user@gmail.com" required>
                            </div>
                            <div class="review-form-name">
                                <label for="subject" class="form-label">Subject*</label>
                                <input type="text" id="subject" name="subject" class="form-control" placeholder="Subject" required>
                            </div>

                        </div>
                        <div class="review-textarea">
                            <label for="floatingTextarea">Message*</label>
                            <textarea class="form-control" placeholder="Write Message..........." id="floatingTextarea" name="message" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="send-btn">Send Now</button>
                    </form>
                </div>
            </div>
        </section>
        <?php
        include '../_foot.php';
        ?>
    </body>
</html>
