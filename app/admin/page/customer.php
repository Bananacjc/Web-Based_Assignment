<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../css/customer.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Customer Management</title>
</head>

<?php
include 'adminHeader.php';

$fields = [
    '',
    'customer_id' => 'Customer ID',
    'username' => 'Username',
    'email' => 'Email',
    'password'=> 'Password',
    'contact_num' => 'Contact Number',
    'banks' => 'Banks',
    'ewallets' => 'E-wallets',
    'addresses' => 'Addresses',
    'cart' => 'Cart',
    'promotion_records' => 'Promotion Records',
    'profile_image' => 'Profile Image',
    'Action'
];
$sort = req('sort');
$valid_sort_fields = ['customer_id', 'username', 'email', 'contact_num', 'banks', 'ewallets', 'addresses', 'cart', 'promotion_records', 'profile_image'];
if (!in_array($sort, $valid_sort_fields)) {
    $sort = 'customer_id'; // Default sort
}

$dir = req('dir');
if (!in_array($dir, ['asc', 'desc'])) {
    $dir = 'asc'; // Default direction
}

$username = req('username');

// Make sure there's a space before 'BY'
$query = "SELECT customer_id, username, email, password,contact_num, banks, ewallets, addresses, cart, promotion_records, profile_image FROM customers WHERE username LIKE ? ORDER BY $sort $dir";

$stm = $_db->prepare($query);
$stm->execute(["%$username%"]);
$customers = $stm->fetchAll();

?>

<div class="main">
    <h1>CUSTOMER MANAGEMENT</h1>
    <form>
        <?= html_search('username', 'Search Customer Name', $username) ?>
        <button>Search</button>
    </form>

    <form method="post" id="f">
        <button formaction="deleteCustomer.php" onclick="return confirmDelete()">Delete</button>
    </form>

    <p><?= count($customers) ?> customer(s)</p>

    <table id="customerTable" class="data-table">
        <thead>
            <tr>
                <?= table_headers($fields, $sort, $dir) ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $c): ?>
                <tr>
                    <td>
                        <input type="checkbox"
                            name="id[]"
                            value="<?= $c->customer_id ?>"
                            form="f">
                    </td>
                    <td><?= $c->customer_id ?></td>
                    <td><?= $c->username ?></td>
                    <td><?= $c->email ?></td>
                    <td><?= $c->password ?></td>
                    <td><?= $c->contact_num ?></td>
                    <td><?= plainTextJson($c->banks) ?></td>
                    <td><?= plainTextJson($c->ewallets) ?></td>
                    <td><?= plainTextJson($c->addresses) ?></td>
                    <td><?= plainTextJson($c->cart) ?></td>
                    <td><?= plainTextJson($c->promotion_records) ?></td>
                    <td>
                        <img src="/uploads/profile_images/<?= $c->profile_image ?>" class="resized-image" alt="Profile Image">
                    </td>
                    <td>
                        <button class="action-button" onclick="showUpdateForm()">Update</button>
                        <form action="deleteCustomer.php" method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $c->customer_id ?>">
                            <button type="submit" class="delete-action-button" onclick="return confirmDelete();">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin: 30px;">
        <button id="addCustomerBtn" class="action-button" onclick="showAddForm()">Add new customer</button>
    </div>

    <!-- Add Customer Modal -->
    <div id="addCustomerModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="hideAddForm()">&times;</span>
            <form id="addForm" action="addCustomer.php" method="POST" enctype="multipart/form-data" class="add-form">
                <h2>Add Customer</h2>
                <input type="hidden" name="action" value="add">

                <label for="username">Username:</label>
                <?php html_text('username', 'required'); ?>
                <span class="error"><?php err('username'); ?></span><br><br>

                <label for="email">Email:</label>
                <?php html_text('email', 'required'); ?>
                <span class="error"><?php err('email'); ?></span><br><br>

                <label for="contact_num">Contact Number:</label>
                <?php html_text('contact_num', 'required'); ?>
                <span class="error"><?php err('contact_num'); ?></span><br><br>

                <label for="banks">Banks:</label>
                <?php html_text('banks'); ?>
                <span class="error"><?php err('banks'); ?></span><br><br>

                <label for="ewallets">E-wallets:</label>
                <?php html_text('ewallets'); ?>
                <span class="error"><?php err('ewallets'); ?></span><br><br>

                <label for="addresses">Addresses:</label>
                <?php html_text('addresses'); ?>
                <span class="error"><?php err('addresses'); ?></span><br><br>

                <label for="cart">Cart (JSON format):</label>
                <?php html_text('cart'); ?>
                <span class="error"><?php err('cart'); ?></span><br><br>

                <label for="promotion_records">Promotion Records (JSON format):</label>
                <?php html_text('promotion_records'); ?>
                <span class="error"><?php err('promotion_records'); ?></span><br><br>

                <label for="profile_image">Profile Image:</label>
                <input type="file" name="profile_image"><br><br>

                <input type="submit" value="Add Customer">
                <button type="button" class="cancel-button" onclick="hideAddForm()">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Update Customer Modal -->
    <div id="updateCustomerModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="hideUpdateForm()">&times;</span>
            <form id="updateForm" action="updateCustomer.php" method="POST" enctype="multipart/form-data" class="update-form">
                <h2>Update Customer</h2>
                <input type="hidden" name="customer_id" id="updateCustomerId">

                <label for="username">Username:</label>
                <?php html_text('username', 'required', 'updateUsername'); ?>
                <span class="error"><?php err('username'); ?></span><br><br>

                <label for="email">Email:</label>
                <?php html_text('email', 'required', 'updateEmail'); ?>
                <span class="error"><?php err('email'); ?></span><br><br>

                <label for="contact_num">Contact Number:</label>
                <?php html_text('contact_num', 'required', 'updateContactNum'); ?>
                <span class="error"><?php err('contact_num'); ?></span><br><br>

                <label for="banks">Banks:</label>
                <?php html_text('banks', 'required', 'updateBanks'); ?>
                <span class="error"><?php err('banks'); ?></span><br><br>

                <label for="ewallets">E-wallets:</label>
                <?php html_text('ewallets', 'required', 'updateEwallets'); ?>
                <span class="error"><?php err('ewallets'); ?></span><br><br>

                <label for="addresses">Addresses:</label>
                <?php html_text('addresses', 'required', 'updateAddresses'); ?>
                <span class="error"><?php err('addresses'); ?></span><br><br>

                <label for="cart">Cart (JSON format):</label>
                <?php html_text('cart', 'required', 'updateCart'); ?>
                <span class="error"><?php err('cart'); ?></span><br><br>

                <label for="promotion_records">Promotion Records (JSON format):</label>
                <?php html_text('promotion_records', 'required', 'updatePromotionRecords'); ?>
                <span class="error"><?php err('promotion_records'); ?></span><br><br>

                <label for="profile_image">Profile Image:</label>
                <input type="file" name="profile_image" id="updateProfileImage"><br><br>

                <input type="submit" value="Update Customer">
            </form>
        </div>
    </div>
</div>

<script>
    function showAddForm() {
        document.getElementById("addCustomerModal").style.display = "block";
    }

    function hideAddForm() {
        document.getElementById("addCustomerModal").style.display = "none";
    }

    function showUpdateForm(customerData) {
        document.getElementById("updateCustomerModal").style.display = "block";

        // Fill the update form with customer data
        document.getElementById("updateCustomerId").value = customerData.customer_id;
        document.getElementById("updateUsername").value = customerData.username;
        document.getElementById("updateEmail").value = customerData.email;
        document.getElementById("updateContactNum").value = customerData.contact_num;
        document.getElementById("updateBanks").value = customerData.banks;
        document.getElementById("updateEwallets").value = customerData.ewallets;
        document.getElementById("updateAddresses").value = customerData.addresses;
        document.getElementById("updateCart").value = customerData.cart;
        document.getElementById("updatePromotionRecords").value = customerData.promotion_records;
        // For profile image, it will be handled separately as it is uploaded from a file
    }

    function hideUpdateForm() {
        document.getElementById("updateCustomerModal").style.display = "none";
    }

    function confirmDelete() {
        return confirm('Are you sure you want to delete this customer?');
    }
</script>
<?php
function plainTextJson($jsonString) {
    $decoded = json_decode($jsonString, true); // Use associative array mode

    // Check if JSON decoding was successful
    if (json_last_error() === JSON_ERROR_NONE) {
        if (is_array($decoded)) {
            // Convert array to a plain string (key-value pairs for associative arrays)
            return implode(", ", array_map(function($key, $value) {
                if (is_array($value)) {
                    // Handle nested arrays
                    return "$key: [" . implode(", ", $value) . "]";
                }
                return "$key: $value";
            }, array_keys($decoded), $decoded));
        } elseif (is_string($decoded)) {
            return $decoded;
        }
    }

    return htmlspecialchars($jsonString); 
}

?>
</html>