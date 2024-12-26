<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../css/customer.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Customer Management</title>
</head>

<?php
include 'adminHeader.php';
require_once '../lib/SimplePager.php'; // Include the SimplePager class

$fields = [
    '',
    'customer_id' => 'Customer ID',
    'username' => 'Username',
    'email' => 'Email',
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
$bannedFilter = req('banned_filter'); // Retrieve the banned filter value
$bannedFilter = in_array($bannedFilter, ['0', '1', 'all']) ? $bannedFilter : 'all'; // Default to 'all'

$whereClause = "WHERE username LIKE ?";
$params = ["%$username%"];
if ($bannedFilter === '0' || $bannedFilter === '1') {
    $whereClause .= " AND banned = ?";
    $params[] = $bannedFilter;
}

// Pagination settings
$page = req('page', 1);
$limit = 10; // Number of items per page
$offset = ($page - 1) * $limit;

// Count total records for pagination
$countQuery = "SELECT COUNT(*) AS total FROM customers $whereClause";
$countStmt = $_db->prepare($countQuery);
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Fetch the records for the current page
$query = "SELECT customer_id, username, email, contact_num, banks, ewallets, addresses, cart, promotion_records, profile_image, banned 
          FROM customers 
          $whereClause 
          ORDER BY $sort $dir
          LIMIT $limit OFFSET $offset";

$stm = $_db->prepare($query);
$stm->execute($params);
$customers = $stm->fetchAll();

?>

<div class="main">
    <h1>CUSTOMER MANAGEMENT</h1>
    <form>
        <?= html_search('username', '', 'Search Customer Name') ?>
        <select name="banned_filter" id="bannedFilter">
            <option value="all" <?= $bannedFilter === 'all' ? 'selected' : '' ?>>All</option>
            <option value="0" <?= $bannedFilter === '0' ? 'selected' : '' ?>>Active</option>
            <option value="1" <?= $bannedFilter === '1' ? 'selected' : '' ?>>Blocked</option>
        </select>
        <button type="submit">Search</button>
    </form>

    <p><?= count($customers) ?> customer(s) on this page | Total: <?= $totalRecords ?> customer(s)</p>

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
                    <td><?= $c->contact_num ?></td>
                    <td><?= plainTextJson($c->banks) ?></td>
                    <td><?= plainTextJson($c->ewallets) ?></td>
                    <td><?= plainTextJson($c->addresses) ?></td>
                    <td><?= plainTextJson($c->cart) ?></td>
                    <td><?= plainTextJson($c->promotion_records) ?></td>
                    <td>
                        <img src="../uploads/profile_images/<?= $c->profile_image ?>" class="resized-image" alt="Profile Image">
                    </td>
                    <td>
                        <button class="button action-button" onclick="showUpdateCustomerForm(
                            '<?= $c->customer_id ?>', 
                            '<?= $c->username ?>', 
                            '<?= $c->email ?>', 
                            '<?= $c->contact_num ?>', 
                            '<?= plainTextJson($c->banks) ?>', 
                            '<?= plainTextJson($c->ewallets) ?>', 
                            '<?= plainTextJson($c->addresses) ?>', 
                            '<?= plainTextJson($c->cart) ?>', 
                            '<?= plainTextJson($c->promotion_records) ?>'
                        )">
                            Update
                        </button>
                        <form action="deleteCustomer.php" method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $c->customer_id ?>">
                            <button type="submit" class="button delete-action-button" onclick="return confirmDelete();">Delete</button>
                        </form>
                        <?php if ($c->banned == 0): ?>
                            <form action="banCustomer.php" method="POST" style="display:inline;">
                                <input type="hidden" name="customer_id" value="<?= $c->customer_id ?>">
                                <button type="submit" class="button ban-action-button" onclick="confirmBlock()">Ban</button>
                            </form>
                        <?php else: ?>
                            <form action="unbanCustomer.php" method="POST" style="display:inline;">
                                <input type="hidden" name="customer_id" value="<?= $c->customer_id ?>">
                                <button type="submit" class="button unban-action-button" onclick="confirmUnblock()">Unban</button>
                            </form>
                        <?php endif; ?>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=1&username=<?= urlencode($username) ?>&banned_filter=<?= $bannedFilter ?>&sort=<?= $sort ?>&dir=<?= $dir ?>" class="first-page">First</a>
            <a href="?page=<?= $page - 1 ?>&username=<?= urlencode($username) ?>&banned_filter=<?= $bannedFilter ?>&sort=<?= $sort ?>&dir=<?= $dir ?>" class="prev-page">Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&username=<?= urlencode($username) ?>&banned_filter=<?= $bannedFilter ?>&sort=<?= $sort ?>&dir=<?= $dir ?>"
                class="page-number <?= $i == $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&username=<?= urlencode($username) ?>&banned_filter=<?= $bannedFilter ?>&sort=<?= $sort ?>&dir=<?= $dir ?>" class="next-page">Next</a>
            <a href="?page=<?= $totalPages ?>&username=<?= urlencode($username) ?>&banned_filter=<?= $bannedFilter ?>&sort=<?= $sort ?>&dir=<?= $dir ?>" class="last-page">Last</a>
        <?php endif; ?>
    </div>


    <div style="margin: 30px;">
        <button id="addCustomerBtn" class="add-button" onclick="showAddForm()">Add new customer</button>
    </div>

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

                <label for="banks">Banks :</label>
                <?php html_text('banks'); ?>
                <span class="error"><?php err('banks'); ?></span><br><br>

                <label for="ewallets">E-wallets :</label>
                <?php html_text('ewallets'); ?>
                <span class="error"><?php err('ewallets'); ?></span><br><br>

                <label for="addresses">Addresses :</label>
                <?php html_text('addresses'); ?>
                <span class="error"><?php err('addresses'); ?></span><br><br>

                <label for="cart">Cart :</label>
                <?php html_text('cart'); ?>
                <span class="error"><?php err('cart'); ?></span><br><br>

                <label for="promotion_records">Promotion Records :</label>
                <?php html_text('promotion_records'); ?>
                <span class="error"><?php err('promotion_records'); ?></span><br><br>

                <label for="profile_image">Profile Image:</label>
                <input type="file" name="profile_image" id="updateProfileImage"><br><br>

                <input type="submit" value="Add Customer">
                <button type="button" class="cancel-button" onclick="hideUpdateProductForm()">Cancel</button>
            </form>
        </div>
    </div>


    <div id="updateCustomerModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="hideUpdateForm()">&times;</span>
            <form id="updateForm" action="updateCustomer.php" method="POST" enctype="multipart/form-data" class="update-form">
                <h2>Update Customer</h2>

                <label for="customer_id">Customer ID:</label>
                <input id="customer_id" name="customer_id" value="" readonly>
                <br>

                <label for="username">Username:</label>
                <?php html_text('username', 'required'); ?>
                <span class="error"><?php err('username'); ?></span><br><br>

                <label for="email">Email:</label>
                <?php html_text('email', 'required'); ?>
                <span class="error"><?php err('email'); ?></span><br><br>

                <label for="contact_num">Contact Number:</label>
                <?php html_text('contact_num', 'required'); ?>
                <span class="error"><?php err('contact_num'); ?></span><br><br>

                <label for="banks">Banks :</label>
                <?php html_text('banks'); ?>
                <span class="error"><?php err('banks'); ?></span><br><br>

                <label for="ewallets">E-wallets :</label>
                <?php html_text('ewallets'); ?>
                <span class="error"><?php err('ewallets'); ?></span><br><br>

                <label for="addresses">Addresses :</label>
                <?php html_text('addresses'); ?>
                <span class="error"><?php err('addresses'); ?></span><br><br>

                <label for="cart">Cart :</label>
                <?php html_text('cart'); ?>
                <span class="error"><?php err('cart'); ?></span><br><br>

                <label for="promotion_records">Promotion Records :</label>
                <?php html_text('promotion_records'); ?>
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

    function showUpdateCustomerForm(customerId, username, email, contactNum, banks, ewallets, addresses, cart, promotionRecords) {
        var modal = document.getElementById('updateCustomerModal');
        var form = document.getElementById('updateForm');
        modal.style.display = "block";

        form.elements['customer_id'].value = customerId;
        form.elements['username'].value = username;
        form.elements['email'].value = email;
        form.elements['contact_num'].value = contactNum;
        form.elements['banks'].value = banks;
        form.elements['ewallets'].value = ewallets;
        form.elements['addresses'].value = addresses;
        form.elements['cart'].value = cart;
        form.elements['promotion_records'].value = promotionRecords;
    }


    function hideUpdateForm() {
        document.getElementById("updateCustomerModal").style.display = "none";
    }

    function confirmDelete() {
        return confirm('Are you sure you want to delete this customer?');
    }

    function confirmBlock() {
        return confirm('Are you sure you want to block this customer?');
    }

    function confirmUnblock() {
        return confirm('Are you sure you want to unblock this customer?');
    }
</script>


</html>