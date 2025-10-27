<?php
// Fichero: app/partidos.php
require_once __DIR__ . '/utils/functions.php';
require_once __DIR__ . '/utils/SessionHelper.php';
require_once __DIR__ . '/persistence/DAO/EquipoDAO.php';
require_once __DIR__ . '/persistence/DAO/PartidoDAO.php';

SessionHelper::startSessionIfNotStarted();

$partidos = [];
$equipos = [];
$jornadas = [];
$jornadasDisponibles = [];
$pageTitle = "Gestión de Partidos";

list($success, $error) = SessionHelper::getFlashMessages();

try {
    $equipoDAO = new EquipoDAO();
    $partidoDAO = new PartidoDAO();

    // 1. GESTIÓN DEL POST (Agregar partido)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $jornada = (int)($_POST['jornada'] ?? 0);
        $localId = (int)($_POST['id_local'] ?? 0);
        $visitanteId = (int)($_POST['id_visitante'] ?? 0);
        $resultado = trim($_POST['resultado'] ?? '');
        $estadio = trim($_POST['estadio_partido'] ?? '');
        
        $validationError = null;

        if ($jornada <= 0 || $localId <= 0 || $visitanteId <= 0 || empty($estadio) || empty($resultado)) {
            $validationError = "Todos los campos son obligatorios.";
        } elseif ($localId === $visitanteId) {
            $validationError = "Un equipo no puede jugar contra sí mismo.";
        } elseif (!in_array($resultado, ['1', 'X', '2'])) {
            $validationError = "El resultado debe ser '1', 'X', o '2'.";
        } elseif ($partidoDAO->checkPartidoExists($localId, $visitanteId)) {
            $validationError = "Estos dos equipos ya han jugado. No se puede duplicar el partido.";
        }
        
        if ($validationError) {
            SessionHelper::setFlashMessage('error', $validationError);
        } else {
            if ($partidoDAO->insert($jornada, $localId, $visitanteId, $resultado, $estadio)) {
                SessionHelper::setFlashMessage('success', "Partido agregado a la jornada $jornada.");
            } else {
                SessionHelper::setFlashMessage('error', "Error desconocido al guardar el partido.");
            }
        }
        
        header("Location: partidos.php?jornada=" . $jornada);
        exit;
    }

    // 2. GESTIÓN DEL GET (Mostrar)
    $jornadaSeleccionada = (int)($_GET['jornada'] ?? 1);
    
    $partidos = $partidoDAO->getByJornada($jornadaSeleccionada);
    $equipos = $equipoDAO->getAll();
    $jornadas = $partidoDAO->getAllJornadas();
    
    $maxJornada = empty($jornadas) ? 1 : max($jornadas);
    $jornadasDisponibles = array_unique(array_merge($jornadas, range(1, $maxJornada + 2)));
    sort($jornadasDisponibles);


} catch (Exception $e) {
    $error = "Error crítico de base de datos: " . $e->getMessage();
}

include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/menu.php';
include __DIR__ . '/templates/partidos_view.php';
include __DIR__ . '/templates/footer.php';