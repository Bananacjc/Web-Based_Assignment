<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />    
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet"/>
        <link rel="stylesheet" href="../css/login.css" />
        <link rel="icon" href="../images/logo.png">
        <title>Login</title>
    </head>
    <body>
        <div id="container">
            <div id="container-left">
                <form id="login-container" action="LoginServlet" method="post">
                    <div id="logo">
                        <img src="../images/logo.png" alt="Logo" width="100" height="100" />
                        <p id="banana">BANANA</p>
                        <p id="sis">SIS</p>
                    </div>
                    <div id="input-container">
                        <div class="input-subcontainer">
                            <input type="text" name="username-email" value="" class="input-box" spellcheck="false" required/>
                            <label for="username-email" class="label">Username or email</label>
                        </div>
                        <div class="input-subcontainer">
                            <input type="password" name="password" id="password" class="input-box" spellcheck="false" required/>
                            <label for="password" class="label">Password</label>
                            <i class="ti ti-eye-off" id="togglePassword"></i>
                        </div>
                    </div>
                    <a href="ForgetPassword.jsp" id="forgotpass">Forgot your password?</a>
                    <button id="loginbtn" type="submit">Login</button>
                    <div id="signup-container">
                        <p>Don't have an account? </p>
                        <a href="register.jsp">Sign up here</a>
                    </div>
                </form>
            </div>
            <div id="container-right">
                <p>Get all your groceries<br>with just a few clicks</p>
                <img src="../images/login-products.png" alt="Grocery items on shelves" style="width:60%; height:auto;">
            </div>          
        </div>
        <script src="js/showPassword.js"></script>
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