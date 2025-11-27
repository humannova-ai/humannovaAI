<?php
/**
 * BackOffice - Gestion des Participations
 * CRUD complet avec PHP
 */

require_once '../../config/database.php';
require_once '../../models/Participation.php';

$database = new Database();
$db = $database->getConnection();

$participation = new Participation($db);

$message = '';
$messageType = '';

// Traitement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // SUPPRIMER
    if ($action === 'delete' && isset($_POST['id'])) {
        $participation->setId($_POST['id']);
        if ($participation->readOne()) {
            $fichier = $participation->getFichierUrl();
            if ($fichier && file_exists('../../uploads/' . $fichier)) {
                unlink('../../uploads/' . $fichier);
            }
        }
        if ($participation->delete()) {
            $message = 'Participation supprimée avec succès';
            $messageType = 'success';
        } else {
            $message = 'Erreur lors de la suppression';
            $messageType = 'error';
        }
    }
    
    // MODIFIER
    if ($action === 'update' && isset($_POST['id'])) {
        $errors = [];
        if (empty($_POST['commentaire']) || strlen($_POST['commentaire']) < 10) {
            $errors[] = 'Le commentaire doit contenir au moins 10 caractères';
        }
        if (empty($_POST['statut'])) {
            $errors[] = 'Le statut est obligatoire';
        }
        
        if (empty($errors)) {
            $participation->setId($_POST['id']);
            $participation->setCommentaire($_POST['commentaire']);
            $participation->setStatut($_POST['statut']);
            
            if ($participation->update()) {
                $message = 'Participation modifiée avec succès';
                $messageType = 'success';
            } else {
                $message = 'Erreur lors de la modification';
                $messageType = 'error';
            }
        } else {
            $message = implode('<br>', $errors);
            $messageType = 'error';
        }
    }
    
    // CHANGER STATUT
    if ($action === 'updateStatut' && isset($_POST['id']) && isset($_POST['statut'])) {
        $participation->setId($_POST['id']);
        $participation->setStatut($_POST['statut']);
        
        if ($participation->updateStatut()) {
            $message = 'Statut mis à jour';
            $messageType = 'success';
        } else {
            $message = 'Erreur';
            $messageType = 'error';
        }
    }
}

$filterStatut = $_GET['statut'] ?? 'all';

if ($filterStatut !== 'all') {
    $stmt = $participation->readByStatut($filterStatut);
} else {
    $stmt = $participation->readAll();
}
$participations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stats = $participation->getStatistics();

$editParticipation = null;
if (isset($_GET['edit'])) {
    $editParticipation = $participation->readOneWithJoinById($_GET['edit']);
}

$viewParticipation = null;
if (isset($_GET['view'])) {
    $viewParticipation = $participation->readOneWithJoinById($_GET['view']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Participations - Back Office</title>
    <style>
        :root {
            --carbon-dark: #0a0a0a;
            --carbon-medium: #141414;
            --metal-dark: #2a2a2a;
            --metal-light: #3a3a3a;
            --accent-cyan: #00ffff;
            --accent-purple: #9945ff;
            --accent-green: #00ff88;
            --accent-orange: #ff9500;
            --accent-red: #ff3333;
            --accent-blue: #00a8ff;
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
            --text-dim: #606060;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: var(--carbon-dark);
            color: var(--text-primary);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(180deg, var(--carbon-medium) 0%, var(--carbon-dark) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 15px 30px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .nav-container {
            display: flex;
            align-items: center;
            gap: 40px;
            max-width: 1600px;
            margin: 0 auto;
        }
        
        .logo { text-decoration: none; }
        .logo-text { font-size: 20px; font-weight: 900; }
        .logo-text .prism { color: var(--accent-cyan); }
        .logo-text .flux { color: var(--accent-purple); }
        
        .nav-menu { display: flex; list-style: none; gap: 5px; }
        
        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 14px;
        }
        
        .nav-link:hover { color: var(--accent-cyan); background: rgba(0,255,255,0.1); }
        .nav-link.active { color: #000; background: var(--accent-cyan); }
        
        .admin-section {
            padding: 100px 30px 50px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .section-header { margin-bottom: 30px; animation: fadeInUp 0.6s ease; }
        
        .section-title {
            font-size: 32px;
            font-weight: 900;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .section-subtitle { color: var(--text-secondary); margin-top: 10px; }
        
        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
            animation: fadeInUp 0.4s ease;
        }
        
        .message.success { background: rgba(0,255,136,0.2); border: 1px solid var(--accent-green); color: var(--accent-green); }
        .message.error { background: rgba(255,51,51,0.2); border: 1px solid var(--accent-red); color: var(--accent-red); }
        
        /* Stats */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: var(--carbon-medium);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease backwards;
        }
        
        .stat-box:hover { border-color: var(--accent-cyan); transform: translateY(-5px); }
        
        .stat-box .value { font-size: 36px; font-weight: 700; color: var(--accent-cyan); }
        .stat-box .label { color: var(--text-secondary); font-size: 12px; margin-top: 5px; text-transform: uppercase; }
        
        /* Filters */
        .filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }
        
        .filter-btn {
            padding: 10px 20px;
            border-radius: 20px;
            border: 1px solid var(--metal-light);
            background: transparent;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background: var(--accent-cyan);
            color: #000;
            border-color: var(--accent-cyan);
        }
        
        /* Table */
        .participations-table {
            background: var(--carbon-medium);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            overflow-x: auto;
        }
        
        table { width: 100%; border-collapse: collapse; }
        
        th {
            background: var(--carbon-dark);
            color: var(--accent-cyan);
            padding: 15px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            color: var(--text-secondary);
        }
        
        tr:hover td { background: rgba(0,255,255,0.02); }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .status-badge.en_attente { background: rgba(255,149,0,0.2); color: var(--accent-orange); }
        .status-badge.approuve { background: rgba(0,255,136,0.2); color: var(--accent-green); }
        .status-badge.rejete { background: rgba(255,51,51,0.2); color: var(--accent-red); }
        
        .action-btn {
            padding: 8px 12px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
        }
        
        .btn-view { background: var(--accent-blue); color: #fff; }
        .btn-edit { background: var(--accent-orange); color: #000; }
        .btn-approve { background: var(--accent-green); color: #000; }
        .btn-reject { background: var(--accent-red); color: #fff; }
        .btn-delete { background: linear-gradient(135deg, #ff4444, #cc0000); color: #fff; }
        
        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            z-index: 2000;
            overflow-y: auto;
            padding: 30px;
            align-items: flex-start;
            justify-content: center;
        }
        
        .modal-overlay.active { display: flex; }
        
        .modal-box {
            background: var(--carbon-medium);
            border: 2px solid var(--accent-cyan);
            border-radius: 16px;
            padding: 30px;
            max-width: 600px;
            width: 100%;
            margin: auto;
            animation: slideIn 0.4s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .modal-title { color: var(--accent-cyan); font-size: 20px; font-weight: 700; }
        
        .modal-close {
            background: rgba(255,51,51,0.2);
            color: var(--accent-red);
            border: 1px solid var(--accent-red);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .modal-close:hover { background: var(--accent-red); color: #fff; }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        
        .detail-label { color: var(--text-secondary); }
        .detail-value { color: var(--text-primary); font-weight: 600; }
        
        .comment-box {
            background: var(--carbon-dark);
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
            color: var(--text-secondary);
            line-height: 1.6;
        }
        
        .file-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            color: #000;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            margin-top: 15px;
            transition: all 0.3s ease;
        }
        
        .file-link:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,255,255,0.4); }
        
        /* Form */
        .form-group { margin-bottom: 20px; }
        
        .form-group label {
            display: block;
            color: var(--accent-cyan);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 13px;
            text-transform: uppercase;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px;
            background: var(--carbon-dark);
            border: 2px solid var(--metal-dark);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--accent-cyan);
        }
        
        .form-group input.valid { border-color: var(--accent-green); }
        .form-group input.invalid,
        .form-group textarea.invalid { border-color: var(--accent-red); }
        
        .validation-message {
            font-size: 12px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .validation-message.valid { color: var(--accent-green); }
        .validation-message.invalid { color: var(--accent-red); }
        
        .form-actions { display: flex; gap: 15px; margin-top: 25px; }
        
        .btn {
            padding: 14px 25px;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-primary { background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue)); color: #000; }
        .btn-success { background: linear-gradient(135deg, var(--accent-green), var(--accent-cyan)); color: #000; }
        .btn-secondary { background: var(--metal-dark); color: var(--text-primary); }
        .btn-danger { background: linear-gradient(135deg, #ff4444, #cc0000); color: #fff; }
        
        /* Confirm Modal */
        .confirm-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
            z-index: 3000;
            align-items: center;
            justify-content: center;
        }
        
        .confirm-overlay.active { display: flex; }
        
        .confirm-box {
            background: var(--carbon-medium);
            border: 2px solid var(--accent-red);
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            max-width: 400px;
            animation: slideIn 0.3s ease;
        }
        
        .confirm-icon { font-size: 50px; margin-bottom: 20px; }
        .confirm-text { color: var(--text-primary); font-size: 18px; margin-bottom: 25px; }
        .confirm-buttons { display: flex; gap: 15px; justify-content: center; }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .participations-table { font-size: 12px; }
            th, td { padding: 10px 8px; }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <a href="../../index.php" class="logo">
                <span class="logo-text">
                    <span class="prism">HUMAN</span>
                    <span class="flux">NOVA AI</span>
                </span>
            </a>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="nav-link">📊 Dashboard</a></li>
                <li><a href="manage-events.php" class="nav-link">📅 Événements</a></li>
                <li><a href="manage-participations.php" class="nav-link active">👥 Participations</a></li>
                <li><a href="../front/events.php" class="nav-link">🌐 Front Office</a></li>
            </ul>
        </nav>
    </header>

    <section class="admin-section">
        <div class="section-header">
            <h2 class="section-title">👥 Gestion des Participations</h2>
            <p class="section-subtitle">Gérez les participations aux événements</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-box">
                <div class="value"><?php echo $stats['total'] ?? 0; ?></div>
                <div class="label">Total</div>
            </div>
            <div class="stat-box">
                <div class="value" style="color: var(--accent-orange);"><?php echo $stats['en_attente'] ?? 0; ?></div>
                <div class="label">En attente</div>
            </div>
            <div class="stat-box">
                <div class="value" style="color: var(--accent-green);"><?php echo $stats['approuve'] ?? 0; ?></div>
                <div class="label">Approuvées</div>
            </div>
            <div class="stat-box">
                <div class="value" style="color: var(--accent-red);"><?php echo $stats['rejete'] ?? 0; ?></div>
                <div class="label">Rejetées</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <a href="?statut=all" class="filter-btn <?php echo $filterStatut === 'all' ? 'active' : ''; ?>">Tous</a>
            <a href="?statut=en_attente" class="filter-btn <?php echo $filterStatut === 'en_attente' ? 'active' : ''; ?>">En attente</a>
            <a href="?statut=approuve" class="filter-btn <?php echo $filterStatut === 'approuve' ? 'active' : ''; ?>">Approuvées</a>
            <a href="?statut=rejete" class="filter-btn <?php echo $filterStatut === 'rejete' ? 'active' : ''; ?>">Rejetées</a>
        </div>

        <!-- Table -->
        <div class="participations-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Événement</th>
                        <th>Fichier</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($participations)): ?>
                        <tr><td colspan="8" style="text-align: center; padding: 40px;">Aucune participation trouvée</td></tr>
                    <?php else: ?>
                        <?php foreach ($participations as $p): ?>
                            <tr>
                                <td>#<?php echo $p['id']; ?></td>
                                <td><?php echo htmlspecialchars($p['utilisateur_prenom'] . ' ' . $p['utilisateur_nom']); ?></td>
                                <td><?php echo htmlspecialchars($p['utilisateur_email']); ?></td>
                                <td><?php echo htmlspecialchars(substr($p['evenement_titre'] ?? '', 0, 20)); ?>...</td>
                                <td>
                                    <?php if (!empty($p['fichier_url'])): ?>
                                        <a href="../../uploads/<?php echo $p['fichier_url']; ?>" target="_blank" style="color: var(--accent-cyan);">📎 Voir</a>
                                    <?php else: ?>
                                        <span style="color: var(--text-dim);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="status-badge <?php echo $p['statut']; ?>"><?php echo $p['statut'] === 'en_attente' ? 'EN ATTENTE' : ($p['statut'] === 'approuve' ? 'APPROUVÉ' : 'REJETÉ'); ?></span></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($p['date_participation'])); ?></td>
                                <td>
                                    <a href="?view=<?php echo $p['id']; ?>" class="action-btn btn-view">👁️</a>
                                    <a href="?edit=<?php echo $p['id']; ?>" class="action-btn btn-edit">✏️</a>
                                    <?php if ($p['statut'] === 'en_attente'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="updateStatut">
                                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                            <input type="hidden" name="statut" value="approuve">
                                            <button type="submit" class="action-btn btn-approve">✓</button>
                                        </form>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="updateStatut">
                                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                            <input type="hidden" name="statut" value="rejete">
                                            <button type="submit" class="action-btn btn-reject">✗</button>
                                        </form>
                                    <?php endif; ?>
                                    <button type="button" class="action-btn btn-delete" onclick="showDeleteConfirm(<?php echo $p['id']; ?>)">🗑️</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- View Modal -->
    <?php if ($viewParticipation): ?>
    <div class="modal-overlay active" id="viewModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">📋 Détails</h3>
                <a href="manage-participations.php<?php echo $filterStatut !== 'all' ? '?statut=' . $filterStatut : ''; ?>" class="modal-close">✕</a>
            </div>
            <div class="detail-row"><span class="detail-label">ID</span><span class="detail-value">#<?php echo $viewParticipation['id']; ?></span></div>
            <div class="detail-row"><span class="detail-label">Utilisateur</span><span class="detail-value"><?php echo htmlspecialchars($viewParticipation['utilisateur_prenom'] . ' ' . $viewParticipation['utilisateur_nom']); ?></span></div>
            <div class="detail-row"><span class="detail-label">Email</span><span class="detail-value"><?php echo htmlspecialchars($viewParticipation['utilisateur_email']); ?></span></div>
            <div class="detail-row"><span class="detail-label">Événement</span><span class="detail-value"><?php echo htmlspecialchars($viewParticipation['evenement_titre']); ?></span></div>
            <div class="detail-row"><span class="detail-label">Statut</span><span class="detail-value"><span class="status-badge <?php echo $viewParticipation['statut']; ?>"><?php echo $viewParticipation['statut'] === 'en_attente' ? 'EN ATTENTE' : ($viewParticipation['statut'] === 'approuve' ? 'APPROUVÉ' : 'REJETÉ'); ?></span></span></div>
            <div class="detail-row"><span class="detail-label">Date</span><span class="detail-value"><?php echo date('d/m/Y à H:i', strtotime($viewParticipation['date_participation'])); ?></span></div>
            <div style="margin-top: 20px;">
                <span class="detail-label">Commentaire:</span>
                <div class="comment-box"><?php echo nl2br(htmlspecialchars($viewParticipation['commentaire'] ?? 'Aucun commentaire')); ?></div>
            </div>
            <?php if (!empty($viewParticipation['fichier_url'])): ?>
                <a href="../../uploads/<?php echo $viewParticipation['fichier_url']; ?>" class="file-link" download>📎 Télécharger le fichier</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Edit Modal -->
    <?php if ($editParticipation): ?>
    <div class="modal-overlay active" id="editModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">✏️ Modifier</h3>
                <a href="manage-participations.php<?php echo $filterStatut !== 'all' ? '?statut=' . $filterStatut : ''; ?>" class="modal-close">✕</a>
            </div>
            <form method="POST" onsubmit="return validateEditForm()">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo $editParticipation['id']; ?>">
                
                <div class="form-group">
                    <label>Utilisateur</label>
                    <input type="text" value="<?php echo htmlspecialchars($editParticipation['utilisateur_prenom'] . ' ' . $editParticipation['utilisateur_nom']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label>Événement</label>
                    <input type="text" value="<?php echo htmlspecialchars($editParticipation['evenement_titre']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label>Commentaire * (min 10 caractères)</label>
                    <textarea name="commentaire" id="editCommentaire" rows="4" placeholder="Commentaire" oninput="validateCommentaire()"><?php echo htmlspecialchars($editParticipation['commentaire']); ?></textarea>
                    <div class="validation-message" id="commentaire-validation"></div>
                </div>
                
                <div class="form-group">
                    <label>Statut *</label>
                    <select name="statut">
                        <option value="en_attente" <?php echo $editParticipation['statut'] === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                        <option value="approuve" <?php echo $editParticipation['statut'] === 'approuve' ? 'selected' : ''; ?>>Approuvé</option>
                        <option value="rejete" <?php echo $editParticipation['statut'] === 'rejete' ? 'selected' : ''; ?>>Rejeté</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <a href="manage-participations.php<?php echo $filterStatut !== 'all' ? '?statut=' . $filterStatut : ''; ?>" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-success">💾 Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Confirm Delete Modal -->
    <div class="confirm-overlay" id="confirmOverlay">
        <div class="confirm-box">
            <div class="confirm-icon">⚠️</div>
            <div class="confirm-text">Êtes-vous sûr de vouloir supprimer cette participation ?</div>
            <form method="POST" id="deleteForm">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="deleteId">
                <div class="confirm-buttons">
                    <button type="button" class="btn btn-secondary" onclick="hideDeleteConfirm()">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showDeleteConfirm(id) {
            document.getElementById('deleteId').value = id;
            document.getElementById('confirmOverlay').classList.add('active');
        }
        
        function hideDeleteConfirm() {
            document.getElementById('confirmOverlay').classList.remove('active');
        }
        
        function validateCommentaire() {
            const textarea = document.getElementById('editCommentaire');
            const value = textarea.value.trim();
            const msgDiv = document.getElementById('commentaire-validation');
            const isValid = value.length >= 10;
            
            textarea.classList.remove('valid', 'invalid');
            textarea.classList.add(isValid ? 'valid' : 'invalid');
            msgDiv.className = 'validation-message ' + (isValid ? 'valid' : 'invalid');
            msgDiv.textContent = isValid ? `✓ ${value.length} caractères` : `⚠ Minimum 10 caractères (${value.length}/10)`;
            
            return isValid;
        }
        
        function validateEditForm() {
            return validateCommentaire();
        }
        
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                hideDeleteConfirm();
                <?php if ($viewParticipation || $editParticipation): ?>
                window.location.href = 'manage-participations.php<?php echo $filterStatut !== 'all' ? '?statut=' . $filterStatut : ''; ?>';
                <?php endif; ?>
            }
        });
    </script>
</body>
</html>
