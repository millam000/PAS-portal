<?php
require_once __DIR__ . '/geo_block.php';

session_start();
session_name('Z4SESSION');

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Rejestracja</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="twoj_css.css">
</head>
<body>
<main class="container" style="max-width:500px;padding:30px 15px;">
    <h1 class="h3 mb-3 text-center">Rejestracja</h1>
    <?php
        if ($error === '1') echo '<div class="alert alert-danger">Hasła nie są identyczne</div>';
        if ($error === '2') echo '<div class="alert alert-danger">Pola nie mogą być puste</div>';
        if ($error === '3') echo '<div class="alert alert-danger">Taki użytkownik już istnieje</div>';
    ?>
    <form method="post" action="add.php" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="user" class="form-label">Login</label>
            <input type="text" class="form-control" id="user" name="user" maxlength="20" required>
        </div>
        <div class="mb-3">
            <label for="pass" class="form-label">Hasło</label>
            <input type="password" class="form-control" id="pass" name="pass" maxlength="20" required>
        </div>
        <div class="mb-3">
            <label for="pass2" class="form-label">Powtórz hasło</label>
            <input type="password" class="form-control" id="pass2" name="pass2" maxlength="20" required>
        </div>
        <div class="mb-3">
            <label for="avatar" class="form-label">Avatar (opcjonalnie)</label>
            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
        </div>
        <button class="w-100 btn btn-primary" type="submit">Zarejestruj</button>
        <p class="mt-3 text-center"><a href="login.php">Masz już konto? Zaloguj się</a></p>
    </form>
</main>
</body>
</html>