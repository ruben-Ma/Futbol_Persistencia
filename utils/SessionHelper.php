<?php

class SessionHelper {

   
    static function startSessionIfNotStarted() {
        if (session_status() == PHP_SESSION_NONE) { //pregunta si la sesion no ha sido iniciada
            session_start([//genera una nueva sesion con las siguientes caracteristicas
                'cookie_lifetime' => 86400, 
                'cookie_httponly' => true,  
                'cookie_samesite' => 'Strict' 
            ]);
        }
    }


    static function destroySession() {
        self::startSessionIfNotStarted(); //funcion que se encarga de eliminar la sesisomm
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    }

    static function setLastTeamViewed(int $teamId) {
        self::startSessionIfNotStarted();
        $_SESSION['last_team_viewed_id'] = $teamId;//cuando se visita un equipo llama a esta funcion y lo apunta en sesion
        $_SESSION['team_viewed_time'] = time(); // Guardar cuando se vio el equipo
    }

   //obtiene el id del equipo que fue visto por ultima vez que guardo en la  funcion anterior
    static function getLastTeamViewed(): ?int {
        self::startSessionIfNotStarted();
        
        // Si no hay equipo guardado, retornar null
        if (!isset($_SESSION['last_team_viewed_id']) || !isset($_SESSION['team_viewed_time'])) {
            return null;
        }
        
        // Verificar si ha pasado más de 1 minuto
        if ((time() - $_SESSION['team_viewed_time']) > 60) {
            // Caducó, limpiar y retornar null
            unset($_SESSION['last_team_viewed_id']);
            unset($_SESSION['team_viewed_time']);
            return null;
        }
        
        // Aún válido, retornar el ID
        return $_SESSION['last_team_viewed_id'];
    }



       //encargado de los mensajes de error qal introducir o no introducir el usurario en las diferentes paginas
    static function setFlashMessage(string $type, string $message) {
        self::startSessionIfNotStarted();
        $_SESSION['flash_' . $type] = $message;
    }
    static function getFlashMessages(): array {
        self::startSessionIfNotStarted();
        $success = null;
        $error = null;

        if (isset($_SESSION['flash_success'])) {
            $success = $_SESSION['flash_success'];
            unset($_SESSION['flash_success']);
        }
        if (isset($_SESSION['flash_error'])) {
            $error = $_SESSION['flash_error'];
            unset($_SESSION['flash_error']);
        }
        return [$success, $error];
    }
}
