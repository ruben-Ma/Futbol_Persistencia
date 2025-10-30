<?php

require_once __DIR__ . '/../utils/SessionHelper.php';
require_once __DIR__ . '/../persistence/DAO/EquipoDAO.php';
require_once __DIR__ . '/../persistence/DAO/PartidoDAO.php';

SessionHelper::startSessionIfNotStarted();//asegura que la sesion este iniciada y si no lo esta crea una nueva


$equipo = null;
$partidos = [];
$error = null;
$pageTitle = "Partidos de Equipo";


$equipoId = (int)($_GET['id'] ?? 0);//obtiene el id del equipo de la url

if ($equipoId <= 0) {//si el id no es valido
    $error = "ID de equipo no válido.";
} else {
    try {//intenta obtener los datos del equipo y sus partidos
        $equipoDAO = new EquipoDAO();
        $partidoDAO = new PartidoDAO();

       
        $equipo = $equipoDAO->getById($equipoId);//obtiene los datos del equipo

        if ($equipo) {//si el equipo existe
            
            SessionHelper::setLastTeamViewed($equipoId);//guarda en sesion el id del equipo visto por ultima vez
            
          
            $partidos = $partidoDAO->getByEquipoId($equipoId);//obtiene los partidos del equipo
            $pageTitle = "Partidos de " . htmlspecialchars($equipo['nombre']);//titulo de la pagina
        } else {
            $error = "Equipo no encontrado.";
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
        
        <h1 style="color:blue" >Partidos de: <?= htmlspecialchars($equipo['nombre']) ?></h1>
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
                    <thead class="table-primary">
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
include __DIR__ . '/../templates/footer.php';