
<!DOCTYPE html>
<html>
    <head>
    <?php
require '../_base.php';
include 'head.php';
?>

        <link rel="stylesheet" href="css/staff.css" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Employee</title>
        <style>
            .modal {
                margin-top: 80px;
                display: none;
                position: absolute;
                z-index: 1;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.4);
            }
            .modal-content {
                background: white;
                width: 60%;
                margin: 35px auto;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                box-sizing: border-box;
            }
            .close-button {
                float: right;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
            }
        </style>
    </head>
    <%@ include file = "adminHeader.jsp" %>
    <body>  
        <h1>STAFF</h1>
        <table id="promoTable" class="data-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <% for (Employee employeeThing : employees) { %>
                <tr>
                    <td><img src="data:image/jpeg;base64,<%= employeeThing.getImage() %>" alt="<%= employeeThing.getEmployeename() %>"></td>
                    <td><%= employeeThing.getEmployeeid() %></td>
                    <td><%= employeeThing.getEmployeename() %></td>
                    <td><%= employeeThing.getEmail() %></td>
                    <td>
                        <button type="button" class="password-button" onclick="showPasswordForm('<%= employeeThing.getEmployeeid() %>')">Update Password</button>
                        <button class="action-button" onclick="showUpdateForm('<%= employeeThing.getEmployeeid() %>', '<%= employeeThing.getEmployeename() %>', '<%= employeeThing.getEmail() %>', '<%= employeeThing.getImage() %>')">Update</button>
                        <form action="../employee" method="post" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="employeeId" value="<%= employeeThing.getEmployeeid() %>">
                            <button type="submit" class="delete-action-button" onclick="return confirmDelete();">Delete</button>
                        </form>
                    </td>
                </tr>
<% } %>
            </tbody>
        </table>
        <div style="margin: 30px;">
            <button id="addStaffBtn" class="action-button" onclick="showAddForm()">Add new staff</button>
        </div>

        <!-- Add Staff Modal -->
        <div id="addStaffModal" class="modal">
            <div class="modal-content">
                <span class="close-button" onclick="hideAddForm()">&times;</span>
                <form id="addForm" action="../employee" method="POST" enctype="multipart/form-data" class="add-form">
                    <input type="hidden" name="action" value="add">
                    <label for="newEmployeeId">Employee ID:</label>
                    <input type="text" id="newEmployeeId" name="employeeId"><br>

                    <label for="newEmployeeName">Employee Name:</label>
                    <input type="text" id="newEmployeeName" name="employeeName"><br>

                    <label for="newEmployeePassword">Employee Password:</label>
                    <input type="password" id="newEmployeePassword" name="employeePassword"><br>

                    <label for="newEmail">Email:</label>
                    <input type="email" id="newEmail" name="email"><br>

                    <label for="newImage">Image:</label>
                    <input type="file" id="newImage" name="employeeImage"><br>

                    <input type="submit" value="Add Staff">
                    <button type="button" class="cancel-button" onclick="hideAddForm()">Cancel</button>
                </form>
            </div>
        </div>


        <!-- Single Modal Outside Loop -->
        <div id="updateModal" class="modal">
            <div class="modal-content">
                <span class="close-button" onclick="hideUpdateForm()">&times;</span>
                <form id="updateForm" action="../employee" method="POST" enctype="multipart/form-data" class="update-form">
                    <input type="hidden" id="updateAction" name="action" value="update">
                    <input type="hidden" id="employeeId" name="employeeId">
                    <label for="employeeName">Employee Name:</label>
                    <input type="text" id="employeeName" name="employeeName">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email">
                    <label for="image">Current Image:</label>
                    <img id="currentImage" style="width: 100px; height: auto;">
                    <label for="newImage">Change Image:</label>
                    <input type="file" id="newImage" name="employeeImage">
                    <input type="submit" value="Update">
                    <button type="button" class="cancel-button" onclick="hideUpdateForm()">Cancel</button>
                    
                </form>
            </div>
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

            function showUpdateForm(employeeId, employeeName, email, image) {
                var modal = document.getElementById('updateModal');
                var form = document.getElementById('updateForm');
                modal.style.display = "block";
                form.elements['employeeId'].value = employeeId;
                form.elements['employeeName'].value = employeeName;
                form.elements['email'].value = email;
                document.getElementById('currentImage').src = 'data:image/jpeg;base64,' + image;
                document.getElementById('currentImage').alt = employeeName;
            }

            function hideUpdateForm() {
                document.getElementById('updateModal').style.display = "none";
            }

            function confirmDelete() {
                return confirm("Are you sure you want to delete this staff member?");
            }

            function showAddForm() {
                var modal = document.getElementById('addStaffModal');
                modal.style.display = "block";
            }

            function hideAddForm() {
                var modal = document.getElementById('addStaffModal');
                modal.style.display = "none";
            }

        </script>
    </body>

</html>
