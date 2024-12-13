<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the query with a placeholder
    $query = "SELECT employee_id, password FROM employees WHERE email = :email";
    
    // Prepare the statement
    $stmt = $conn->prepare($query);
    
    // Bind the parameter
    $stmt->bindParam(':email', $email);
    
    // Execute the query
    $stmt->execute();
    
    // Fetch the result
    $stmt->bindColumn(1, $employee_id);
    $stmt->bindColumn(2, $hashed_password);
    
    if ($stmt->fetch(PDO::FETCH_BOUND)) {
        // Check if the password matches
        if (password_verify($password, $hashed_password)) {
            $_SESSION['employee_id'] = $employee_id;  // Set session variable
            header('Location: dashboard.php');  // Redirect to dashboard
            exit();
        } else {
            echo "Invalid credentials!";
        }
    } else {
        echo "No user found with that email!";
    }
}
?>

<form method="POST" action="login.php">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>
