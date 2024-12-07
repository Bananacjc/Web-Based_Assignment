<!DOCTYPE html>
<html>
<?php
$_title = 'Register';
$_css = '../css/register.css';
require '../_base.php';


if (is_post()) {
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg = '';
    $isSuccess = false;

    $username = post('username');
    $email = post('email');
    $password = post('password');
    $confirmPassword = post('confirm-password');

    if ($password !== $confirmPassword && $password && $confirmPassword) {
        $msg = 'Passwords do not match.';
    } else {
        $stmt = $_db->prepare("SELECT * FROM customers WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $msg = 'Username or Email already exists.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $customerId = generate_unique_id('CUS', 'customers', 'customer_id', $_db);
            $stmt = $_db->prepare("INSERT INTO customers (customer_id, username, email, password) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$customerId, $username, $email, $hashedPassword])) {
                $msg = 'Registration successful.';
                $isSuccess = true;
            } else {
                $msg = 'Something went wrong during registration.';
            }
        }
    }

    if ($msg) {
        // $_SESSION['popup_message'] = ['msg' => $msg, 'isSuccess' => $isSuccess];
        temp('popup_message', ['msg' => $msg, 'isSuccess' => $isSuccess]);
        if ($isSuccess) {
            header("Location: login.php");
            exit();
        }
        echo "<script>showPopup('$msg', $isSuccess);</script>";
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
</body>

</html>