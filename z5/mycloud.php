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

$home_dir = $_SESSION['home_dir'] ?? 'user_files/' . $_SESSION['username'];
if (!is_dir($home_dir)) {
    mkdir($home_dir, 0777, true);
}

$dir = $_GET['dir'] ?? '';
$dir = trim($dir, '/');

$real_home = realpath($home_dir);
$real_dir = realpath($home_dir . '/' . $dir);
if ($real_dir === false || strpos($real_dir, $real_home) !== 0) {
    $dir = '';
}

// wysylanie
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $filename = basename($_FILES['file']['name']);
        $target = $home_dir . '/' . ($dir ? $dir . '/' : '') . $filename;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
            $_SESSION['flash'] = "Plik $filename został przesłany.";
        } else {
            $_SESSION['flash'] = "Błąd podczas przesyłania pliku.";
        }
    }
    if (isset($_POST['new_dir']) && $dir === '') {
        $new_dir = trim($_POST['new_dir']);
        if (preg_match('/^[a-zA-Z0-9_\-]+$/', $new_dir)) {
            $target = $home_dir . '/' . $new_dir;
            if (!is_dir($target)) {
                if (mkdir($target, 0777)) {
                    $_SESSION['flash'] = "Katalog $new_dir został utworzony.";
                } else {
                    $_SESSION['flash'] = "Nie udało się utworzyć katalogu.";
                }
            } else {
                $_SESSION['flash'] = "Katalog o tej nazwie już istnieje.";
            }
        } else {
            $_SESSION['flash'] = "Nazwa katalogu może zawierać tylko litery, cyfry, _ i -.";
        }
    }
    header('Location: mycloud.php?dir=' . urlencode($dir));
    exit();
}

//usuwanie
if (isset($_GET['delete'])) {
    $item = basename($_GET['delete']);
    $path = $home_dir . '/' . ($dir ? $dir . '/' : '') . $item;
    if (is_file($path)) {
        unlink($path);
        $_SESSION['flash'] = "Plik $item został usunięty.";
    } elseif (is_dir($path)) {
        $files = scandir($path);
        if (count($files) <= 2) {
            rmdir($path);
            $_SESSION['flash'] = "Katalog $item został usunięty.";
        } else {
            $_SESSION['flash'] = "Katalog $item nie jest pusty.";
        }
    }
    header('Location: mycloud.php?dir=' . urlencode($dir));
    exit();
}

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$files = scandir($home_dir . '/' . $dir);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyCloud – Dysk sieciowy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="twoj_css.css">
</head>
<body>
<div id="myHeader"></div>
<main class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">MyCloud – Dysk sieciowy</h5>
            <div>
                <a href="mycloud.php" class="btn btn-sm btn-light">Katalog główny</a>
                <?php if ($dir): ?>
                    <a href="mycloud.php" class="btn btn-sm btn-light">Poziom wyżej</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php if ($flash): ?>
                <div class="alert alert-info"><?= htmlspecialchars($flash) ?></div>
            <?php endif; ?>

            <p><strong>Aktualny katalog:</strong> /<?= htmlspecialchars($dir) ?></p>

            <?php if ($dir === ''): ?>
                <form method="post" class="mb-3 d-flex">
                    <input type="text" name="new_dir" class="form-control me-2" placeholder="Nazwa nowego katalogu" required>
                    <button type="submit" class="btn btn-success">Utwórz katalog</button>
                </form>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" class="mb-4 d-flex">
                <input type="file" name="file" class="form-control me-2" required>
                <button type="submit" class="btn btn-primary">Prześlij plik</button>
            </form>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Ikona</th>
                            <th>Nazwa</th>
                            <th>Rozmiar</th>
                            <th>Data</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($files as $file): 
                            if ($file === '.' || $file === '..' || $file === '.htaccess') continue;
                            $path = $home_dir . '/' . ($dir ? $dir . '/' : '') . $file;
                            $rel_path = ($dir ? $dir . '/' : '') . $file;
                            $is_dir = is_dir($path);
                            $size = $is_dir ? '—' : number_format(filesize($path) / 1024, 1) . ' KB';
                            $date = date('Y-m-d H:i', filemtime($path));
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            $icon = $is_dir ? 'bi-folder' : 'bi-file-earmark';
                            if (!$is_dir) {
                                if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) $icon = 'bi-file-image';
                                elseif (in_array($ext, ['mp3','wav','ogg'])) $icon = 'bi-file-music';
                                elseif (in_array($ext, ['mp4','webm','ogg'])) $icon = 'bi-file-play';
                                elseif ($ext === 'pdf') $icon = 'bi-file-pdf';
                                elseif (in_array($ext, ['doc','docx'])) $icon = 'bi-file-word';
                            }
                        ?>
                        <tr>
                            <td><i class="bi <?= $icon ?>" style="font-size:1.5rem;"></i></td>
                            <td>
                                <?php if ($is_dir): ?>
                                    <a href="mycloud.php?dir=<?= urlencode($rel_path) ?>"><?= htmlspecialchars($file) ?></a>
                                <?php else: ?>
                                    <a href="download.php?file=<?= urlencode($rel_path) ?>&download=1"><?= htmlspecialchars($file) ?></a>
                                    <?php if (in_array($ext, ['jpg','jpeg','png','gif','webp'])): ?>
                                        <br><img src="download.php?file=<?= urlencode($rel_path) ?>" style="max-width:100px;max-height:100px;" class="mt-1">
                                    <?php elseif (in_array($ext, ['mp3','wav','ogg'])): ?>
                                        <br><audio controls src="download.php?file=<?= urlencode($rel_path) ?>" class="mt-1" style="max-width:200px;"></audio>
                                    <?php elseif (in_array($ext, ['mp4','webm','ogg'])): ?>
                                        <br><video controls src="download.php?file=<?= urlencode($rel_path) ?>" class="mt-1" style="max-width:200px;"></video>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td><?= $size ?></td>
                            <td><?= $date ?></td>
                            <td>
                                <a href="mycloud.php?dir=<?= urlencode($dir) ?>&delete=<?= urlencode($file) ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Czy na pewno usunąć?')">Usuń</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script>$(document).ready(function(){ $('#myHeader').load('header.php'); });</script>
</body>
</html>