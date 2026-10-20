<?php
session_name('Z4SESSION');
session_start();

$user = htmlentities($_POST['user'], ENT_QUOTES, "UTF-8");
$pass = htmlentities($_POST['pass'], ENT_QUOTES, "UTF-8");

$link = mysqli_connect("127.0.0.1", "pas", "dAAxGmyHfLtnJi8W8h1FWB8sG", "z4");
if (!$link) { echo "Błąd: " . mysqli_connect_errno() . " " . mysqli_connect_error(); exit(); }
mysqli_query($link, "SET NAMES 'utf8'");

$result = mysqli_query($link, "SELECT * FROM users WHERE username='$user'");
$rekord = mysqli_fetch_array($result);

if (!$rekord || $rekord['password'] !== $pass) {
    mysqli_close($link);
    header("Location: login.php?error=1");
    exit();
}

$_SESSION["logged_in"] = true;
$_SESSION["username"]  = $rekord['username'];
$_SESSION["avatar"]    = $rekord['avatar'] ?: 'media/avatars/default.png';
$_SESSION["user_id"]   = $rekord['id'];          // NOWE
$_SESSION["is_admin"]  = ($rekord['username'] === 'admin'); // NOWE

mysqli_close($link);
header("Location: index.php");
exit();
?>