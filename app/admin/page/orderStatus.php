<?php
include 'adminHeader.php';

$fields = [
    '',
    'order_id' => 'Order ID',
    'customer_id' => 'Customer ID',
    'addresses' => 'Address',
    'order_items' => 'Order Items',
    'order_time' => 'Order Time',
    'status' => 'Order Status',  // Adding the status column
    'Action'
];

$sort = req('sort');
$valid_sort_fields = ['order_id', 'customer_id', 'order_items', 'order_time', 'status'];
if (!in_array($sort, $valid_sort_fields)) {
    $sort = 'order_id'; // Default sort
}

$dir = req('dir');
if (!in_array($dir, ['asc', 'desc'])) {
    $dir = 'asc'; // Default direction
}

$order_id = req('order_id');
$whereClause = "WHERE order_id LIKE ?";
$params = ["%$order_id%"];

$page = req('page', 1);
$limit = 10; // Number of items per page
$offset = ($page - 1) * $limit;

// Count total records for pagination
$countQuery = "SELECT COUNT(*) AS total FROM orders $whereClause";
$countStmt = $_db->prepare($countQuery);
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Fetch the records for the current page
$query = "
    SELECT 
        o.order_id, 
        o.customer_id, 
        o.order_items, 
        o.order_time, 
        o.status, 
        c.addresses
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    $whereClause
    ORDER BY $sort $dir
    LIMIT $limit OFFSET $offset
";

$stm = $_db->prepare($query);
$stm->execute($params);
$orders = $stm->fetchAll();

?>

<div class="main">
    <h1>ORDER MANAGEMENT</h1>
    <form>
        <?= html_search('order_id', 'Search Order ID', $order_id) ?>
        <button type="submit">Search</button>
    </form>

    <p><?= count($orders) ?> order(s) on this page | Total: <?= $totalRecords ?> order(s)</p>

    <table id="orderTable" class="data-table">
        <thead>
            <tr>
                <?= table_headers($fields, $sort, $dir) ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>
                        <input type="checkbox" name="id[]" value="<?= $order->order_id ?>" form="f">
                    </td>
                    <td><?= $order->order_id ?></td>
                    <td><?= $order->customer_id ?></td>
                    <td><?= $order->addresses ?></td>
                    <td><?= plainTextJson($order->order_items) ?></td>
                    <td><?= $order->order_time ?></td>
                    <td><?= $order->status ?></td> <!-- Display order status -->
                    <td>
                        <button class="button action-button" 
                                onclick="showUpdateForm(<?= $order->order_id ?>, '<?= $order->status ?>')">Update</button>
                        <form action="deleteOrder.php" method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $order->order_id ?>">
                            <button type="submit" class="button delete-action-button" onclick="return confirmDelete();">Delete</button>
                        </form>
                        <form action="updateOrderStatus.php" method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?= $order->order_id ?>">
                            <select name="status">
                                <option value="PENDING" <?= $order->status === 'PENDING' ? 'selected' : '' ?>>Pending</option>
                                <option value="PAID" <?= $order->status === 'PAID' ? 'selected' : '' ?>>Paid</option>
                                <option value="SHIPPING" <?= $order->status === 'SHIPPING' ? 'selected' : '' ?>>Shipping</option>
                                <option value="DELIVERED" <?= $order->status === 'DELIVERED' ? 'selected' : '' ?>>Delivered</option>
                            </select>
                            <button type="submit" class="button update-status-button">Update Status</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=1&order_id=<?= urlencode($order_id) ?>&sort=<?= $sort ?>&dir=<?= $dir ?>" class="first-page">First</a>
            <a href="?page=<?= $page - 1 ?>&order_id=<?= urlencode($order_id) ?>&sort=<?= $sort ?>&dir=<?= $dir ?>" class="prev-page">Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&order_id=<?= urlencode($order_id) ?>&sort=<?= $sort ?>&dir=<?= $dir ?>"
                class="page-number <?= $i == $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&order_id=<?= urlencode($order_id) ?>&sort=<?= $sort ?>&dir=<?= $dir ?>" class="next-page">Next</a>
            <a href="?page=<?= $totalPages ?>&order_id=<?= urlencode($order_id) ?>&sort=<?= $sort ?>&dir=<?= $dir ?>" class="last-page">Last</a>
        <?php endif; ?>
    </div>
</div>

<!-- Modal for updating order status -->
<div id="updateFormModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeUpdateForm()">&times;</span>
        <h2>Update Order</h2>
        <form action="updateOrderStatus.php" method="POST" id="updateForm">
            <input type="hidden" name="order_id" id="update_order_id">
            
            <label for="update_status">Order Status:</label>
            <select name="status" id="update_status">
                <option value="PENDING">Pending</option>
                <option value="PAID">Paid</option>
                <option value="SHIPPING">Shipping</option>
                <option value="DELIVERED">Delivered</option>
            </select>
            
            <button type="submit" class="button update-status-button">Update Status</button>
        </form>
    </div>
</div>

<script>
    // Function to show the update form modal and populate the fields
    function showUpdateForm(orderId, currentStatus) {
        document.getElementById('updateFormModal').style.display = 'block';
        document.getElementById('update_order_id').value = orderId;

        // Set the current status as selected in the dropdown
        document.getElementById('update_status').value = currentStatus;
    }

    // Function to close the update form modal
    function closeUpdateForm() {
        document.getElementById('updateFormModal').style.display = 'none';
    }
</script>
