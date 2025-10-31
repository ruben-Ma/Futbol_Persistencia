<?php

require_once __DIR__ . '/utils/SessionHelper.php';

SessionHelper::startSessionIfNotStarted();//asegura que la sesion este iniciada y si no lo esta crea una nueva
$teamId = SessionHelper::getLastTeamViewed();//obtiene el id del ultimo equipo visto de la sesion




if ($teamId) {//redirecciona a la pagina de los partidos del ultimo equipo visto



    header("Location: app/partidos_equipo.php?id=" . $teamId);//redirecciona a la pagina de los partidos del ultimo equipo visto
    exit;



} else {// si no hay ultimo equipo visto en temId



    header("Location: app/equipos.php");//redirecciona a la pagina de equipos
    exit; 


}
?>
