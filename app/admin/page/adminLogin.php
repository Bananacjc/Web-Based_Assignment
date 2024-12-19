<?php
include '../_base.php';
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
            $_err['password'] = 'Not matched';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/adminLogin.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet" />
    <link rel="icon" href="/app/images/logo.png" />
    <title>Admin Login</title>
</head>

<body>
    <div id="container">
        <div id="container-left">
            <form id="login-container" method="post">
                <div id="logo">
                    <img src="../images/logo.png" alt="Logo" width="70" height="70" />
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
            <img src="../images/login-products.png" alt="Grocery items on shelves" style="width:60%; height:auto;">
        </div>
    </div>
    <script src="js/showPassword.js"></script>
</body>

</html>