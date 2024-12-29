<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../css/orderStatus.css" />
    <script src="../js/orderStatus.js"></script>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Order Management</title>
</head>

<?php
include 'adminHeader.php';


$fields = [
    '',
    'order_id'    => 'Order ID',
    'customer_id' => 'Customer ID',
    'order_items' => 'Order Items',
    'promo_amount' => 'Promo Amount',
    'subtotal'    => 'Subtotal',
    'shipping_fee' => 'Shipping Fee',
    'total'        => 'Total',
    'payment_method' => 'Payment Method',
    'order_time'   => 'Order Time',
    'status'       => 'Status',
    'Action'
];

$searchTerm = req('search');
$status = req('status');

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'order_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

$page = req('page', 1);
$limit = 10;
$offset = ($page - 1) * $limit;

$where_clauses = ['(order_id LIKE ? OR customer_id LIKE ?)'];
$params = ["%$searchTerm%", "%$searchTerm%"];

if (!empty($status)) {
    $where_clauses[] = "status = ?";
    $params[] = $status;
}

$total_orders_query = 'SELECT COUNT(*) FROM orders WHERE ' . implode(' AND ', $where_clauses);
$total_orders_stm = $_db->prepare($total_orders_query);
$total_orders_stm->execute($params);
$total_orders = $total_orders_stm->fetchColumn();
$total_pages = ceil($total_orders / $limit);

$query = "
    SELECT * FROM orders 
    WHERE " . implode(' AND ', $where_clauses) . "
    ORDER BY $sort $dir
    LIMIT $limit OFFSET $offset";

$stm = $_db->prepare($query);
$stm->execute($params);
$orders = $stm->fetchAll();

$userRole = ['MANAGER', 'DELIVERY_GUY'];

?>

<div class="main">
    <h1>ORDERS</h1>

    <form>
        <?= html_search('search', '', 'Search by Order ID or Customer ID') ?>

        <select name="status" id="status">
            <option value="">All Statuses</option>
            <option value="PAID" <?= $status === 'PAID' ? 'selected' : '' ?>>Paid</option>
            <option value="SHIPPING" <?= $status === 'SHIPPING' ? 'selected' : '' ?>>Shipping</option>
            <option value="DELIVERED" <?= $status === 'DELIVERED' ? 'selected' : '' ?>>Delivered</option>
        </select>
        <button type="submit">Search</button>
    </form>

    <?php if ($_user->role == 'MANAGER'): ?>
        <form method="post" id="f">
            <button class="delete-btn" formaction="deleteOrder.php" onclick="return confirmDelete()">Batch Delete</button>
        </form>
    <?php endif; ?>



    <p><?= count($orders) ?> order(s) on this page | Total: <?= $total_orders ?> order(s)</p>

    <table id="orderTable" class="data-table">
        <thead>
            <tr>
                <?= table_headers($fields, $sort, $dir) ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr class="order-row">
                    <td>
                        <input type="checkbox" name="id[]" value="<?= $o->order_id ?>" form="f">
                    </td>
                    <td><?= $o->order_id ?></td>
                    <td><?= $o->customer_id ?></td>
                    <td><?= plainTextJson($o->order_items) ?></td>
                    <td><?= $o->promo_amount ?></td>
                    <td><?= $o->subtotal ?></td>
                    <td><?= $o->shipping_fee ?></td>
                    <td><?= $o->total ?></td>
                    <td><?= plainTextJson($o->payment_method) ?></td>
                    <td><?= $o->order_time ?></td>
                    <td><?= $o->status ?></td>
                    <td>
                        <?php if ($_user->role == 'DELIVERY_GUY'): ?>
                            <form action="updateStatus.php" method="post" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?= $o->order_id ?>">
                                <select name="status1" onchange="this.form.submit()">
                                <option value="PAID" <?= $o->status === 'PAID' ? 'selected' : '' ?>>Paid</option>
                                    <option value="SHIPPING" <?= $o->status === 'SHIPPING' ? 'selected' : '' ?>>Shipping</option>
                                    <option value="DELIVERED" <?= $o->status === 'DELIVERED' ? 'selected' : '' ?>>Delivered</option>
                                </select>
                            </form>
                        <?php endif; ?>
                    </td>
                    <td>

                        <?php if ($_user->role == 'STAFF'): ?>
                            <button class="button view-action-button" onclick="showViewOrderForm(
    '<?= $o->order_id ?>', 
    '<?= $o->customer_id ?>', 
    '<?= plainTextJson($o->order_items) ?>', 
    '<?= $o->promo_amount ?>', 
    '<?= $o->subtotal ?>', 
    '<?= $o->shipping_fee ?>', 
    '<?= $o->total ?>', 
    '<?= plainTextJson($o->payment_method) ?>', 
    '<?= $o->order_time ?>', 
    '<?= $o->status ?>')">View
                            </button>
                        <?php endif; ?>

                        <?php if ($_user->role == 'MANAGER'): ?>

                            <button class="button action-button" onclick="showUpdateOrderForm(
                            '<?= $o->order_id ?>', 
                            '<?= $o->customer_id ?>', 
    '<?= plainTextJson($o->order_items) ?>', 
    '<?= $o->promo_amount ?>', 
    '<?= $o->subtotal ?>', 
    '<?= $o->shipping_fee ?>', 
    '<?= plainTextJson($o->payment_method) ?>', 
    '<?= $o->order_time ?>', 
    '<?= $o->status ?>', 
                                )">
                                Update
                            </button>

                            <form action="deleteOrder.php" method="post" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $o->order_id ?>">
                                <button type="submit" class="button delete-action-button" onclick="return confirmDelete();">Delete</button>
                            </form>
                        <?php endif; ?>


                    </td>

                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=1" class="first-page">First</a>
            <a href="?page=<?= $page - 1 ?>" class="prev-page">Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>" class="page-number <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>" class="next-page">Next</a>
            <a href="?page=<?= $total_pages ?>" class="last-page">Last</a>
        <?php endif; ?>
    </div>


    <div style="margin: 20px;">
        <button id="addOrderBtn" class="add-button" onclick="showAddForm()">Add New Order</button>
    </div>


    <div id="viewOrderModal" class="modal">
        <div class="modal-content">
            <form id='viewForm'>
                <span class="close-button" onclick="hideViewForm()">&times;</span>
                <h2>Orders Details</h2>
                <p><strong>Order ID:</strong> <span id="viewOrderID"></span></p>
                <p><strong>Customer ID:</strong> <span id="viewCusId"></span></p>
                <p><strong>Order Items:</strong> <span id="viewOrderItems"></span></p>
                <p><strong>Promo Amount(RM):</strong> <span id="viewPromoAmount"></span></p>
                <p><strong>Sub Total(RM):</strong> <span id="viewSubTotal"></span></p>
                <p><strong>Shipping Fee(RM):</strong> <span id="viewShippingFee"></span></p>
                <p><strong>Total(RM):</strong> <span id="viewTotal"></span></p>
                <p><strong>Payment Method:</strong> <span id="viewPaymentMethod"></span></p>
                <p><strong>Order Time:</strong> <span id="viewOrderTime"></span></p>
                <p><strong>Status:</strong> <span id="viewStatus"></span></p>
                <button type="button" class="cancel-button" onclick="hideViewForm()">Close</button>
        </div>
        </form>
    </div>

    <div id="addOrderModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="hideAddForm()">&times;</span>
            <form id="addForm" action="addOrder.php" method="POST" enctype="multipart/form-data" class="add-form">
                <h2>Add Order</h2>
                <label for="customer_id">Customer ID:</label>
                <?php html_text('customer_id'); ?>
                <br><br>

                <label for="order_items">Order Items:</label>
                <?php html_text('order_items'); ?>
                <br><br>

                <label for="promo_amount">Promo Amount(RM):</label>
                <?php html_text('promo_amount'); ?>
                <br><br>

                <label for="sub_total">Sub Total(RM):</label>
                <?php html_text('sub_total'); ?>

                <label for="shipping_fee">Shipping Fee(RM):</label>
                <?php html_text('shipping_fee'); ?>

                <label for="payment_method">Payment Method:</label>
                <?php html_text('payment_method'); ?>

                <label for="order_time">Order Time:</label>
                <?php html_datetime('order_time'); ?>
                <br><br>
                <label for="status">Status:</label>
                <?php html_select('status', [
                    'PAID' => 'Paid',
                    'SHIPPING' => 'Shipping',
                    'DELIVERED' => 'Delivered'
                ], '- Select Status -', 'required'); ?>

                <button type="submit" class="action-button" onclick="confirmAddOrder()">Add Order</button>
            </form>



        </div>

    </div>

    <div id="updateOrderModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="hideUpdateForm()">&times;</span>
            <form id="updateForm" action="updateOrder.php" method="POST" enctype="multipart/form-data" class="update-form">
                <label for="order_id">Order ID:</label>
                <input id="order_id" name="order_id" value="" readonly>

                <label for="customer_id">Customer ID:</label>
                <input id="customer_id" name="customer_id" value="" readonly>
                <label for="order_items">Order Items:</label>
                <?php html_text('order_items'); ?>
                <br><br>

                <label for="promo_amount">Promo Amount(RM):</label>
                <?php html_text('promo_amount'); ?>
                <br><br>

                <label for="sub_total">Sub Total(RM):</label>
                <?php html_text('sub_total'); ?>

                <label for="shipping_fee">Shipping Fee(RM):</label>
                <?php html_text('shipping_fee'); ?>



                <label for="payment_method">Payment Method:</label>
                <?php html_text('payment_method'); ?>

                <label for="order_time">Order Time:</label>
                <?php html_datetime('order_time'); ?>


                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="PAID">Paid</option>
                    <option value="SHIPPING">Shipping</option>
                    <option value="DELIVERED">Delivered</option>
                </select>


                <button type="submit" class="action-button">Update Order</button>
            </form>

        </div>
    </div>
</div>

</html>