
<?php

$currentPage = basename($_SERVER['PHP_SELF']);
$isInApp = strpos($_SERVER['PHP_SELF'], '/app/') !== false;
$basePath = $isInApp ? '' : 'app/';
$homePath = $isInApp ? '../index.php' : 'index.php';
?>


<link rel="stylesheet" href="<?php echo $urlApp ?>../assets/css/bootstrap.min.css">   
<nav class="navbar navbar-expand-lg navbar-dark bg-black mb-4">
    <div class="container">
        
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'equipos.php') ? 'active' : '' ?>" 
                       href="<?= $basePath ?>equipos.php">
                        <i class="bi bi-shield-fill me-1"></i>Equipos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'partidos.php') ? 'active' : '' ?>" 
                       href="<?= $basePath ?>partidos.php">
                        <i class="bi bi-calendar-event me-1"></i>Partidos
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>