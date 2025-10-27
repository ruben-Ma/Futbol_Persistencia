<?php
// Fichero: app/partidos_equipo.php
require_once __DIR__ . '/utils/functions.php';
require_once __DIR__ . '/utils/SessionHelper.php';
require_once __DIR__ . '/persistence/DAO/EquipoDAO.php';
require_once __DIR__ . '/persistence/DAO/PartidoDAO.php';

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
            // REQUISITO DE SESIÓN: Guardar como último equipo visto
            SessionHelper::setLastTeamViewed($equipoId);
            
            $partidos = $partidoDAO->getByEquipoId($equipoId);
            $pageTitle = "Partidos de " . e($equipo['nombre']);
        } else {
            $error = "Equipo no encontrado.";
            SessionHelper::clearLastTeamViewed(); 
        }

    } catch (Exception $e) {
        $error = "Error crítico de base de datos: " . $e->getMessage();
    }
}

include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/menu.php';
include __DIR__ . '/templates/partidos_equipo_view.php';
include __DIR__ . '/templates/footer.php';