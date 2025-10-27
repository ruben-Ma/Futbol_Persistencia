<?php
// Fichero: app/equipos.php
// Usamos __DIR__ para que las rutas sean relativas a este fichero (app/)
require_once __DIR__ . '/utils/functions.php';
require_once __DIR__ . '/utils/SessionHelper.php';
require_once __DIR__ . '/persistence/DAO/EquipoDAO.php';

SessionHelper::startSessionIfNotStarted();

$equipos = [];
$pageTitle = "Gestión de Equipos";
list($success, $error) = SessionHelper::getFlashMessages();

try {
    $equipoDAO = new EquipoDAO();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = trim($_POST['nombre'] ?? '');
        $estadio = trim($_POST['estadio'] ?? '');

        if (empty($nombre) || empty($estadio)) {
            SessionHelper::setFlashMessage('error', 'El nombre y el estadio son obligatorios.');
        } else {
            if ($equipoDAO->insert($nombre, $estadio)) {
                SessionHelper::setFlashMessage('success', "Equipo '$nombre' agregado con éxito.");
            } else {
                SessionHelper::setFlashMessage('error', "El equipo '$nombre' ya existe.");
            }
        }
        
        // Redirige a este mismo fichero
        header("Location: equipos.php");
        exit;
    }

    $equipos = $equipoDAO->getAll();

} catch (Exception $e) {
    $error = "Error crítico de base de datos: " . $e->getMessage();
}

// Carga las plantillas (las rutas son relativas a este fichero)
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/menu.php';
include __DIR__ . '/templates/equipos_view.php';
include __DIR__ . '/templates/footer.php';