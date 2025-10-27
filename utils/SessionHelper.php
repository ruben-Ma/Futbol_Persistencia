<?php
// Fichero: app/utils/SessionHelper.php

class SessionHelper {

    static function startSessionIfNotStarted() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start([
                'cookie_lifetime' => 86400,
                'cookie_httponly' => true,
                'cookie_samesite' => 'Strict'
            ]);
        }
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