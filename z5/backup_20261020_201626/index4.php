<?php
session_start();
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Miłosz Lamański</title>
</head>

<BODY>
    <?php
    if (!isset($_SESSION["logged_in"])) {
        header('Location: index3.php');
    }

    echo "Użytkownik zalogowany";
    ?>

    <a href="logout.php">Wyloguj</a>
</BODY>

</HTML>