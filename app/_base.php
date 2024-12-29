<?php

// ============================================================================
// PHP Setups
// ============================================================================

date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();

// ============================================================================
// General Page Functions
// ============================================================================

function validate_address_with_google($address, $apiKey)
        {
            $formattedAddress = urlencode("{$address['line_1']}, {$address['village']}, {$address['city']}, {$address['state']}, {$address['postal_code']}");
            $apiUrl = "https://maps.googleapis.com/maps/api/geocode/json?address=$formattedAddress&key=$apiKey";
    
            $response = file_get_contents($apiUrl);
            if (!$response) {
                return false; // API call failed
            }
    
            $data = json_decode($response, true);
            return isset($data['status']) && $data['status'] === 'OK'; // Address is valid if status is 'OK'
        }
    

function popup($msg, $isSuccess)
{
    echo "<script>showAlertPopup('$msg', $isSuccess);</script>";
}

function cartPopup($imagePath)
{
    echo "<script>showCartPopup('$imagePath');</script>";
}

function priceFormat($price): string
{
    return number_format($price, 2, '.', ',');
}

// Is GET request?
function is_get(): bool
{
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain GET parameter
function get($key, $value = null)
{
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain POST parameter
function post($key, $value = null)
{
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null)
{
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Redirect to target url
function redirect($url = null): null
{
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: " . $url);
    exit();
}

/* 
Set temp session variable with temp(key, value)
Get temp session variable with temp(key, null)
*/
function temp($key, $value = null)
{

    if ($value !== null) {
        $_SESSION['temp_' . $key] = $value;
    } else {
        $value = $_SESSION['temp_' . $key] ?? null;
        unset($_SESSION['temp_' . $key]);
        return $value;
    }
}

// Obtain uploaded file --> cast to object
function get_file($key)
{
    $f = $_FILES[$key] ?? null;

    if ($f && $f['error'] == 0) {
        return (object)$f;
    }

    return null;
}

// Crop, resize and save photo
function save_photo($f, $folder)
{
    $photo = uniqid() . '.png'; // Use PNG to preserve transparency

    require_once 'lib/SimpleImage.php';
    $img = new SimpleImage();

    // Load the file and save it directly without resizing
    $img->fromFile($f->tmp_name)
        ->toFile("$folder/$photo", 'image/png'); // Save as PNG to retain transparency

    return $photo;
}

// Is money?
function is_money($value)
{
    return preg_match('/^\-?\d+(\.\d{1,2})?$/', $value);
}

// Is email?
function is_email($value)
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

// Is date?
function is_date($value, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $value);
    return $d && $d->format($format) == $value;
}

// Is time?
function is_time($value, $format = 'H:i')
{
    $d = DateTime::createFromFormat($format, $value);
    return $d && $d->format($format) == $value;
}

// Return year list items
function get_years($min, $max, $reverse = false)
{
    $arr = range($min, $max);

    if ($reverse) {
        $arr = array_reverse($arr);
    }

    return array_combine($arr, $arr);
}

// Return month list items
function get_months()
{
    return [
        1  => 'January',
        2  => 'February',
        3  => 'March',
        4  => 'April',
        5  => 'May',
        6  => 'June',
        7  => 'July',
        8  => 'August',
        9  => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    ];
}

// Return local root path
function root($path = '')
{
    return "$_SERVER[DOCUMENT_ROOT]/$path";
}

// Return base url (host + port)
function base($path = '')
{
    return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]/$path";
}

// Return TRUE if ALL array elements meet the condition given
function array_all($arr, $fn)
{
    foreach ($arr as $k => $v) {
        if (!$fn($v, $k)) {
            return false;
        }
    }
    return true;
}


// ============================================================================
// HTML Helpers
// ============================================================================

// Generate Logo

function html_logo($width = 50, $length = 50, $darker = false, $aslink = false, $path = null)
{
    $logoPath = '/images/logo.png';
    $ele = $aslink ? 'a' : 'div';
    $link ??= "href='$path'";
    $yellow = 'yellow-light';
    $green = 'green-light';
    if ($darker) {
        $yellow = 'gold';
        $green = 'green-darker';
    }

    echo
    "<$ele $link class='logo'>
    <img src='$logoPath' alt='logo' width='$width' length='$length'>
    <p class='text-$yellow'>BANANA</p>
    <p class='text-$green'>SIS</p>
    </$ele>";
}

// Encode HTML code characters
function encode($value)
{
    return htmlentities($value);
}

// Generate <input type='hidden'>
function html_hidden($key, $attr = '')
{
    $value ??= encode($GLOBALS[$key] ?? '');
    echo "<input type='hidden' id='$key' name='$key' value='$value' $attr>";
}


// Generate <input type='text'>
function html_text($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='password'>
function html_password($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='password' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='number'>
function html_number($key, $min = '', $max = '', $step = '', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='number' id='$key' name='$key' value='$value'
                 min='$min' max='$max' step='$step' $attr>";
}

// Generate <input type='search'>
function html_search($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='search' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='date'>
function html_date($key, $min = '', $max = '', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='date' id='$key' name='$key' value='$value'
                 min='$min' max='$max' $attr>";
}

// Generate <input type='time'>
function html_time($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='time' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='datetime-local'>
function html_datetime($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='datetime-local' id='$key' name='$key' value='$value' $attr>";
}

// Generate <textarea>
function html_textarea($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<textarea id='$key' name='$key' $attr>$value</textarea>";
}

// Generate SINGLE <input type= 'checkbox'>
function html_checkbox($key, $label = '', $inputAttr = '', $labelAttr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    $status = $value == 1 ? 'checked' : '';
    echo "<label $labelAttr><input type='checkbox' id='$key' name='$key' value='1' $status $inputAttr>$label</label>";
}

// Generate <input type='checkbox'> list
function html_checkboxes($key, $items, $br = false)
{
    $values = $GLOBALS[$key] ?? [];
    if (!is_array($values)) $values = [];

    echo '<div>';
    foreach ($items as $id => $text) {
        $state = in_array($id, $values) ? 'checked' : '';
        echo "<label><input type='checkbox' id='{$key}_$id' name='{$key}[]' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}

// Generate <input type='radio'> list
function html_radios($key, $items, $br = false)
{
    $value = encode($GLOBALS[$key] ?? '');
    echo '<div>';
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'checked' : '';
        echo "<label><input type='radio' id='{$key}_$id' name='$key' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}

// Generate <select>
function html_select($key, $items, $default = '- Select One -', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// Generate <input type='file'>
function html_file($key, $accept = '', $attr = '')
{
    echo "<input type='file' id='$key' name='$key' accept='$accept' $attr>";
}

// Generate table headers <th>
function table_headers($fields, $sort, $dir, $href = '')
{
    foreach ($fields as $k => $v) {
        $d = 'asc'; // Default direction
        $c = '';    // Default class

        if ($k == $sort) {
            $d = $dir == 'asc' ? 'desc' : 'asc';
            $c = $dir;
        }

        echo "<th><a href='?sort=$k&dir=$d&$href' class='$c'>$v</a></th>";
    }
}

// ============================================================================
// Error Handlings
// ============================================================================

// Global error array
$_err = [];

// Generate <span class='err'>
function err($key)
{
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<span class='err'>$_err[$key]</span>";
    } else {
        echo '<span></span>';
    }
}

// ============================================================================
// Security
// ============================================================================

// Global user object
$_user = $_SESSION['user'] ?? null;

function require_login()
{
    global $_user;
    if (!$_user) {
        redirect('/page/login.php');
    }
}

function reset_user()
{
    global $_user;
    global $_db;
    $stmt = $_db->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->execute([$_user->customer_id]);
    $_user = $stmt->fetch();
    $_SESSION['user'] = $_user;
}

// Login user
function login($user, $url = '/')
{
    $_SESSION['user'] = $user;
    redirect($url);
}

// Logout user
function logout($url = '/')
{
    if (isset($_COOKIE['remember_me'])) {
        // Clear the token from the database
        global $_db;
        $stmt = $_db->prepare("UPDATE customers SET remember_token = NULL WHERE remember_token = ?");
        $stmt->execute([$_COOKIE['remember_me']]);

        // Clear the cookie
        setcookie('remember_me', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
    }

    unset($_SESSION['user']);
    redirect($url);
}

// Authorization
function auth(...$roles)
{
    global $_user;
    if ($_user) {
        if ($roles) {
            if (in_array($_user->role, $roles)) {
                return; // OK
            }
        } else {
            return; // OK
        }
    }

    redirect('/login.php');
}

// ============================================================================
// Email Functions
// ============================================================================

// Initialize and return mail object
function get_mail()
{
    require_once 'lib/PHPMailer.php';
    require_once 'lib/SMTP.php';

    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->SMTPAuth = true;
    $m->Host = 'smtp.gmail.com';
    $m->Port = 587;
    $m->Username = 'bananasis.com@gmail.com';
    $m->Password = 'lgxf qnts ffkt biaf';
    $m->CharSet = 'utf-8';
    $m->setFrom($m->Username, '🍌BANANASIS👧🏻');

    return $m;
}

// ============================================================================
// Shopping Cart
// ============================================================================

// Get shopping cart
function get_cart(): array
{
    global $_db;
    global $_user;

    // Prepare query
    $stmt = $_db->prepare('SELECT cart FROM customers WHERE customer_id = ?');
    $stmt->execute([$_user->customer_id]);
    $cartRow = $stmt->fetch(PDO::FETCH_OBJ); // Fetch as object

    // If user exists and 'cart' is not empty
    if ($cartRow && !empty($cartRow->cart)) {
        $cart = json_decode($cartRow->cart, true);  // Decode JSON into array
        if (json_last_error() === JSON_ERROR_NONE) {
            set_cart($cart);
            return $cart;
        } else {
            error_log("JSON Decode Error: " . json_last_error_msg());
        }
    }

    return [];
}

// Set shopping cart
function set_cart($cart = [])
{
    global $_db;
    global $_user;

    // Prepare query
    $stmt = $_db->prepare('UPDATE customers SET cart = ? WHERE customer_id = ?');

    // Encode cart into json
    $cart_json = json_encode($cart);
    if (json_last_error() == JSON_ERROR_NONE) {
        try {
            $stmt->execute([$cart_json, $_user->customer_id]);
            $_user->cart = $cart_json;
        } catch (PDOException $e) {
            throw new PDOException("Database Error: " . $e->getMessage(), (int)$e->getCode());
        }
    } else {
        error_log("JSON Decode Error: " . json_last_error_msg());
    }
}


function update_cart($id, $unit)
{
    global $_db;
    global $_user;

    $cart = get_cart();     // Get cart
    $stmt = $_db->prepare('UPDATE customers SET cart = ? WHERE customer_id = ?');  // Prepare update query

    // Update local cart if valid
    if ($unit >= 1 && is_exists($id, 'products', 'product_id')) {
        $cart[$id] = $unit;
    } else {
        unset($cart[$id]);
    }

    // Convert to json
    $cart_json = json_encode($cart);
    if (json_last_error() == JSON_ERROR_NONE) {
        // Write to db
        try {
            $stmt->execute([$cart_json, $_user->customer_id]);
            $_user->cart = $cart_json;
        } catch (PDOException $e) {
            throw new PDOException("Database Error: " . $e->getMessage(), (int)$e->getCode());
        }
    } else {
        error_log("JSON Decode Error: " . json_last_error_msg());
    }
}

// ============================================================================
// Database Setups and Functions
// ============================================================================

// Global PDO object
$_db = new PDO('mysql:dbname=db_bananasis', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);

// Remember ME
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];

    // Verify the token
    $stmt = $_db->prepare("SELECT * FROM customers WHERE remember_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        login($user); // Log the user in
    }
}

// Is unique?
function is_unique($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

// Is exists?
function is_exists($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

//Generate a unique id
function generate_unique_id($prefix, $table, $column, $pdo)
{
    do {
        // Generate the date part
        $date = date('Ymd');

        // Generate the random 6-character suffix
        $random_suffix = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 6);

        // Combine all parts to form the ID
        $generated_id = "$prefix-$date-$random_suffix";

        // Check for collision using is_exists function
        $collision = is_exists($generated_id, $table, $column);
    } while ($collision);

    return $generated_id;
}

function renderStars($rating) {
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars >= 0.5) ? 1 : 0;
    $emptyStars = 5 - $fullStars - $halfStar;

    $stars = str_repeat('<i class="ti ti-star-filled"></i>', $fullStars);
    if ($halfStar) $stars .= '<i class="ti ti-star-half-filled"></i>';
    $stars .= str_repeat('<i class="ti ti-star"></i>', $emptyStars);

    return $stars;
}


// ============================================================================
// Global Constants and Variables
// ============================================================================


// ============================================================================
// Session, Cookie, Cache Handling
// ============================================================================
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];

    // Verify the token
    $stmt = $_db->prepare("SELECT * FROM customers WHERE remember_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        login($user); // Log the user in
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet" />
    <link rel="icon" href="../images/logo.png">
    <link rel="stylesheet" href="../css/base.css" type="text/css">
    <link href="<?= $_css ?>" rel="stylesheet" type="text/css">
    <title><?= $_title ?? 'Untitled' ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="/js/custom.js"></script>
</head>

<body>
    <div id="popup" class="hide">
        <div id="popup-content">
            <h3 id="popup-title">Title</h3>
            <p id="popup-msg">Message</p>
            <button id="popup-btn" type="button">OK</button>
        </div>
    </div>
    <script src="/js/popup.js"></script>
</body>

<?php
$popup_message = temp('popup-msg') ?? null;
if ($popup_message) {
    $msg = $popup_message['msg'];
    $isSuccess = $popup_message['isSuccess'];
    popup($msg, $isSuccess);
}

?>