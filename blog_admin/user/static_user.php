<?php
// static_user.php
// Lightweight placeholder for admin integration during development.
// Replace this with the real AuthController/Utilisateur when integrating.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class StaticUser {
    // Call once to ensure a dev admin session exists. Remove when integrating.
    public static function bootstrap() {
        if (!isset($_SESSION['user_id'])) {
            // Static admin identity (change as needed)
            $_SESSION['user_id'] = 'static_admin';
            $_SESSION['user_email'] = 'admin@example.com';
            $_SESSION['user_role'] = 'admin';
            $_SESSION['user_nom'] = 'Static';
            $_SESSION['user_prenom'] = 'Admin';
        }
    }

    public static function estConnecte() {
        return isset($_SESSION['user_id']);
    }

    public static function estAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    // Helper to clear the static session (useful for testing)
    public static function clear() {
        unset($_SESSION['user_id'], $_SESSION['user_email'], $_SESSION['user_role'], $_SESSION['user_nom'], $_SESSION['user_prenom']);
    }
}

// Auto-bootstrap so admin area is accessible during local dev.
StaticUser::bootstrap();

?>
