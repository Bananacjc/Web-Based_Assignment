<%@page contentType="text/html" pageEncoding="UTF-8"%>
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
        <link rel="stylesheet" href="css/adminLogin.css" />
        <link rel="icon" href="../img/logo.png">
        <title>Admin Login</title>
    </head>
    <style>body {
    margin: 0;
    padding: 0;
    height: 100vh;
    background-color: #EFEFEF;
    background-image: radial-gradient(circle at 10% 70%, #5AB2FF 25%, transparent 25.1%), radial-gradient(circle at 105% 0%, #EFEFEF 13%, transparent 13.1%), radial-gradient(circle at 105% 0%, #5AB2FF 20%, transparent 20.1%);
    background-size: 100%;
    background-repeat: no-repeat;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: "League Spartan", sans-serif;
}

#container {
    height: 80%;
    width: 80%;
    display: flex;
    border-radius: 30px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

#container-left {
    width: 50%;
    background-color: #FFFFFF;
    display: flex;
    justify-content: center;
    align-items: center;
}

#container-right {
    width: 50%;
    background-image: radial-gradient(circle at 35% 65%, #7bc1ff 25%, transparent 25.1%), radial-gradient(circle at 70% 40%, #488ecc 20%, transparent 20.1%), linear-gradient(to bottom right, #7bc1ff, #488ecc);
    display: flex;
    flex-direction: column;
    align-items: center;
}

#login-container {
    width: 65%;
    height: 80%;
    display: flex;
    flex-direction: column;
}

#logo {
    margin: 0 auto;
    display: flex;
    justify-content: center;
    align-items: center;
}

#banana, #sis {
    font-weight: 700;
    letter-spacing: 2px;
    font-size: 26px;
}

#banana {
    color: #FFD700;
}

#sis {
    color: #32CD32;
}

#input-container {
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-top: 50px;
}

.input-subcontainer {
    width: 100%;
    position: relative;
    margin: 15px 0;
    display: flex;
    align-items: center;
}

.input-box {
    width: 100%;
    flex-grow: 1;
    padding: 16px 40px 16px 16px;
    line-height: 16px;
    font-size: 16px;
    border-radius: 8px;
    border: 1px solid #7bc1ff;
    background: transparent;
    transition: border-color 0.2s;
    position: relative;
    z-index: 1;
    box-sizing: border-box;
}

.ti {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #888;
    font-size: 18px;
    z-index: 10;
}

.ti-eye, .ti-eye-off {
    position: absolute;
    right: 10px;
    cursor: pointer;
    color: #888;
    font-size: 18px;
    display: block;
}

.ti-eye:hover, .ti-eye-off:hover {
    color: #555;
}

.label {
    user-select: none;
    position: absolute;
    bottom: 16px;
    left: 20px;
    transition: all 0.2s ease;
    color: #888;
    pointer-events: none;
    font-size: 18px;
}

.input-box:focus,
.input-box:valid {
    border-color: #488ecc;
    outline: solid 1px #488ecc;
    color: #4d4d4d;
}

.input-box:focus + .label,
.input-box:valid + .label {
    transform: translateX(-15px) translateY(-165%) scale(0.9);
    padding: 0 10px;
    color: #5AB2FF;
    background-color: #FFFFFF;
    z-index: 2;
}

#loginbtn{
    margin: 20px 0;
    padding: 20px 0;
    border: none;
    border-radius: 8px;
    font-family: "Inter", sans-serif;
    font-weight: 600;
    font-style: normal;
    font-size: 18px;
    background-color: #5AB2FF;
    text-align: center;
    text-decoration: none;
    color: #4d4d4d;
    transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
    cursor: pointer;
    outline: none;
    display: inline-block;
}

#loginbtn:hover{
    background-color: #000000;
    color: #EFEFEF;
}

#container-right p {
    font-family: "League Spartan", sans-serif;
    letter-spacing: 2px;
    font-size: 40px;
    font-weight: 700;
    color: #EFEFEF;
}</style>
    <body>
        <input type="hidden" id="status" value="<% if (request.getAttribute("status") != "") {out.print(request.getAttribute("status")); request.setAttribute("status", "");} %>">
        <div id="container">
            <div id="container-left">
                <form id="login-container" action="j_security_check" method="post">
                    <div id="logo">
                        <img src="../${logo}" alt="Logo" width="70" height="70" />
                        <p id="banana">${companyName1}</p>
                        <p id="sis">${companyName2}</p>
                    </div>
                    <div id="input-container">
                        <div class="input-subcontainer">
                            <input type="text" name="j_username" value="" class="input-box" spellcheck="false" required/>
                            <label for="username" class="label">Username</label>
                        </div>
                        <div class="input-subcontainer">
                            <input type="password" name="j_password" id="password" class="input-box" spellcheck="false" required/>
                            <label for="password" class="label">Password</label>
                            <i class="ti ti-eye-off" id="togglePassword"></i>
                        </div>
                    </div>
                    <button id="loginbtn" type="submit">Login</button>
                </form>
            </div>
            <div id="container-right">
                <p>Welcome Back</p>
                <img src="../img/login-products.png" alt="Grocery items on shelves" style="width:60%; height:auto;">
            </div>          
        </div>
        <script src="js/showPassword.js"></script>
    </body>
</html>
