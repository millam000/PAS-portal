<?php
$ip = $_SERVER['REMOTE_ADDR'];
$token = '4eec4678c2d69e75e61a5a3b5e8edc85d5d5f05e54c85c809cee662c9df92cbc';
$url = 'https://ip.spyte.xyz/geo/' . urlencode($ip) . '?key=' . urlencode($token);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
$response = curl_exec($ch);
$kod = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$dane = json_decode($response, true);
$country = $dane['country'] ?? '';

if ($country !== 'PL') {
    header('Location: /z3/blocked.php');
    exit;
}
?>