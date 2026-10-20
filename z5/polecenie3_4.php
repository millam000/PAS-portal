<?php declare(strict_types=1);
require_once __DIR__ . '/geo_block.php';

session_name('Z5SESSION');
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$event = $_GET['event'] ?? 'all';
$allowed_events = ['visit', 'login_success', 'login_fail', 'all'];
if (!in_array($event, $allowed_events, true)) {
    $event = 'visit';
}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Twój Opis">
    <meta name="author" content="Twoje dane">
    <meta name="keywords" content="Twoje słowa kluczowe">
    <title>Miłosz Lamański</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <style type="text/css" class="init"></style>
    <link rel="stylesheet" type="text/css" href="twoj_css.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" src="twoj_js.js"></script>
</head>

<body onload="myLoadHeader()">
    <div id='myHeader'></div>

    <main class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Goście portalu</h5>
                <form method="get" class="d-flex gap-2 mb-0">
                    <select name="event" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="visit"        <?= $event === 'visit'        ? 'selected' : '' ?>>Tylko wizyty</option>
                        <option value="login_success"<?= $event === 'login_success'? 'selected' : '' ?>>Udane logowania</option>
                        <option value="login_fail"   <?= $event === 'login_fail'   ? 'selected' : '' ?>>Nieudane logowania</option>
                        <option value="all"          <?= $event === 'all'          ? 'selected' : '' ?>>Wszystko</option>
                    </select>
                </form>
            </div>
            <div class="card-body">

                <?php
                function geo_lookup(string $ip): ?object
                {
                    static $cache = [];

                    if (array_key_exists($ip, $cache)) {
                        return $cache[$ip];
                    }

                    $key = '4eec4678c2d69e75e61a5a3b5e8edc85d5d5f05e54c85c809cee662c9df92cbc';
                    $url = 'https://ip.spyte.xyz/geo/' . urlencode($ip) . '?key=' . urlencode($key);

                    $ch = curl_init($url);
                    curl_setopt_array($ch, [
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT        => 2,
                        CURLOPT_SSL_VERIFYPEER => true,
                        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
                    ]);
                    $resp = curl_exec($ch);
                    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if ($resp === false || $code !== 200) {
                        return $cache[$ip] = null;
                    }
                    return $cache[$ip] = (json_decode($resp) ?: null);
                }

                $link = mysqli_connect("127.0.0.1", "pas", "dAAxGmyHfLtnJi8W8h1FWB8sG", "z5");
                mysqli_query($link, "SET NAMES 'utf8'");

                if ($event === 'all') {
                    $sql = "SELECT * FROM goscieportalu ORDER BY datetime DESC";
                    $result = mysqli_query($link, $sql);
                } else {
                    $stmt = mysqli_prepare($link,
                        "SELECT * FROM goscieportalu WHERE event_type = ? ORDER BY datetime DESC");
                    mysqli_stmt_bind_param($stmt, 's', $event);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                }

                $labels = [
                    'visit'         => ['Wizyta',        'secondary'],
                    'login_success' => ['Logowanie OK',  'success'],
                    'login_fail'    => ['Logowanie FAIL','danger'],
                ];

                echo "<div class='table-responsive'>";
                echo "<table class='table table-striped mb-0'>";
                echo "<thead><tr>
                        <th>Typ</th>
                        <th>Użytkownik</th>
                        <th>IP</th>
                        <th>Data</th>
                        <th>Przeglądarka</th>
                        <th>Ekran</th>
                        <th>Okno</th>
                        <th>Kolory</th>
                        <th>Cookies</th>
                        <th>Java</th>
                        <th>Język</th>
                        <th>Geolokalizacja</th>
                        <th>Mapa</th>
                      </tr></thead><tbody>";

                while ($row = mysqli_fetch_assoc($result)) {
                    $ip = $row['ipaddress'];

                    $details = geo_lookup($ip);

                    $loc     = $details->loc     ?? '';
                    $city    = $details->city    ?? '';
                    $region  = $details->region  ?? '';
                    $country = $details->country ?? '';

                    $etype  = $row['event_type'] ?? 'visit';
                    [$elabel, $ecolor] = $labels[$etype] ?? [$etype, 'secondary'];

                    echo "<tr>";
                    echo "<td><span class='badge bg-$ecolor'>" . htmlspecialchars($elabel) . "</span></td>";
                    echo "<td>" . htmlspecialchars($row['username'] ?? '—') . "</td>";
                    echo "<td>" . htmlspecialchars($ip) . "</td>";
                    echo "<td>" . htmlspecialchars($row['datetime']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['browser']       ?? '—') . "</td>";
                    echo "<td>" . htmlspecialchars($row['screen_res']    ?? '—') . "</td>";
                    echo "<td>" . htmlspecialchars($row['window_res']    ?? '—') . "</td>";
                    echo "<td>" . htmlspecialchars($row['screen_colors'] ?? '—') . "</td>";
                    echo "<td>" . (isset($row['cookies_enabled']) ? ($row['cookies_enabled'] ? 'Tak' : 'Nie') : '—') . "</td>";
                    echo "<td>" . (isset($row['java_enabled'])    ? ($row['java_enabled']    ? 'Tak' : 'Nie') : '—') . "</td>";
                    echo "<td>" . htmlspecialchars($row['language'] ?? '—') . "</td>";

                    if ($country || $city) {
                        echo "<td>" . htmlspecialchars(trim("$city, $region, $country", ', ')) . "</td>";
                    } else {
                        echo "<td>Brak</td>";
                    }

                    if ($loc) {
                        echo "<td><a href='https://www.google.pl/maps/place/" . htmlspecialchars($loc) . "' target='_blank'>LINK</a></td>";
                    } else {
                        echo "<td>Brak</td>";
                    }
                    echo "</tr>";
                }
                echo "</tbody></table></div>";
                mysqli_close($link);
                ?>

            </div>
        </div>
    </main>

    <?php require_once 'footer.php'; ?>
</body>

</html>