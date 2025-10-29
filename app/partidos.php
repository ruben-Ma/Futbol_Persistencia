<?php

require_once __DIR__ . '/../utils/SessionHelper.php';
require_once __DIR__ . '/../persistence/DAO/EquipoDAO.php';
require_once __DIR__ . '/../persistence/DAO/PartidoDAO.php';

SessionHelper::startSessionIfNotStarted();

$partidos = [];
$equipos = [];
$jornadas = [];
$jornadasDisponibles = []; // Para el combo
$pageTitle = "Gestión de Partidos";

list($success, $error) = SessionHelper::getFlashMessages();

try {
    $equipoDAO = new EquipoDAO();
    $partidoDAO = new PartidoDAO();

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
            // VALIDACIÓN CLAVE: Comprobar si ya han jugado
            $validationError = "Estos dos equipos ya han jugado. No se puede duplicar el partido.";
        }
        
        if ($validationError) {
            // Si hay un error, lo guardamos en la sesión para mostrarlo
            SessionHelper::setFlashMessage('error', $validationError);
        } else {
            // Si todo está OK, insertamos
            if ($partidoDAO->insert($jornada, $localId, $visitanteId, $resultado, $estadio)) {
                SessionHelper::setFlashMessage('success', "Partido agregado a la jornada $jornada.");
            } else {
                SessionHelper::setFlashMessage('error', "Error desconocido al guardar el partido.");
            }
        }
        
        // Usamos el patrón Post-Redirect-Get para evitar reenvíos
        // Redirigimos a la misma página (y jornada)
        header("Location: partidos.php?jornada=" . $jornada);
        exit;
    }

    
    // Obtenemos la jornada seleccionada del combo (o 1 por defecto)
    $jornadaSeleccionada = (int)($_GET['jornada'] ?? 1);
    
    // Pedimos al DAO los datos para rellenar la página
    $partidos = $partidoDAO->getByJornada($jornadaSeleccionada);
    $equipos = $equipoDAO->getAll(); // Para los <select> del formulario
    $jornadas = $partidoDAO->getAllJornadas(); // Para el combo de filtro
    
    // Lógica para el combo: mostrar jornadas existentes y 2 más para añadir
    $maxJornada = empty($jornadas) ? 1 : max($jornadas);
    $jornadasDisponibles = array_unique(array_merge($jornadas, range(1, $maxJornada + 2)));
    sort($jornadasDisponibles);


} catch (Exception $e) {
    $error = "Error crítico de base de datos: " . $e->getMessage();
}

include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/menu.php';


?>

<div class="container mt-4">
    <h2>Partidos por Jornada</h2>

    <!-- Filtro de Jornada (Formulario GET) -->
    <form action="partidos.php" method="GET" class="row mb-3">
        <div class="col-md-4">
            <label for="jornada_select" class="form-label">Ver Jornada:</label>
            <!-- 
    'onchange="this.form.submit()"' hace que la página se recargue
              automáticamente al cambiar de jornada.
            -->
            <select name="jornada" id="jornada_select" class="form-select" onchange="this.form.submit()">
                <?php foreach ($jornadasDisponibles as $j): ?>
                    <option value="<?= $j ?>" <?= ($j == $jornadaSeleccionada) ? 'selected' : '' ?>>
                        Jornada <?= $j ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
    <hr>
    
    <!-- Mensajes de Éxito o Error (del POST) -->
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Resultados Jornada <?= (int)$jornadaSeleccionada ?></h3>
        </div>
        <div class="card-body">
            <?php if (empty($partidos)): ?>
                <p>No hay partidos registrados para esta jornada.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Local</th>
                                <th>Visitante</th>
                                <th>Resultado (1X2)</th>
                                <th>Estadio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($partidos as $partido): ?>
                                <tr>
                                    <td><?= htmlspecialchars($partido['local']) ?></td>
                                    <td><?= htmlspecialchars($partido['visitante']) ?></td>
                                    <td><strong><?= htmlspecialchars($partido['resultado']) ?></strong></td>
                                    <td><?= htmlspecialchars($partido['estadio_partido']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <hr>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Agregar Nuevo Partido (Jornada <?= (int)$jornadaSeleccionada ?>)</h3>
        </div>
        <div class="card-body">
           
            <form action="partidos.php?jornada=<?= (int)$jornadaSeleccionada ?>" method="POST" class="border p-3 bg-light">
                
                <input type="hidden" name="jornada" value="<?= (int)$jornadaSeleccionada ?>">
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="id_local" class="form-label">Equipo Local:</label>
                        <select name="id_local" id="id_local" class="form-select" required>
                            <option value="">-- Seleccione... --</option>
                            <?php foreach ($equipos as $eq): ?>
                                <option value="<?= (int)$eq['id'] ?>"><?= htmlspecialchars($eq['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="id_visitante" class="form-label">Equipo Visitante:</label>
                        <select name="id_visitante" id="id_visitante" class="form-select" required>
                            <option value="">-- Seleccione... --</option>
                            <?php foreach ($equipos as $eq): ?>
                                <option value="<?= (int)$eq['id'] ?>"><?= htmlspecialchars($eq['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="estadio_partido" class="form-label">Estadio del Partido:</label>
                        <input type="text" name="estadio_partido" id="estadio_partido" class="form-control" required>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="resultado" class="form-label">Resultado:</label>
                        <select name="resultado" id="resultado" class="form-select" required>
                            <option value="">-- 1X2 --</option>
                            <option value="1">1</option>
                            <option value="X">X</option>
                            <option value="2">2</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 align-self-end">
                        <button type="submit" class="btn btn-primary w-100">
                            Agregar Partido
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php

include __DIR__ . '/../templates/footer.php';
?>