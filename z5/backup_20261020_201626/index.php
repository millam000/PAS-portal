<?php
declare(strict_types=1);
require_once __DIR__ . '/geo_block.php';

if (session_status() === PHP_SESSION_NONE) {
	session_name('Z4SESSION');
	session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
	header('Location: login.php');
	exit;
}

date_default_timezone_set('Europe/Warsaw');
$link = mysqli_connect("127.0.0.1", "pas", "dAAxGmyHfLtnJi8W8h1FWB8sG", "z4");
mysqli_query($link, "SET NAMES 'utf8'");
$user_id = (int) $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'] ?? false;

$sql = $is_admin
	? "SELECT d.*, u.username FROM domeny d JOIN users u ON d.user_id = u.id ORDER BY d.id"
	: "SELECT * FROM domeny WHERE user_id = $user_id ORDER BY id";

$result = mysqli_query($link, $sql);
$hosts = [];
$playAlarm = false;

while ($row = mysqli_fetch_assoc($result)) {
	$port = (int) $row['port'];
	$fp = @fsockopen($row['host'], $port, $errno, $errstr, 2);

	if ($fp) {
		fclose($fp);
		if ($row['errors'] > 0 || $row['failure'] !== null) {
			mysqli_query($link, "UPDATE domeny SET errors = 0, failure = NULL WHERE id = {$row['id']}");
			$row['errors'] = 0;
			$row['failure'] = null;
		}
		$row['status'] = 'Online';
	} else {
		$errors = $row['errors'] + 1;
		$total = $row['total_errors'] + 1;
		$failure = $row['failure'] ?? date('Y-m-d H:i:s');

		if ((time() - strtotime($failure)) >= 40) {
			$playAlarm = true;
		}

		mysqli_query($link, "UPDATE domeny SET errors = $errors, total_errors = $total, failure = '$failure' WHERE id = {$row['id']}");
		$row['errors'] = $errors;
		$row['total_errors'] = $total;
		$row['failure'] = $failure;
		$row['status'] = 'Offline';
	}
	$hosts[] = $row;
}
mysqli_close($link);

if (isset($_GET['ajax'])) {
	$html = '';
	foreach ($hosts as $h) {
		$html .= "<tr>
            <td>{$h['id']}</td>
            <td>" . htmlspecialchars($h['host']) . "</td>
            <td>{$h['port']}</td>
            <td><span class='badge bg-" . ($h['status'] === 'Online' ? 'success' : 'danger') . "'>{$h['status']}</span></td>
            <td>{$h['errors']}</td>
            <td>" . ($h['failure'] ?: '—') . "</td>
            <td>{$h['total_errors']}</td>";
		if ($is_admin)
			$html .= "<td>" . htmlspecialchars($h['username']) . "</td>";
		$html .= "</tr>";
	}
	header('Content-Type: application/json');
	echo json_encode(['html' => $html, 'playAlarm' => $playAlarm]);
	exit;
}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Monitorowanie usług – Z4</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
	<div id="myHeader"></div>
	<audio id="alarm" src="alarm.mp3" preload="auto"></audio>
	<main class="container mt-5">
		<div class="card shadow-sm">
			<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
				<h5 class="mb-0">Monitorowane usługi</h5>
				<div>
					<button id="snd-btn" class="btn btn-sm btn-secondary me-2">Dźwięk: OFF</button>
					<a href="dodaj.php" class="btn btn-sm btn-light">Dodaj</a>
					<a href="kasu.php" class="btn btn-sm btn-light">Usuń</a>
					<?php if ($is_admin): ?><a href="staff.php"
							class="btn btn-sm btn-light">Pracownicy</a><?php endif; ?>
				</div>
			</div>
			<div class="card-body">
				<table class="table table-striped table-hover align-middle">
					<thead>
						<tr>
							<th>ID</th>
							<th>Host</th>
							<th>Port</th>
							<th>Status</th>
							<th>Błędy</th>
							<th>Ostatnia awaria</th>
							<th>Łączne błędy</th>
							<?php if ($is_admin)
								echo '<th>Właściciel</th>'; ?>
						</tr>
					</thead>
					<tbody id="hosts-tbody">
						<!-- Tutaj JS będzie wstawiał dane -->
					</tbody>
				</table>
			</div>
		</div>
	</main>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script>
		$(document).ready(function () { $('#myHeader').load('header.php'); });

		const audio = document.getElementById('alarm');
		const btn = document.getElementById('snd-btn');

		// Domyślnie dźwięk wyłączony, stan tylko w pamięci strony
		let sndEnabled = false;
		let nextAlarm = 0;

		function updateBtn() {
			btn.textContent = sndEnabled ? 'Dźwięk: ON' : 'Dźwięk: OFF';
			btn.className = sndEnabled ? 'btn btn-sm btn-success me-2' : 'btn btn-sm btn-secondary me-2';
		}
		updateBtn();

		// Funkcja pobierająca dane z serwera
		function fetchData() {
			fetch('index.php?ajax=1')
				.then(response => response.json())
				.then(data => {

					document.getElementById('hosts-tbody').innerHTML = data.html;

					if (data.playAlarm) {
						if (sndEnabled && Date.now() >= nextAlarm) {
							audio.play().catch(e => console.log('Autoplay blocked'));
							nextAlarm = Date.now() + 40000; // Zablokuj na 40s
						}
					} else {
						nextAlarm = 0; // Reset gdy działa
					}
				}).catch(err => console.error("Błąd pobierania:", err));
		}

		// Uruchomienie pobierania od razu i potem co 10 sekund
		fetchData();
		setInterval(fetchData, 10000);

		// Obsługa przycisku dźwięku
		btn.addEventListener('click', () => {
			sndEnabled = !sndEnabled;
			updateBtn();
			if (sndEnabled) {
				nextAlarm = 0; // reset po wciśnięciu przycisku
				fetchData();
			} else {
				audio.pause();
			}
		});
	</script>
</body>

</html>