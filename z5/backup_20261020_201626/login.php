<?php
require_once __DIR__ . '/geo_block.php';
session_name('Z4SESSION');

session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: index.php');
    exit();
}
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logowanie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="twoj_css.css">
</head>

<body class="text-center">
    <main class="form-signin w-100 m-auto" style="max-width:330px;padding:15px;">
        <form method="post" action="weryfikuj.php">
            <i class="bi bi-person-circle" style="font-size:3rem;"></i>
            <h1 class="h3 mb-3 fw-normal">Zaloguj się</h1>
            <?php if ($error === '1'): ?>
                <div class="alert alert-danger">Błędny login lub hasło</div>
            <?php endif; ?>
            <div class="form-floating">
                <input type="text" class="form-control" id="user" name="user" placeholder="Login" required>
                <label for="user">Login</label>
            </div>
            <div class="form-floating mt-2">
                <input type="password" class="form-control" id="pass" name="pass" placeholder="Hasło" required>
                <label for="pass">Hasło</label>
            </div>
            <button class="w-100 btn btn-lg btn-primary mt-3" type="submit">Zaloguj</button>
            <p class="mt-3"><a href="register.php">Zarejestruj się</a></p>
        </form>
    </main>
</body>

</html>