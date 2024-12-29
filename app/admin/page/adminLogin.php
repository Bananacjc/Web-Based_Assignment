<!DOCTYPE html>
<html lang="en">
    <title>BananaSIS</title>
<?php
$_css = '../css/base.css';
$_css1 = '../css/adminLogin.css';
require '../_base.php';
$msg = '';
$isSuccess = false;
if (is_post()) {
    $email = req('email');
    $password = req('password');
    $_err = []; 

    if ($email == '') {
        $_err['email'] = 'Email is required';
    } elseif (!is_email($email)) {
        $_err['email'] = 'Invalid email format';
    }

    if ($password == '') {
        $_err['password'] = 'Password is required';
    }

    if (empty($_err)) {
        $hashedPassword = sha1($password); 

        $stm = $_db->prepare('
            SELECT * FROM employees
            WHERE email = ? AND password = ?
        ');
        $stm->execute([$email, $hashedPassword]);
        $u = $stm->fetch();

        if ($u) {
            if ($u->banned == 1) {
                $msg = 'Your account is banned. Please contact support.';
                popup($msg, false);
            } else {
                temp('info', 'Login successful');
                login($u); 
            }
        } else {
            $msg = 'Invalid email or password';
            popup($msg, false);
        }
    } else {
        foreach ($_err as $field => $error) {
            popup("$field: $error", false);
        }
    }
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
                        <input type="text" name="email" id="email" class="input-box" spellcheck="false" placeholder=" " />
                        <label for="email" class="label">Email</label>
                    </div>
                    <div class="input-subcontainer">
                        <input type="password" name="password" id="password" class="input-box" spellcheck="false" placeholder=" " />
                        <label for="password" class="label">Password</label>
                        <i class="ti ti-eye-off" id="togglePassword"></i>
                    </div>
                    <div>
                        <a href="forgetPassword.php" id="forgotpass" class="hover-underline-anim">Forgot your password?</a>
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
</body>
<script src="../js/showPassword.js"></script>
</html>