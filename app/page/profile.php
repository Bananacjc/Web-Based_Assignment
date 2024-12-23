<?php
$_title = 'Profile';
$_css = '../css/profile.css';
require '../_base.php';
include '../_head.php';

require_login();
reset_user();

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
            <li id="logout-btn"><a href="LogoutServlet" id="logout-link"><i class="ti ti-logout"></i>Logout</a></li>
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
                    <input type="text" name="username" value="<?= $_user->username ?? '' ?>" class="input-box" spellcheck="false" />
                    <label for="username" class="label">Username</label>
                </div>
                <div class="input-subcontainer">
                    <input type="text" name="email" value="<?= $_user->email ?? '' ?>" class="input-box" spellcheck="false" />
                    <label for="email" class="label">Email</label>
                </div>
                <div class="input-subcontainer">
                    <input type="text" name="phone" value="<?= $_user->contact_num ?? '' ?>" class="input-box" spellcheck="false" />
                    <label for="phone" class="label">Phone</label>
                </div>
                <button class="btn" type="submit">Save</button>
            </div>
        </form>
    </div>
    <div class="content" id="payment-method-content" style="display: none;">
        <div id="payment-method-container" style="display: flex;">
            <!-- Bank form -->
            <form id="bank-container" action="BankServlet" method="post">
                <div>
                    <h2>Bank</h2>
                    <div class="input-subcontainer">
                        <input type="text" name="name" value="Public Bank" class="input-box" spellcheck="false" required />
                        <label for="name" class="label">Name</label>
                    </div>
                    <div class="input-subcontainer">
                        <input type="text" name="acc-num" value="1234567890" class="input-box" spellcheck="false" required />
                        <label for="acc-num" class="label">Account Number</label>
                    </div>
                    <div class="input-subcontainer">
                        <input type="text" name="cvv" value="123" class="input-box" spellcheck="false" required />
                        <label for="cvv" class="label">CVV</label>
                    </div>
                    <div class="input-subcontainer">
                        <label for="expiry-date" class="normal-label">Expiry Date</label>
                        <input type="month" name="expiry-date" value="" spellcheck="false" id="expiry-date-input" required />
                    </div>
                    <div class="input-subcontainer">
                        <label for="card-type" class="normal-label">Card Type</label>
                        <select name="card-type" id="card-type">
                            <option value="">Select a card type</option>
                            <option value="visa" ${bank !=null && 'visa' .equals(bank.cardType) ? 'selected' : '' }>Visa</option>
                            <option value="mastercard" ${bank !=null && 'mastercard' .equals(bank.cardType) ? 'selected' : '' }>MasterCard</option>
                        </select>
                    </div>
                    <button class="btn" type="submit">Save</button>
                </div>
            </form>
            <!-- E-Wallet form -->
            <form id="e-wallet-container" style="margin-left: 50px;" action="EwalletServlet" method="post">
                <div>
                    <h2>E-Wallet</h2>
                    <div class="input-subcontainer">
                        <input type="text" name="name" value="TouchNGo" class="input-box" spellcheck="false" required />
                        <label for="name" class="label">Name</label>
                    </div>
                    <div class="input-subcontainer">
                        <input type="text" name="phone" value="+601163985186" class="input-box" spellcheck="false" required />
                        <label for="phone" class="label">Phone</label>
                    </div>
                    <button class="btn" type="submit">Save</button>
                </div>
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
                <button class='btn edit-address-btn' data-index='$index' title='Edit Address'>
                    <i class='ti ti-edit'></i>
                </button>
                <button class='btn delete-address-btn' data-index='$index' title='Delete Address'>
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
                <input type="text" name="address" id="address-input" class="input-box" spellcheck="false" />
                <label for="address" class="label">New Address</label>
            </div>
            <button class="btn" type="submit" id="save-address-btn">Add Address</button>
        </form>
        <div id="map" style="height: 400px; width: 100%;"></div>
        <input type="text" id="autocomplete" placeholder="Type your address" class="input-box" />
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
        <button class="btn" id="confirm-address-btn">Confirm Address</button>
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
<script src="../js/imageDragAndDrop.js"></script>
<script src="../js/inputHasContent.js"></script>
<script src="../js/showPassword.js"></script>
<script src="../js/addressManagement.js"></script>

<?php include '../_foot.php'; ?>