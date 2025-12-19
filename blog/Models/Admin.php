<?php
require_once __DIR__ . '/../Core/Connection.php';

class Admin {
    private $conn;
    
    public function __construct() {
        $connection = new Connection();
        $this->conn = $connection->connect();
    }
    
    // Authentification de l'admin
    public function authenticate($username, $password) {
        $query = "SELECT * FROM admins WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin) {
            // Vérifier le mot de passe (supporte ancien et nouveau format)
            if (password_verify($password, $admin['password']) || $password === $admin['password']) {
                return $admin;
            }
        }
        
        return false;
    }
    
    // Récupérer admin par email
    public function getAdminByEmail($email) {
        $query = "SELECT * FROM admins WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Sauvegarder le token de réinitialisation
    public function saveResetToken($adminId, $token, $expiry) {
        $query = "UPDATE admins SET reset_token = :token, reset_token_expiry = :expiry WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expiry', $expiry);
        $stmt->bindParam(':id', $adminId);
        
        return $stmt->execute();
    }
    
    // Récupérer admin par token de réinitialisation
    public function getAdminByResetToken($token) {
        $query = "SELECT * FROM admins WHERE reset_token = :token LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Mettre à jour le mot de passe
    public function updatePassword($adminId, $newPassword) {
        // Hasher le nouveau mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        
        $query = "UPDATE admins SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $adminId);
        
        return $stmt->execute();
    }
    
    // Supprimer le token de réinitialisation
    public function clearResetToken($adminId) {
        $query = "UPDATE admins SET reset_token = NULL, reset_token_expiry = NULL WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $adminId);
        
        return $stmt->execute();
    }
}
?>
