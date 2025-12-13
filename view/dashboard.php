<?php
// Démarrer la session et vérifier les autorisations
session_start();

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php?error=unauthorized');
    exit();
}

// Inclure le modèle Utilisateur et la configuration de la base de données
require_once __DIR__ . '/../model/Utilisateur.php';
require_once __DIR__ . '/../config/database.php';

// Initialiser la connexion à la base de données
$database = new Database();
$db = $database->getConnection();
$utilisateur = new Utilisateur($db);

// Variables pour les messages
$message = '';
$error = '';

// Récupérer tous les utilisateurs
$utilisateurs = [];
$query = "SELECT id, nom, prenom, email, telephone, role, created_at, IFNULL(banned, 0) as banned FROM `user` ORDER BY created_at DESC";
$stmt = $db->prepare($query);
if ($stmt->execute()) {
    $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Traitement des actions (ajout, modification, suppression)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajout d'un nouvel utilisateur
    if (isset($_POST['action']) && $_POST['action'] === 'ajouter') {
        if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email']) && !empty($_POST['mdp'])) {
            // Récupérer les données optionnelles
            $telephone = isset($_POST['telephone']) ? $_POST['telephone'] : '';
            $adresse = isset($_POST['adresse']) ? $_POST['adresse'] : '';
            $role = $_POST['role'] ?? 'utilisateur';
            $hashed_password = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
            
            $query = "INSERT INTO `user` (nom, prenom, email, mdp, telephone, adresse, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute([
                $_POST['nom'], 
                $_POST['prenom'], 
                $_POST['email'], 
                $hashed_password,
                $telephone,
                $adresse,
                $role
            ])) {
                $message = 'Utilisateur ajouté avec succès !';
                // Rafraîchir la liste des utilisateurs
                $stmt = $db->prepare("SELECT id, nom, prenom, email, telephone, role, created_at, IFNULL(banned, 0) as banned FROM `user` ORDER BY created_at DESC");
                $stmt->execute();
                $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $error = 'Erreur lors de l\'ajout de l\'utilisateur';
            }
        } else {
            $error = 'Tous les champs obligatoires doivent être remplis';
        }
    }
    // Modification d'un utilisateur
    elseif (isset($_POST['action']) && $_POST['action'] === 'modifier') {
        if (!empty($_POST['id']) && !empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email'])) {
            // Vérifier si l'utilisateur essaie de modifier son propre compte
            if ($_POST['id'] == $_SESSION['user_id']) {
                $error = 'Vous ne pouvez pas modifier votre propre compte';
            } else {
                // Vérifier si l'utilisateur est un administrateur
                if ($_SESSION['user_role'] !== 'admin') {
                    $error = 'Seuls les administrateurs peuvent modifier les utilisateurs';
                } else {
                    // Préparer la requête de mise à jour
                    $query = "UPDATE `user` SET nom = ?, prenom = ?, email = ?, role = ?, telephone = ?, adresse = ? WHERE id = ?";
                    $stmt = $db->prepare($query);
                    
                    $telephone = isset($_POST['telephone']) ? $_POST['telephone'] : '';
                    $adresse = isset($_POST['adresse']) ? $_POST['adresse'] : '';
                    
                    if ($stmt->execute([
                        $_POST['nom'], 
                        $_POST['prenom'], 
                        $_POST['email'], 
                        $_POST['role'],
                        $telephone,
                        $adresse,
                        $_POST['id']
                    ])) {
                        // Mettre à jour le mot de passe si fourni
                        if (!empty($_POST['mdp'])) {
                            $hashed_password = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
                            $stmt = $db->prepare("UPDATE `user` SET mdp = ? WHERE id = ?");
                            $stmt->execute([$hashed_password, $_POST['id']]);
                        }
                        
                        $message = 'Utilisateur mis à jour avec succès !';
                        // Rafraîchir la liste des utilisateurs
                        $stmt = $db->prepare("SELECT id, nom, prenom, email, telephone, role, created_at, IFNULL(banned, 0) as banned FROM `user` ORDER BY created_at DESC");
                        $stmt->execute();
                        $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        $error = 'Erreur lors de la mise à jour de l\'utilisateur';
                    }
                }
            }
        } else {
            $error = 'Tous les champs obligatoires doivent être remplis';
        }
    }
}

// Suppression d'un utilisateur
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    // Empêcher l'auto-suppression
    if ($_GET['id'] != $_SESSION['user_id']) {
        $query = "DELETE FROM `user` WHERE id = ?";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$_GET['id']])) {
            $message = 'Utilisateur supprimé avec succès !';
            // Rafraîchir la liste des utilisateurs
            $stmt = $db->prepare("SELECT id, nom, prenom, email, telephone, role, created_at, IFNULL(banned, 0) as banned FROM `user` ORDER BY created_at DESC");
            $stmt->execute();
            $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error = 'Erreur lors de la suppression de l\'utilisateur';
        }
    } else {
        $error = 'Vous ne pouvez pas supprimer votre propre compte';
    }
}

// Bannir/débannir un utilisateur
if (isset($_GET['action']) && $_GET['action'] === 'bannir' && isset($_GET['id'])) {
    // Empêcher l'auto-bannissement
    if ($_GET['id'] != $_SESSION['user_id']) {
        // Vérifier d'abord si l'utilisateur est déjà banni
        $checkQuery = "SELECT IFNULL(banned, 0) as is_banned FROM `user` WHERE id = ?";
        $stmt = $db->prepare($checkQuery);
        $stmt->execute([$_GET['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $isBanned = $result ? (bool)$result['is_banned'] : false;
        $newStatus = $isBanned ? 0 : 1;
        
        // Mettre à jour le statut de bannissement
        $updateQuery = "UPDATE `user` SET banned = ? WHERE id = ?";
        $stmt = $db->prepare($updateQuery);
        
        if ($stmt->execute([$newStatus, $_GET['id']])) {
            $action = $isBanned ? 'débanni' : 'banni';
            $message = "Utilisateur $action avec succès !";
            
            // Rafraîchir la liste des utilisateurs
            $stmt = $db->prepare("SELECT id, nom, prenom, email, telephone, role, created_at, IFNULL(banned, 0) as banned FROM `user` ORDER BY created_at DESC");
            $stmt->execute();
            $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error = 'Erreur lors du bannissement de l\'utilisateur';
        }
    } else {
        $error = 'Vous ne pouvez pas bannir votre propre compte';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord administrateur - PRISM FLUX</title>
    <link rel="stylesheet" href="../public/assets/css/templatemo-prism-flux.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Styles spécifiques au tableau de bord */
        .dashboard-container {
            max-width: 1200px;
            margin: 80px auto 40px;
            padding: 0 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .stat-card .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #6e45e2;
            margin: 10px 0;
        }
        
        .stat-card .stat-label {
            color: #88d3ce;
            font-size: 1rem;
        }
        
        .table-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow-x: auto;
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        th {
            background: rgba(110, 69, 226, 0.2);
            color: #6e45e2;
            font-weight: 600;
        }
        
        tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
        }
        
        .btn-primary {
            background: #6e45e2;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5d3ac9;
            transform: translateY(-2px);
        }
        
        .btn-edit {
            background: #4CAF50;
            color: white;
        }
        
        .btn-edit:hover {
            background: #3e8e41;
            transform: translateY(-2px);
        }
        
        .btn-delete {
            background: #f44336;
            color: white;
        }
        
        .btn-delete:hover {
            background: #d32f2f;
            transform: translateY(-2px);
        }
        
        .btn-add {
            background: #2196F3;
            color: white;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-add:hover {
            background: #0b7dda;
            transform: translateY(-2px);
        }
        
        .btn-logout {
            background: #ff9800;
            color: white;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .btn-logout:hover {
            background: #e68a00;
            transform: translateY(-2px);
        }
        
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            z-index: 999;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .logo-text {
            font-size: 1.5rem;
            font-weight: bold;
            margin-left: 10px;
        }
        
        .prism { color: #6e45e2; }
        .flux { color: #88d3ce; }
        
        .admin-badge {
            background: #6e45e2;
            color: white;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
            margin-left: 10px;
            vertical-align: middle;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #88ffaa;
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #ff6b6b;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: #1a1a2e;
            padding: 25px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 1.5rem;
            color: #aaa;
            cursor: pointer;
        }
        
        .close:hover {
            color: #fff;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #88d3ce;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.3);
            border-radius: 5px;
            color: white;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #6e45e2;
            box-shadow: 0 0 0 2px rgba(110, 69, 226, 0.3);
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-admin {
            background: rgba(110, 69, 226, 0.2);
            color: #9d7eff;
        }
        
        .badge-user {
            background: rgba(136, 211, 206, 0.2);
            color: #88d3ce;
        }
        
        .actions {
            display: flex;
            gap: 5px;
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                margin-top: 100px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
                gap: 5px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <header class="header">
        <div class="nav-container">
            <a href="dashboard.php" class="logo">
                <span class="logo-text">
                    <span class="prism">PRISM</span>
                    <span class="flux">FLUX</span>
                    <span class="admin-badge">ADMIN</span>
                </span>
            </a>
            <div>
                <a href="../index.php?action=logout" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <h1>Tableau de bord administrateur</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number"><?php echo count($utilisateurs); ?></div>
                <div class="stat-label">Utilisateurs inscrits</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
                <div class="stat-number">
                    <?php 
                        echo count(array_filter($utilisateurs, function($u) { 
                            return $u['role'] === 'admin'; 
                        })); 
                    ?>
                </div>
                <div class="stat-label">Administrateurs</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user"></i></div>
                <div class="stat-number">
                    <?php 
                        echo count(array_filter($utilisateurs, function($u) { 
                            return $u['role'] === 'utilisateur'; 
                        })); 
                    ?>
                </div>
                <div class="stat-label">Utilisateurs standards</div>
            </div>
        </div>
        
        <!-- Gestion des utilisateurs -->
        <div class="table-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Gestion des utilisateurs</h2>
                <button class="btn btn-add" onclick="openModal('ajouter')">
                    <i class="fas fa-plus"></i> Ajouter un utilisateur
                </button>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Inscrit le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($utilisateurs) > 0): ?>
                            <?php foreach ($utilisateurs as $user): ?>
                                <tr class="<?php echo (isset($user['banned']) && $user['banned'] == 1) ? 'banned' : ''; ?>">
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $user['role'] === 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                                            <?php echo $user['role'] === 'admin' ? 'Admin' : 'Utilisateur'; ?>
                                        </span>
                                        <?php if (isset($user['banned']) && $user['banned'] == 1): ?>
                                            <span class="badge badge-banned" style="background-color: #dc3545; margin-left: 5px;">
                                                Banni
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-edit" onclick="openModal('modifier', <?php echo htmlspecialchars(json_encode($user)); ?>)">
                                            <i class="fas fa-edit"></i> Modifier
                                        </button>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <a href="?action=supprimer&id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-delete"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </a>
                                            <?php if (isset($user['banned']) && $user['banned'] == 1): ?>
                                                <a href="?action=bannir&id=<?php echo $user['id']; ?>" 
                                                   class="btn btn-sm btn-success"
                                                   onclick="return confirm('Êtes-vous sûr de vouloir débannir cet utilisateur ?')"
                                                   style="background-color: #28a745; color: #fff; margin-left: 5px;">
                                                    <i class="fas fa-check-circle"></i> Débannir
                                                </a>
                                            <?php else: ?>
                                                <a href="?action=bannir&id=<?php echo $user['id']; ?>" 
                                                   class="btn btn-sm btn-warning"
                                                   onclick="return confirm('Êtes-vous sûr de vouloir bannir cet utilisateur ?')"
                                                   style="background-color: #ffc107; color: #000; margin-left: 5px;">
                                                    <i class="fas fa-ban"></i> Bannir
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">
                                    Aucun utilisateur trouvé.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal pour ajouter/modifier un utilisateur -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Ajouter un utilisateur</h2>
            
            <form id="userForm" method="POST" onsubmit="return validateForm()">
                <input type="hidden" name="action" id="formAction" value="ajouter">
                <input type="hidden" name="id" id="userId">
                
                <div class="form-group">
                    <label for="nom">Nom <span class="text-danger">*</span></label>
                    <input type="text" id="nom" name="nom" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="prenom">Prénom <span class="text-danger">*</span></label>
                    <input type="text" id="prenom" name="prenom" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="mdp" id="mdpLabel">Mot de passe <span class="text-danger">*</span></label>
                    <input type="password" id="mdp" name="mdp" class="form-control">
                    <small id="passwordHelp" class="text-muted">Laissez vide pour ne pas modifier le mot de passe</small>
                </div>
                
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="text" id="telephone" name="telephone" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <textarea id="adresse" name="adresse" class="form-control" rows="3"></textarea>
                </div>
                
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <div class="form-group">
                    <label for="role">Rôle <span class="text-danger">*</span></label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="utilisateur">Utilisateur</option>
                        <option value="admin">Administrateur</option>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour ouvrir la modal
        function openModal(action, userData = null) {
            const modal = document.getElementById('userModal');
            const modalTitle = document.getElementById('modalTitle');
            const formAction = document.getElementById('formAction');
            const userId = document.getElementById('userId');
            const nom = document.getElementById('nom');
            const prenom = document.getElementById('prenom');
            const email = document.getElementById('email');
            const mdp = document.getElementById('mdp');
            const mdpLabel = document.getElementById('mdpLabel');
            const passwordHelp = document.getElementById('passwordHelp');
            const role = document.getElementById('role');
            
            if (action === 'ajouter') {
                modalTitle.textContent = 'Ajouter un utilisateur';
                formAction.value = 'ajouter';
                userId.value = '';
                nom.value = '';
                prenom.value = '';
                email.value = '';
                mdp.required = true;
                mdpLabel.innerHTML = 'Mot de passe <span class="text-danger">*</span>';
                passwordHelp.style.display = 'none';
                role.value = 'utilisateur';
            } else if (action === 'modifier' && userData) {
                modalTitle.textContent = 'Modifier un utilisateur';
                formAction.value = 'modifier';
                userId.value = userData.id;
                nom.value = userData.nom || '';
                prenom.value = userData.prenom || '';
                email.value = userData.email || '';
                mdp.required = false;
                mdp.value = '';
                mdpLabel.innerHTML = 'Nouveau mot de passe';
                passwordHelp.style.display = 'block';
                // Si l'utilisateur n'est pas admin, on ne permet pas de modifier le rôle
                if (document.getElementById('role')) {
                    role.value = userData.role || 'utilisateur';
                }
                // Remplir les champs supplémentaires
                if (document.getElementById('telephone')) {
                    document.getElementById('telephone').value = userData.telephone || '';
                }
                if (document.getElementById('adresse')) {
                    document.getElementById('adresse').value = userData.adresse || '';
                }
            }
            
            modal.style.display = 'flex';
        }
        
        // Fonction pour fermer la modal
        function closeModal() {
            document.getElementById('userModal').style.display = 'none';
        }
        
        // Fermer la modal en cliquant en dehors
        window.onclick = function(event) {
            const modal = document.getElementById('userModal');
            if (event.target === modal) {
                closeModal();
            }
        }
        
        // Validation du formulaire
        function validateForm() {
            const formAction = document.getElementById('formAction').value;
            const mdp = document.getElementById('mdp');
            const mdpValue = mdp.value.trim();
            
            // Pour l'ajout, le mot de passe est obligatoire
            if (formAction === 'ajouter' && mdpValue === '') {
                alert('Veuillez saisir un mot de passe');
                mdp.focus();
                return false;
            }
            
            // Vérification de la force du mot de passe s'il est fourni
            if (mdpValue !== '') {
                if (mdpValue.length < 8) {
                    alert('Le mot de passe doit contenir au moins 8 caractères');
                    return false;
                }
            }
            
            return true;
        }
        
        // Afficher un message de confirmation avant de supprimer un utilisateur
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>
