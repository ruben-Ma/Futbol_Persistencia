<?php

require_once __DIR__ . '/../utils/SessionHelper.php';
require_once __DIR__ . '/../persistence/DAO/EquipoDAO.php';

SessionHelper::startSessionIfNotStarted();

$equipos = [];
$pageTitle = "Gestión de Equipos";
$success = '';
$error = '';


if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

try {
    $equipoDAO = new EquipoDAO();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = trim($_POST['nombre'] ?? '');
        $estadio = trim($_POST['estadio'] ?? '');

        if (empty($nombre) || empty($estadio)) {
            $_SESSION['error'] = 'El nombre y el estadio son obligatorios.';
        } else {
            if ($equipoDAO->insert($nombre, $estadio)) {
                $_SESSION['success'] = "Equipo '$nombre' agregado con éxito.";
            } else {
                $_SESSION['error'] = "El equipo '$nombre' ya existe.";
            }
        }
        
        header("Location: equipos.php");
        exit;
    }

    $equipos = $equipoDAO->getAll();

} catch (Exception $e) {
    $error = "Error crítico de base de datos: " . $e->getMessage();
}

include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/menu.php';
?>

<div class="container mt-4">
    <h1>Gestión de Equipos</h1>
    
    <!-- Mensajes de éxito/error -->
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Formulario para agregar equipos -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Agregar Nuevo Equipo</h3>
        </div>
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-6">
                    <label for="nombre" class="form-label">Nombre del Equipo</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="col-md-6">
                    <label for="estadio" class="form-label">Estadio</label>
                    <input type="text" class="form-control" id="estadio" name="estadio" required>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Agregar Equipo
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Lista de equipos existentes -->
   

<?php
include __DIR__ . '/../templates/footer.php';