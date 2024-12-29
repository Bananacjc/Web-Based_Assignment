<?php
$_title = 'Register';
$_css = '../css/register.css';
require '../_base.php';

$msg = '';
$isSuccess = false;

// Handle OTP Request via AJAX
if (is_post() && isset($_POST['request_otp'])) {
    header('Content-Type: text/plain'); // Set response type to plain text
    ob_clean(); // Clear any output buffer before sending the response
    try {
        $email = trim($_POST['email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo 'Invalid email format.';
            exit;
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_email'] = $email;

        $m = get_mail();
        $m->addAddress($email);
        $m->isHTML(true);
        $m->Subject = 'Your OTP for Registration';
        $m->Body = "<p>Your OTP is <b>$otp</b>. Please use it to complete your registration.</p>";

        if ($m->send()) {
            echo 'Success';
        } else {
            echo 'Failed to send OTP.';
        }
    } catch (Exception $e) {
        echo 'Unexpected server error occurred.';
    }
    exit; // Stop further execution
}

// Full-page logic (HTML + registration logic)
if (is_post()) {
    $msg = '';
    $isSuccess = false;

    $username = trim(post('username'));
    $email = trim(post('email'));
    $contact_num = trim(post('contact-num'));
    $password = post('password');
    $confirmPassword = post('confirm-password');
    $otpEntered = post('otp');

    // Ensure OTP is entered and session OTP is set
    if (empty($otpEntered)) {
        $msg = 'Please enter the OTP sent to your email.';
    } elseif (!isset($_SESSION['otp'])) {
        $msg = 'No OTP found. Please request OTP again.';
    } elseif ($otpEntered != $_SESSION['otp']) {
        $msg = 'Invalid OTP. Please check your email.';
    } else {
        // OTP is valid, proceed to validate other fields
        if (strlen($username) < 3) {
            $msg = 'Username must be at least 3 characters long.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = 'Invalid email format.';
        } elseif (!preg_match('/^01[0-9]-?\d{7,8}$/', $contact_num)) {
            $msg = 'Invalid Malaysian contact number format. It should start with "01" followed by 7-8 digits.';
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
            $msg = 'Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.';
        } elseif ($password !== $confirmPassword) {
            $msg = 'Passwords do not match.';
        } else {
            // Check if username or email already exists
            $stmt = $_db->prepare("SELECT * FROM customers WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);

            if ($stmt->rowCount() > 0) {
                $msg = 'Username or Email already exists.';
            } else {
                // Create new user
                $hashedPassword = sha1($password);
                $customerId = generate_unique_id('CUS', 'customers', 'customer_id', $_db);
                $stmt = $_db->prepare("INSERT INTO customers (customer_id, username, email, contact_num, password, profile_image) VALUES (?, ?, ?, ?, ?, ?)");

                if ($stmt->execute([$customerId, $username, $email, $contact_num, $hashedPassword, 'guest.png'])) {
                    $msg = 'Registration successful!';
                    $isSuccess = true;

                    // Clear OTP after successful registration
                    unset($_SESSION['otp']);
                    unset($_SESSION['otp_email']);
                } else {
                    $msg = 'Registration failed. Please try again.';
                }
            }
        }
    }

    // Display popup message
    if ($msg) {
        if ($isSuccess) {
            temp('popup-msg', ['msg' => $msg, 'isSuccess' => true]);
            redirect('login.php'); // Redirect on successful registration
        } else {
            popup($msg, false); // Show popup for errors
        }
    }
}
?>

<body>
    <div id="container">
        <div id="container-left">
            <p>Join us to make grocery shopping faster and easier than ever</p>
            <img src="../images/register-products.webp" alt="Grocery items on shelves" style="width:60%; height:auto;">
        </div>
        <div id="container-right">
            <form id="register-container" action="" method="POST">
                <?= html_logo(70, 70, true); ?>
                <div id="input-container">
                    <div class="input-subcontainer">
                        <input type="text" name="username" class="input-box" spellcheck="false" placeholder=" " />
                        <label for="username" class="label">Username</label>
                    </div>
                    <div class="input-subcontainer">
                        <input type="text" name="email" id="email" class="input-box" spellcheck="false" placeholder=" " />
                        <label for="email" class="label">Email</label>
                    </div>
                    <div class="d-flex justify-content-space-around">
                        <div class="input-subcontainer">
                            <input type="text" name="otp" class="input-box" spellcheck="false" placeholder=" " />
                            <label for="otp" class="label">OTP</label>
                        </div>
                        <button type="button" id="request-otp-btn">Request OTP</button>
                    </div>
                    <div class="input-subcontainer">
                        <input type="text" name="contact-num" id="contact-num" class="input-box" spellcheck="false" placeholder=" " />
                        <label for="contact-num" class="label">Contact Number</label>
                    </div>
                    <div class="input-subcontainer">
                        <input type="password" name="password" id="password" class="input-box" spellcheck="false" placeholder=" " />
                        <label for="password" class="label">Password</label>
                        <i class="ti ti-eye-off" id="togglePassword"></i>
                    </div>
                    <div class="input-subcontainer">
                        <input type="password" name="confirm-password" id="confirm-password" class="input-box" spellcheck="false" placeholder=" " />
                        <label for="confirm-password" class="label">Confirm Password</label>
                        <i class="ti ti-eye-off" id="toggleConfirmPassword"></i>
                    </div>
                </div>
                <button id="registerbtn" type="submit" class="btn btn-register">Register</button>
                <div id="login-container">
                    <p>Already have an account? </p>
                    <a href="login.php">Login here</a>
                </div>
            </form>
        </div>
    </div>
    <script src="../js/showPassword.js"></script>
    <script src="../js/contactNumLabel.js"></script>
    <script src="../js/requestOTP.js"></script>
</body>

</html>