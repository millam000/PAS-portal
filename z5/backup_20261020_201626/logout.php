<?php
session_name('Z4SESSION');
session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit();
?>