<?php
session_name('Z5SESSION');
session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit();
?>