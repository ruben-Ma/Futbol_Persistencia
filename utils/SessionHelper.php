<?php

class SessionHelper {

   
    static function startSessionIfNotStarted() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start([
                'cookie_lifetime' => 86400, // 1 día
                'cookie_httponly' => true,  // No accesible por JS
                'cookie_samesite' => 'Strict' // Mitiga CSRF
            ]);
        }
    }

    static function destroySession() {
        self::startSessionIfNotStarted();
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
        $_SESSION['last_team_viewed_id'] = $teamId;
    }

   
    static function getLastTeamViewed(): ?int {
        self::startSessionIfNotStarted();
        return $_SESSION['last_team_viewed_id'] ?? null;
    }

    
    static function clearLastTeamViewed() {
        self::startSessionIfNotStarted();
        unset($_SESSION['last_team_viewed_id']);
    }

   
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
