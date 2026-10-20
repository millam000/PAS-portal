<?php
session_name('Z4SESSION');
session_start();

$user = htmlentities($_POST['user'], ENT_QUOTES, "UTF-8");
$pass = htmlentities($_POST['pass'], ENT_QUOTES, "UTF-8");
$pass_repeat = htmlentities($_POST['pass2'], ENT_QUOTES, "UTF-8");

if ($pass_repeat !== $pass) {
    header("Location: register.php?error=1");
    exit();
}
if ($user === "" || $pass === "") {
    header("Location: register.php?error=2");
    exit();
}

$link = mysqli_connect("127.0.0.1", "pas", "dAAxGmyHfLtnJi8W8h1FWB8sG", "z4");
if (!$link) {
    echo "Błąd: " . mysqli_connect_errno() . " " . mysqli_connect_error();
    exit();
}
mysqli_query($link, "SET NAMES 'utf8'");

$result = mysqli_query($link, "SELECT * FROM users WHERE username='$user'");
if (mysqli_fetch_array($result)) {
    mysqli_close($link);
    header("Location: register.php?error=3");
    exit();
}

// domyślny avatar
$avatar = 'media/avatars/default.png';

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    if (!is_dir('media/avatars')) {
        mkdir('media/avatars', 0777, true);
    }
    $target = 'media/avatars/' . uniqid() . '_' . basename($_FILES['avatar']['name']);
    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
        $avatar = $target;
    }
}

$ok = mysqli_query($link, "INSERT INTO users (username, password, avatar, worker) VALUES ('$user', '$pass', '$avatar', 0)");mysqli_close($link);

if ($ok) {
    header("Location: login.php");
    exit();
}
header("Location: register.php");
exit();
?>