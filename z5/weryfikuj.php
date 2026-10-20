<?php
session_name('Z5SESSION');
session_start();

$user = htmlentities($_POST['user'], ENT_QUOTES, "UTF-8");
$pass = htmlentities($_POST['pass'], ENT_QUOTES, "UTF-8");

$screen_res = $_POST['screen_res'] ?? '';
$window_res = $_POST['window_res'] ?? '';
$colors     = $_POST['colors']     ?? '';
$cookies    = isset($_POST['cookies']) ? (int)$_POST['cookies'] : 0;
$java       = isset($_POST['java'])    ? (int)$_POST['java']    : 0;
$lang       = $_POST['language']   ?? '';

// Oryginalne wyciąganie przeglądarki
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$info = @get_browser($ua, true);
$browser = $info['browser'] ?? 'Nieznana'; // Wyciągamy tylko nazwę, bez wersji

$link = mysqli_connect("127.0.0.1", "pas", "dAAxGmyHfLtnJi8W8h1FWB8sG", "z5");
if (!$link) { echo "Błąd: " . mysqli_connect_errno() . " " . mysqli_connect_error(); exit(); }
mysqli_query($link, "SET NAMES 'utf8'");

$ip = $_SERVER['REMOTE_ADDR'];

$result = mysqli_query($link, "SELECT * FROM users WHERE username='$user'");
$rekord = mysqli_fetch_array($result);

$login_ok = $rekord && $rekord['password'] === $pass;
$event_type = $login_ok ? 'login_success' : 'login_fail';

// POPRAWNE TYPY: sssssiisss (5 stringów, 2 inty, 3 stringi)
$stmt = mysqli_prepare($link,
    "INSERT INTO goscieportalu
        (ipaddress, browser, screen_res, window_res, screen_colors,
         cookies_enabled, java_enabled, language, event_type, username)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
     
mysqli_stmt_bind_param($stmt, 'sssssiisss',
    $ip, $browser, $screen_res, $window_res, $colors,
    $cookies, $java, $lang, $event_type, $user);
mysqli_stmt_execute($stmt);

if (!$login_ok) {
    mysqli_close($link);
    header("Location: login.php?error=1");
    exit();
}

$_SESSION["logged_in"] = true;
$_SESSION["username"]  = $rekord['username'];
$_SESSION["avatar"]    = $rekord['avatar'] ?: 'media/avatars/default.png';
$_SESSION["user_id"]   = $rekord['id'];
$_SESSION["is_admin"]  = ($rekord['username'] === 'admin');
$_SESSION["home_dir"]  = 'user_files/' . $rekord['username'];

mysqli_close($link);
header("Location: index.php");
exit();
?>