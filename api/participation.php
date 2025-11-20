<?php
/**
 * API Participation - Version améliorée avec debugging
 */

// Activer TOUS les messages d'erreur
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Fonction pour envoyer la réponse JSON
function sendResponse($success, $message, $data = array()) {
    echo json_encode(array_merge(array(
        'success' => $success,
        'message' => $message
    ), $data));
    exit();
}

// Log de debug
function logDebug($message) {
    error_log("[PARTICIPATION API] " . $message);
}

logDebug("=== NOUVELLE REQUÊTE ===");
logDebug("Method: " . $_SERVER['REQUEST_METHOD']);
logDebug("Action: " . ($_GET['action'] ?? $_POST['action'] ?? 'none'));

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, "Méthode non autorisée (utilisez POST)");
}

// Inclure les fichiers
try {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../models/Utilisateur.php';
    require_once __DIR__ . '/../models/Evenement.php';
    logDebug("Fichiers inclus avec succès");
} catch (Exception $e) {
    logDebug("Erreur inclusion: " . $e->getMessage());
    sendResponse(false, "Erreur de chargement des fichiers: " . $e->getMessage());
}

// Connexion à la base de données
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Connexion à la base de données échouée");
    }
    logDebug("Connexion BD réussie");
} catch (Exception $e) {
    logDebug("Erreur BD: " . $e->getMessage());
    sendResponse(false, "Erreur de connexion à la base de données: " . $e->getMessage());
}

// Déterminer l'action
$action = $_GET['action'] ?? $_POST['action'] ?? '';
logDebug("Action demandée: $action");

// ============================================
// ACTION: soumettreParticipation
// ============================================
if ($action === 'soumettreParticipation') {
    logDebug("--- Traitement participation ---");
    logDebug("POST: " . print_r($_POST, true));
    logDebug("FILES: " . print_r($_FILES, true));
    
    try {
        // Validation étape par étape
        if (empty($_POST['nom'])) {
            throw new Exception("Le nom est vide");
        }
        $nom = trim($_POST['nom']);
        logDebug("Nom: $nom");
        
        if (empty($_POST['prenom'])) {
            throw new Exception("Le prénom est vide");
        }
        $prenom = trim($_POST['prenom']);
        logDebug("Prénom: $prenom");
        
        if (empty($_POST['email'])) {
            throw new Exception("L'email est vide");
        }
        $email = trim($_POST['email']);
        logDebug("Email: $email");
        
        if (empty($_POST['evenement_id'])) {
            throw new Exception("L'ID de l'événement est vide");
        }
        $evenement_id = intval($_POST['evenement_id']);
        logDebug("Événement ID: $evenement_id");
        
        if (empty($_POST['commentaire'])) {
            throw new Exception("Le commentaire est vide");
        }
        $commentaire = trim($_POST['commentaire']);
        logDebug("Commentaire: " . substr($commentaire, 0, 50) . "...");
        
        // Vérifier que l'événement existe
        $stmt = $db->prepare("SELECT id, type FROM evenements WHERE id = ? LIMIT 1");
        $stmt->execute([$evenement_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$event) {
            throw new Exception("Événement introuvable (ID: $evenement_id)");
        }
        logDebug("Événement trouvé: " . $event['type']);
        
        // Créer ou récupérer l'utilisateur
        logDebug("Création/récupération utilisateur...");
        $utilisateur = new Utilisateur($db);
        $utilisateur->nom = $nom;
        $utilisateur->prenom = $prenom;
        $utilisateur->email = $email;
        
        if (!$utilisateur->getOrCreate()) {
            throw new Exception("Impossible de créer/récupérer l'utilisateur");
        }
        logDebug("Utilisateur ID: " . $utilisateur->id);
        
        // Gérer le fichier si présent
        $fichier_url = null;
        if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
            logDebug("Upload de fichier en cours...");
            
            $upload_dir = __DIR__ . '/../uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
                logDebug("Dossier uploads créé");
            }
            
            $file = $_FILES['fichier'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'zip');
            
            if (!in_array($extension, $allowed)) {
                throw new Exception("Type de fichier non autorisé: $extension");
            }
            
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception("Fichier trop volumineux (max 5MB)");
            }
            
            $fichier_url = uniqid() . '_' . time() . '.' . $extension;
            $destination = $upload_dir . $fichier_url;
            
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new Exception("Échec du déplacement du fichier");
            }
            logDebug("Fichier uploadé: $fichier_url");
        }
        
        // Insérer la participation DIRECTEMENT
        logDebug("Insertion dans la base de données...");
        $query = "INSERT INTO participations (utilisateur_id, evenement_id, commentaire, fichier_url, statut) 
                  VALUES (:utilisateur_id, :evenement_id, :commentaire, :fichier_url, 'en_attente')";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':utilisateur_id', $utilisateur->id, PDO::PARAM_INT);
        $stmt->bindParam(':evenement_id', $evenement_id, PDO::PARAM_INT);
        $stmt->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
        $stmt->bindParam(':fichier_url', $fichier_url, PDO::PARAM_STR);
        
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Erreur SQL: " . $errorInfo[2]);
        }
        
        $participation_id = $db->lastInsertId();
        logDebug("Participation enregistrée avec ID: $participation_id");
        
        // Vérifier que ça a bien été inséré
        $stmt = $db->prepare("SELECT COUNT(*) FROM participations WHERE id = ?");
        $stmt->execute([$participation_id]);
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            throw new Exception("La participation n'a pas été trouvée après insertion");
        }
        
        logDebug("✅ SUCCÈS - Participation enregistrée");
        sendResponse(true, "Participation enregistrée avec succès", array(
            'participation_id' => $participation_id,
            'utilisateur_id' => $utilisateur->id,
            'evenement_id' => $evenement_id,
            'fichier' => $fichier_url
        ));
        
    } catch (Exception $e) {
        logDebug("❌ ERREUR: " . $e->getMessage());
        logDebug("Trace: " . $e->getTraceAsString());
        sendResponse(false, $e->getMessage());
    }
}

// ============================================
// ACTION: soumettreQuiz
// ============================================
else if ($action === 'soumettreQuiz') {
    logDebug("--- Traitement quiz ---");
    
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            throw new Exception("Données JSON invalides");
        }
        
        logDebug("Données reçues: " . print_r($data, true));
        
        // Validation
        if (empty($data['nom']) || empty($data['prenom']) || empty($data['email'])) {
            throw new Exception("Informations utilisateur manquantes");
        }
        
        if (empty($data['evenement_id'])) {
            throw new Exception("ID événement manquant");
        }
        
        if (empty($data['reponses']) || !is_array($data['reponses'])) {
            throw new Exception("Réponses manquantes");
        }
        
        // Créer l'utilisateur
        $utilisateur = new Utilisateur($db);
        $utilisateur->nom = $data['nom'];
        $utilisateur->prenom = $data['prenom'];
        $utilisateur->email = $data['email'];
        
        if (!$utilisateur->getOrCreate()) {
            throw new Exception("Erreur création utilisateur");
        }
        
        logDebug("Utilisateur ID: " . $utilisateur->id);
        
        // Récupérer les questions et réponses
        $evenement = new Evenement($db);
        $evenement->id = $data['evenement_id'];
        $stmt = $evenement->readWithQuestions();
        
        $questions = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!isset($questions[$row['question_id']]) && $row['question_id']) {
                $questions[$row['question_id']] = array('reponses' => array());
            }
            if ($row['reponse_id']) {
                $questions[$row['question_id']]['reponses'][$row['reponse_id']] = array(
                    'est_correcte' => $row['est_correcte']
                );
            }
        }
        
        if (empty($questions)) {
            throw new Exception("Aucune question trouvée");
        }
        
        logDebug("Questions chargées: " . count($questions));
        
        // Calculer le score
        $score = 0;
        $total = count($questions);
        $reponses_details = array();
        
        logDebug("Calcul du score...");
        logDebug("Total de questions: $total");
        
        foreach ($data['reponses'] as $question_id => $reponse_id) {
            $est_correcte = false;
            if (isset($questions[$question_id]['reponses'][$reponse_id])) {
                $est_correcte = (bool)$questions[$question_id]['reponses'][$reponse_id]['est_correcte'];
                if ($est_correcte) {
                    $score++;
                    logDebug("Question $question_id: CORRECTE (réponse $reponse_id)");
                } else {
                    logDebug("Question $question_id: INCORRECTE (réponse $reponse_id)");
                }
            } else {
                logDebug("Question $question_id: réponse $reponse_id NON TROUVÉE");
            }
            $reponses_details[] = array(
                'question_id' => $question_id,
                'reponse_id' => $reponse_id,
                'est_correcte' => $est_correcte
            );
        }
        
        logDebug("Score final: $score/$total");
        $pourcentage = $total > 0 ? round(($score / $total) * 100, 2) : 0;
        logDebug("Pourcentage: $pourcentage%");
        
        // Enregistrer le résultat
        $result = $utilisateur->enregistrerResultatQuiz(
            $data['evenement_id'],
            $score,
            $total,
            $reponses_details
        );
        
        if (!$result['success']) {
            throw new Exception($result['message']);
        }
        
        logDebug("✅ SUCCÈS - Quiz enregistré");
        sendResponse(true, "Quiz enregistré avec succès", array(
            'score' => $score,
            'total' => $total,
            'pourcentage' => round(($score / $total) * 100, 2),
            'resultat_id' => $result['resultat_id']
        ));
        
    } catch (Exception $e) {
        logDebug("❌ ERREUR: " . $e->getMessage());
        sendResponse(false, $e->getMessage());
    }
}

// ============================================
// ACTION INCONNUE
// ============================================
else {
    logDebug("❌ Action inconnue: $action");
    sendResponse(false, "Action non reconnue: $action");
}
?>