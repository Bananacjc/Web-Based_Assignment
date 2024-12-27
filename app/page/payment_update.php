
<?php
ob_start();
require_once '../_base.php';


if (is_post()) {

    $paymentMethod = req('selectPayment');
    $selectedAddress = req('selectAddress');
    $action = req('action');

    if ($action === 'changeBankDetails' && isset($paymentMethod)) {
        $banks = json_decode($_uesr->banks, true);
        $bankDetails = null;

        foreach($banks as $bank) {
            if ($paymentMethod === $bank) {
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
            exit();
        } else {
            echo json_encode([
                'success' => false
            ]);
            exit();
        }
    }

   
    if ($action === 'changeAddress' && isset($selectedAddress)) {
        $addresses = json_decode($_user->addresses, true);
        $addressDetails = null;

        foreach($addresses as $address) {
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
            exit();
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'IDK'
            ]);
            exit();
        }
    }
}

