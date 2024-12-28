<!DOCTYPE html>
<html>
<?php
$_title = 'Promotion';
$_css = '../css/promotion.css';
require '../_base.php';
include '../_head.php';

if (!$_user) {
    redirect('login.php');
}

reset_user();

if (is_post()) {
    $promoID = post('promoID');
    $promoLimit = post('promoLimit');

    if (isset($promoID) && isset($promoLimit)) {

        $stmt = $_db->prepare('SELECT * FROM promotions');
        $stmt->execute([]);
        $promotions = $stmt->fetchAll();

        // Exist in db
        $invalid = true;
        foreach ($promotions as $promotion) {
            if ($promotion->promo_id === $promoID) {
                $invalid = false;
                break;
            }
        }

        if ($invalid) {
            temp('popup-msg', ['msg' => 'No such promotion', 'isSuccess' => false]);
            redirect();
        }

        // Add to user db
        $userPromos = json_decode($_user->promotion_records, true);

        $userPromos[$promoID] = [
            "promoLimit" => $promoLimit == 0 ? 99999 : $promoLimit
        ];

        $stmt = $_db->prepare('UPDATE customers SET promotion_records = ? WHERE customer_id = ?');

        if ($stmt->execute([json_encode($userPromos), $_user->customer_id])) {
            $_user->promotion_records = json_encode($userPromos);
            temp('popup-msg', ['msg' => 'Promotion Added', 'isSuccess' => true]);
            redirect();
        } else {
            temp('popup-msg', ['msg' => 'Server Error', 'isSuccess' => false]);
            redirect();
        }
    }
}

?>

<h1 class="h1 header-banner">Promotions</h1>
<div id="promo-container">
    <?php
    $stmt = $_db->prepare('SELECT * FROM promotions');
    $stmt->execute();
    $promotions = $stmt->fetchAll();

    if ($promotions) {

        usort($promotions, function ($a, $b) {
            $today = new DateTime();

            $aDate = new DateTime($a->start_date);
            $bDate = new DateTime($b->start_date);

            $aPriority = $today->diff($aDate)->days;
            $bPriotity = $today->diff($bDate)->days;

            return $aPriority <=> $bPriotity;
        });
    }

    ?>

    <?php if ($promotions): ?>

        <?php foreach ($promotions as $promo): ?>
            <?php

            $uPromos = json_decode($_user->promotion_records, true);

            $promoID = $promo->promo_id;
            $promoName = $promo->promo_name;
            $promoCode = $promo->promo_code;
            $promoDesc = $promo->description;
            $promoReq = $promo->requirement;
            $promoAmount = $promo->promo_amount;
            $promoLimit = $promo->limit_usage;
            $promoImage = $promo->promo_image;
            $promoStatus = $promo->status;

            $promoStart  = date('d-M-Y h:i:s A', strtotime($promo->start_date));
            $promoEnd = date('d-M-Y h:i:s A', strtotime($promo->end_date));
            $today = new DateTime();

            $upcoming = $today < new DateTime($promoStart);
            $expired = $today > new DateTime($promoEnd);

            $claimed = false;

            if (isset($uPromos[$promoID])) {
                $claimed = true;
            }

            ?>
            <div class="promo-card">
                <div class="promo-image">
                    <img src="../uploads/promo_images/<?= $promoImage ?>" alt="<?= $promoName ?>">
                </div>
                <div class="promo-details">
                    <table class="promo-details-table">
                        <thead>
                            <tr>
                                <th colspan="3">
                                    <h2><?= $promoName ?></h2>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Code</th>
                                <td>:</td>
                                <td><?= $promoCode ?></td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>:</td>
                                <td><?= $promoDesc ?></td>
                            </tr>
                            <tr>
                                <th>Requirement</th>
                                <td>:</td>
                                <td><?= $promoReq == 0 ? 'None' : 'Minimum Purchase of RM ' . $promoReq ?></td>
                            </tr>
                            <tr>
                                <th>Discount</th>
                                <td>:</td>
                                <td>RM <?= $promoAmount ?></td>
                            </tr>
                            <tr>
                                <th>Limit</th>
                                <td>:</td>
                                <td><?= $promoLimit == 0 ? 'None' : $promoLimit . ' purchases' ?></td>
                            </tr>
                            <tr>
                                <th>Start Date</th>
                                <td>:</td>
                                <td><?= $promoStart ?></td>
                            </tr>
                            <tr>
                                <th>End Date</th>
                                <td>:</td>
                                <td><?= $promoEnd ?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">
                                    <?php if ($claimed): ?>
                                        <button class="promo-btn claimed">Claimed</button>
                                    <?php elseif ($upcoming): ?>
                                        <button class="promo-btn upcoming">Coming Soon</button>
                                    <?php elseif ($expired): ?>
                                        <button class="promo-btn expired">Expired</button>
                                    <?php elseif ($promoStatus === 'AVAILABLE'): ?>
                                        <form action="" method="post">
                                            <?= html_hidden('promoID') ?>
                                            <?= html_hidden('promoLimit') ?>
                                            <button class="promo-btn available">Get Promo Code</button>
                                        </form>
                                    <?php endif ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?php endforeach ?>
    <?php else: ?>
        <div class="promo-card">
            No promotions available... <br>
            Stay tuned!
        </div>
    <?php endif ?>
</div>

<?php include '../_foot.php'; ?>
</body>

</html>