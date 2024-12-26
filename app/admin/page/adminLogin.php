<!DOCTYPE html>
<html lang="en">
    
<?php
$_css ='../css/_base.css';
$_css1='../css/adminLogin.css';
require '../_base.php';
$msg = '';
$isSuccess = false;

if (is_post()) {
    $email    = req('email');
    $password = req('password');

    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Required';
    } else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }

    // Validate: password
    if ($password == '') {
        $_err['password'] = 'Required';
    }

    // Login user
    if (!$_err) {
        // TODO
        $stm = $_db->prepare('
            SELECT * FROM employees
            WHERE email = ? AND password= ?
        ');
        $stm->execute([$email, $password]);
        $u = $stm->fetch();

        if ($u) {
            temp('info', 'Login successfully');
            login($u);
        } else {
            $msg = 'Invalid email or password';
            popup($msg, false);
        }

    }
    $msg = 'Invalid email or password';
    popup($msg, false);
}
?>


<head>
    <meta charset="UTF-8" />
       <link rel="icon" href="/app/images/logo.png" />
    <title>Admin Login</title>
</head>

<body>
    <div id="container">
        <div id="container-left">
            <form id="login-container" method="post">
                <div id="logo">
                    <img src="../../images/logo.png" alt="Logo" width="70" height="70" />
                    <p id="banana">BANANA</p>
                    <p id="sis">SIS</p>
                </div>
                <div id="input-container">
                    <div class="input-subcontainer">

                        <label for="email">Email</label>
                        <?= html_text('email', 'maxlength="100"') ?>
                        <?= err('email') ?>
                    </div>
                    <div class="input-subcontainer">
                        <label for="password">Password</label>
                        <?= html_password('password', 'maxlength="100"') ?>
                        <?= err('password') ?>

                        <i class="ti ti-eye-off" id="togglePassword"></i>
                    </div>
                </div>

                <?php err('error'); ?>
                <button id="loginbtn" type="submit">Login</button>
            </form>
        </div>
        <div id="container-right">
            <p>Welcome Back</p>
            <img src="../../images/login-products.png" alt="Grocery items on shelves" style="width:60%; height:auto;">
        </div>
    </div>
    <script src="js/showPassword.js"></script>
</body>

</html>