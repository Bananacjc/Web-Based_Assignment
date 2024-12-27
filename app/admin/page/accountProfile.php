<?php
$_title = 'Profile';
$_css = '../css/accountProfile.css';
$_css1 = '../css/base.css';
require '../_base.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = post('form_type'); // Fetch the hidden input field

    if ($formType === 'personal_info') {
        // Handle personal info update
        $username = trim($_POST['employee_name']);
        $email = trim($_POST['email']);
        $role = trim($_POST['role']);
        $profilePic = get_file('profile-pic');

        if (!$username || !$email || !$role) {
            temp('popup-msg', ['msg' => 'All fields are required.', 'isSuccess' => false]);
            redirect('accountProfile.php');
        }
        if (!is_email($email)) {
            temp('popup-msg', ['msg' => 'Invalid email format.', 'isSuccess' => false]);
            redirect('accountProfile.php');
        }

        $profileImage = $_user->profile_image;
        if ($profilePic) {
            $newProfileImage = save_photo($profilePic, '../uploads/profile_images');
            if ($profileImage && $profileImage !== 'guest.png') {
                $oldImagePath = "../uploads/profile_images/$profileImage";
                if (file_exists($oldImagePath)) unlink($oldImagePath);
            }
            $profileImage = $newProfileImage;
        }

        $stmt = $_db->prepare("UPDATE employees SET employee_name = ?, email = ?,  role= ?, profile_image = ? WHERE employee_id = ?");
        $success = $stmt->execute([$username, $email, $role, $profileImage, $_user->employee_id]);

        if ($success) {
            $_user->employee_name = $username;
            $_user->email = $email;
            $_user->role = $role;
            $_user->profile_image = $profileImage;

            temp('popup-msg', ['msg' => 'Profile updated successfully.', 'isSuccess' => true]);
        } else {
            temp('popup-msg', ['msg' => 'Failed to update profile.', 'isSuccess' => false]);
        }
        redirect('accountProfile.php');
    } elseif ($formType === 'change_password') {
        $oldPassword = trim(post('old-password'));
        $newPassword = trim(post('new-password'));
        $confirmPassword = trim(post('confirm-password'));

        if (!$oldPassword || !$newPassword || !$confirmPassword) {
            temp('popup-msg', ['msg' => 'All fields are required.', 'isSuccess' => false]);
            redirect();

        }

        if (sha1($oldPassword) !== $_user->password) {
            temp('popup-msg', ['msg' => 'Incorrect old password.', 'isSuccess' => false]);
            redirect();

        }

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $newPassword)) {
            temp('popup-msg', [
                'msg' => 'New password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.',
                'isSuccess' => false
                
            ]);
            redirect();

        }

        if ($newPassword !== $confirmPassword) {
            temp('popup-msg', ['msg' => 'New password and confirmation password do not match.', 'isSuccess' => false]);
            redirect();
        }

        $hashedNewPassword = sha1($newPassword);
        $stmt = $_db->prepare("UPDATE employees SET password = ? WHERE employee_id = ?");
        $success = $stmt->execute([$hashedNewPassword, $_user->employee_id]);

        if ($success) {
            temp('popup-msg', ['msg' => 'Password changed successfully.', 'isSuccess' => true]);
        } else {
            temp('popup-msg', ['msg' => 'Failed to update password. Please try again.', 'isSuccess' => false]);
        }

        redirect();
    }
}



?>
<div id="profile-container">
    <div class="sidebar">
        <a href="adminDashboard.php" class="back-button">
            <i class="fa fa-arrow-left"></i> Back
        </a>

        <ul>
            <li id="personal-info-btn"><i class="ti ti-user"></i> Personal Info</li>
            <li id="change-password-btn"><i class="ti ti-lock"></i> Change Password</li>
            <li id="logout-btn"><a href="logout.php" id="logout-link" onclick="return confirmLogOut()"><i class="ti ti-logout"></i>Logout</a></li>
        </ul>
    </div>
    <div class="content" id="personal-info-content" style="display: block;">
        <h2>Personal Info</h2>
        <form id="personal-info-container" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="form_type" value="personal_info" />
            <div class="input-file-container" id="drop-zone">
                <div class="image-preview-container">
                    <img id="image-preview" src="../uploads/profile_images/<?= $_user->profile_image ?>" alt="Profile Picture" />
                </div>
                <input type="file" name="profile-pic" id="profile-pic" class="input-file" accept="image/*" onchange="previewFile()" />
                <div class="drag-overlay" id="drag-overlay">
                    <p>Drop your image here</p>
                </div>
            </div>
            <div>
                <div class="input-subcontainer">
                    <input type="text" name="employee_name" value="<?= $_user->employee_name ?? '' ?>" class="input-box" spellcheck="false" />
                    <label for="employee_name" class="label">Username</label>
                </div>
                <div class="input-subcontainer">
                    <input type="text" name="email" value="<?= $_user->email ?? '' ?>" class="input-box" spellcheck="false" />
                    <label for="email" class="label">Email</label>
                </div>
                <div class="input-subcontainer">
                    <input name="role" value="<?= $_user->role ?? '' ?>" class="input-box" spellcheck="false" readonly />
                    <label for="role" class="label">Role</label>
                </div>

                <button class="btn" type="submit">Save</button>
            </div>
        </form>
    </div>

    <div class="content" id="change-password-content" style="display: none;">
        <h2>Change Password</h2>
        <form id="change-password-container" action="" method="post">
            <input type="hidden" name="form_type" value="change_password" />
            <div class="input-subcontainer">
                <input type="password" name="old-password" id="old-password" class="input-box" spellcheck="false" />
                <label for="old-password" class="label">Old Password</label>
                <i class="ti ti-eye-off" id="toggleOldPassword"></i>
            </div>
            <div class="input-subcontainer">
                <input type="password" name="new-password" id="new-password" class="input-box" spellcheck="false" />
                <label for="new-password" class="label">New Password</label>
                <i class="ti ti-eye-off" id="toggleNewPassword"></i>
            </div>
            <div class="input-subcontainer">
                <input type="password" name="confirm-password" id="confirm-password" class="input-box" spellcheck="false"/>
                <label for="confirm-password" class="label">Confirm Password</label>
                <i class="ti ti-eye-off" id="toggleConfirmPassword"></i>
            </div>
            <button class="btn" type="submit">Save</button>
        </form>

    </div>
</div>
<script>
function confirmLogOut() {
        return confirm('Are you sure you want to logout?');
    }
    </script>
    
<script src="../js/profileSidebar.js"></script>
<script src="../js/imageDragAndDrop.js"></script>
<script src="../js/inputHasContent.js"></script>
<script src="../js/showPassword.js"></script>