<?php

require_once __DIR__ . '/../utils/SessionHelper.php';
require_once __DIR__ . '/../persistence/DAO/EquipoDAO.php';
require_once __DIR__ . '/../persistence/DAO/PartidoDAO.php';

SessionHelper::startSessionIfNotStarted();


$equipo = null;
$partidos = [];
$error = null;
$pageTitle = "Partidos de Equipo";


$equipoId = (int)($_GET['id'] ?? 0);

if ($equipoId <= 0) {
    $error = "ID de equipo no válido.";
} else {
    try {
        $equipoDAO = new EquipoDAO();
        $partidoDAO = new PartidoDAO();

       
        $equipo = $equipoDAO->getById($equipoId);

        if ($equipo) {
            
            SessionHelper::setLastTeamViewed($equipoId);
            
          
            $partidos = $partidoDAO->getByEquipoId($equipoId);
            $pageTitle = "Partidos de " . htmlspecialchars($equipo['nombre']);
        } else {
            $error = "Equipo no encontrado.";
          
            SessionHelper::clearLastTeamViewed(); 
        }

    } catch (Exception $e) {
        $error = "Error crítico de base de datos: " . $e->getMessage();
    }
}


include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/menu.php';


?>

<div class="container mt-4">

    <?php if ($error): ?>
        
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>

    <?php elseif ($equipo): ?>
        
        <h1>Partidos de: <?= htmlspecialchars($equipo['nombre']) ?></h1>
        <p class="lead">Estadio: <?= htmlspecialchars($equipo['estadio']) ?></p>

        <?php if (empty($partidos)): ?>
            <!-- Mensaje si no hay partidos -->
            <div class="alert alert-info text-center mt-5">
                <h4 class="alert-heading">Sin Partidos</h4>
                <p>Este equipo aún no ha jugado ningún partido.</p>
            </div>
            
        <?php else: ?>
            <!-- Tabla de partidos -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Jornada</th>
                            <th>Local</th>
                            <th>Visitante</th>
                            <th>Resultado (1X2)</th>
                            <th>Estadio del Partido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($partidos as $partido): ?>
                            <tr>
                                <td><?= htmlspecialchars($partido['jornada']) ?></td>
                                <td><?= htmlspecialchars($partido['local']) ?></td>
                                <td><?= htmlspecialchars($partido['visitante']) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($partido['resultado']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($partido['estadio_partido']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</div>

<?php
// 8. Cargar el FOOTER
include __DIR__ . '/../templates/footer.php';