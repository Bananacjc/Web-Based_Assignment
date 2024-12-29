<?php
$_title = 'Profile';
$_css = '../css/profile.css';
require '../_base.php';
include '../_head.php';

require_login();
reset_user();

$activeTab = $_GET['activeTab'] ?? 'personal-info-btn';

// Handle logout directly if the logout query parameter is set
if (isset($_GET['logout'])) {
    logout('/page/login.php'); // Call the logout function and redirect to the login page
}

if (is_post()) {
    $formType = post('form_type'); // Fetch the hidden input field
    if (isset($_POST['request_otp'])) { // Handle OTP request
        header('Content-Type: text/plain'); // Set response type to plain text
        ob_clean(); // Clear any output buffer before sending the response
        try {
            $email = trim($_POST['email']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo 'Invalid email format.';
                exit;
            }

            // Generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_email'] = $email;

            $m = get_mail();
            $m->addAddress($email);
            $m->isHTML(true);
            $m->Subject = 'Your OTP for Email Verification';
            $m->Body = "<p>Your OTP is <b>$otp</b>. Please use it to verify your email address update.</p>";

            if ($m->send()) {
                echo 'Success';
            } else {
                echo 'Failed to send OTP.';
            }
        } catch (Exception $e) {
            echo 'Unexpected server error occurred.';
        }
        exit; // Stop further execution
    } elseif ($formType === 'personal_info') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $otpEntered = trim(post('otp'));
        $profilePic = get_file('profile-pic');

        if (!$username || !$email || !$phone) {
            temp('popup-msg', ['msg' => 'All fields are required.', 'isSuccess' => false]);
            redirect();
        }

        if (!is_email($email)) {
            temp('popup-msg', ['msg' => 'Invalid email format.', 'isSuccess' => false]);
            redirect();
        }

        if (!preg_match('/^01[0-9]-?\d{7,8}$/', $contact_num)) {
            temp('popup-msg', ['msg' => 'Invalid Malaysian contact number format. It should start with "01" followed by 7-8 digits.', 'isSuccess' => false]);
            redirect();
        }

        if ($email !== $_user->email) { // Email update requires OTP verification
            if (empty($otpEntered)) {
                temp('popup-msg', ['msg' => 'Please enter the OTP sent to your email.', 'isSuccess' => false]);
                redirect();
            }

            if (!isset($_SESSION['otp']) || $_SESSION['otp_email'] !== $email) {
                temp('popup-msg', ['msg' => 'No OTP found or mismatch. Please request OTP again.', 'isSuccess' => false]);
                redirect();
            }

            if ($otpEntered != $_SESSION['otp']) {
                temp('popup-msg', ['msg' => 'Invalid OTP. Please check your email.', 'isSuccess' => false]);
                redirect();
            }

            // Clear OTP session on successful verification
            unset($_SESSION['otp']);
            unset($_SESSION['otp_email']);
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
        $googleMapsApiKey = "AIzaSyBFPpOlKxMJuu6PxnVrwxNd1G6vERpptro";

        if ($action === 'save-address') {
            // Fetch structured address inputs
            $line_1 = trim(post('line_1'));
            $village = trim(post('village'));
            $postal_code = trim(post('postal_code'));
            $city = trim(post('city'));
            $state = trim(post('state'));

            // Validate required fields
            if (!$line_1 || !$postal_code || !$city || !$state) {
                temp('popup-msg', ['msg' => 'Please fill in all required fields.', 'isSuccess' => false]);
                redirect();
            }

            // Create structured address
            $newAddress = [
                'line_1' => $line_1,
                'village' => $village,
                'postal_code' => $postal_code,
                'city' => $city,
                'state' => $state,
            ];

            // Validate the address with Google Maps
            if (!validate_address_with_google($newAddress, $googleMapsApiKey)) {
                temp('popup-msg', ['msg' => 'Invalid address. Please check the details.', 'isSuccess' => false]);
                redirect();
            }

            // Check for duplicate address
            foreach ($addresses as $address) {
                if (
                    $address['line_1'] === $newAddress['line_1'] &&
                    $address['village'] === $newAddress['village'] &&
                    $address['postal_code'] === $newAddress['postal_code'] &&
                    $address['city'] === $newAddress['city'] &&
                    $address['state'] === $newAddress['state']
                ) {
                    temp('popup-msg', ['msg' => 'This address already exists.', 'isSuccess' => false]);
                    redirect();
                }
            }

            // Add the new address
            $addresses[] = $newAddress;
            $addressesJson = json_encode($addresses);

            $stmt = $_db->prepare("UPDATE customers SET addresses = ? WHERE customer_id = ?");
            $stmt->execute([$addressesJson, $_user->customer_id]);

            $_user->addresses = $addressesJson;
            temp('popup-msg', ['msg' => 'Address added successfully.', 'isSuccess' => true]);
            redirect();
        } elseif ($action === 'edit-address' && is_numeric($index)) {
            $line_1 = trim(post('line_1'));
            $village = trim(post('village'));
            $postal_code = trim(post('postal_code'));
            $city = trim(post('city'));
            $state = trim(post('state'));

            if ($line_1 && $postal_code && $city && $state && isset($addresses[$index])) {
                $updatedAddress = [
                    'line_1' => $line_1,
                    'village' => $village,
                    'postal_code' => $postal_code,
                    'city' => $city,
                    'state' => $state,
                ];

                // Validate the address with Google Maps
                if (!validate_address_with_google($updatedAddress, $googleMapsApiKey)) {
                    temp('popup-msg', ['msg' => 'Invalid address. Please check the details.', 'isSuccess' => false]);
                    redirect();
                }

                // Check for duplicate address (excluding the current one being updated)
                foreach ($addresses as $i => $address) {
                    if (
                        $i !== (int)$index &&
                        $address['line_1'] === $updatedAddress['line_1'] &&
                        $address['village'] === $updatedAddress['village'] &&
                        $address['postal_code'] === $updatedAddress['postal_code'] &&
                        $address['city'] === $updatedAddress['city'] &&
                        $address['state'] === $updatedAddress['state']
                    ) {
                        temp('popup-msg', ['msg' => 'This address already exists.', 'isSuccess' => false]);
                        redirect();
                    }
                }

                // Update the address
                $addresses[$index] = $updatedAddress;
                $addressesJson = json_encode($addresses);

                $stmt = $_db->prepare("UPDATE customers SET addresses = ? WHERE customer_id = ?");
                $stmt->execute([$addressesJson, $_user->customer_id]);

                $_user->addresses = $addressesJson;
                temp('popup-msg', ['msg' => 'Address updated successfully.', 'isSuccess' => true]);
            } else {
                temp('popup-msg', ['msg' => 'Invalid address update.', 'isSuccess' => false]);
            }
            redirect();
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
            redirect();
        }
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

<script src="../js/profileSidebar.js"></script>
<h1 class="h1 header-banner">Profile</h1>
<div id="profile-container">
    <div class="sidebar">
        <ul>
            <li id="personal-info-btn" class="<?= $activeTab === 'personal-info-btn' ? 'active' : '' ?>"><i class="ti ti-user"></i> Personal Info</li>
            <li id="payment-method-btn" class="<?= $activeTab === 'payment-method-btn' ? 'active' : '' ?>"><i class="ti ti-credit-card"></i> Payment Method</li>
            <li id="address-btn" class="<?= $activeTab === 'address-btn' ? 'active' : '' ?>"><i class="ti ti-map-pins"></i>Address</li>
            <li id="promotion-btn" class="<?= $activeTab === 'promotion-btn' ? 'active' : '' ?>"><i class="ti ti-map-pins"></i>My Promotions</li>
            <li id="order-history-btn" class="<?= $activeTab === 'order-history-btn' ? 'active' : '' ?>"><i class="ti ti-shopping-cart"></i> Order and Reviews</li>
            <li id="change-password-btn" class="<?= $activeTab === 'change-password-btn' ? 'active' : '' ?>"><i class="ti ti-lock"></i> Change Password</li>
            <li id="logout-btn"><a href="?logout=true" id="logout-link"><i class="ti ti-logout"></i>Logout</a></li>
        </ul>
    </div>
    <div class="content" id="personal-info-content" style="display: <?= $activeTab === 'personal-info-btn' ? 'block' : 'none' ?>;">
        <h2>Personal Info</h2>
        <form id="personal-info-container" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="activeTab" value="<?= $_GET['activeTab'] ?? 'personal-info-btn' ?>">
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
            <div id="personal-info-input-container">
                <div class="input-subcontainer">
                    <input type="text" name="username" value="<?= $_user->username ?? '' ?>" class="input-box" spellcheck="false" required />
                    <label for="username" class="label">Username</label>
                </div>
                <div class="input-subcontainer">
                    <input type="text" name="email" id="email" value="<?= $_user->email ?? '' ?>" class="input-box" spellcheck="false" required />
                    <label for="email" class="label">Email</label>
                </div>
                <div class="d-flex justify-content-space-around">
                    <div class="input-subcontainer">
                        <input type="text" name="otp" class="input-box" spellcheck="false" placeholder=" " />
                        <label for="otp" class="label">OTP</label>
                    </div>
                    <button type="button" id="request-otp-btn">Request OTP</button>
                </div>
                <div class="input-subcontainer">
                    <input type="text" name="phone" value="<?= $_user->contact_num ?? '' ?>" class="input-box" spellcheck="false" required />
                    <label for="phone" class="label">Phone</label>
                </div>
                <button class="btn" type="submit">Save</button>
            </div>
        </form>
    </div>
    <div class="content" id="payment-method-content" style="display: <?= $activeTab === 'payment-method-btn' ? 'block' : 'none' ?>;">
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
                <input type="hidden" name="activeTab" value="<?= $_GET['activeTab'] ?? 'personal-info-btn' ?>">
                <input type="hidden" name="form_type" value="bank_management" />
                <input type="hidden" name="action" id="bank-action" value="save-bank" />
                <input type="hidden" name="index" id="bank-index" value="" />
                <div class="input-subcontainer">
                    <input type="text" name="acc-num" id="bank-account-input" class="input-box" placeholder=" " required />
                    <label for="acc-num" class="label">Account Number</label>
                </div>
                <div class="input-subcontainer">
                    <input type="text" name="cvv" id="bank-cvv-input" class="input-box" placeholder=" " required />
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
    <div class="content" id="address-content" style="display: <?= $activeTab === 'address-btn' ? 'block' : 'none' ?>;">
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
                    // Concatenate the address fields for display
                    $formattedAddress = "{$address['line_1']}";
                    if (!empty($address['village'])) {
                        $formattedAddress .= ", {$address['village']}";
                    }
                    $formattedAddress .= ", {$address['postal_code']} {$address['city']}, {$address['state']}";

                    echo "<tr>
                            <td>" . ($index + 1) . "</td>
                            <td class='address-text'>$formattedAddress</td>
                            <td class='text-center'>
                                <button class='btn edit-address-btn' data-index='$index' 
                                    data-line1='{$address['line_1']}'
                                    data-village='{$address['village']}'
                                    data-postalcode='{$address['postal_code']}'
                                    data-city='{$address['city']}'
                                    data-state='{$address['state']}'>
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
        <form id="address-form" action="" method="post" class="d-flex">
            <input type="hidden" name="activeTab" value="<?= $_GET['activeTab'] ?? 'personal-info-btn' ?>">
            <input type="hidden" name="form_type" value="address_management" />
            <input type="hidden" name="action" id="action" value="save-address" />
            <input type="hidden" name="index" id="address-index" value="" />

            <div>
                <div class="input-subcontainer">
                    <input id="line-1" type="text" name="line_1" class="input-box" placeholder=" " required />
                    <label for="line-1" class="label">Address Line 1</label>
                </div>
                <div class="input-subcontainer">
                    <input id="village" type="text" name="village" class="input-box" placeholder=" " />
                    <label for="village" class="label">Village</label>
                </div>
                <div class="input-subcontainer">
                    <input id="postal-code" type="text" name="postal_code" class="input-box" placeholder=" " required />
                    <label for="postal-code" class="label">Postal Code</label>
                </div>
                <div class="input-subcontainer">
                    <input id="city" type="text" name="city" class="input-box" placeholder=" " required />
                    <label for="city" class="label">City</label>
                </div>
                <div class="input-subcontainer" style="position: relative;">
                    <input id="state" type="text" name="state" class="input-box" placeholder=" " required />
                    <label for="state" class="label">State</label>
                </div>
                <button class="btn" type="submit" id="save-address-btn">Add Address</button>
            </div>
            <div id="map-container">
                <div id="map" style="width: 100%; height: 300px; margin-top: 20px;"></div>
                <button class="btn" id="use-my-location-btn"><i class="ti ti-map-pin"></i>Use My Location</button>
                <div id="coordinates">
                    <p>Latitude: <span id="latitude">0</span></p>
                    <p>Longitude: <span id="longitude">0</span></p>
                </div>
            </div>
        </form>
    </div>
    <?php
    $customer_promo_stmt = $_db->prepare('SELECT promotion_records FROM customers WHERE customer_id = ?');
    $customer_promo_stmt->execute([$_user->customer_id]);
    $promotionRecords = json_decode($customer_promo_stmt->fetchColumn(), true);
    $promo_stmt = $_db->prepare('SELECT * FROM promotions WHERE promo_id = ?');

    ?>

    <div class="content" id="promotion-content" style="display: <?= $activeTab === 'promotion-btn' ? 'block' : 'none' ?>;">
        <h2>Collected Promotions</h2>
        <link rel="stylesheet" href="../css/promotion.css">
        <?php if (!empty($promotionRecords)): ?>
            <?php foreach ($promotionRecords as $promoID => $promoDetails): ?>
                <?php
                $promo_stmt->execute([$promoID]);
                $promo = $promo_stmt->fetch(PDO::FETCH_OBJ);

                if (!$promo) {
                    continue;
                }

                $today = new DateTime();
                $promoStart = new DateTime($promo->start_date);
                $promoEnd = new DateTime($promo->end_date);

                $upcoming = $today < $promoStart;
                $expired = $today > $promoEnd;

                if ($upcoming || $expired) {
                    continue;
                }
                ?>
                <div class="promo-card">
                    <div class="promo-image">
                        <img src="../uploads/promo_images/<?= htmlspecialchars($promo->promo_image) ?>" alt="<?= htmlspecialchars($promo->promo_name) ?>">
                    </div>
                    <div class="promo-details">
                        <table class="promo-details-table">
                            <thead>
                                <tr>
                                    <th colspan="3">
                                        <h2><?= htmlspecialchars($promo->promo_name) ?></h2>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>Code</th>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($promo->promo_code) ?></td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($promo->description) ?></td>
                                </tr>
                                <tr>
                                    <th>Requirement</th>
                                    <td>:</td>
                                    <td><?= $promo->requirement == 0 ? 'None' : 'Minimum Purchase of RM ' . $promo->requirement ?></td>
                                </tr>
                                <tr>
                                    <th>Discount</th>
                                    <td>:</td>
                                    <td>RM <?= $promo->promo_amount ?></td>
                                </tr>
                                <tr>
                                    <th>Start Date</th>
                                    <td>:</td>
                                    <td><?= $promoStart->format('d-M-Y h:i:s A') ?></td>
                                </tr>
                                <tr>
                                    <th>End Date</th>
                                    <td>:</td>
                                    <td><?= $promoEnd->format('d-M-Y h:i:s A') ?></td>
                                </tr>
                                <tr>
                                    <th>Usage Left</th>
                                    <td>:</td>
                                    <td><?= $promoDetails['promoLimit'] ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">
                                        <button class="promo-btn claimed">Claimed</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <?php endforeach ?>
        <?php endif ?>
    </div>

    <?php
    $orderHistory = []; // Initialize an empty array for order history

    // Fetch order history for the logged-in user
    $stmt = $_db->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_time DESC");
    $stmt->execute([$_user->customer_id]);
    $orderHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="content" id="order-history-content" style="display: <?= $activeTab === 'order-history-btn' ? 'block' : 'none' ?>;">
        <h2>Order History</h2>
        <table class="table">
            <thead>
                <tr>
                    <th class="order-id-header">ORDER ID</th>
                    <th class="date-header">DATE</th>
                    <th class="time-header">TIME</th>
                    <th class="total-price-header">TOTAL (RM)</th>
                    <th class="payment-method-header">PAYMENT METHOD</th>
                    <th class="delivery-status-header">STATUS</th>
                    <th class="action-header">ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderHistory as $order):
                    $paymentMethodRaw = $order['payment_method'];
                    $paymentDisplay = '';
                    $paymentIcon = '';

                    // Decode order items and check review status
                    $orderItems = json_decode($order['order_items'], true);
                    $allReviewed = true;

                    foreach ($orderItems as $productID => $quantity) {
                        $stmt = $_db->prepare("SELECT COUNT(*) FROM reviews WHERE customer_id = ? AND product_id = ?");
                        $stmt->execute([$_user->customer_id, $productID]);
                        $isReviewed = $stmt->fetchColumn() > 0;

                        if (!$isReviewed) {
                            $allReviewed = false;
                            break;
                        }
                    }

                    // Check if payment method is a JSON object or plain text
                    $decodedMethod = json_decode($paymentMethodRaw, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedMethod)) {
                        // Handle bank payment method
                        if (isset($decodedMethod['accNum'])) {
                            $cardLastFour = substr($decodedMethod['accNum'], -4);
                            $paymentDisplay = "***$cardLastFour";
                            $paymentIcon = "../images/card.svg";
                        }
                    } else {
                        // Handle other payment methods (plain text)
                        $paymentMethodText = trim($paymentMethodRaw, '"'); // Remove surrounding quotes if any
                        switch (strtolower($paymentMethodText)) {
                            case 'fpx':
                                $paymentIcon = "../images/fpx.svg";
                                break;
                            case 'grabpay':
                                $paymentIcon = "../images/grabpay.svg";
                                break;
                            case 'alipay':
                                $paymentIcon = "../images/alipay.svg";
                                break;
                            case 'link':
                                $paymentIcon = "../images/link.svg";
                                break;
                            default:
                                $paymentIcon = ""; // Default to no icon
                                break;
                        }
                        $paymentDisplay = ucfirst($paymentMethodText); // Capitalize first letter
                    }
                ?>
                    <tr>
                        <td>
                            <p class="order-id"><?= $order['order_id'] ?></p>
                        </td>
                        <td>
                            <p class="date"><?= date('d M Y', strtotime($order['order_time'])) ?></p>
                        </td>
                        <td>
                            <p class="time"><?= date('h:i A', strtotime($order['order_time'])) ?></p>
                        </td>
                        <td class="total-price"><?= number_format($order['total'], 2) ?></td>
                        <td class="payment-method text-center">
                            <?php if ($paymentIcon): ?>
                                <img src="<?= $paymentIcon ?>" alt="<?= $paymentDisplay ?>" style="width: 24px; vertical-align: middle;" />
                            <?php endif; ?>
                            <span><?= $paymentDisplay ?></span>
                        </td>
                        <td class="delivery-status"><?= $order['status'] ?></td>
                        <td class="action">
                            <a class="receiptbtn" href="receipt.php?order_id=<?= urlencode($order['order_id']) ?>">
                                <span>View Receipt</span>
                            </a>
                            <?php if ($order['status'] === 'DELIVERED'): ?>
                                <?php if ($allReviewed): ?>
                                    <a class="reviewbtn" href="review.php?order_id=<?= urlencode($order['order_id']) ?>">
                                        <span>Reviewed&nbsp;&nbsp;</span><i class="ti ti-check"></i>
                                    </a>
                                <?php else: ?>
                                    <a class="reviewbtn" href="review.php?order_id=<?= urlencode($order['order_id']) ?>">
                                        <span>Review&nbsp;&nbsp;</span><i class="ti ti-circle-filled"></i>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="content" id="change-password-content" style="display: <?= $activeTab === 'change-password-btn' ? 'block' : 'none' ?>;">
        <h2>Change Password</h2>
        <form id="change-password-container" action="" method="post">
            <input type="hidden" name="activeTab" value="<?= $_GET['activeTab'] ?? 'personal-info-btn' ?>">
            <input type="hidden" name="form_type" value="change_password" />
            <div class="input-subcontainer">
                <input type="password" name="old-password" id="old-password" class="input-box" spellcheck="false" placeholder=" " required />
                <label for="old-password" class="label">Old Password</label>
                <i class="ti ti-eye-off" id="toggleOldPassword"></i>
            </div>
            <div class="input-subcontainer">
                <input type="password" name="new-password" id="new-password" class="input-box" spellcheck="false" placeholder=" " required />
                <label for="new-password" class="label">New Password</label>
                <i class="ti ti-eye-off" id="toggleNewPassword"></i>
            </div>
            <div class="input-subcontainer">
                <input type="password" name="confirm-password" id="confirm-password" class="input-box" spellcheck="false" placeholder=" " required />
                <label for="confirm-password" class="label">Confirm Password</label>
                <i class="ti ti-eye-off" id="toggleConfirmPassword"></i>
            </div>
            <button class="btn" type="submit">Save</button>
        </form>

    </div>
    <!-- Add other content divs similarly with display: none; -->
</div>
<script src="../js/imageDragAndDrop.js"></script>
<script src="../js/inputHasContent.js"></script>
<script src="../js/showPassword.js"></script>
<script src="../js/paymentMethodManagement.js"></script>
<script src="../js/addressManagement.js"></script>
<script src="../js/requestOTP.js"></script>
<script src="../js/googleMap.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFPpOlKxMJuu6PxnVrwxNd1G6vERpptro&libraries=places"></script>


<?php include '../_foot.php'; ?>