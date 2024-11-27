<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Reset Password</title>
        <style>
            body {
                font-family: Arial, sans-serif; /* Sets the font for the page */
                background: #f7f7f7; /* Light grey background */
                display: flex;
                justify-content: center; /* Center horizontally */
                align-items: center; /* Center vertically */
                height: 100vh; /* Full viewport height */
                margin: 0; /* Remove default margin */
            }
            form {
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1); /* Adds a subtle shadow around the form */
                width: 300px; /* Fixed width */
            }
            .input, .password-button {
                width: 100%; /* Full width of the form */
                padding: 10px;
                margin: 10px 0;
                border: 1px solid #ccc; /* Light gray border */
                border-radius: 4px; /* Rounded corners */
            }
            .label {
                display: block; /* Makes the label take up the full width */
                margin-bottom: 5px; /* Adds space between label and input box */
            }
            .password-button {
                background-color: #007BFF; /* Bootstrap blue */
                color: white;
                border: none;
                cursor: pointer; /* Pointer cursor on hover */
                font-size: 16px;
            }
            .password-button:hover {
                background-color: #0056b3; /* Darker blue on hover */
            }
        </style>
    </head>
    <body>
        <form id="passwordForm" action="ResetCusPassword" method="POST" >
            
                    <label>Update your password</label>

                    <label for="newPassword" class="label">New Password:</label>
                    <input type="password" id="newPassword" name="newPassword" class="input" required><br>

                    <label for="confirmPassword" class="label">Confirm Password:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="input" required><br>

                    <input type="submit" class="password-button" value="Update Password">
                    </form>
    </body>
</html>
