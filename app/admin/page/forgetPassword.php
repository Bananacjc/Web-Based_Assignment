<?php
$_title = 'Forgot Password';
$_css1 = '../css/adminLogin.css';
require '../_base.php';


$msg = '';
$isSuccess = false;

if (is_post() && isset($_POST['request_otp'])) {
    header('Content-Type: text/plain'); // Set response type to plain text
    ob_clean(); // Clear any output buffer before sending the response
    try {
        $email = trim($_POST['email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo 'Invalid email format.';
            exit;
        }

        $stmt = $_db->prepare("SELECT * FROM employees WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() === 0) {
            echo 'Email not found.';
            exit;
        }
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_email'] = $email;

        $m = get_mail();
        $m->addAddress($email);
        $m->isHTML(true);
        $m->Subject = 'Your OTP for Password Reset';
        $m->Body = "<p>Your OTP is <b>$otp</b>. Please use it to reset your password.</p>";

        if ($m->send()) {
            echo 'Success';
        } else {
            echo 'Failed to send OTP.';
        }
    } catch (Exception $e) {
        echo 'Unexpected server error occurred.';
    }
    exit; 
}

if (is_post()) {
    $email = trim(post('email'));
    $otpEntered = post('otp');
    $newPassword = post('new-password');
    $confirmPassword = post('confirm-password');

    if (empty($otpEntered)) {
        $msg = 'Please enter the OTP sent to your email.';
    } elseif (!isset($_SESSION['otp'])) {
        $msg = 'No OTP found. Please request OTP again.';
    } elseif ($otpEntered != $_SESSION['otp']) {
        $msg = 'Invalid OTP. Please check your email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $email !== $_SESSION['otp_email']) {
        $msg = 'Invalid email or email does not match the OTP.';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $newPassword)) {
        $msg = 'Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.';
    } elseif ($newPassword !== $confirmPassword) {
        $msg = 'Passwords do not match.';
    } else {
        // Update the password in the database
        $hashedPassword = sha1($newPassword);
        $stmt = $_db->prepare("UPDATE employees SET password = ? WHERE email = ?");

        if ($stmt->execute([$hashedPassword, $email])) {
            $msg = 'Password reset successful! You can now log in with your new password.';
            $isSuccess = true;

            // Clear OTP after successful reset
            unset($_SESSION['otp']);
            unset($_SESSION['otp_email']);
        } else {
            $msg = 'Password reset failed. Please try again.';
        }
    }

    // Display popup message
    if ($msg) {
        popup($msg, $isSuccess);

        if ($isSuccess) {
            temp('popup-msg', ['msg' => $msg, 'isSuccess' => true]);
            redirect('adminLogin.php'); // Redirect on successful password reset
        }
    }
}
?>

<body>
    <div id="container">
        <div id="container-left">

            <form id="forgot-password-container" action="" method="POST">
                <div id="logo">
                    <img src="../../images/logo.png" alt="Logo" width="70" height="70" />
                    <p id="banana">BANANA</p>
                    <p id="sis">SIS</p>
                </div>
                <div id="input-container">
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
                        <input type="password" name="new-password" id="new-password" class="input-box" spellcheck="false" placeholder=" " />
                        <label for="new-password" class="label">New Password</label>
                        <i class="ti ti-eye-off" id="toggleNewPassword"></i>
                    </div>
                    <div class="input-subcontainer">
                        <input type="password" name="confirm-password" id="confirm-password" class="input-box" spellcheck="false" placeholder=" " />
                        <label for="confirm-password" class="label">Confirm Password</label>
                        <i class="ti ti-eye-off" id="toggleConfirmPassword"></i>
                    </div>
                </div>
                <div>
                <div>    <button id="reset-password-btn" type="submit" class="btn btn-reset">Reset Password</button></div>
                    <a href="adminLogin.php" id="back-btn">
                        Back</button>
                    </a>
                </div>
        </div>

        </form>
        <div>


        </div>
        <div id="container-right">
            <p>Reset your password to regain access to your account</p>
            <img src="../images/register-products.webp" alt="Password reset illustration" style="width:60%; height:auto;">
        </div>
    </div>
    <script src="/js/showPassword.js"></script>
    <script src="../js/requestOTP.js"></script>
</body>

</html>