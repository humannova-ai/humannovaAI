<?php
/**
 * BackOffice - Gestion des Événements
 * CRUD complet avec PHP + Validation
 */

require_once '../../config/database.php';
require_once '../../models/Evenement.php';
require_once '../../models/Question.php';
require_once '../../models/Reponse.php';

$database = new Database();
$db = $database->getConnection();

$evenement = new Evenement($db);
$question = new Question($db);
$reponse = new Reponse($db);

$message = '';
$messageType = '';

// Traitement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // SUPPRIMER
    if ($action === 'delete' && isset($_POST['id'])) {
        $evenement->setId($_POST['id']);
        if ($evenement->delete()) {
            $message = 'Événement supprimé avec succès';
            $messageType = 'success';
        } else {
            $message = 'Erreur lors de la suppression';
            $messageType = 'error';
        }
    }
    
    // CRÉER
    if ($action === 'create') {
        $errors = [];
        if (empty($_POST['type'])) $errors[] = 'Le type est obligatoire';
        if (empty($_POST['titre']) || strlen($_POST['titre']) < 3) $errors[] = 'Le titre doit contenir au moins 3 caractères';
        if (empty($_POST['description']) || strlen($_POST['description']) < 10) $errors[] = 'La description doit contenir au moins 10 caractères';
        if (empty($_POST['date_debut'])) $errors[] = 'La date de début est obligatoire';
        if (empty($_POST['date_fin'])) $errors[] = 'La date de fin est obligatoire';
        if (!empty($_POST['date_debut']) && !empty($_POST['date_fin']) && strtotime($_POST['date_fin']) <= strtotime($_POST['date_debut'])) {
            $errors[] = 'La date de fin doit être postérieure à la date de début';
        }
        
        if (empty($errors)) {
            $imageUrl = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../../uploads/images/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $fileName = 'event_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                    $imageUrl = 'uploads/images/' . $fileName;
                }
            }
            
            $evenement->setType($_POST['type']);
            $evenement->setTitre($_POST['titre']);
            $evenement->setDescription($_POST['description']);
            $evenement->setDateDebut($_POST['date_debut']);
            $evenement->setDateFin($_POST['date_fin']);
            $evenement->setImageUrl($imageUrl ?: 'https://via.placeholder.com/600x400/1a1a1a/00ffff?text=Event');
            $evenement->setNombreQuestions(0);
            
            if ($evenement->create()) {
                $eventId = $evenement->getId();
                
                if ($_POST['type'] === 'quiz' && isset($_POST['questions'])) {
                    $nombreQuestions = 0;
                    foreach ($_POST['questions'] as $index => $q) {
                        if (!empty($q['texte'])) {
                            $question->setEvenementId($eventId);
                            $question->setTexteQuestion($q['texte']);
                            $question->setOrdre($index + 1);
                            
                            if ($question->create()) {
                                $questionId = $question->getId();
                                $nombreQuestions++;
                                
                                if (isset($q['reponses'])) {
                                    $correctAnswers = $_POST['reponse_correcte_' . $index] ?? [];
                                    if (!is_array($correctAnswers)) $correctAnswers = [$correctAnswers];
                                    
                                    foreach ($q['reponses'] as $rIndex => $r) {
                                        if (!empty($r['texte'])) {
                                            $reponse->setQuestionId($questionId);
                                            $reponse->setTexteReponse($r['texte']);
                                            $reponse->setEstCorrecte(in_array($rIndex, $correctAnswers));
                                            $reponse->setOrdre($rIndex + 1);
                                            $reponse->create();
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $evenement->setId($eventId);
                    $evenement->setNombreQuestions($nombreQuestions);
                    $evenement->updateNombreQuestions();
                }
                
                $message = 'Événement créé avec succès';
                $messageType = 'success';
            } else {
                $message = 'Erreur lors de la création';
                $messageType = 'error';
            }
        } else {
            $message = implode('<br>', $errors);
            $messageType = 'error';
        }
    }
    
    // MODIFIER
    if ($action === 'update' && isset($_POST['id'])) {
        $errors = [];
        if (empty($_POST['type'])) $errors[] = 'Le type est obligatoire';
        if (empty($_POST['titre']) || strlen($_POST['titre']) < 3) $errors[] = 'Le titre doit contenir au moins 3 caractères';
        if (empty($_POST['description']) || strlen($_POST['description']) < 10) $errors[] = 'La description doit contenir au moins 10 caractères';
        if (empty($_POST['date_debut'])) $errors[] = 'La date de début est obligatoire';
        if (empty($_POST['date_fin'])) $errors[] = 'La date de fin est obligatoire';
        
        if (empty($errors)) {
            $evenement->setId($_POST['id']);
            $evenement->readOne();
            
            $imageUrl = $_POST['current_image'] ?? '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../../uploads/images/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $fileName = 'event_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                    $imageUrl = 'uploads/images/' . $fileName;
                }
            }
            
            $evenement->setType($_POST['type']);
            $evenement->setTitre($_POST['titre']);
            $evenement->setDescription($_POST['description']);
            $evenement->setDateDebut($_POST['date_debut']);
            $evenement->setDateFin($_POST['date_fin']);
            $evenement->setImageUrl($imageUrl ?: 'https://via.placeholder.com/600x400/1a1a1a/00ffff?text=Event');
            
            if ($evenement->update()) {
                if ($_POST['type'] === 'quiz') {
                    $question->setEvenementId($_POST['id']);
                    $question->deleteByEvenement();
                    
                    $nombreQuestions = 0;
                    if (isset($_POST['questions'])) {
                        foreach ($_POST['questions'] as $index => $q) {
                            if (!empty($q['texte'])) {
                                $question->setEvenementId($_POST['id']);
                                $question->setTexteQuestion($q['texte']);
                                $question->setOrdre($index + 1);
                                
                                if ($question->create()) {
                                    $questionId = $question->getId();
                                    $nombreQuestions++;
                                    
                                    if (isset($q['reponses'])) {
                                        $correctAnswers = $_POST['reponse_correcte_' . $index] ?? [];
                                        if (!is_array($correctAnswers)) $correctAnswers = [$correctAnswers];
                                        
                                        foreach ($q['reponses'] as $rIndex => $r) {
                                            if (!empty($r['texte'])) {
                                                $reponse->setQuestionId($questionId);
                                                $reponse->setTexteReponse($r['texte']);
                                                $reponse->setEstCorrecte(in_array($rIndex, $correctAnswers));
                                                $reponse->setOrdre($rIndex + 1);
                                                $reponse->create();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $evenement->setNombreQuestions($nombreQuestions);
                    $evenement->updateNombreQuestions();
                }
                
                $message = 'Événement modifié avec succès';
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
}

$stmt = $evenement->readAll();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$eventsJson = [];
foreach ($events as $e) {
    $eventData = $e;
    $eventData['questions'] = [];
    if ($e['type'] === 'quiz') {
        $question->setEvenementId($e['id']);
        $questionsStmt = $question->readByEvenement();
        while ($q = $questionsStmt->fetch(PDO::FETCH_ASSOC)) {
            $reponse->setQuestionId($q['id']);
            $reponsesStmt = $reponse->readByQuestion();
            $q['reponses'] = $reponsesStmt->fetchAll(PDO::FETCH_ASSOC);
            $eventData['questions'][] = $q;
        }
    }
    $eventsJson[] = $eventData;
}

function getImagePath($url) {
    if (empty($url)) return 'https://via.placeholder.com/600x400/1a1a1a/00ffff?text=Event';
    if (strpos($url, 'uploads/') === 0) return '../../' . $url;
    return $url;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Événements - Back Office</title>
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
        
        .btn {
            padding: 14px 30px;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-add {
            background: linear-gradient(135deg, var(--accent-green), var(--accent-cyan));
            color: #000;
            margin-bottom: 30px;
        }
        
        .btn-add:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,255,255,0.4); }
        
        .btn-primary { background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue)); color: #000; }
        .btn-danger { background: linear-gradient(135deg, #ff4444, #cc0000); color: #fff; }
        .btn-secondary { background: var(--metal-dark); color: var(--text-primary); }
        .btn-success { background: linear-gradient(135deg, var(--accent-green), var(--accent-cyan)); color: #000; }
        
        /* Events Grid */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }
        
        .event-card {
            background: var(--carbon-medium);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.4s ease;
            animation: fadeInUp 0.6s ease backwards;
        }
        
        .event-card:hover {
            transform: translateY(-10px);
            border-color: var(--accent-cyan);
            box-shadow: 0 20px 50px rgba(0,255,255,0.2);
        }
        
        .card-image {
            position: relative;
            height: 160px;
            overflow: hidden;
        }
        
        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .event-card:hover .card-image img { transform: scale(1.1); }
        
        .card-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .badge-quiz { background: var(--accent-purple); color: #fff; }
        .badge-normal { background: var(--accent-cyan); color: #000; }
        
        .card-content { padding: 20px; }
        
        .card-title {
            color: var(--text-primary);
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .card-description {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        
        .card-date {
            color: var(--accent-cyan);
            font-size: 13px;
            font-weight: 600;
        }
        
        .card-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .card-actions .btn { flex: 1; justify-content: center; padding: 12px; }
        
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
        }
        
        .modal-overlay.active { display: block; }
        
        .modal-box {
            background: var(--carbon-medium);
            border: 2px solid var(--accent-cyan);
            border-radius: 16px;
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
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
        
        .modal-title { color: var(--accent-cyan); font-size: 22px; font-weight: 700; }
        
        .modal-close {
            background: rgba(255,51,51,0.2);
            color: var(--accent-red);
            border: 1px solid var(--accent-red);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 20px;
            transition: all 0.3s ease;
        }
        
        .modal-close:hover { background: var(--accent-red); color: #fff; }
        
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
            box-shadow: 0 0 15px rgba(0,255,255,0.2);
        }
        
        .form-group input.valid { border-color: var(--accent-green); }
        .form-group input.invalid { border-color: var(--accent-red); }
        
        .form-group textarea { min-height: 100px; resize: vertical; }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        
        .form-actions { display: flex; gap: 15px; justify-content: flex-end; margin-top: 25px; }
        
        /* Validation Message */
        .validation-message {
            font-size: 12px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .validation-message.valid { color: var(--accent-green); }
        .validation-message.invalid { color: var(--accent-red); }
        
        /* Image Upload */
        .image-upload {
            border: 2px dashed var(--metal-light);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .image-upload:hover { border-color: var(--accent-cyan); background: rgba(0,255,255,0.05); }
        
        .image-preview { max-width: 150px; max-height: 100px; border-radius: 8px; margin-top: 10px; }
        
        /* Quiz Section */
        .quiz-section {
            background: rgba(153,69,255,0.05);
            border: 1px solid rgba(153,69,255,0.3);
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .quiz-section-title { color: var(--accent-purple); font-size: 16px; font-weight: 700; margin-bottom: 15px; }
        
        .question-block {
            background: var(--carbon-dark);
            border: 1px solid rgba(153,69,255,0.3);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            color: var(--accent-purple);
            font-weight: 700;
        }
        
        .reponse-block {
            background: var(--carbon-medium);
            border-left: 3px solid var(--accent-cyan);
            padding: 12px;
            margin: 8px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
            cursor: pointer;
            color: var(--text-secondary);
            padding: 8px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .checkbox-label:hover { background: rgba(0,255,136,0.1); }
        .checkbox-label input { width: 18px; height: 18px; accent-color: var(--accent-green); }
        .checkbox-label.checked { background: rgba(0,255,136,0.2); color: var(--accent-green); }
        
        .btn-remove { background: rgba(255,51,51,0.2); color: var(--accent-red); border: 1px solid var(--accent-red); padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 12px; }
        
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
            .form-row { grid-template-columns: 1fr; }
            .events-grid { grid-template-columns: 1fr; }
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
                <li><a href="manage-events.php" class="nav-link active">📅 Événements</a></li>
                <li><a href="manage-participations.php" class="nav-link">👥 Participations</a></li>
                <li><a href="../front/events.php" class="nav-link">🌐 Front Office</a></li>
            </ul>
        </nav>
    </header>

    <section class="admin-section">
        <div class="section-header">
            <h2 class="section-title">📅 Gestion des Événements</h2>
            <p class="section-subtitle">Créez, modifiez ou supprimez vos événements</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <button class="btn btn-add" onclick="openAddModal()">➕ Ajouter un événement</button>

        <div class="events-grid">
            <?php if (empty($events)): ?>
                <p style="text-align: center; color: var(--text-secondary); grid-column: 1/-1;">Aucun événement. Cliquez sur "Ajouter" pour créer le premier.</p>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <div class="card-image">
                            <img src="<?php echo getImagePath($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['titre']); ?>" onerror="this.src='https://via.placeholder.com/600x400/1a1a1a/00ffff?text=Event'">
                            <span class="card-badge <?php echo $event['type'] === 'quiz' ? 'badge-quiz' : 'badge-normal'; ?>">
                                <?php echo $event['type'] === 'quiz' ? '🎯 Quiz' : '📅 Normal'; ?>
                            </span>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?php echo htmlspecialchars($event['titre']); ?></h3>
                            <p class="card-description"><?php echo htmlspecialchars(substr($event['description'], 0, 80)); ?>...</p>
                            <div class="card-date">📅 <?php echo date('d M Y', strtotime($event['date_debut'])); ?> - <?php echo date('d M Y', strtotime($event['date_fin'])); ?></div>
                            <?php if ($event['type'] === 'quiz'): ?>
                                <p style="color: var(--accent-purple); font-size: 13px; margin-top: 10px;">❓ <?php echo $event['nombre_questions'] ?? 0; ?> question(s)</p>
                            <?php endif; ?>
                            <div class="card-actions">
                                <button class="btn btn-primary" onclick="openEditModal(<?php echo $event['id']; ?>)">✏️ Modifier</button>
                                <button class="btn btn-danger" onclick="showDeleteConfirm(<?php echo $event['id']; ?>, '<?php echo htmlspecialchars(addslashes($event['titre'])); ?>')">🗑️ Supprimer</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Modal Add/Edit -->
    <div class="modal-overlay" id="eventModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">➕ Ajouter un événement</h3>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
            
            <form method="POST" enctype="multipart/form-data" id="eventForm" onsubmit="return validateForm()">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="eventId" value="">
                <input type="hidden" name="current_image" id="currentImage" value="">
                
                <div class="form-group">
                    <label>Type d'événement *</label>
                    <select name="type" id="eventType" onchange="toggleQuizSection(); validateField(this, 'required')">
                        <option value="">-- Sélectionner --</option>
                        <option value="normal">📅 Événement Normal</option>
                        <option value="quiz">🎯 Quiz</option>
                    </select>
                    <div class="validation-message" id="type-validation"></div>
                </div>
                
                <div class="form-group">
                    <label>Titre * (min 3 caractères)</label>
                    <input type="text" name="titre" id="eventTitre" placeholder="Titre de l'événement" maxlength="150" oninput="validateField(this, 'minlength', 3)">
                    <div class="validation-message" id="titre-validation"></div>
                </div>
                
                <div class="form-group">
                    <label>Description * (min 10 caractères)</label>
                    <textarea name="description" id="eventDescription" placeholder="Description de l'événement" maxlength="500" oninput="validateField(this, 'minlength', 10)"></textarea>
                    <div class="validation-message" id="description-validation"></div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Date de début *</label>
                        <input type="datetime-local" name="date_debut" id="eventDateDebut" onchange="validateField(this, 'required'); validateDates()">
                        <div class="validation-message" id="date_debut-validation"></div>
                    </div>
                    <div class="form-group">
                        <label>Date de fin *</label>
                        <input type="datetime-local" name="date_fin" id="eventDateFin" onchange="validateField(this, 'required'); validateDates()">
                        <div class="validation-message" id="date_fin-validation"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Image</label>
                    <div class="image-upload" onclick="document.getElementById('imageInput').click()">
                        <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;" onchange="previewImage(this)">
                        <div>📷 Cliquez pour choisir une image</div>
                        <div style="color: var(--text-dim); font-size: 12px; margin-top: 5px;">JPG, PNG, GIF, WEBP (max 5MB)</div>
                        <img src="" class="image-preview" id="imagePreview" style="display: none;">
                    </div>
                </div>
                
                <div id="quizSection" class="quiz-section" style="display: none;">
                    <div class="quiz-section-title">🎯 Configuration du Quiz</div>
                    <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 15px;">✓ Plusieurs réponses correctes possibles</p>
                    <div id="questionsContainer"></div>
                    <button type="button" class="btn btn-secondary" onclick="addQuestion()" style="margin-top: 10px;">➕ Ajouter une question</button>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Annuler</button>
                    <button type="submit" class="btn btn-success" id="submitBtn">➕ Créer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="confirm-overlay" id="confirmOverlay">
        <div class="confirm-box">
            <div class="confirm-icon">⚠️</div>
            <div class="confirm-text" id="confirmText">Êtes-vous sûr de vouloir supprimer cet événement ?</div>
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
        const eventsData = <?php echo json_encode($eventsJson); ?>;
        let questionCount = 0;
        
        function openAddModal() {
            document.getElementById('eventForm').reset();
            document.getElementById('formAction').value = 'create';
            document.getElementById('eventId').value = '';
            document.getElementById('currentImage').value = '';
            document.getElementById('modalTitle').textContent = '➕ Ajouter un événement';
            document.getElementById('submitBtn').textContent = '➕ Créer';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('quizSection').style.display = 'none';
            document.getElementById('questionsContainer').innerHTML = '';
            questionCount = 0;
            clearValidations();
            document.getElementById('eventModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function openEditModal(id) {
            const event = eventsData.find(e => e.id == id);
            if (!event) return;
            
            document.getElementById('formAction').value = 'update';
            document.getElementById('eventId').value = event.id;
            document.getElementById('currentImage').value = event.image_url || '';
            document.getElementById('modalTitle').textContent = '✏️ Modifier l\'événement';
            document.getElementById('submitBtn').textContent = '💾 Enregistrer';
            
            document.getElementById('eventType').value = event.type;
            document.getElementById('eventTitre').value = event.titre;
            document.getElementById('eventDescription').value = event.description;
            document.getElementById('eventDateDebut').value = formatDateForInput(event.date_debut);
            document.getElementById('eventDateFin').value = formatDateForInput(event.date_fin);
            
            if (event.image_url) {
                document.getElementById('imagePreview').src = getImagePath(event.image_url);
                document.getElementById('imagePreview').style.display = 'block';
            } else {
                document.getElementById('imagePreview').style.display = 'none';
            }
            
            document.getElementById('questionsContainer').innerHTML = '';
            questionCount = 0;
            
            if (event.type === 'quiz') {
                document.getElementById('quizSection').style.display = 'block';
                if (event.questions && event.questions.length > 0) {
                    event.questions.forEach(q => addQuestion(q.texte_question, q.reponses));
                } else {
                    addQuestion();
                }
            } else {
                document.getElementById('quizSection').style.display = 'none';
            }
            
            clearValidations();
            document.getElementById('eventModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal() {
            document.getElementById('eventModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        
        function toggleQuizSection() {
            const type = document.getElementById('eventType').value;
            if (type === 'quiz') {
                document.getElementById('quizSection').style.display = 'block';
                if (document.getElementById('questionsContainer').children.length === 0) addQuestion();
            } else {
                document.getElementById('quizSection').style.display = 'none';
            }
        }
        
        function addQuestion(texte = '', reponses = null) {
            const qIndex = questionCount;
            let reponsesHtml = '';
            
            if (reponses && reponses.length > 0) {
                reponses.forEach((r, rIndex) => {
                    reponsesHtml += createReponseHtml(qIndex, rIndex, r.texte_reponse, r.est_correcte == 1);
                });
            } else {
                for (let i = 0; i < 4; i++) {
                    reponsesHtml += createReponseHtml(qIndex, i, '', false);
                }
            }
            
            const html = `
                <div class="question-block" id="question-${qIndex}">
                    <div class="question-header">
                        <span>Question ${qIndex + 1}</span>
                        <button type="button" class="btn-remove" onclick="removeQuestion(${qIndex})">✕</button>
                    </div>
                    <div class="form-group">
                        <label>Texte de la question *</label>
                        <input type="text" name="questions[${qIndex}][texte]" value="${escapeHtml(texte)}" placeholder="Entrez votre question" oninput="validateField(this, 'minlength', 3)">
                        <div class="validation-message"></div>
                    </div>
                    <div id="reponses-${qIndex}">${reponsesHtml}</div>
                    <button type="button" class="btn btn-secondary" style="padding: 8px 15px; font-size: 12px; margin-top: 10px;" onclick="addReponse(${qIndex})">➕ Réponse</button>
                </div>
            `;
            document.getElementById('questionsContainer').insertAdjacentHTML('beforeend', html);
            questionCount++;
        }
        
        function createReponseHtml(qIndex, rIndex, texte, isCorrect) {
            return `
                <div class="reponse-block">
                    <div class="form-group" style="margin-bottom: 8px;">
                        <label style="font-size: 11px;">Réponse ${rIndex + 1}</label>
                        <input type="text" name="questions[${qIndex}][reponses][${rIndex}][texte]" value="${escapeHtml(texte)}" placeholder="Texte de la réponse">
                    </div>
                    <label class="checkbox-label ${isCorrect ? 'checked' : ''}">
                        <input type="checkbox" name="reponse_correcte_${qIndex}[]" value="${rIndex}" ${isCorrect ? 'checked' : ''} onchange="updateCheckboxStyle(this)">
                        ✓ Correcte
                    </label>
                </div>
            `;
        }
        
        function addReponse(qIndex) {
            const container = document.getElementById(`reponses-${qIndex}`);
            const rIndex = container.children.length;
            container.insertAdjacentHTML('beforeend', createReponseHtml(qIndex, rIndex, '', false));
        }
        
        function removeQuestion(qIndex) {
            const q = document.getElementById(`question-${qIndex}`);
            if (q) q.remove();
        }
        
        function updateCheckboxStyle(checkbox) {
            const label = checkbox.parentElement;
            label.classList.toggle('checked', checkbox.checked);
        }
        
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function validateField(field, type, minVal) {
            const value = field.value.trim();
            const msgDiv = field.parentElement.querySelector('.validation-message');
            let isValid = true;
            let message = '';
            
            if (type === 'required') {
                isValid = value !== '';
                message = isValid ? '✓ Valide' : '⚠ Ce champ est obligatoire';
            } else if (type === 'minlength') {
                isValid = value.length >= minVal;
                message = isValid ? `✓ ${value.length} caractères` : `⚠ Minimum ${minVal} caractères (${value.length}/${minVal})`;
            }
            
            field.classList.remove('valid', 'invalid');
            field.classList.add(isValid ? 'valid' : 'invalid');
            
            if (msgDiv) {
                msgDiv.className = 'validation-message ' + (isValid ? 'valid' : 'invalid');
                msgDiv.textContent = message;
            }
            
            return isValid;
        }
        
        function validateDates() {
            const debut = document.getElementById('eventDateDebut').value;
            const fin = document.getElementById('eventDateFin').value;
            const msgDiv = document.getElementById('date_fin-validation');
            
            if (debut && fin && new Date(fin) <= new Date(debut)) {
                document.getElementById('eventDateFin').classList.add('invalid');
                document.getElementById('eventDateFin').classList.remove('valid');
                msgDiv.className = 'validation-message invalid';
                msgDiv.textContent = '⚠ La date de fin doit être après la date de début';
                return false;
            }
            return true;
        }
        
        function validateForm() {
            let isValid = true;
            isValid = validateField(document.getElementById('eventType'), 'required') && isValid;
            isValid = validateField(document.getElementById('eventTitre'), 'minlength', 3) && isValid;
            isValid = validateField(document.getElementById('eventDescription'), 'minlength', 10) && isValid;
            isValid = validateField(document.getElementById('eventDateDebut'), 'required') && isValid;
            isValid = validateField(document.getElementById('eventDateFin'), 'required') && isValid;
            isValid = validateDates() && isValid;
            return isValid;
        }
        
        function clearValidations() {
            document.querySelectorAll('.validation-message').forEach(el => {
                el.textContent = '';
                el.className = 'validation-message';
            });
            document.querySelectorAll('input, select, textarea').forEach(el => {
                el.classList.remove('valid', 'invalid');
            });
        }
        
        function showDeleteConfirm(id, titre) {
            document.getElementById('deleteId').value = id;
            document.getElementById('confirmText').textContent = `Êtes-vous sûr de vouloir supprimer "${titre}" ?`;
            document.getElementById('confirmOverlay').classList.add('active');
        }
        
        function hideDeleteConfirm() {
            document.getElementById('confirmOverlay').classList.remove('active');
        }
        
        function formatDateForInput(dateStr) {
            const date = new Date(dateStr);
            return date.toISOString().slice(0, 16);
        }
        
        function getImagePath(url) {
            if (!url) return 'https://via.placeholder.com/600x400/1a1a1a/00ffff?text=Event';
            if (url.startsWith('uploads/')) return '../../' + url;
            return url;
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeModal();
                hideDeleteConfirm();
            }
        });
        
        document.getElementById('eventModal').addEventListener('click', e => {
            if (e.target.id === 'eventModal') closeModal();
        });
    </script>
</body>
</html>
