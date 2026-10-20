<?php
declare(strict_types=1);
require_once __DIR__ . '/geo_block.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name('Z4SESSION');
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id === 0) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = trim($_POST['host'] ?? '');
    $port = (int)($_POST['port'] ?? 0);
    
    if ($host === '' || $port < 1 || $port > 65535) {
        $error = 'Podaj poprawny host i port.';
    } else {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $link = mysqli_connect("127.0.0.1", "pas", "dAAxGmyHfLtnJi8W8h1FWB8sG", "z4");
            mysqli_query($link, "SET NAMES 'utf8'");
            
            $stmt = mysqli_prepare($link, "INSERT INTO domeny (host, port, user_id) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sii', $host, $port, $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_close($link);
            
            header('Location: index.php');
            exit();
        } catch (mysqli_sql_exception $e) {
            $error = "Błąd bazy danych: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dodaj host – Z4</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="twoj_css.css">
</head>
<body>
    <div id="myHeader"></div>
    <main class="container mt-5" style="max-width:600px;">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Dodaj nowy host do monitorowania</h5>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="host" class="form-label">Host (nazwa domenowa lub IP)</label>
                        <input type="text" class="form-control" id="host" name="host" required>
                    </div>
                    <div class="mb-3">
                        <label for="port" class="form-label">Port</label>
                        <input type="number" class="form-control" id="port" name="port" min="1" max="65535" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Dodaj</button>
                    <a href="index.php" class="btn btn-secondary w-100 mt-2">Powrót</a>
                </form>
            </div>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script>
        $(document).ready(function(){ $('#myHeader').load('header.php'); });
    </script>
</body>
</html>