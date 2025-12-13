<?php
class Utilisateur {
    private $id;
    private $nom;
    private $prenom;
    private $email;
    private $mdp;
    private $adresse;
    private $telephone;
    private $role;

    // Getters et Setters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getAdresse() { return $this->adresse; }
    public function getTelephone() { return $this->telephone; }
    public function getRole() { return $this->role; }

    public function setNom($nom) { $this->nom = $nom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setEmail($email) { $this->email = $email; }
    public function setMdp($mdp) { $this->mdp = password_hash($mdp, PASSWORD_DEFAULT); }
    public function setAdresse($adresse) { $this->adresse = $adresse; }
    public function setTelephone($telephone) { $this->telephone = $telephone; }
    public function setRole($role) { $this->role = $role; }

    // Vérifie si un email existe déjà dans la base de données
    public function emailExists($email) {
        global $db;
        $query = $db->prepare("SELECT id FROM user WHERE email = ?");
        $query->execute([$email]);
        return $query->rowCount() > 0;
    }
    
    // Met à jour le mot de passe d'un utilisateur
    public function updatePassword($email, $newPassword) {
        global $db;
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = $db->prepare("UPDATE user SET mdp = ? WHERE email = ?");
        return $query->execute([$hashedPassword, $email]);
    }

    // Méthode pour enregistrer un nouvel utilisateur
    public function enregistrer($db) {
        $query = $db->prepare("INSERT INTO user (nom, prenom, email, mdp, adresse, telephone, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        try {
            $result = $query->execute([
                $this->nom,
                $this->prenom,
                $this->email,
                $this->mdp,
                $this->adresse,
                $this->telephone,
                $this->role
            ]);
            
            if ($result) {
                $this->id = $db->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur lors de l'inscription: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour trouver un utilisateur par son email
    public static function trouverParEmail($db, $email) {
        try {
            $query = $db->prepare("SELECT * FROM user WHERE email = ?");
            $query->execute([$email]);
            $userData = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($userData) {
                // Vérifier si l'utilisateur est banni
                if (isset($userData['banned']) && $userData['banned'] == 1) {
                    throw new Exception("Ce compte a été banni. Veuillez contacter l'administrateur.");
                }
                
                $user = new self();
                $user->id = $userData['id'];
                $user->nom = $userData['nom'];
                $user->prenom = $userData['prenom'];
                $user->email = $userData['email'];
                $user->mdp = $userData['mdp'];
                $user->adresse = $userData['adresse'];
                $user->telephone = $userData['telephone'];
                $user->role = $userData['role'];
                return $user;
            }
            return null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche par email: " . $e->getMessage());
            return null;
        }
    }

    // Vérifier le mot de passe
    public function verifierMdp($mdp) {
        return password_verify($mdp, $this->mdp);
    }
}
?>
