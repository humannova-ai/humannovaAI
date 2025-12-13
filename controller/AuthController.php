<?php
require_once __DIR__ . '/../model/Utilisateur.php';
require_once __DIR__ . '/../config/database.php';

class AuthController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Gérer l'inscription
    public function register($data) {
        try {
            // Journalisation des données reçues pour le débogage
            error_log("Données reçues pour l'inscription: " . print_r($data, true));
            
            // Validation des données
            $required = ['nom', 'prenom', 'email', 'mdp', 'confirmer_mdp', 'adresse', 'telephone'];
            $missingFields = [];
            
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                $fields = implode(', ', $missingFields);
                return ['success' => false, 'message' => "Champs obligatoires manquants : $fields"];
            }

            // Vérification de l'email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Format d\'email invalide'];
            }

            // Vérification de la correspondance des mots de passe
            if ($data['mdp'] !== $data['confirmer_mdp']) {
                return ['success' => false, 'message' => 'Les mots de passe ne correspondent pas'];
            }

            // Vérifier si l'email existe déjà
            $existingUser = Utilisateur::trouverParEmail($this->conn, $data['email']);
            if ($existingUser) {
                return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
            }

            try {
                // Création du nouvel utilisateur
                $user = new Utilisateur();
                $user->setNom(htmlspecialchars($data['nom']));
                $user->setPrenom(htmlspecialchars($data['prenom']));
                $user->setEmail(htmlspecialchars($data['email']));
                $user->setMdp($data['mdp']);
                $user->setAdresse(htmlspecialchars($data['adresse']));
                $user->setTelephone(htmlspecialchars($data['telephone']));
                $user->setRole('utilisateur'); // Rôle par défaut

                // Enregistrement de l'utilisateur
                if ($user->enregistrer($this->conn)) {
                    return [
                        'success' => true, 
                        'message' => 'Inscription réussie ! Vous pouvez maintenant vous connecter.',
                        'redirect' => 'login.php'
                    ];
                } else {
                    error_log("Échec de l'enregistrement dans la base de données");
                    return ['success' => false, 'message' => 'Échec de l\'enregistrement dans la base de données'];
                }
            } catch (Exception $e) {
                error_log("Erreur lors de la création de l'utilisateur: " . $e->getMessage());
                return ['success' => false, 'message' => 'Erreur lors de la création du compte: ' . $e->getMessage()];
            }
            
        } catch (Exception $e) {
            error_log("Erreur dans register: " . $e->getMessage());
            return ['success' => false, 'message' => 'Une erreur technique est survenue. Veuillez réessayer.'];
        }
    }

    // Gérer la connexion
    public function login($email, $mdp) {
        if (empty($email) || empty($mdp)) {
            return ['success' => false, 'message' => 'Tous les champs sont obligatoires'];
        }

        $user = Utilisateur::trouverParEmail($this->conn, $email);
        
        if (!$user || !$user->verifierMdp($mdp)) {
            return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
        }

        // Connexion réussie
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_email'] = $user->getEmail();
        $_SESSION['user_role'] = $user->getRole();
        $_SESSION['user_nom'] = $user->getNom();
        $_SESSION['user_prenom'] = $user->getPrenom();
        
        // Retourner la redirection au format JSON
        return [
            'success' => true,
            'message' => 'Connexion réussie',
            'redirect' => $user->getRole() === 'admin' ? '../view/dashboard.php' : '../view/home.php'
        ];
    }

    // Déconnexion
    public function logout() {
        // Détruire toutes les données de session
        session_unset();
        session_destroy();
        
        // Rediriger vers la page de connexion
        header('Location: login.php');
        exit();
    }

    // Vérifier si l'utilisateur est connecté
    public static function estConnecte() {
        return isset($_SESSION['user_id']);
    }

    // Vérifier si l'utilisateur est administrateur
    public static function estAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
}
?>
