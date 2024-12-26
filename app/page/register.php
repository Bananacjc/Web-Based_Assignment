<!DOCTYPE html>
<html>
<?php
$_title = 'Register';
$_css = '../css/register.css';
require '../_base.php';

if (is_post()) {
    $msg = '';
    $isSuccess = false;
    $username = trim(post('username'));
    $email = trim(post('email'));
    $contact_num = trim(post('contact_num'));
    $password = post('password');
    $confirmPassword = post('confirm-password');
    $profileImage = 'guest.png';

    // Validate username
    if (strlen($username) < 3) {
        $msg = 'Username must be at least 3 characters long.';
    }
    // Validate email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Invalid email format.';
    }
    // Validate contact number for Malaysia
    elseif (!preg_match('/^01[0-9]-?\d{7,8}$/', $contact_num)) {
        $msg = 'Invalid Malaysian contact number format. It should start with "01" followed by 7-8 digits.';
    }
    // Validate password complexity
    elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $msg = 'Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.';
    }
    // Check if passwords match
    elseif ($password !== $confirmPassword && $password && $confirmPassword) {
        $msg = 'Passwords do not match.';
    }
    // Check if username and email are provided and unique
    elseif ($username && $email) {
        $stmt = $_db->prepare("SELECT * FROM customers WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $msg = 'Username or Email already exists.';
        } else {
            // Hash password using SHA1
            $hashedPassword = sha1($password);
            $customerId = generate_unique_id('CUS', 'customers', 'customer_id', $_db);
            $stmt = $_db->prepare("INSERT INTO customers (customer_id, username, email, contact_num, password, profile_image) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$customerId, $username, $email, $contact_num, $hashedPassword, $profileImage])) {
                $msg = 'Registration successful.';
                $isSuccess = true;
            } else {
                $msg = 'Something went wrong during registration.';
            }
        }
    }

    // Display message if any
    if ($msg) {
        if ($isSuccess) {
            temp('popup-msg', ['msg' => $msg, 'isSuccess' => $isSuccess]);
            redirect('login.php');
        } else {
            popup($msg, $isSuccess);
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
        <div id="container-right" class="flex-direction-column">

            <form id="register-container" action="" method="POST">
                <?= html_logo(70, 70, true); ?>
                <div id="input-container">
                    <div class="input-subcontainer">
                        <input type="text" name="username" value="" class="input-box" spellcheck="false" required />
                        <label for="username" class="label">Username</label>
                    </div>
                    <div class="input-subcontainer">
                        <input type="text" name="email" value="" class="input-box" spellcheck="false" required />
                        <label for="email" class="label">Email</label>
                    </div>
                    <div class="input-subcontainer">
                        <input type="text" name="contact_num" id="contact-num" class="input-box" spellcheck="false" required />
                        <label for="contact_num" id="contact-label" class="label">Contact Number (e.g., 0123456789)</label>
                    </div>
                    <div class="input-subcontainer">
                        <input type="password" name="password" id="password" class="input-box" spellcheck="false" required />
                        <label for="password" class="label">Password</label>
                        <i class="ti ti-eye-off" id="togglePassword"></i>
                    </div>
                    <div class="input-subcontainer">
                        <input type="password" name="confirm-password" id="confirm-password" class="input-box" spellcheck="false" required />
                        <label for="confirm-password" class="label">Confirm Password</label>
                        <i class="ti ti-eye-off" id="toggleConfirmPassword"></i>
                    </div>
                </div>
                <button id="registerbtn" type="submit">Register</button>
                <div id="login-container">
                    <p>Already have an account? </p>
                    <a href="login.php">Login here</a>
                </div>
            </form>
        </div>
    </div>
    <script src="../js/showPassword.js"></script>
    <script src="../js/contactNumLabel.js"></script>
</body>

</html>