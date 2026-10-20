<?php declare(strict_types=1);
require_once __DIR__ . '/geo_block.php';

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
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
        <?php
        $ipaddress = $_SERVER["REMOTE_ADDR"];
        function ip_details($ip)
        {
            $key = '4eec4678c2d69e75e61a5a3b5e8edc85d5d5f05e54c85c809cee662c9df92cbc';   
            $url = 'https://ip.spyte.xyz/geo/' . urlencode($ip) . '?key=' . urlencode($key);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 2,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_HTTPHEADER => ['Accept: application/json'],
            ]);
            $resp = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($resp === false || $code !== 200) {
                return null;
            }
            return json_decode($resp) ?: null;
        }
        $details = ip_details($ipaddress);
        ?>

        <div class="card mx-auto" style="max-width: 600px;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Geolokalizacja gościa portalu</h5>
            </div>
            <div class="card-body">
                <?php if ($details && !empty($details->country)): ?>
                    <table class="table table-striped mb-3">
                        <tr>
                            <th>IP</th>
                            <td><?= htmlspecialchars($details->ip) ?></td>
                        </tr>
                        <tr>
                            <th>Kraj</th>
                            <td><?= htmlspecialchars($details->country) ?></td>
                        </tr>
                        <tr>
                            <th>Region</th>
                            <td><?= htmlspecialchars($details->region ?? '—') ?></td>
                        </tr>
                        <tr>
                            <th>Miasto</th>
                            <td><?= htmlspecialchars($details->city) ?></td>
                        </tr>
                        <tr>
                            <th>Współrzędne</th>
                            <td><?= htmlspecialchars($details->loc) ?></td>
                        </tr>
                    </table>
                    <a href="https://www.google.pl/maps/place/<?= htmlspecialchars($details->loc) ?>" target="_blank"
                        class="btn btn-primary w-100">
                        Pokaż na Google Maps
                    </a>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        Nie udało się pobrać geolokalizacji dla IP <?= htmlspecialchars($ipaddress) ?>.
                    </div>
                <?php endif; ?>

                <!-- <?php echo json_encode($details) ?> -->

            </div>
        </div>
    </main>

    <?php require_once 'footer.php'; ?>
</body>

</html>