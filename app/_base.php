<?php

// Basic PHP Setup
date_default_timezone_set('Asia/Kuala_Lumpur'); // Set Timezone
session_start(); // Enable session

function redirect($url = null) {
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: ".$url);
    exit();
}

?>