<?php //estoy es muy imortante para que se vean las rutas en app
$dir = __DIR__;
$urlApp = '/FUTBOL_PERSISTENCIA/FUTBOL_PERSISTENCIA/';
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Proyecto Liga', ENT_QUOTES, 'UTF-8') ?> - Liga de Fútbol</title>
    
    
<link rel="stylesheet" href="<?php echo $urlApp ?>../assets/css/bootstrap.min.css">   
    
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="bg-black text-white p-3">
        <div class="container">
            <div class="d-flex align-items-center">
                <i class="bi bi-trophy-fill me-3" style="font-size: 1.5rem;"></i>
                <h2 class="mb-0">Liga de Fútbol    </h2>
            </div>
        </div>
    </header>