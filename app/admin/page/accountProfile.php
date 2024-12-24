<?php
$_title = 'Profile';
$_css = '../css/accountProfile.css';
include '../_base.php';
?>
<h1 class="h1 header-banner">Profile</h1>
<div id="profile-container">
    <div class="sidebar">
        <ul>
            <li id="personal-info-btn"><i class="ti ti-user"></i> Personal Info</li>
            <li id="change-password-btn"><i class="ti ti-lock"></i> Change Password</li>
            <li id="logout-btn"><a href="LogoutServlet" id="logout-link"><i class="ti ti-logout"></i>Logout</a></li>
        </ul>
    </div>
    <div class="content" id="personal-info-content" style="display: block;">
        <h2>Personal Info</h2>
        <form id="personal-info-container" action="ProfileModify" method="post" enctype="multipart/form-data">
            <div class="input-file-container">
                <div class="image-preview-container">
                    <img id="image-preview" src="data:image/jpeg;base64,${customer.image}" alt="">
                </div>
                <input type="file" name="profile-pic" id="profile-pic" class="input-file" onchange="previewFile()" />
                <label for="profile-pic" class="input-label">Upload Profile Picture</label>
            </div>
            <div>
                <div class="input-subcontainer">
                    <input type="text" name="username" value="tanjc" class="input-box" spellcheck="false" />
                    <label for="username" class="label">Username</label>
                </div>
                <div class="input-subcontainer">
                    <input type="text" name="email" value="haha@gmail.com" class="input-box" spellcheck="false" />
                    <label for="email" class="label">Email</label>
                </div>
                <div class="input-subcontainer">
                    <input type="text" name="phone" value="+601163985186" class="input-box" spellcheck="false" />
                    <label for="phone" class="label">Phone</label>
                </div>
                <button class="btn" type="submit">Save</button>
            </div>
        </form>
    </div>
   
    <div class="content" id="change-password-content" style="display: none;">
        <h2>Change Password</h2>
        <form id="change-password-container" action="ChangePassword" method="post">
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
            <button class="btn" type="submit">Save</button>
        </form>
    </div>
    <!-- Add other content divs similarly with display: none; -->
</div>
<script src="../js/profileSidebar.js"></script>
<script src="../js/imagePreview.js"></script>
<script src="../js/inputHasContent.js"></script>
<script src="../js/showPassword.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    let status = document.getElementById("status").value;

    if (status === "personalSuccess") {
        swal.fire("Congratulations", "Personal info submitted successfully.", "success");
    }
    
    if (status === "passwordChanged") {
        swal.fire("Congratulations", "Password changed successfully.", "success");
    }
    if (status === "incompleteForm") {
        swal.fire("Sorry", "Incomplete Form.", "error");
    }
    if (status === "personalProcessFail") {
        swal.fire("Sorry", "Personal info submit failed.", "error");
    }

    if (status === "invalidConfirmPassword") {
        swal.fire("Sorry", "Invalid Confirm Password.", "error");
    }
</script>
