<!DOCTYPE html>
<html>

<?php
$_title = 'Register';
$_css = '../css/register.css';
require '../_base.php';
?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = post('username');
    $email = post('email');
    $password = post('password');
    $confirmPassword = post('confirm-password');

    if ($password !== $confirmPassword) {
        echo "<script>swal.fire('Error', 'Passwords do not match', 'error');</script>";
    } else {
        // Check for duplicate username or email
        $stmt = $_db->prepare("SELECT * FROM customers WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            echo "<script>swal.fire('Error', 'Username or Email already exists', 'error');</script>";
        } else {
            // Insert into database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $customerId = generate_unique_id('CUS', 'customers', 'customer_id', $_db);
            $stmt = $_db->prepare("INSERT INTO customers (customer_id, username, email, password) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$customerId, $username, $email, $hashedPassword])) {
                echo "<script>swal.fire('Success', 'Registration successful', 'success').then(() => {
                    window.location.href = 'login.php';
                });</script>";
            } else {
                echo "<script>swal.fire('Error', 'Something went wrong', 'error');</script>";
            }
        }
    }
}
?>

<body>
    <div id="container">
        <div id="container-left">
            <p>Join us to make grocery shopping faster and easier than ever</p>
            <img src="../images/register-products.webp" alt="Grocery items on shelves" style="width:60%; height:auto;">
        </div>
        <div id="container-right">
            <form id="register-container" action="" method="POST">
                <div id="logo">
                    <img src="../images/logo.png" alt="Logo" width="70" height="70" />
                    <p id="banana">BANANA</p>
                    <p id="sis">SIS</p>
                </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        let status = document.getElementById("status").value;

        if (status === "invalidConfirmPassword") {
            swal.fire("Sorry", "Password do not match with Confirmation Password", "error");
        }
        if (status === "duplicateRegister") {
            swal.fire("Sorry", "Register duplicated", "error");
        }
    </script>

</body>

</html>