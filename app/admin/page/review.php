<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../css/orderStatus.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Review Management</title>
</head>
<?php
include 'adminHeader.php';

$fields = [
    '',
    'review_id' => 'Review ID',
    'customer_id' => 'Customer ID',
    'product_id' => 'Product ID',
    'rating' => 'Rating',
    'comment' => 'Comment',
    'review_image' => 'Review Image',
    'comment_date_time' => 'Comment Date Time',
    'Action'
];

$search = req('search');
$sort = req('sort');
$filter_rating = req('filter_rating');

key_exists($sort, $fields) || $sort = 'product_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

$page = req('page', 1);
$limit = 10;
$offset = ($page - 1) * $limit;

$rating_condition = $filter_rating ? "AND  rating = ?" : "";

$query = "
    SELECT r.review_id, r.customer_id, r.product_id, r.rating, r.comment, r.review_image, r.comment_date_time, p.product_name
    FROM reviews r
    JOIN products p ON r.product_id = p.product_id
    WHERE (r.review_id LIKE ? 
       OR r.product_id LIKE ? 
       OR r.customer_id LIKE ?)
       $rating_condition
    ORDER BY $sort $dir
    LIMIT $limit OFFSET $offset";

// Prepare the parameters for the query
$params = ["%$search%", "%$search%", "%$search%"];
if ($filter_rating) {
    $params[] = $filter_rating; // Add rating filter to params
}

// Execute the query
$stm = $_db->prepare($query);
$stm->execute($params);
$reviews = $stm->fetchAll();

$total_review_query = "
    SELECT COUNT(*) 
    FROM reviews 
    WHERE (review_id LIKE ? 
       OR product_id LIKE ? 
       OR customer_id LIKE ?)
       $rating_condition";

$total_review_stm = $_db->prepare($total_review_query);
$total_review_stm->execute($params);
$total_review = $total_review_stm->fetchColumn();
$total_pages = ceil($total_review / $limit);
?>

<div class="main">
    <h1>Review Management</h1>
    <form>
        <input type="search" name="search" placeholder="Search Review Id,Product Id,Customer Id" value="<?= htmlspecialchars($search) ?>">
        <select name="filter_rating">
            <option value="">All Ratings</option>
            <option value="1" <?= $filter_rating === '1' ? 'selected' : '' ?>>1</option>
            <option value="2" <?= $filter_rating === '2' ? 'selected' : '' ?>>2</option>
            <option value="3" <?= $filter_rating === '3' ? 'selected' : '' ?>>3</option>
            <option value="4" <?= $filter_rating === '4' ? 'selected' : '' ?>>4</option>
            <option value="5" <?= $filter_rating === '5' ? 'selected' : '' ?>>5</option>
        </select>

        <button type="submit">Search</button>
    </form>
    <?php if ($_user?->role == 'MANAGER'): ?>

        <form method="post" id="f">
            <button class="delete-btn" formaction="deleteReview.php" onclick="return confirmDelete()">Batch Delete</button>
        </form>
    <?php endif; ?>

    <p><?= count($reviews) ?> review(s) on this page | Total: <?= $total_review ?> review(s)</p>
    <table id="reviewTable" class="data-table">
        <thead>
            <tr>
                <?= table_headers($fields, $sort, $dir) ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reviews as $r): ?>
                <tr>
                    <td>
                        <input type="checkbox"
                            name="id[]"
                            value="<?= $r->review_id ?>"
                            form="f">
                    </td>
                    <td><?= $r->review_id ?></td>
                    <td><?= $r->customer_id ?></td>
                    <td><?= $r->product_id ?></td>
                    <td><?= $r->rating ?></td>
                    <td><?= $r->comment ?></td>
                    <td>
                        <img src="../../uploads/review_images/<?= $r->review_image ?>" class="resized-image">
                    </td>
                    <td><?= $r->comment_date_time ?></td>


                    <?php if ($_user?->role == 'STAFF'): ?>
                       <td>

                            <button class="button view-action-button" onclick="showViewReviewForm(
                            '<?= $r->review_id ?>',
                            '<?= $r->customer_id ?>',
                            '<?= $r->product_id ?>',
                            '<?= $r->rating ?>',
                            '<?= $r->comment ?>',
                            '<?= $r->review_image ?>',
                            '<?= $r->comment_date_time ?>'
                        )">View</button>
                        <?php endif; ?>

                        <?php if ($_user?->role == 'MANAGER'): ?>

                            <button class="button action-button" onclick="showUpdateForm(
                            '<?= $r->review_id ?>',
                            '<?= $r->customer_id ?>',
                            '<?= $r->product_id ?>',
                            '<?= $r->rating ?>',
                            '<?= $r->comment ?>',
                            '<?= $r->review_image ?>',
                            '<?= $r->comment_date_time ?>'
                        )">Update</button>

                            <form action="deleteReview.php" method="post" style="display: inline;" onsubmit="return confirmDelete();">
                                <input type="hidden" name="id" value="<?= $r->review_id ?>">
                                <button type="submit" class="button delete-action-button">Delete</button>
                            </form>


                        </td>
                    <?php endif; ?>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
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




    <div id="viewReviewModal" class="modal" style="margin-top: 80px;">
        <div class="modal-content">
            <span class="close-button" onclick="hideViewReviewForm()">&times;</span>
            <h2>Review Details</h2>
            <p><strong>Review ID:</strong> <span id="viewReviewId"></span></p>
            <p><strong>Customer ID:</strong> <span id="viewCustomerId"></span></p>
            <p><strong>Product ID:</strong> <span id="viewProductId"></span></p>
            <p><strong>Rating:</strong> <span id="viewRating"></span></p>
            <p><strong>Comment:</strong> <span id="viewComment"></span></p>
            <p><strong>Review Image:</strong></p>
            <img id="viewReviewImage" class="resized-image" style="max-width: 300px;">
            <p><strong>Comment Date Time:</strong> <span id="viewCommentDateTime"></span></p>
            <button type="button" class="cancel-button" onclick="hideViewReviewForm()">Close</button>
        </div>
    </div>

    <div id="updateReviewModal" class="modal" style="margin-top: 80px;">
        <div class="modal-content">
            <span class="close-button" onclick="hideAddForm()">&times;</span>
            <form id="updateForm" action="updateReview.php" method="POST" enctype="multipart/form-data" class="update-form">
                <h2>Update Review Details</h2>
                <label for="review_id">Review ID:</label>
                <input id="review_id" name="review_id" readonly>
                <br>

                <label for="customer_id">Customer ID:</label>
                <input id="customer_id" name="customer_id" readonly>
                <br>

                <label for="product_id">Product ID:</label>
                <input id="product_id" name="product_id" readonly>
                <br>

                <label for="rating">Rating:</label>
                <input type="double" id="rating" name="rating">
                <br>

                <label for="comment">Comment:</label>
                <textarea id="comment" name="comment"></textarea>
                <br>

                <label for="review_image">Review Image:</label>
                <div>
                    <img id="currentReviewImage" class="resized-image" style="max-width: 150px;">
                    <br><br>
                    <input type="file" id="review_image" name="review_image" accept="image/*">
                </div>
                <br>

                <input type="submit" value="Update Review" class="update-button">
                <button type="button" class="cancel-button" onclick="hideAddForm()">Close</button>

            </form>
        </div>
    </div>
</div>

<script>
    function showViewReviewForm(review_id, customer_id, product_id, rating, comment, review_image, comment_date_time) {
        document.getElementById("viewReviewId").innerText = review_id;
        document.getElementById("viewCustomerId").innerText = customer_id;
        document.getElementById("viewProductId").innerText = product_id;
        document.getElementById("viewRating").innerText = rating;
        document.getElementById("viewComment").innerText = comment;
        document.getElementById("viewReviewImage").src = "../../uploads/review_images/" + review_image;
        document.getElementById("viewCommentDateTime").innerText = comment_date_time;
        document.getElementById("viewReviewModal").style.display = "block";
    }

    function hideViewReviewForm() {
        document.getElementById("viewReviewModal").style.display = "none";
    }

    function showUpdateForm(review_id, customer_id, product_id, rating, comment, review_image, comment_date_time) {
        var modal = document.getElementById('updateReviewModal');
        var form = document.getElementById('updateForm');

        modal.style.display = "block";

        form.elements['review_id'].value = review_id;
        form.elements['customer_id'].value = customer_id;
        form.elements['product_id'].value = product_id;
        form.elements['rating'].value = rating;
        form.elements['comment'].value = comment;

        var reviewImageUrl = "../../uploads/review_images/" + review_image;
        document.getElementById('currentReviewImage').src = reviewImageUrl;

        form.elements['comment_date_time'].value = comment_date_time;
    }

    function hideAddForm() {
        document.getElementById("updateReviewModal").style.display = "none";
    }

    function confirmDelete() {
        return confirm("Are you sure you want to delete this review?");
    }
</script>

</html>