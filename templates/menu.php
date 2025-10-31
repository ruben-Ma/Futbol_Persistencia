
<?php

//funciones para el menu y mover entre pantallas

$currentPage = basename($_SERVER['PHP_SELF']); //la ruta del fichero que se esta ejecunatndo
$isInApp = strpos($_SERVER['PHP_SELF'], '/app/') !== false;//esto sirve para ver si esta no esta en app, si es false no esta por eso inicia en false por index
$basePath = $isInApp ? '' : 'app/';//si ya estamos dentro de app no hace falta poner app/ en los enlaces
$homePath = $isInApp ? '../index.php' : 'index.php';
?>

<!-- estilo y diseño del menu -->
<link rel="stylesheet" href="<?php echo $urlApp ?>../assets/css/bootstrap.min.css">   
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">   
            
            <li class="nav-item ">
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