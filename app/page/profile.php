<?php
$_title = 'Profile';
$_css = '../css/profile.css';
require '../_base.php';
include '../_head.php';

require_login();
reset_user();

// Handle logout directly if the logout query parameter is set
if (isset($_GET['logout'])) {
    logout('/page/login.php'); // Call the logout function and redirect to the login page
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = post('form_type'); // Fetch the hidden input field

    if ($formType === 'personal_info') {
        // Handle personal info update
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $profilePic = get_file('profile-pic');

        if (!$username || !$email || !$phone) {
            temp('popup-msg', ['msg' => 'All fields are required.', 'isSuccess' => false]);
            redirect();
        }
        if (!is_email($email)) {
            temp('popup-msg', ['msg' => 'Invalid email format.', 'isSuccess' => false]);
            redirect();
        }

        $profileImage = $_user->profile_image;
        if ($profilePic) {
            $newProfileImage = save_photo($profilePic, '../uploads/customer_images');
            if ($profileImage && $profileImage !== 'guest.png') {
                $oldImagePath = "../uploads/customer_images/$profileImage";
                if (file_exists($oldImagePath)) unlink($oldImagePath);
            }
            $profileImage = $newProfileImage;
        }

        $stmt = $_db->prepare("UPDATE customers SET username = ?, email = ?, contact_num = ?, profile_image = ? WHERE customer_id = ?");
        $success = $stmt->execute([$username, $email, $phone, $profileImage, $_user->customer_id]);

        if ($success) {
            $_user->username = $username;
            $_user->email = $email;
            $_user->contact_num = $phone;
            $_user->profile_image = $profileImage;

            temp('popup-msg', ['msg' => 'Profile updated successfully.', 'isSuccess' => true]);
        } else {
            temp('popup-msg', ['msg' => 'Failed to update profile.', 'isSuccess' => false]);
        }
        redirect();
    } elseif ($formType === 'bank_management') {
        $action = post('action');
        $index = post('index');
        $banks = json_decode($_user->banks ?? '[]', true);
    
        if ($action === 'save-bank') {
            // Add a new bank
            $bankData = [
                'accNum' => trim(post('acc-num')),
                'cvv' => trim(post('cvv')),
                'expiry' => trim(post('expiry-date'))
            ];
    
            if (in_array('', $bankData)) {
                temp('popup-msg', ['msg' => 'All fields are required for adding a bank.', 'isSuccess' => false]);
                redirect();
            }
    
            // Check for duplicate account numbers
            foreach ($banks as $bank) {
                if ($bank['accNum'] === $bankData['accNum']) {
                    temp('popup-msg', ['msg' => 'This account number is already added.', 'isSuccess' => false]);
                    redirect();
                }
            }
    
            // Add the bank
            $banks[] = $bankData;
            $banksJson = json_encode($banks);
    
            $stmt = $_db->prepare("UPDATE customers SET banks = ? WHERE customer_id = ?");
            $stmt->execute([$banksJson, $_user->customer_id]);
    
            $_user->banks = $banksJson;
            temp('popup-msg', ['msg' => 'Bank added successfully.', 'isSuccess' => true]);
        } elseif ($action === 'edit-bank' && is_numeric($index)) {
            // Validate expiry date format
            $expiry = trim(post('expiry-date'));
            if (!preg_match('/^\d{4}-\d{2}$/', $expiry)) {
                temp('popup-msg', ['msg' => 'Invalid expiry date format.', 'isSuccess' => false]);
                redirect();
            }
    
            // Check for duplicate account numbers (excluding the current index being edited)
            $newAccNum = trim(post('acc-num'));
            foreach ($banks as $i => $bank) {
                if ($i !== (int)$index && $bank['accNum'] === $newAccNum) {
                    temp('popup-msg', ['msg' => 'This account number is already added.', 'isSuccess' => false]);
                    redirect();
                }
            }
    
            $banks[$index] = [
                'accNum' => $newAccNum,
                'cvv' => trim(post('cvv')),
                'expiry' => $expiry,
            ];
    
            // Update the database
            $banksJson = json_encode($banks);
            $stmt = $_db->prepare("UPDATE customers SET banks = ? WHERE customer_id = ?");
            $stmt->execute([$banksJson, $_user->customer_id]);
    
            $_user->banks = $banksJson;
            temp('popup-msg', ['msg' => 'Bank updated successfully.', 'isSuccess' => true]);
        } elseif ($action === 'delete-bank' && is_numeric($index)) {
            // Delete a bank
            if (isset($banks[$index])) {
                unset($banks[$index]);
                $banks = array_values($banks); // Re-index the array
                $banksJson = json_encode($banks);
    
                $stmt = $_db->prepare("UPDATE customers SET banks = ? WHERE customer_id = ?");
                $stmt->execute([$banksJson, $_user->customer_id]);
    
                $_user->banks = $banksJson;
                temp('popup-msg', ['msg' => 'Bank deleted successfully.', 'isSuccess' => true]);
            } else {
                temp('popup-msg', ['msg' => 'Invalid bank deletion.', 'isSuccess' => false]);
            }
        }
        redirect();
    } elseif ($formType === 'address_management') {
        $action = post('action');
        $index = post('index');
        $addresses = json_decode($_user->addresses ?? '[]', true);

        if ($action === 'save-address') {
            // Add a new address
            $newAddress = trim(post('address'));
            if ($newAddress) {
                $addresses[] = $newAddress;
                $addressesJson = json_encode($addresses);

                $stmt = $_db->prepare("UPDATE customers SET addresses = ? WHERE customer_id = ?");
                $stmt->execute([$addressesJson, $_user->customer_id]);

                $_user->addresses = $addressesJson;
                temp('popup-msg', ['msg' => 'Address added successfully.', 'isSuccess' => true]);
            } else {
                temp('popup-msg', ['msg' => 'Address cannot be empty.', 'isSuccess' => false]);
            }
        } elseif ($action === 'edit-address' && is_numeric($index)) {
            // Edit an existing address
            $newAddress = trim(post('address'));
            if ($newAddress && isset($addresses[$index])) {
                $addresses[$index] = $newAddress;
                $addressesJson = json_encode($addresses);

                $stmt = $_db->prepare("UPDATE customers SET addresses = ? WHERE customer_id = ?");
                $stmt->execute([$addressesJson, $_user->customer_id]);

                $_user->addresses = $addressesJson;
                temp('popup-msg', ['msg' => 'Address updated successfully.', 'isSuccess' => true]);
            } else {
                temp('popup-msg', ['msg' => 'Invalid address update.', 'isSuccess' => false]);
            }
        } elseif ($action === 'delete-address' && is_numeric($index)) {
            // Delete an address
            if (isset($addresses[$index])) {
                unset($addresses[$index]);
                $addresses = array_values($addresses); // Re-index the array
                $addressesJson = json_encode($addresses);

                $stmt = $_db->prepare("UPDATE customers SET addresses = ? WHERE customer_id = ?");
                $stmt->execute([$addressesJson, $_user->customer_id]);

                $_user->addresses = $addressesJson;
                temp('popup-msg', ['msg' => 'Address deleted successfully.', 'isSuccess' => true]);
            } else {
                temp('popup-msg', ['msg' => 'Invalid address deletion.', 'isSuccess' => false]);
            }
        }

        redirect(); // Reload the page to reflect changes
    } elseif ($formType === 'change_password') {
        // Handle change password
        $oldPassword = trim(post('old-password'));
        $newPassword = trim(post('new-password'));
        $confirmPassword = trim(post('confirm-password'));

        // Validate old password
        if (!$oldPassword || !$newPassword || !$confirmPassword) {
            temp('popup-msg', ['msg' => 'All fields are required.', 'isSuccess' => false]);
            redirect();
        }

        if (sha1($oldPassword) !== $_user->password) {
            temp('popup-msg', ['msg' => 'Incorrect old password.', 'isSuccess' => false]);
            redirect();
        }

        // Validate new password complexity
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $newPassword)) {
            temp('popup-msg', [
                'msg' => 'New password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.',
                'isSuccess' => false
            ]);
            redirect();
        }

        // Check if new password matches confirm password
        if ($newPassword !== $confirmPassword) {
            temp('popup-msg', ['msg' => 'New password and confirmation password do not match.', 'isSuccess' => false]);
            redirect();
        }

        // Update the password in the database
        $hashedNewPassword = sha1($newPassword);
        $stmt = $_db->prepare("UPDATE customers SET password = ? WHERE customer_id = ?");
        $success = $stmt->execute([$hashedNewPassword, $_user->customer_id]);

        if ($success) {
            temp('popup-msg', ['msg' => 'Password changed successfully.', 'isSuccess' => true]);
        } else {
            temp('popup-msg', ['msg' => 'Failed to update password. Please try again.', 'isSuccess' => false]);
        }

        redirect();
    }
}
?>
<h1 class="h1 header-banner">Profile</h1>
<div id="profile-container">
    <div class="sidebar">
        <ul>
            <li id="personal-info-btn"><i class="ti ti-user"></i> Personal Info</li>
            <li id="payment-method-btn"><i class="ti ti-credit-card"></i> Payment Method</li>
            <li id="address-btn"><i class="ti ti-map-pins"></i>Address</li>
            <li id="order-history-btn"><i class="ti ti-shopping-cart"></i> Order and Reviews</li>
            <li id="change-password-btn"><i class="ti ti-lock"></i> Change Password</li>
            <li id="logout-btn"><a href="?logout=true" id="logout-link"><i class="ti ti-logout"></i>Logout</a></li>
        </ul>
    </div>
    <div class="content" id="personal-info-content" style="display: block;">
        <h2>Personal Info</h2>
        <form id="personal-info-container" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="form_type" value="personal_info" />
            <div class="input-file-container" id="drop-zone">
                <div class="image-preview-container">
                    <img id="image-preview" src="../uploads/customer_images/<?= $_user->profile_image ?>" alt="Profile Picture" />
                </div>
                <input type="file" name="profile-pic" id="profile-pic" class="input-file" accept="image/*" onchange="previewFile()" />
                <div class="drag-overlay" id="drag-overlay">
                    <p>Drop your image here</p>
                </div>
            </div>
            <div>
                <div class="input-subcontainer">
                    <input type="text" name="username" value="<?= $_user->username ?? '' ?>" class="input-box" spellcheck="false" required />
                    <label for="username" class="label">Username</label>
                </div>
                <div class="input-subcontainer">
                    <input type="text" name="email" value="<?= $_user->email ?? '' ?>" class="input-box" spellcheck="false" required />
                    <label for="email" class="label">Email</label>
                </div>
                <div class="input-subcontainer">
                    <input type="text" name="phone" value="<?= $_user->contact_num ?? '' ?>" class="input-box" spellcheck="false" required />
                    <label for="phone" class="label">Phone</label>
                </div>
                <button class="btn" type="submit">Save</button>
            </div>
        </form>
    </div>
    <div class="content" id="payment-method-content" style="display: none;">
        <div id="payment-method-container">
            <!-- Bank Section -->
            <h2>Bank</h2>
            <table class="table" id="bank-table">
                <thead>
                    <tr>
                        <th class="text-left">#</th>
                        <th class="text-left">Account Number</th>
                        <th class="text-left">CVV</th>
                        <th class="text-left">Expiry Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $banks = json_decode($_user->banks ?? '[]', true);
                    foreach ($banks as $index => $bank) {
                        echo "<tr>
            <td>" . ($index + 1) . "</td>
            <td class='bank-account'>{$bank['accNum']}</td>
            <td class='bank-cvv'>{$bank['cvv']}</td>
            <td class='bank-expiry'>{$bank['expiry']}</td>
            <td class='text-center'>
                <button class='btn edit-bank-btn' data-index='$index'>
                    <i class='ti ti-edit'></i>
                </button>
                <button class='btn delete-bank-btn' data-index='$index'>
                    <i class='ti ti-trash'></i>
                </button>
            </td>
        </tr>";
                    }
                    ?>
                </tbody>
            </table>
            <form id="bank-form" action="" method="post">
                <input type="hidden" name="form_type" value="bank_management" />
                <input type="hidden" name="action" id="bank-action" value="save-bank" />
                <input type="hidden" name="index" id="bank-index" value="" />
                <div class="input-subcontainer">
                    <input type="text" name="acc-num" id="bank-account-input" class="input-box" required />
                    <label for="acc-num" class="label">Account Number</label>
                </div>
                <div class="input-subcontainer">
                    <input type="text" name="cvv" id="bank-cvv-input" class="input-box" required />
                    <label for="cvv" class="label">CVV</label>
                </div>
                <div class="input-subcontainer">
                    <label for="expiry-date" class="normal-label">Expiry Date</label>
                    <input type="month" name="expiry-date" id="expiry-date-input" required />
                </div>
                <button class="btn" type="submit" id="save-bank-btn">Add Bank</button>
            </form>
        </div>
    </div>

    <div class="content" id="address-content" style="display: none;">
        <h2>Addresses</h2>
        <table class="table">
            <thead>
                <tr>
                    <th class="text-left">#</th>
                    <th class="text-left">ADDRESS</th>
                    <th class="text-center">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $addresses = json_decode($_user->addresses ?? '[]', true);
                foreach ($addresses as $index => $address) {
                    echo "<tr data-index='$index'>
            <td>" . ($index + 1) . "</td>
            <td class='address-text'>$address</td>
            <td class='text-center'>
                <button class='btn edit-address-btn' data-index='$index'>
                    <i class='ti ti-edit'></i>
                </button>
                <button class='btn delete-address-btn' data-index='$index'>
                    <i class='ti ti-trash'></i>
                </button>
            </td>
        </tr>";
                }
                ?>
            </tbody>
        </table>
        <form id="address-form" action="" method="post">
            <input type="hidden" name="form_type" value="address_management" />
            <input type="hidden" name="action" id="action" value="save-address" />
            <input type="hidden" name="index" id="address-index" value="" />
            <div class="input-subcontainer" id="address-input-container">
                <input type="text" name="address" id="address-input" class="input-box" spellcheck="false" required />
                <label for="address" class="label">New Address</label>
            </div>
            <button class="btn" type="submit" id="save-address-btn">Add Address</button>
        </form>
    </div>
    <div class="content" id="order-history-content" style="display: none;">
        <h2>Order History</h2>
        <table class="table">
            <thead>
                <tr>
                    <th class="order-id-header">ORDER ID</th>
                    <th class="date-header">DATE</th>
                    <th class="time-header">TIME</th>
                    <th class="total-price-header">TOTAL (RM)</th>
                    <th class="delivery-status-header">STATUS</th>
                    <th class="action-header">ACTION</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <p class="order-id">ORD-20241201-g9hsaP</p>
                    </td>
                    <td>
                        <p class="date">1 Dec 2024</p>
                    </td>
                    <td>
                        <p class="time">12.00pm</p>
                    </td>
                    <td class="total-price">123.45</td>
                    <td class="delivery-status">Delivered</td>
                    <td class="action">
                        <a class="reviewbtn" href=""><span>Review&nbsp;&nbsp;</span><i class="ti ti-circle-filled"></i></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="content" id="change-password-content" style="display: none;">
        <h2>Change Password</h2>
        <form id="change-password-container" action="" method="post">
            <input type="hidden" name="form_type" value="change_password" />
            <div class="input-subcontainer">
                <input type="password" name="old-password" id="old-password" class="input-box" spellcheck="false" required />
                <label for="old-password" class="label">Old Password</label>
                <i class="ti ti-eye-off" id="toggleOldPassword"></i>
            </div>
            <div class="input-subcontainer">
                <input type="password" name="new-password" id="new-password" class="input-box" spellcheck="false" required />
                <label for="new-password" class="label">New Password</label>
                <i class="ti ti-eye-off" id="toggleNewPassword"></i>
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
<script src="../js/imageDragAndDrop.js"></script>
<script src="../js/inputHasContent.js"></script>
<script src="../js/showPassword.js"></script>
<script src="../js/paymentMethodManagement.js"></script>
<script src="../js/addressManagement.js"></script>

<?php include '../_foot.php'; ?>