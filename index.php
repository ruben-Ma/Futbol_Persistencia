<?php

require_once __DIR__ . '/utils/SessionHelper.php';


SessionHelper::startSessionIfNotStarted();


$teamId = SessionHelper::getLastTeamViewed();

if ($teamId) {
    
    
    header("Location: app/partidos_equipo.php?id=" . $teamId);
    exit;
    
} else {
    
    
    header("Location: app/equipos.php");
    exit;
    
}
?>
