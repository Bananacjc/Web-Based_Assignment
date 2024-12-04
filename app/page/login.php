<?php
$_css = '../css/login.css';
$_title = 'Login';
require '../_base.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = post('username-email');
    $password = post('password');

    // Fetch user from the database
    $stmt = $_db->prepare("SELECT * FROM customers WHERE username = ? OR email = ?");
    $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user->password)) {
        login($user, "/index.php");
    } else {
        // Pass an error message to be displayed in a popup
        $error = 'Invalid username/email or password';
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
                <div class="logo">
                    <img src="../images/logo.png" alt="Logo" width="60" height="60" />
                    <p class="text-gold">BANANA</p>
                    <p class="text-green-darker">SIS</p>
                </div>
                <div id="input-container" class="w-100 d-flex flex-direction-column justify-content-center align-items-center">
                    <div class="input-subcontainer">
                        <input type="text" name="username-email" value="" class="input-box" spellcheck="false" required />
                        <label for="username-email" class="label">Username or email</label>
                    </div>
                    <div class="input-subcontainer">
                        <input type="password" name="password" id="password" class="input-box" spellcheck="false" required />
                        <label for="password" class="label">Password</label>
                        <i class="ti ti-eye-off" id="togglePassword"></i>
                    </div>
                </div>
                <div id="login-helper-container" class="d-flex justify-content-space-between align-items-center">
                    <div class="d-flex align-items-center">
                        <input type="checkbox" name="remember_me">
                        <label id="remember-me" for="remember_me">Remember Me</label>
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
                <div class="d-flex justify-content-center">
                    <a href="../index.php" id="cont-guest"><i class="ti ti-user-off"></i>Continue As Guest</a>
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