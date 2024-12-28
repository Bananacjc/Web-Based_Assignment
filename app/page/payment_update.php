
<?php

ob_start();
require_once '../_base.php';


if (is_post()) {

    $paymentMethod = req('selectPayment');
    $selectedAddress = req('selectAddress');
    $selectedPromoID = req('selectPromo');
    $action = req('action');

    if ($action === 'changeBankDetails' && isset($paymentMethod) && $paymentMethod) {
        $banks = json_decode($_user->banks, true);
        $bankDetails = null;

        foreach ($banks as $bank) {
            if ($paymentMethod === $bank['accNum']) {
                $bankDetails = $bank;
                break;
            }
        }


        ob_end_clean();
        if ($bankDetails) {
            echo json_encode([
                'success' => true,
                'paymentMethod' => [
                    'accNum' => $bankDetails['accNum'],
                    'cvvNum' => $bankDetails['cvv'],
                    'exDate' => $bankDetails['expiry']
                ]

            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No such payment method'
            ]);
        }
        exit();
    }


    if ($action === 'changeAddress' && isset($selectedAddress) && $selectedAddress) {
        $addresses = json_decode($_user->addresses, true);
        $addressDetails = null;

        foreach ($addresses as $address) {
            if ($selectedAddress === $address) {
                $addressDetails = $address;
                break;
            }
        }

        ob_end_clean();
        if ($addressDetails) {
            echo json_encode([
                'success' => true,
                'address' => $address
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No such address'
            ]);
        }
        exit();
    }

    if ($action === 'changePromo' && isset($selectedPromoID) && $selectedPromoID) {
        $stmt = $_db->prepare('SELECT promo_id, promo_code, promo_amount FROM promotions WHERE promo_id = ?');
        $stmt->execute([$selectedPromoID]);
        $promotion = $stmt->fetch(PDO::FETCH_ASSOC);
    
        ob_end_clean();
        if ($promotion) {
            echo json_encode([
                'success' => true,
                'promoAmount' => $promotion['promo_amount']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Promotion not found.'
            ]);
        }
        exit();
    }
    
}

?>

