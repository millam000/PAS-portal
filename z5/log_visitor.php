<?php
session_name('Z5SESSION');
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    exit;
}

$link = mysqli_connect("127.0.0.1", "pas", "dAAxGmyHfLtnJi8W8h1FWB8sG", "z5");
if (!$link) { echo "Błąd: " . mysqli_connect_error(); exit; }
mysqli_query($link, "SET NAMES 'utf8'");

$ip = $_SERVER['REMOTE_ADDR'];
$user = $_SESSION['username'] ?? null; 

$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$info = @get_browser($ua, true);
$browser = $info['browser'] ?? 'Nieznana';

$screen_res = $_POST['screen_res'] ?? '';
$window_res = $_POST['window_res'] ?? '';
$colors     = $_POST['colors'] ?? '';
$cookies    = isset($_POST['cookies']) ? (int)$_POST['cookies'] : 0;
$java       = isset($_POST['java']) ? (int)$_POST['java'] : 0;
$lang       = $_POST['language'] ?? '';

$stmt = mysqli_prepare($link,
    "INSERT INTO goscieportalu 
    (ipaddress, browser, screen_res, window_res, screen_colors, cookies_enabled, java_enabled, language, event_type, username) 
    VALUES (?,?,?,?,?,?,?,?, 'visit', ?)"
);

mysqli_stmt_bind_param($stmt, 'sssssisss', $ip, $browser, $screen_res, $window_res, $colors, $cookies, $java, $lang, $user);
mysqli_stmt_execute($stmt);
mysqli_close($link);

echo json_encode(['status' => 'ok']);
?>