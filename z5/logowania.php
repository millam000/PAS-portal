<?php
declare(strict_types=1);
require_once __DIR__ . '/geo_block.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name('Z5SESSION');
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$is_admin = $_SESSION['is_admin'] ?? false;
$me       = $_SESSION['username'] ?? '';

// filtry
$filter_user  = $_GET['user']  ?? '';
$filter_event = $_GET['event'] ?? '';

$link = mysqli_connect("127.0.0.1", "pas", "dAAxGmyHfLtnJi8W8h1FWB8sG", "z5");
if (!$link) { echo "Błąd: " . mysqli_connect_error(); exit(); }
mysqli_query($link, "SET NAMES 'utf8'");

$where  = ["event_type IN ('login_success','login_fail')"];
$params = [];
$types  = '';

if (!$is_admin) {
    $where[]  = "username = ?";
    $params[] = $me;
    $types   .= 's';
} elseif ($filter_user !== '') {
    $where[]  = "username = ?";
    $params[] = $filter_user;
    $types   .= 's';
}

if (in_array($filter_event, ['login_success','login_fail'], true)) {
    $where[]  = "event_type = ?";
    $params[] = $filter_event;
    $types   .= 's';
}

$sql = "SELECT * FROM goscieportalu WHERE " . implode(' AND ', $where) . " ORDER BY datetime DESC LIMIT 500";

$stmt = mysqli_prepare($link, $sql);
if ($types !== '') {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Miłosz Lamański</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="twoj_css.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="twoj_js.js"></script>
</head>
<body>
<div id="myHeader"></div>
<main class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Logi logowań</h5>
        </div>
        <div class="card-body">

            <form method="get" class="row g-2 mb-3">
                <?php if ($is_admin): ?>
                <div class="col-md-4">
                    <input type="text" name="user" class="form-control"
                           placeholder="Filtruj po loginie" value="<?= htmlspecialchars($filter_user) ?>">
                </div>
                <?php endif; ?>
                <div class="col-md-4">
                    <select name="event" class="form-select">
                        <option value="">— wszystkie zdarzenia —</option>
                        <option value="login_success" <?= $filter_event === 'login_success' ? 'selected' : '' ?>>Udane</option>
                        <option value="login_fail"    <?= $filter_event === 'login_fail'    ? 'selected' : '' ?>>Nieudane</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Filtruj</button>
                </div>
                <div class="col-md-2">
                    <a href="logowania.php" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Użytkownik</th>
                            <th>Zdarzenie</th>
                            <th>IP</th>
                            <th>Przeglądarka</th>
                            <th>Ekran</th>
                            <th>Okno</th>
                            <th>Kolory</th>
                            <th>Cookies</th>
                            <th>Java</th>
                            <th>Język</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)):
                        $ok = $row['event_type'] === 'login_success';
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['datetime']) ?></td>
                            <td><?= htmlspecialchars($row['username'] ?? '—') ?></td>
                            <td>
                                <span class="badge bg-<?= $ok ? 'success' : 'danger' ?>">
                                    <?= $ok ? 'Udane' : 'Nieudane' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['ipaddress']) ?></td>
                            <td><?= htmlspecialchars($row['browser']    ?? '—') ?></td>
                            <td><?= htmlspecialchars($row['screen_res'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($row['window_res'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($row['screen_colors'] ?? '—') ?></td>
                            <td><?= isset($row['cookies_enabled']) ? ($row['cookies_enabled'] ? 'Tak' : 'Nie') : '—' ?></td>
                            <td><?= isset($row['java_enabled'])    ? ($row['java_enabled']    ? 'Tak' : 'Nie') : '—' ?></td>
                            <td><?= htmlspecialchars($row['language'] ?? '—') ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <?php if (mysqli_num_rows($result) === 0): ?>
                <div class="alert alert-info mb-0">Brak wpisów dla wybranych filtrów.</div>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php mysqli_close($link); ?>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script>$(document).ready(function(){ $('#myHeader').load('header.php'); });</script>
</body>
</html>