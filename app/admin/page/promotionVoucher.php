<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../css/productStaffAdmin.css" />
    <link rel="stylesheet" href="../css/promotion.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Promotion Management</title>
</head>

<?php include 'adminHeader.php'; ?>

<?php
$fields = [
    '',
    'promo_id'       => 'Promo ID',
    'promo_name'     => 'Promo Name',
    'promo_code'     => 'Promo Code',
    'promo_amount'   => 'Discount Amount',
    'limit_usage'    => 'Usage Limit',
    'start_date'     => 'Start Date',
    'end_date'       => 'End Date',
    'status'         => 'Status',
    'Action'
];

$search = req('search');
$searchStatus = req('statusFilter');
$whereClauses = [];
$params = [];

if ($search) {
    $whereClauses[] = "(promo_id LIKE ? OR promo_name LIKE ? OR promo_code LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($searchStatus) {
    $whereClauses[] = "status = ?";
    $params[] = $searchStatus;
}

$whereSql = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'promo_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

$page = req('page', 1);
$limit = 10;
$offset = ($page - 1) * $limit;

$total_promotions_query = "
    SELECT COUNT(*) 
    FROM promotions 
    $whereSql
";
$total_promotions_stm = $_db->prepare($total_promotions_query);
$total_promotions_stm->execute($params);
$total_promotions = $total_promotions_stm->fetchColumn();
$total_pages = ceil($total_promotions / $limit);

$query = "
    SELECT * 
    FROM promotions 
    $whereSql
    ORDER BY $sort $dir
    LIMIT $limit OFFSET $offset
";
$stm = $_db->prepare($query);
$stm->execute($params);
$promotions = $stm->fetchAll();
?>

<div class="main">
    <h1>PROMOTION MANAGEMENT</h1>

    <form method="get">
        <?= html_search('search', $search, 'Search Promo ID or Name or Code') ?>
        <label for="statusFilter">Status:</label>
        <select name="statusFilter" id="statusFilter">
            <option value="">All</option>
            <option value="AVAILABLE" <?= $searchStatus == 'AVAILABLE' ? 'selected' : '' ?>>Available</option>
            <option value="UNAVAILABLE" <?= $searchStatus == 'UNAVAILABLE' ? 'selected' : '' ?>>Unavailable</option>
        </select>

        <button>Search</button>
    </form>
    <form method="post" id="f">
        <button formaction="restorePromotion.php">Restore</button>
        <button formaction="deleteVoucher.php" onclick="return confirmDelete()">Delete</button>
    </form>

    <p><?= count($promotions) ?> promotion(s) on this page | Total: <?= $total_promotions ?> promotion(s)</p>

    <!-- Promotions Table -->
    <table id="promoTable" class="data-table">
        <thead>
            <tr>
                <?= table_headers($fields, $sort, $dir) ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($promotions as $p): ?>
                <tr>
                    <td>
                        <input type="checkbox"
                            name="id[]"
                            value="<?= $p->promo_id ?>"
                            form="f">
                    </td>
                    <td><?= $p->promo_id ?></td>
                    <td><?= $p->promo_name ?></td>
                    <td><?= $p->promo_code ?></td>
                    <td><?= $p->promo_amount ?></td>
                    <td><?= $p->limit_usage ?></td>
                    <td><?= $p->start_date ?></td>
                    <td><?= $p->end_date ?></td>
                    <td><?= $p->status ?></td>
                    <td>
                        <button class="button action-button" onclick="showUpdatePromotionForm(
                            '<?= $p->promo_image ?>', 
                            '<?= $p->promo_id ?>', 
    '<?= $p->promo_name ?>', 
    '<?= $p->promo_code ?>', 
    '<?= $p->description ?>', 
    '<?= $p->requirement ?>', 
    '<?= $p->promo_amount ?>', 
    '<?= $p->limit_usage ?>', 
    '<?= $p->start_date ?>', 
    '<?= $p->end_date ?>', 
    '<?= $p->status ?>'
                                )">
                            Update
                        </button>
                        <form action="deleteVoucher.php" method="post" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $p->promo_id ?>">
                            <button type="submit" class="button delete-action-button" onclick="return confirmDelete();">Delete</button>
                        </form>

                        <button class="button view-action-button" onclick="showViewPromotionForm(
                          '<?= $p->promo_image ?>', 
                            '<?= $p->promo_id ?>', 
    '<?= $p->promo_name ?>', 
    '<?= $p->promo_code ?>', 
    '<?= $p->description ?>', 
    '<?= $p->requirement ?>', 
    '<?= $p->promo_amount ?>', 
    '<?= $p->limit_usage ?>', 
    '<?= $p->start_date ?>', 
    '<?= $p->end_date ?>', 
    '<?= $p->status ?>'
)">
                            View
                        </button>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=1&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&dir=<?= $dir ?>" class="first-page">First</a>
            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&dir=<?= $dir ?>" class="prev-page">Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&dir=<?= $dir ?>"
                class="page-number <?= $i == $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&dir=<?= $dir ?>" class="next-page">Next</a>
            <a href="?page=<?= $total_pages ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&dir=<?= $dir ?>" class="last-page">Last</a>
        <?php endif; ?>
    </div>

    <div style="margin: 20px;">
        <button id="addPromotionBtn" class="add-button" onclick="showAddForm()">Add new customer</button>
    </div>
    <div id="viewPromoModal" class="modal">
        <div class="modal-content">
            <form id='viewForm'>
                <span class="close-button" onclick="hideViewForm()">&times;</span>
                <h2>Promotion Details</h2>
                <p><strong>Promo ID:</strong> <span id="viewPromoID"></span></p>
                <p><strong>Promo Name:</strong> <span id="viewPromoName"></span></p>
                <p><strong>Promo Code:</strong> <span id="viewPromoCode"></span></p>
                <strong>Current Promotion Image:</strong>
                <p><img id="viewCurrentImage" src="<?= isset($promotions['promo_image']) && $promotions['promo_image'] ? '../../uploads/promo_images/' . $promotions['promo_image'] : ''; ?>" alt="Current Product Image" style="max-width: 150px; max-height: 150px;">
                    <span class="error"><?php err('promo_image'); ?></span><br><br>
                </p>
                <p><strong>Description:</strong> <span id="viewDescription"></span></p>
                <p><strong>Requirement:</strong> <span id="viewRequirement"></span></p>
                <p><strong>Discount Amount:</strong> <span id="viewPromoAmount"></span></p>
                <p><strong>Usage Limit:</strong> <span id="viewLimitUsage"></span></p>
                <p><strong>Start Date:</strong> <span id="viewStartDate"></span></p>
                <p><strong>End Date:</strong> <span id="viewEndDate"></span></p>
                <p><strong>Status:</strong> <span id="viewStatus"></span></p>
        </div>
        </form>
    </div>

    <div id="addPromoModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="hideAddForm()">&times;</span>
            <form id="addForm" action="addPromotion.php" method="POST" enctype="multipart/form-data" class="add-form">
                <h2>Add Promotion</h2>

                <label for="promo_name">Promo Name:</label>
                <?php html_text('promo_name'); ?>
                <br><br>

                <label for="promo_code">Promo Code:</label>
                <?php html_text('promo_code'); ?>
                <br><br>

                <label for="promo_image">Promotion Image:</label>
                <?php html_file('promo_image', 'image/*'); ?>
                <span class="error"><?php err('promo_image'); ?></span><br><br>


                <label for="description">Description:</label>
                <?php html_text('description'); ?>
                <br><br>

                <label for="requirement">Requirement:</label>
                <?php html_number('requirement', '0', '100000', '0.01'); ?>
                <br><br>

                <label for="promo_amount">Discount Amount:</label>
                <?php html_number('promo_amount', '0', '100000', '0.01'); ?>
                <br><br>

                <label for="limit_usage">Usage Limit:</label>
                <?php html_number('limit_usage', '1', '100000', '1'); ?>
                <br><br>

                <label for="start_date">Start Date:</label>
                <?php html_date('start_date'); ?>
                <br><br>

                <label for="end_date">End Date:</label>
                <?php html_date('end_date'); ?>
                <br><br>

                <label for="status">Status:</label>
                <?php html_select('status', ['AVAILABLE' => 'Available', 'UNAVAILABLE' => 'Unavailable'], '- Select Status -', 'required'); ?>
                <br><br>

                <button type="submit" class="action-button" onclick="confirmAddVoucher()">Add Promotion</button>
            </form>



        </div>
    </div>


    <div id="updatePromotionModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="hideUpdateForm()">&times;</span>
            <form id="updateForm" action="updatePromotion.php" method="POST" enctype="multipart/form-data" class="update-form">
                <h2>Update Promotion</h2>
                <label for="promo_id">Promo ID:</label>
                <input id="promo_id" name="promo_id" value="" readonly>

                <label for="promo_name">Promo Name:</label>
                <?php html_text('promo_name', 'required'); ?>
                <br><br>

                <label for="promo_code">Promo Code:</label>
                <?php html_text('promo_code', 'required'); ?>
                <br><br>

                <label for="promo_image">Current Promotion Image:</label>
                <img id="viewCurrentImage2" src="<?= isset($promotions['promo_image']) && $promotions['promo_image'] ? '../../uploads/promo_images/' . $promotions['promo_image'] : ''; ?>" alt="Current Product Image" style="max-width: 150px; max-height: 150px;">
                <span class="error"><?php err('promo_image'); ?></span><br><br>

                <label for="promo_image">Promotion Image:</label>
                <?php html_file('promo_image', 'image/*'); ?>
                <span class="error"><?php err('promo_image'); ?></span><br><br>

                <label for="description">Description:</label>
                <?php html_text('description', 'required'); ?>
                <br><br>

                <label for="requirement">Requirement:</label>
                <?php html_number('requirement', '0', '100000', '0.01', 'required'); ?>
                <br><br>

                <label for="promo_amount">Discount Amount:</label>
                <?php html_number('promo_amount', '0', '100000', '0.01', 'required'); ?>
                <br><br>

                <label for="limit_usage">Usage Limit:</label>
                <?php html_number('limit_usage', '1', '100000', '1', 'required'); ?>
                <br><br>

                <label for="start_date">Start Date:</label>
                <?php html_date('start_date', 'required'); ?>
                <br><br>

                <label for="end_date">End Date:</label>
                <?php html_date('end_date', 'required'); ?>
                <br><br>

                <label for="status">Status:</label>
                <?php html_select('status', ['AVAILABLE' => 'Available', 'UNAVAILABLE' => 'Unavailable'], '- Select Status -', 'required'); ?>
                <br><br>

                <button type="submit" class="action-button">Update Promotion</button>
            </form>
        </div>
    </div>

</div>
</div>

</html>

<script>
    function showViewPromotionForm(promoImage, promo_id, promo_name, promo_code, description, requirement, promo_amount, limit_usage, start_date, end_date, status) {
        var modal = document.getElementById('viewPromoModal');

        modal.style.display = 'block';
        document.getElementById('viewCurrentImage').src = "/uploads/promo_images/" + promoImage;
        document.getElementById('viewPromoID').innerText = promo_id;
        document.getElementById('viewPromoName').innerText = promo_name;
        document.getElementById('viewPromoCode').innerText = promo_code;
        document.getElementById('viewDescription').innerText = description;
        document.getElementById('viewRequirement').innerText = requirement;
        document.getElementById('viewPromoAmount').innerText = promo_amount;
        document.getElementById('viewLimitUsage').innerText = limit_usage;
        document.getElementById('viewStartDate').innerText = start_date;
        document.getElementById('viewEndDate').innerText = end_date;
        document.getElementById('viewStatus').innerText = status;

    }


    function showUpdatePromotionForm(
        promoImage,
        promo_id,
        promo_name,
        promo_code,
        description,
        requirement,
        promo_amount,
        limit_usage,
        start_date,
        end_date,
        status
    ) {
        const modal = document.getElementById('updatePromotionModal');
        const form = document.getElementById('updateForm');

        // Display the modal
        modal.style.display = 'block';

        // Set the values in the form
        form.elements['promo_id'].value = promo_id;
        form.elements['promo_name'].value = promo_name;
        form.elements['promo_code'].value = promo_code;
        form.elements['description'].value = description;
        form.elements['requirement'].value = requirement;
        form.elements['promo_amount'].value = promo_amount;
        form.elements['limit_usage'].value = limit_usage;

        // Format dates to YYYY-MM-DD
        const formattedStartDate = formatDate(start_date);
        const formattedEndDate = formatDate(end_date);

        form.elements['start_date'].value = formattedStartDate;
        form.elements['end_date'].value = formattedEndDate;

        form.elements['status'].value = status;

        // Set the current image
        document.getElementById('viewCurrentImage2').src = "/uploads/promo_images/" + promoImage;
    }

    // Helper function to format dates to YYYY-MM-DD
    function formatDate(date) {
        const d = new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }



    function hideViewForm() {
        document.getElementById('viewPromoModal').style.display = 'none';
    }

    function hideAddForm() {
        document.getElementById('addPromoModal').style.display = "none";
    }

    function hideUpdateForm() {
        document.getElementById('updatePromotionModal').style.display = "none";
    }

    function confirmDelete() {
        return confirm("Are you sure you want to delete this voucher?");
    }

    function showAddForm() {
        var modal = document.getElementById('addPromoModal');
        modal.style.display = "block";
    }


    function confirmAddVoucher() {
        const confirmation = confirm("Are you sure you want to add this voucher?");
        return confirmation;
    }
</script>