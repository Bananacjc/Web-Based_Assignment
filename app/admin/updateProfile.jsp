<%@page contentType="text/html" pageEncoding="UTF-8"%>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/updateStaffProfile.css" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Update Profile</title>

    </head>
    <body>
        <%@ include file = "adminHeader.jsp" %>
        <h1 style="margin-top: 150px;">Update Profile</h1>
        <div class="profile-container">
            <form id="updateForm" action="../employee" method="POST" enctype="multipart/form-data" class="updateprofile-form">
                <div class="profile-image-section">
                    <label for="newImage">Current Image:</label>
                    <img src="data:image/jpeg;base64,<%= employee.getImage() %>" alt="Profile Image">
                    <label for="newImage">Change Image:</label>
                    <input type="file" id="newImage" name="employeeImage">
                </div>
                <div class="profile-form-section">
                    <input type="hidden" id="updateAction" name="action" value="update">
                    <input type="hidden" id="employeeId" name="employeeId" value="${employee.employeeid}">
                    <label for="employeeName">Employee Name:</label>
                    <input type="text" id="employeeName" name="employeeName" value="${employee.employeename}">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="${employee.email}">
                    <input type="submit" value="Update">
                    <button type="button" class="password-button" onclick="showPasswordForm('${employee.employeeid}')">Update Password</button>
                </div>
            </form>

        </div>
        <!-- Password Update Form -->
        <div id="passwordModal" class="modal">
            <div class="modal-content">
                <span class="close-button" onclick="hidePasswordForm()">&times;</span>
                <form id="passwordForm" action="../employee" method="POST" onsubmit="return checkPasswords()">
                    <input type="hidden" name="action" value="updatePassword">
                    <input type="hidden" id="passwordEmployeeId" name="employeeId">

                    <label for="newPassword" class="label">New Password:</label>
                    <input type="password" id="newPassword" name="newPassword" class="input"><br>

                    <label for="confirmPassword" class="label">Confirm Password:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="input"><br>

                    <input type="submit" class="password-button" value="Update Password">
                    <button type="button" class="cancel-button" onclick="hidePasswordForm()">Cancel</button>

                </form>
            </div>
        </div>
    </body>
    <script>
        function showPasswordForm(employeeId) {
            var modal = document.getElementById('passwordModal');
            modal.style.display = "block";
            document.getElementById('passwordEmployeeId').value = employeeId;
        }

        function hidePasswordForm() {
            var modal = document.getElementById('passwordModal');
            modal.style.display = "none";
        }

        function checkPasswords() {
            var newPassword = document.getElementById('newPassword').value;
            var confirmPassword = document.getElementById('confirmPassword').value;
            if (newPassword === confirmPassword) {
                return true;
            } else {
                alert('Passwords do not match.');
                return false;
            }
        }

    </script>
</html>
