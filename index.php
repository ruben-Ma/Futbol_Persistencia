<?php
// Fichero: index.php (en la raíz: FUTBOL_PERSISTENCIA/index.php)

// 1. Cargar dependencias (usando rutas absolutas desde la raíz)
require_once __DIR__ . '/utils/functions.php';
require_once __DIR__ . '/utils/SessionHelper.php';
require_once __DIR__ . '/persistence/DAO/EquipoDAO.php';

// 2. Iniciar la sesión
SessionHelper::startSessionIfNotStarted();

// 3. Lógica de sesión
$equipo_favorito = null;
$team_id = SessionHelper::getLastTeamViewed(); 

if ($team_id) {
    try {
        $equipoDAO = new EquipoDAO();
        $equipo = $equipoDAO->getById((int)$team_id);
        if ($equipo) {
            $equipo_favorito = $equipo;
        } else {
            SessionHelper::clearLastTeamViewed();
        }
    } catch (Exception $e) {
        // Error de BBDD
    }
}

// 4. Cargar la Vista (Header)
$pageTitle = "Bienvenida - Liga DAM";
// Las rutas a las plantillas también deben incluir 'app/'
require_once __DIR__ . '/templates/header.php';
require_once __DIR__ . '/templates/menu.php';
?>

<div class="container-fluid py-5 my-5 bg-light">
    <div id="bienvenida" class="container">
        <h1 class='display-3'>Bienvenid@ a la Liga DAM</h1>
        
        <?php if ($equipo_favorito): ?>
            
            <p class'display-6'>
                Bienvenido de vuelta. Tu última consulta fue <strong><?= e($equipo_favorito['nombre']) ?></strong>.
            </p>
            <a href="app/partidos_equipo.php?id=<?= (int)$equipo_favorito['id'] ?>" class="btn btn-primary btn-lg">
                Ver partidos de <?= e($equipo_favorito['nombre']) ?> &raquo;
            </a>
            
        <?php else: ?>
            
            <p class='display-6'>
                Consulta los equipos y los resultados de las jornadas.
            </p>
            <a href="app/equipos.php" class="btn btn-primary btn-lg">
                Ver Equipos &raquo;
            </a>
            <a href="app/partidos.php" class="btn btn-secondary btn-lg">
                Ver Partidos &raquo;
            </a>

        <?php endif; ?>
    </div>
</div>



<?php
// 5. Cargar la Vista (Footer)
require_once __DIR__ . '/app/templates/footer.php';
?>