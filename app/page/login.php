<?php
$_css = '../css/login.css';
$_title = 'Login';
require '../_base.php';

$msg = '';
$isSuccess = false;

if (is_post()) {
    $usernameOrEmail = post('username-email');
    $password = post('password');
    $rememberMe = post('remember_me'); // Check if "Remember Me" is checked

    // Fetch user from the database
    $stmt = $_db->prepare("SELECT * FROM customers WHERE username = ? OR email = ?");
    $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user->password)) {
        login($user); // Log the user in

        if ($rememberMe) {
            // Generate a secure token
            $token = bin2hex(random_bytes(32)); // 64-character secure token

            // Store the token in the database
            $updateStmt = $_db->prepare("UPDATE customers SET remember_token = ? WHERE customer_id = ?");
            $updateStmt->execute([$token, $user->customer_id]);

            // Set the token in a secure cookie
            setcookie('remember_me', $token, [
                'expires' => time() + (30 * 24 * 60 * 60), // 30 days
                'path' => '/',
                'secure' => true,    // Ensures the cookie is sent over HTTPS only
                'httponly' => true,  // Prevents JavaScript access to the cookie
                'samesite' => 'Strict',
            ]);
        }
    } else {
        $msg = 'Invalid username/email or password';
        popup($msg, false);
    }
}

?>

<body>
    <?php if (!empty($error)): ?>
        <script>
            showPopup('<?= $error ?>', 'error');
        </script>
    <?php endif; ?>
    <div id="container">
        <div id="container-left">
            <form id="login-container" action="" method="post">
                <?= html_logo(60, 60, true) ?>
                <div id="input-container" class="w-100 d-flex flex-direction-column justify-content-center align-items-center">
                    <div class="input-subcontainer">
                        <?= html_text('username-email', "class='input-box' spellcheck='false' required")?>
                        <label for="username-email" class="label">Username or email</label>
                    </div>
                    <div class="input-subcontainer">
                        <?= html_password('password', "class='input-box' spellcheck='false' required")?>
                        <label for="password" class="label">Password</label>
                        <i class="ti ti-eye-off" id="togglePassword"></i>
                    </div>
                </div>
                <div id="login-helper-container" class="d-flex justify-content-space-between align-items-center">
                    <div class="d-flex align-items-center">
                        <?= html_checkbox('remember_me', 'Remember Me', '', "id='remember-me'")?>
                    </div>
                    <div>
                        <a href="ForgetPassword.php" id="forgotpass" class="hover-underline-anim">Forgot your password?</a>
                    </div>
                </div>
                <button id="loginbtn" type="submit">Login</button>
                <div id="signup-container" class="d-flex justify-content-center">
                    <p>Don't have an account?</p>
                    <a href="register.php" class="hover-underline-anim">Sign up here</a>
                </div>
                <div id="cont-guest-container" class="hover-translate-y">
                <a href="../index.php" id="cont-guest"><i class="ti ti-user-off position-relative"></i>Continue As Guest</a>
            </div>

            </form>
            
        </div>
        <div id="container-right">
            <p>Get all your groceries<br>with just a few clicks</p>
            <img src="../images/login-products.png" alt="Grocery items on shelves" style="width:60%; height:auto;">
        </div>
    </div>
    <script src="../js/showPassword.js"></script>
</body>

</html>