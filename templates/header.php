<?php
// Fichero: app/templates/header.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Proyecto Liga') ?> - ArteanV1</title>
    
    <link rel="stylesheet" href="app/assets/css/bootstrap.min.css">
    
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="bg-dark text-white p-3">
        <div class="container">
            <h2>Proyecto Liga (Arquitectura ArteanV1)</h2>
        </div>
    </header>