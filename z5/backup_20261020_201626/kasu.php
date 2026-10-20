<?php
declare(strict_types=1);
require_once __DIR__ . '/geo_block.php';
session_name('Z4SESSION');
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$link = mysqli_connect("127.0.0.1", "pas", "dAAxGmyHfLtnJi8W8h1FWB8sG", "z4");
mysqli_query($link, "SET NAMES 'utf8'");
$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'] ?? false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['host_id'])) {
    $host_id = (int)$_POST['host_id'];
    if ($is_admin) {
        $stmt = mysqli_prepare($link, "DELETE FROM domeny WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $host_id);
    } else {
        $stmt = mysqli_prepare($link, "DELETE FROM domeny WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, 'ii', $host_id, $user_id);
    }
    mysqli_stmt_execute($stmt);
    header('Location: index.php');
    exit();
}

$sql = $is_admin ? "SELECT * FROM domeny ORDER BY host, port" : "SELECT * FROM domeny WHERE user_id = $user_id ORDER BY host, port";
$result = mysqli_query($link, $sql);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuń host – Z4</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="twoj_css.css">
</head>
<body>
    <div id="myHeader"></div>
    <main class="container mt-5" style="max-width:600px;">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Usuń host z monitorowania</h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="host_id" class="form-label">Wybierz host</label>
                        <select class="form-select" id="host_id" name="host_id" required>
                            <option value="">-- wybierz --</option>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <option value="<?= $row['id'] ?>">
                                    <?= htmlspecialchars($row['host']) ?>:<?= $row['port'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">Usuń</button>
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
<?php mysqli_close($link); ?>