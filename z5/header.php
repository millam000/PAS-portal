<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_name('Z5SESSION');
    session_start();
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<header>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top mt-0 mb-0 ms-0 me-0 pt-0 pb-0 ps-0 pe-0">
            <div class="container-fluid">
                <div class="collapse navbar-collapse" id="main_nav">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Polecenia 1.x</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="polecenie1_1.php"> <img
                                            src="media/menu_icons/info.svg" height="18"> Polecenie 1.1 </a></li>
                                <li><a class="dropdown-item" href="polecenie1_2.php"> <img
                                            src="media/menu_icons/help.svg" height="18"> Polecenie 1.2 </a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Polecenia 2.x</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="register.php"> Rejestracja z obrazkiem </a></li>
                                <li><a class="dropdown-item" href="polecenie2_2.php"> Polecenie 2.2 </a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Polecenia 3.x</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="polecenie3_1.php"> 3.1 Netstat </a></li>
                                <li><a class="dropdown-item" href="polecenie3_2.php"> 3.2 Informacje o systemie </a>
                                </li>
                                <li><a class="dropdown-item" href="polecenie3_3.php"> 3.3 Informacje o IP </a></li>
                                <li><a class="dropdown-item" href="polecenie3_4.php"> 3.4 Lista gości </a></li>
                                <li><a class="dropdown-item" href="polecenie3_5.php"> 3.5 Lista gości, unikalne IP </a>
                                </li>
                                <li><a class="dropdown-item" href="polecenie3_6.php"> 3.6 Lista gości, unikalne IP,
                                        sortowanie po wejściach </a></li>


                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Polecenia 4.x</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="monitorowanie.php">Panel monitorowania</a></li>
                                <li><a class="dropdown-item" href="dodaj.php">Dodaj host</a></li>
                                <li><a class="dropdown-item" href="kasu.php">Usuń host</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Polecenia 5.x</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="mycloud.php">MyCloud</a></li>
                                <li><a class="dropdown-item" href="polecenie3_4.php">Lista logowań</a></li>
                            </ul>
                        </li>
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                            <li class="nav-item">
                                <span class="nav-link">
                                    <img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" height="24"
                                        class="rounded-circle" alt="avatar">
                                    <?= htmlspecialchars($_SESSION['username']) ?>
                                </span>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Wyloguj
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="login.php">Zaloguj</a></li>
                            <li class="nav-item"><a class="nav-link" href="register.php">Zarejestruj</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main_nav"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>
    </div>
</header>