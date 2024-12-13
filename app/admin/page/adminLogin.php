<?php
require '../_base.php';

// Start session to track user state
// Check if the form is submitted
if (is_post()) {
    // Retrieve and sanitize input
    $userId = post('userId');
    $password = post('userPassword');

    // Validate input
    if (empty($userId) || empty($password)) {
        $_err['error'] = 'Please fill in all fields.';
    } else {
        // Prepare and execute the query securely
        try {
            $stmt = $_db->prepare('SELECT * FROM employees WHERE employee_id = :userId');
            $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Successful login
                $_SESSION['user_id'] = $user['employee_id'];
                $_SESSION['role'] = $user['role']; // Store the user role in session

                // Redirect to the dashboard based on role
                if ($user['role'] == 'admin') {
                    redirect('adminDashboard.php');
                } else {
                    redirect('userDashboard.php');
                }
            } else {
                $_err['error'] = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $_err['error'] = 'An error occurred while processing your request. Please try again later.';
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
                        <input type="text" name="userId" id="userId" class="input-box" required />
                        <label for="userId" class="label">UserId</label>
                    </div>
                    <div class="input-subcontainer">
                        <input type="password" name="userPassword" id="userPassword" class="input-box" required />
                        <label for="userPassword" class="label">Password</label>
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
