<?php
declare(strict_types=1);
require_once __DIR__ . '/geo_block.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name('Z5SESSION');
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    exit('Brak dostępu.');
}

$home_dir = $_SESSION['home_dir'] ?? 'user_files/' . $_SESSION['username'];
$file = $_GET['file'] ?? '';

$real_home = realpath($home_dir);
$full_path = realpath($home_dir . '/' . $file);

if ($full_path === false || strpos($full_path, $real_home) !== 0 || !is_file($full_path)) {
    http_response_code(404);
    exit('Plik nie istnieje.');
}

$ext = strtolower(pathinfo($full_path, PATHINFO_EXTENSION));
$mime_types = [
    'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
    'gif' => 'image/gif', 'webp' => 'image/webp',
    'mp3' => 'audio/mpeg', 'wav' => 'audio/wav', 'ogg' => 'audio/ogg',
    'mp4' => 'video/mp4', 'webm' => 'video/webm',
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
];
$mime = $mime_types[$ext] ?? 'application/octet-stream';

$is_media = in_array($ext, ['jpg','jpeg','png','gif','webp','mp3','wav','ogg','mp4','webm']);
$disposition = (isset($_GET['download']) || !$is_media) ? 'attachment' : 'inline';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($full_path));
header('Content-Disposition: ' . $disposition . '; filename="' . basename($full_path) . '"');
readfile($full_path);
exit;