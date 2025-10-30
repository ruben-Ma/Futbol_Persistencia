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

list($success, $error) = SessionHelper::getFlashMessages();//obtiene los mensajes de exito o error de la sesion para mostrarlos en la pagina

try {//intenta obtener los datos necesarios para la pagina
    $equipoDAO = new EquipoDAO();
    $partidoDAO = new PartidoDAO();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {//si se envia el formulario para agregar un partido
        $jornada = (int)($_POST['jornada'] ?? 0);//obtiene los datos del formulario
        $localId = (int)($_POST['id_local'] ?? 0);//obtiene el id del equipo local
        $visitanteId = (int)($_POST['id_visitante'] ?? 0);//obtiene el id del equipo visitante
        $resultado = trim($_POST['resultado'] ?? '');//obtiene el resultado del partido
        $estadio = trim($_POST['estadio_partido'] ?? '');//obtiene el estadio donde se jugo el partido
        
        $validationError = null;//variable para errores de validacion

        if ($jornada <= 0 || $localId <= 0 || $visitanteId <= 0 || empty($estadio) || empty($resultado)) {//valida que todos los campos esten completos
            $validationError = "Todos los campos son obligatorios.";
        } elseif ($localId === $visitanteId) {//valida que los equipos no sean el mismo
            $validationError = "Un equipo no puede jugar contra sí mismo.";
        } elseif (!in_array($resultado, ['1', 'X', '2'])) {//valida que el resultado sea valido
            $validationError = "El resultado debe ser '1', 'X', o '2'.";
        } elseif ($partidoDAO->checkPartidoExists($localId, $visitanteId)) {//validacion clave: comprobar si ya han jugado
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

    
    $jornadaSeleccionada = (int)($_GET['jornada'] ?? 1);//obtiene la jornada seleccionada del combo 
    
    // Pedimos al DAO los datos para rellenar la página
    $partidos = $partidoDAO->getByJornada($jornadaSeleccionada);//obtiene los partidos de la jornada seleccionada
    $equipos = $equipoDAO->getAll(); // Para los <select> del formulario
    $jornadas = $partidoDAO->getAllJornadas(); // Para el combo de filtro
    
    $maxJornada = empty($jornadas) ? 1 : max($jornadas);// mostrar jornadas existentes y 2 + para añadir
    $jornadasDisponibles = array_unique(array_merge($jornadas, range(1, $maxJornada + 2)));//crea un array con las jornadas existentes y 2 mas
    sort($jornadasDisponibles);//ordena las jornadas


} catch (Exception $e) {
    $error = "Error crítico de base de datos: " . $e->getMessage();
}

include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/menu.php';




// HTML de la pagina donde esta la gestion de los partidos
?>

<div class="container-fluid px-4">
    <!-- Header de esta pagina -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="text-center">
                <h1 class="display-4 text-primary fw-bold">
                    <i class="bi bi-calendar-event me-3"></i>Gestión de Partidos
                </h1>
                <p class="lead text-muted">Administra los partidos por jornada</p>
            </div>
        </div>
    </div>

    <!-- Filtro de Jornada -->
    <div class="row mb-4">
        <div class="col-lg-6 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>Seleccionar Jornada
                    </h5>
                </div>
                <div class="card-body">
                    <form action="partidos.php" method="GET" class="d-flex justify-content-center">
                        <div class="col-md-8">
                            <div class="form-floating">
                                <select name="jornada" id="jornada_select" class="form-select" onchange="this.form.submit()">
                                    <?php foreach ($jornadasDisponibles as $j): ?>
                                        <option value="<?= $j ?>" <?= ($j == $jornadaSeleccionada) ? 'selected' : '' ?>>
                                            Jornada <?= $j ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="jornada_select">
                                    <i class="bi bi-calendar-week me-1"></i>Ver Jornada
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
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
                        <label for="estadio_partido" class="form-label">Estadio del Partido:</label>
                        <input type="text" name="estadio_partido" id="estadio_partido" class="form-control" required>
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