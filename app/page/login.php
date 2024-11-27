<?php
$_css = '../css/login.css';
$_title = 'Login';
require '../_base.php';
?>
<div id="container">
    <div id="container-left">
        <form id="login-container" action="LoginServlet" method="post">
            <div id="logo">
                <img src="../images/logo.png" alt="Logo" width="70" height="70" />
                <p id="banana">BANANA</p>
                <p id="sis">SIS</p>
            </div>
            <div id="input-container">
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
            <a href="ForgetPassword.php" id="forgotpass">Forgot your password?</a>
            <button id="loginbtn" type="submit">Login</button>
            <div id="signup-container">
                <p>Don't have an account? </p>
                <a href="register.php">Sign up here</a>
            </div>
        </form>
    </div>
    <div id="container-right">
        <p>Get all your groceries<br>with just a few clicks</p>
        <img src="../images/login-products.png" alt="Grocery items on shelves" style="width:60%; height:auto;">
    </div>
</div>
<script src="../js/showPassword.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    let status = document.getElementById("status").value;

    if (status === "userNotFound") {
        swal.fire("Sorry", "User not found.", "error");
    }
    if (status === "passwordNotMatch") {
        swal.fire("Sorry", "Password not match.", "error");
    }
</script>
</body>

</html>