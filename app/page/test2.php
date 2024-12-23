<?php
require '../_base.php'; // Include base functions and database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cardNumber = preg_replace('/\s+/', '', $_POST['cardNumber']);
    $cardHolderName = $_POST['cardHolderName'];
    $expiryDate = $_POST['expiryDate'];
    $cvv = $_POST['cvv'];

    // Simple validation
    if (strlen($cardNumber) !== 16 || !is_numeric($cardNumber)) {
        echo json_encode(['error' => 'Invalid card number.']);
        exit;
    }

    if (!preg_match('/^\d{2}\/\d{2}$/', $expiryDate)) {
        echo json_encode(['error' => 'Invalid expiry date format.']);
        exit;
    }

    if (strlen($cvv) !== 3 || !is_numeric($cvv)) {
        echo json_encode(['error' => 'Invalid CVV.']);
        exit;
    }

    // Save or process the card details (e.g., save to DB)
    // Here, we just echo a success message for simplicity
    echo json_encode(['success' => 'Card details are valid and submitted.']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flipping Card</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <style>
body {
  font-family: "Overpass Mono", monospace;
  font-weight: 400;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  margin: 0;
  background-image: linear-gradient(
    to right top,
    #d16ba5,
    #c777b9,
    #ba83ca,
    #aa8fd8,
    #9a9ae1,
    #8aa7ec,
    #79b3f4,
    #69bff8,
    #52cffe,
    #41dfff,
    #46eefa,
    #5ffbf1
  );
}

.card {
  width: 320px;
  height: 200px;
  border-radius: 10px;
  perspective: 1000px;
}

.card:hover .card-inner {
  transform: rotateY(180deg);
}

.card-inner {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 10px;
  transition: transform 600ms ease;
  transform-style: preserve-3d;
  box-shadow: 0 0 25px 2px rgba(0, 0, 0, 0.2);
}

.card-front,
.card-back {
  position: absolute;
  top: 0;
  width: 100%;
  height: 100%;
  border-radius: 10px;
  overflow: hidden;
  backface-visibility: hidden;
  background: linear-gradient(321.03deg, #01adef 0%, #0860bf 91.45%);
}

.card-front {
  border-radius: 10px;
  overflow: hidden;
  position: relative;
  transition: transform 300ms ease-in-out;
}

.card-back {
  transform: rotateY(180deg);
}

.card-back::before {
  content: "";
  position: absolute;
  top: 40%;
  left: 20%;
  width: 180%;
  height: 120%;
  border-radius: 100%;
  background-image: linear-gradient(
    to right top,
    #a3d4e7,
    #a7d5e6,
    #abd5e4,
    #aed6e3,
    #b2d6e2,
    #aed4e2,
    #abd3e1,
    #a7d1e1,
    #9bcee1,
    #8ecae1,
    #81c7e1,
    #73c3e1
  );
  filter: blur(10px);
  opacity: 0.15;
}

.card-back::after {
  content: "";
  position: absolute;
  top: 15%;
  width: 100%;
  height: 40px;
  background-image: linear-gradient(
    to right top,
    #021318,
    #07191f,
    #0a1f26,
    #0b262e,
    #0c2c35,
    #0c2c35,
    #0c2c35,
    #0c2c35,
    #0b262e,
    #0a1f26,
    #07191f,
    #021318
  );
}

.card-bg {
  position: absolute;
  top: -20px;
  right: -120px;
  width: 380px;
  height: 250px;
  background: linear-gradient(321.03deg, #01adef 0%, #0860bf 91.45%);
  border-top-left-radius: 100%;
}

.card-bg::before {
  content: "";
  position: absolute;
  top: -20px;
  right: -80px;
  width: 380px;
  height: 250px;
  background: linear-gradient(321.03deg, #01adef 0%, #0860bf 91.45%);
  border-top-left-radius: 100%;
}

.card-bg::after {
  content: "";
  position: absolute;
  top: -20px;
  right: -120px;
  width: 380px;
  height: 250px;
  background: linear-gradient(321.03deg, #01adef 0%, #0860bf 91.45%);
  border-top-left-radius: 100%;
}

.card-glow {
  position: absolute;
  top: -140px;
  left: -65px;
  height: 200px;
  width: 400px;
  background: rgba(0, 183, 255, 0.4);
  filter: blur(10px);
  border-radius: 100%;
  transform: skew(-15deg, -15deg);
}

.card-contactless {
  position: absolute;
  right: 15px;
  top: 55px;
  transform: scale(0.5);
}

.card-chip {
  position: absolute;
  top: 65px;
  left: 25px;
  width: 45px;
  height: 34px;
  border-radius: 5px;
  background-color: #ffda7b;
  overflow: hidden;
}

.card-chip::before {
  content: "";
  position: absolute;
  left: 49%;
  top: -7%;
  transform: translateX(-50%);
  background: #ffda7b;
  border: 1px solid #a27c1f;
  width: 25%;
  height: 110%;
  border-radius: 100%;
  z-index: 2;
}

.card-chip::after {
  content: "";
  position: absolute;
  top: 30%;
  left: -10%;
  background: transparent;
  border: 1px solid #a27c1f;
  width: 120%;
  height: 33%;
}

.card-holder {
  position: absolute;
  left: 25px;
  bottom: 30px;
  color: white;
  font-size: 14px;
  letter-spacing: 0.2em;
  filter: drop-shadow(1px 1px 1px rgba(0, 0, 0, 0.3));
}

.card-number {
  position: absolute;
  left: 25px;
  bottom: 65px;
  color: white;
  font-size: 16px;
  font-weight: 600;
  letter-spacing: 0.2em;
  filter: drop-shadow(1px 1px 1px rgba(0, 0, 0, 0.3));
}

.card-valid {
  position: absolute;
  right: 25px;
  bottom: 30px;
  color: white;
  font-size: 14px;
  letter-spacing: 0.2em;
  filter: drop-shadow(1px 1px 1px rgba(0, 0, 0, 0.3));
}

.card-valid::before {
  content: "GOOD THRU";
  position: absolute;
  top: 1px;
  left: -35px;
  width: 50px;
  font-size: 7px;
}

.card-signature {
  position: absolute;
  top: 120px;
  left: 15px;
  width: 70%;
  height: 30px;
  background: rgb(238, 236, 236);
  display: flex;
  justify-content: center;
  align-items: center;
  color: #021318;
  font-family: "Mr Dafoe", cursive;
  font-size: 18px;
  font-weight: 400;
}

.card-signature::before {
  content: "Authorized Signature";
  position: absolute;
  top: -15px;
  left: 0;
  font-family: "Overpass Mono", monospace;
  font-size: 9px;
  color: rgb(238, 236, 236);
}

.card-seccode {
  position: absolute;
  top: 125px;
  left: 245px;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 40px;
  height: 17px;
  color: #021318;
  background-color: rgb(238, 236, 236);
  text-align: center;
  font-size: 11px;
}

.logo {
  position: absolute;
  right: 25px;
  top: 30px;
}

.hint {
  padding: 2em 0;
  font-family: "Noto Sans KR", sans-serif;
  letter-spacing: 0.025em;
  font-weight: 400;
  color: #a3d4e7;
}

    </style>
    <div class="card">
        <div class="card-inner">
            <div class="card-front">
                <div class="card-bg"></div>
                <div class="card-glow"></div>
                <svg
                    width="72"
                    height="24"
                    viewBox="0 0 72 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    class="logo">
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M52.3973 1.01093L51.5588 5.99054C49.0448 4.56717 43.3231 4.23041 43.3231 6.85138C43.3231 7.89285 44.6177 8.60913 46.178 9.47241C48.5444 10.7817 51.5221 12.4291 51.5221 16.062C51.5221 21.8665 45.4731 24 41.4645 24C37.4558 24 34.8325 22.6901 34.8325 22.6901L35.7065 17.4848C38.1115 19.4688 45.4001 20.032 45.4001 16.8863C45.4001 15.5645 43.9656 14.785 42.3019 13.8811C40.0061 12.6336 37.2742 11.1491 37.2742 7.67563C37.2742 1.30988 44.1978 0 47.1132 0C49.8102 0 52.3973 1.01093 52.3973 1.01093ZM66.6055 23.6006H72L67.2966 0.414276H62.5732C60.3923 0.414276 59.8612 2.14215 59.8612 2.14215L51.0996 23.6006H57.2234L58.4481 20.1566H65.9167L66.6055 23.6006ZM60.1406 15.399L63.2275 6.72235L64.9642 15.399H60.1406ZM14.7942 16.3622L20.3951 0.414917H26.7181L17.371 23.6012H11.2498L6.14551 3.45825C2.83215 1.41281 0 0.807495 0 0.807495L0.108643 0.414917H9.36816C11.9161 0.414917 12.1552 2.50314 12.1552 2.50314L14.1313 12.9281L14.132 12.9294L14.7942 16.3622ZM25.3376 23.6006H31.2126L34.8851 0.414917H29.0095L25.3376 23.6006Z"
                        fill="white" />
                </svg>
                <div class="card-contactless">
                    <svg xmlns="http://www.w3.org/2000/svg" width="46" height="56">
                        <path
                            fill="none"
                            stroke="#f9f9f9"
                            stroke-width="6"
                            stroke-linecap="round"
                            d="m35,3a50,50 0 0,1 0,50M24,8.5a39,39 0 0,1 0,39M13.5,13.55a28.2,28.5
  0 0,1 0,28.5M3,19a18,17 0 0,1 0,18" />
                    </svg>
                </div>
                <div class="card-chip"></div>
                <div class="card-holder">John Doe</div>
                <div class="card-number">1234 5678 9000 1234</div>
                <div class="card-valid">12/24</div>
            </div>
            <div class="card-back">
                <div class="card-signature">John Doe</div>
                <div class="card-seccode">123</div>
            </div>
        </div>
    </div>
    <div class="hint">Hover me ;)</div>
    <script>
    </script>
</body>

</html>