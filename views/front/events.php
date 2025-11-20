<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - Human Nova AI</title>
    <link rel="stylesheet" href="../../assets/css/templatemo-prism-flux.css">
    <style>
        /* Barre de recherche fixe et transparente */
        .search-bar {
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 600px;
            z-index: 999;
            background: rgba(18, 18, 18, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 255, 255, 0.3);
            border-radius: 50px;
            padding: 15px 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
        }

        .search-bar:focus-within {
            background: rgba(18, 18, 18, 0.95);
            border-color: var(--accent-cyan);
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.4);
        }

        .search-input {
            width: 100%;
            background: transparent;
            border: none;
            color: var(--text-primary);
            font-size: 16px;
            outline: none;
            padding: 5px 10px;
        }

        .search-input::placeholder {
            color: var(--text-dim);
        }

        /* Ajustement de la section événements */
        .admin-events-section {
            margin-top: 180px;
        }

        /* Modal détails personnalisé */
        .details-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2000;
            overflow-y: auto;
        }

        .details-modal-content {
            position: relative;
            background: linear-gradient(135deg, var(--carbon-medium), var(--carbon-dark));
            max-width: 900px;
            margin: 60px auto;
            border-radius: 16px;
            padding: 40px;
            border: 2px solid var(--accent-cyan);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.9);
            animation: slideInUp 0.4s ease;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .details-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(0, 255, 255, 0.2);
        }

        .details-title {
            font-size: 32px;
            font-weight: 900;
            color: var(--accent-cyan);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .details-close {
            background: rgba(255, 51, 51, 0.2);
            color: var(--accent-red);
            border: 1px solid var(--accent-red);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 24px;
            transition: all 0.3s ease;
        }

        .details-close:hover {
            background: var(--accent-red);
            color: #fff;
            transform: rotate(90deg);
        }

        .details-body {
            color: var(--text-secondary);
            line-height: 1.8;
        }

        .detail-item {
            margin: 20px 0;
            padding: 15px;
            background: rgba(0, 255, 255, 0.05);
            border-left: 3px solid var(--accent-cyan);
            border-radius: 6px;
        }

        .detail-label {
            color: var(--accent-cyan);
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .detail-value {
            color: var(--text-primary);
            font-size: 16px;
        }

        .event-image-large {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 12px;
            margin: 20px 0;
            border: 2px solid rgba(0, 255, 255, 0.2);
        }

        /* Quiz styles */
        .quiz-container {
            margin-top: 30px;
        }

        .question-card {
            background: linear-gradient(135deg, rgba(153, 69, 255, 0.1), rgba(0, 168, 255, 0.1));
            border: 2px solid var(--accent-purple);
            border-radius: 12px;
            padding: 25px;
            margin: 20px 0;
        }

        .question-number {
            color: var(--accent-purple);
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .question-text {
            color: var(--text-primary);
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .answer-option {
            background: var(--carbon-light);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 15px 20px;
            margin: 10px 0;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .answer-option:hover {
            border-color: var(--accent-cyan);
            background: rgba(0, 255, 255, 0.05);
            transform: translateX(5px);
        }

        .answer-option.selected {
            border-color: var(--accent-cyan);
            background: rgba(0, 255, 255, 0.15);
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.3);
        }

        .answer-option input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .answer-label {
            color: var(--text-secondary);
            font-size: 16px;
            cursor: pointer;
            flex: 1;
        }

        /* Formulaire de participation */
        .participation-form {
            margin-top: 30px;
        }

        .form-field {
            margin: 20px 0;
        }

        .form-field label {
            display: block;
            color: var(--accent-cyan);
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .form-field input,
        .form-field textarea {
            width: 100%;
            background: var(--carbon-dark);
            color: var(--text-primary);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-field input:focus,
        .form-field textarea:focus {
            outline: none;
            border-color: var(--accent-cyan);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.3);
        }

        .file-upload {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .file-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            color: #000;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(153, 69, 255, 0.4);
        }

        /* Résultat du quiz */
        .quiz-result {
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(0, 255, 255, 0.1));
            border: 3px solid var(--accent-green);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin-top: 30px;
        }

        .result-score {
            font-size: 64px;
            font-weight: 900;
            color: var(--accent-green);
            margin: 20px 0;
        }

        .result-text {
            font-size: 24px;
            color: var(--text-primary);
            margin: 10px 0;
        }

        .result-detail {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid rgba(0, 255, 136, 0.2);
        }

        .result-stat {
            text-align: center;
        }

        .result-stat-number {
            font-size: 36px;
            font-weight: 900;
            margin-bottom: 10px;
        }

        .result-stat-number.correct {
            color: var(--accent-green);
        }

        .result-stat-number.incorrect {
            color: var(--accent-red);
        }

        .result-stat-label {
            color: var(--text-secondary);
            font-size: 14px;
            text-transform: uppercase;
        }

        /* Boutons d'action */
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn-action {
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            color: #000;
            border: none;
            padding: 15px 40px;
            border-radius: 8px;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0, 255, 255, 0.5);
            color: #fff;
        }

        .btn-action.secondary {
            background: linear-gradient(135deg, var(--metal-dark), var(--metal-light));
            color: var(--text-primary);
        }

        /* Badges de score */
        .score-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .score-badge.excellent {
            background: linear-gradient(135deg, var(--accent-green), var(--accent-cyan));
            color: #000;
        }

        .score-badge.good {
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            color: #000;
        }

        .score-badge.average {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            color: #fff;
        }

        .score-badge.poor {
            background: linear-gradient(135deg, var(--accent-red), var(--accent-purple));
            color: #fff;
        }

        /* Success message */
        .success-notification {
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.2), rgba(0, 255, 255, 0.2));
            border: 2px solid var(--accent-green);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            animation: slideInDown 0.5s ease;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .details-modal-content {
                margin: 20px;
                padding: 25px;
            }

            .details-title {
                font-size: 24px;
            }

            .event-image-large {
                height: 250px;
            }

            .result-score {
                font-size: 48px;
            }

            .result-text {
                font-size: 18px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
            }
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
                <li><a href="events.php" class="nav-link active">Événements</a></li>
            </ul>
        </nav>
    </header>

    <!-- Barre de recherche fixe -->
    <div class="search-bar">
        <input type="text" class="search-input" id="searchInput" placeholder="🔍 Rechercher un événement...">
    </div>

    <section class="admin-events-section">
        <div class="section-header">
            <h2 class="section-title">Nos Événements</h2>
            <p class="section-subtitle">Découvrez nos prochains événements et participez !</p>
        </div>
        
        <div class="events-grid" id="eventsGrid"></div>
    </section>

    <!-- Modal détails -->
    <div id="detailsModal" class="details-modal">
        <div class="details-modal-content">
            <div class="details-header">
                <h2 class="details-title" id="modalTitle"></h2>
                <button class="details-close" onclick="closeDetailsModal()">×</button>
            </div>
            <div class="details-body" id="modalBody"></div>
        </div>
    </div>

    <!-- Backdrop -->
    <div id="modalBackdrop" class="modal-backdrop" style="display:none;" onclick="closeDetailsModal()"></div>

    <footer class="footer" style="text-align: center; padding: 40px 20px; border-top: 1px solid rgba(255,255,255,0.1); margin-top: 80px;">
        <p style="color: var(--text-secondary);">&copy; 2026 Human Nova AI. Tous droits réservés.</p>
    </footer>

    <script>
        const API_URL = '../../controllers/EvenementController.php';
        const PARTICIPATION_URL = '../../api/participation.php';
        let allEvents = [];
        let currentEvent = null;

        // Charger les événements au chargement
        document.addEventListener('DOMContentLoaded', function() {
            loadEvenements();
            setupSearchFilter();
        });

        // Charger tous les événements
        function loadEvenements() {
            fetch(`${API_URL}?action=getAll`)
                .then(response => response.json())
                .then(data => {
                    allEvents = data;
                    displayEvenements(data);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('eventsGrid').innerHTML = 
                        '<p style="text-align:center; color: var(--text-secondary);">Erreur lors du chargement</p>';
                });
        }

        // Afficher les événements
        function displayEvenements(evenements) {
            const grid = document.getElementById('eventsGrid');
            grid.innerHTML = '';
            
            if(evenements.length === 0) {
                grid.innerHTML = '<p style="text-align:center; color: var(--text-secondary);">Aucun événement trouvé</p>';
                return;
            }
            
            evenements.forEach(event => {
                const typeLabel = event.type === 'quiz' ? 'QUIZ' : 'ÉVÉNEMENT';
                const typeColor = event.type === 'quiz' ? 'var(--accent-purple)' : 'var(--accent-cyan)';
                
                const card = document.createElement('div');
                card.className = 'event-card';
                card.innerHTML = `
                    <div class="event-image">
                        <img src="${escapeHtml(event.image_url || 'https://via.placeholder.com/600x400?text=No+Image')}" 
                             alt="${escapeHtml(event.titre)}" 
                             onerror="this.src='https://via.placeholder.com/600x400?text=No+Image'" />
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h3 class="event-title">${escapeHtml(event.titre)}</h3>
                        <span style="background: ${typeColor}; color: #000; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 700;">${typeLabel}</span>
                    </div>
                    <p class="event-date">📅 ${formatDate(event.date_debut)}</p>
                    <p class="event-description">${escapeHtml(event.description.substring(0, 100))}...</p>
                    ${event.type === 'quiz' ? `<p style="color: var(--accent-cyan); font-size: 12px; margin-top: 8px;">📝 ${event.nombre_questions} question(s)</p>` : ''}
                    <button class="card-cta" onclick="showDetails(${event.id})">Détails</button>
                `;
                grid.appendChild(card);
            });
        }

        // Configuration du filtre de recherche
        function setupSearchFilter() {
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const filtered = allEvents.filter(event => 
                    event.titre.toLowerCase().includes(searchTerm) ||
                    event.description.toLowerCase().includes(searchTerm)
                );
                displayEvenements(filtered);
            });
        }

        // Afficher les détails d'un événement
        function showDetails(eventId) {
            fetch(`${API_URL}?action=getOne&id=${eventId}`)
                .then(response => response.json())
                .then(event => {
                    if(!event) {
                        alert('Événement introuvable');
                        return;
                    }
                    
                    currentEvent = event;
                    document.getElementById('modalTitle').textContent = event.titre;
                    
                    let bodyHTML = `
                        <img src="${event.image_url || 'https://via.placeholder.com/600x400'}" 
                             class="event-image-large" 
                             onerror="this.src='https://via.placeholder.com/600x400'">
                        
                        <div class="detail-item">
                            <div class="detail-label">Type</div>
                            <div class="detail-value">${event.type === 'quiz' ? '🎯 Quiz Interactif' : '📅 Événement'}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Description</div>
                            <div class="detail-value">${escapeHtml(event.description)}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Date de début</div>
                            <div class="detail-value">${formatDateLong(event.date_debut)}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Date de fin</div>
                            <div class="detail-value">${formatDateLong(event.date_fin)}</div>
                        </div>
                    `;
                    
                    if(event.type === 'quiz') {
                        bodyHTML += `<div class="action-buttons">
                            <button class="btn-action" onclick="startQuiz()">Commencer le Quiz</button>
                        </div>`;
                    } else {
                        bodyHTML += `<div class="action-buttons">
                            <button class="btn-action" onclick="showParticipationForm()">Participer</button>
                        </div>`;
                    }
                    
                    document.getElementById('modalBody').innerHTML = bodyHTML;
                    openModal();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du chargement des détails');
                });
        }

        // Démarrer le quiz
        function startQuiz() {
            if(!currentEvent || !currentEvent.questions) return;
            
            let quizHTML = `
                <form id="quizForm" onsubmit="submitQuiz(event); return false;">
                    <div class="participation-form">
                        <h3 style="color: var(--accent-purple); margin-bottom: 20px;">Informations personnelles</h3>
                        <div class="form-field">
                            <label>Nom *</label>
                            <input type="text" id="quizNom" name="nom" required>
                        </div>
                        <div class="form-field">
                            <label>Prénom *</label>
                            <input type="text" id="quizPrenom" name="prenom" required>
                        </div>
                        <div class="form-field">
                            <label>Email *</label>
                            <input type="email" id="quizEmail" name="email" required>
                        </div>
                    </div>
                    
                    <div class="quiz-container" id="quizQuestions">
            `;
            
            currentEvent.questions.forEach((question, index) => {
                quizHTML += `
                    <div class="question-card">
                        <div class="question-number">Question ${index + 1} sur ${currentEvent.questions.length}</div>
                        <div class="question-text">${escapeHtml(question.texte_question)}</div>
                        <div class="answers">
                `;
                
                question.reponses.forEach((reponse, repIndex) => {
                    quizHTML += `
                        <div class="answer-option" onclick="selectAnswer(${question.id}, ${reponse.id}, this)">
                            <input type="radio" name="question_${question.id}" value="${reponse.id}" id="rep_${question.id}_${reponse.id}" required>
                            <label class="answer-label" for="rep_${question.id}_${reponse.id}">${escapeHtml(reponse.texte_reponse)}</label>
                        </div>
                    `;
                });
                
                quizHTML += `
                        </div>
                    </div>
                `;
            });
            
            quizHTML += `
                    </div>
                    <div class="action-buttons">
                        <button type="button" class="btn-action secondary" onclick="showDetails(${currentEvent.id})">Retour</button>
                        <button type="submit" class="btn-action">Valider le Quiz</button>
                    </div>
                </form>
            `;
            
            document.getElementById('modalBody').innerHTML = quizHTML;
        }

        // Sélectionner une réponse
        function selectAnswer(questionId, reponseId, element) {
            // Retirer la sélection des autres réponses
            const parent = element.closest('.answers');
            parent.querySelectorAll('.answer-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Sélectionner cette réponse
            element.classList.add('selected');
            document.getElementById(`rep_${questionId}_${reponseId}`).checked = true;
        }

        // Soumettre le quiz
        function submitQuiz(e) {
            if(e) e.preventDefault();
            
            const form = document.getElementById('quizForm');
            if(!form) {
                alert('Formulaire introuvable');
                return;
            }
            
            // Récupérer les données du formulaire
            const formData = new FormData(form);
            const nom = formData.get('nom');
            const prenom = formData.get('prenom');
            const email = formData.get('email');
            
            console.log('Données du quiz:', { nom, prenom, email });
            
            if(!nom || !prenom || !email) {
                alert('Veuillez remplir toutes les informations personnelles');
                return;
            }
            
            // Collecter les réponses
            const reponses = {};
            let allAnswered = true;
            let questionCount = 0;
            
            currentEvent.questions.forEach(question => {
                questionCount++;
                const selected = document.querySelector(`input[name="question_${question.id}"]:checked`);
                if(selected) {
                    reponses[question.id] = parseInt(selected.value);
                    console.log(`Question ${question.id}: réponse ${selected.value}`);
                } else {
                    allAnswered = false;
                    console.log(`Question ${question.id}: pas de réponse`);
                }
            });
            
            console.log('Total questions:', questionCount);
            console.log('Réponses collectées:', reponses);
            console.log('Toutes répondues?', allAnswered);
            
            if(!allAnswered) {
                alert('Veuillez répondre à toutes les questions');
                return;
            }
            
            // Désactiver le bouton
            const submitBtn = form.querySelector('button[type="submit"]');
            if(submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Envoi en cours...';
            }
            
            // Préparer les données
            const data = {
                nom: nom.trim(),
                prenom: prenom.trim(),
                email: email.trim(),
                evenement_id: currentEvent.id,
                reponses: reponses
            };
            
            console.log('Envoi des données:', data);
            
            // Envoyer au serveur
            fetch(`${PARTICIPATION_URL}?action=soumettreQuiz`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log('Statut:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('Réponse brute:', text);
                try {
                    const result = JSON.parse(text);
                    console.log('Résultat:', result);
                    
                    if(result.success) {
                        showQuizResult(result);
                    } else {
                        alert('Erreur: ' + result.message);
                        if(submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Valider le Quiz';
                        }
                    }
                } catch(e) {
                    console.error('Erreur parsing:', e);
                    alert('Erreur: Réponse invalide du serveur');
                    if(submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Valider le Quiz';
                    }
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la soumission du quiz: ' + error.message);
                if(submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Valider le Quiz';
                }
            });
        }

        // Afficher le résultat du quiz
        function showQuizResult(result) {
            const incorrect = result.total - result.score;
            const percentage = result.pourcentage || 0;
            
            // Déterminer le badge selon le score
            let badgeClass = 'poor';
            let badgeText = 'À améliorer';
            let emoji = '😐';
            
            if(percentage >= 80) {
                badgeClass = 'excellent';
                badgeText = 'Excellent !';
                emoji = '🎉';
            } else if(percentage >= 60) {
                badgeClass = 'good';
                badgeText = 'Bien joué !';
                emoji = '👍';
            } else if(percentage >= 40) {
                badgeClass = 'average';
                badgeText = 'Peut mieux faire';
                emoji = '🙂';
            }
            
            const resultHTML = `
                <div class="quiz-result">
                    <h3 style="color: var(--accent-green); font-size: 28px; margin-bottom: 20px;">
                        ${emoji} Quiz Terminé !
                    </h3>
                    
                    <div class="result-score">${Math.round(percentage)}%</div>
                    
                    <div class="score-badge ${badgeClass}" style="
                        display: inline-block;
                        padding: 10px 20px;
                        border-radius: 20px;
                        font-weight: 700;
                        font-size: 16px;
                        margin: 20px 0;
                    ">
                        ${badgeText}
                    </div>
                    
                    <div class="result-text">
                        Votre score : <strong>${result.score} / ${result.total}</strong>
                    </div>
                    
                    <div class="result-detail">
                        <div class="result-stat">
                            <div class="result-stat-number correct">${result.score}</div>
                            <div class="result-stat-label">✓ Réponses Correctes</div>
                        </div>
                        <div class="result-stat">
                            <div class="result-stat-number incorrect">${incorrect}</div>
                            <div class="result-stat-label">✗ Réponses Incorrectes</div>
                        </div>
                    </div>
                    
                    <div style="
                        background: rgba(0, 255, 136, 0.1);
                        border: 1px solid var(--accent-green);
                        border-radius: 8px;
                        padding: 15px;
                        margin-top: 30px;
                        color: var(--text-secondary);
                        font-size: 14px;
                    ">
                        <strong style="color: var(--accent-green);">✓ Enregistrement réussi</strong><br>
                        Votre résultat a été enregistré dans notre système.<br>
                        ID du résultat : #${result.resultat_id || 'N/A'}
                    </div>
                </div>
                
                <div class="action-buttons">
                    <button class="btn-action secondary" onclick="closeDetailsModal()">Fermer</button>
                    <button class="btn-action" onclick="showDetails(${currentEvent.id})">Voir les détails de l'événement</button>
                </div>
            `;
            
            document.getElementById('modalBody').innerHTML = resultHTML;
            
            // Animation confetti si score >= 80%
            if(percentage >= 80) {
                createConfetti();
            }
        }
        
        // Créer des confettis pour célébrer
        function createConfetti() {
            const colors = ['#00ff88', '#00ffff', '#9945ff', '#ff3333'];
            const confettiCount = 50;
            
            for(let i = 0; i < confettiCount; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.style.position = 'fixed';
                    confetti.style.width = '10px';
                    confetti.style.height = '10px';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.left = Math.random() * 100 + '%';
                    confetti.style.top = '-10px';
                    confetti.style.opacity = '1';
                    confetti.style.zIndex = '10000';
                    confetti.style.borderRadius = '50%';
                    confetti.style.pointerEvents = 'none';
                    
                    document.body.appendChild(confetti);
                    
                    const fallDuration = 3000 + Math.random() * 2000;
                    const swayAmount = (Math.random() - 0.5) * 100;
                    
                    confetti.animate([
                        { transform: 'translateY(0) translateX(0) rotate(0deg)', opacity: 1 },
                        { transform: `translateY(${window.innerHeight + 10}px) translateX(${swayAmount}px) rotate(${Math.random() * 360}deg)`, opacity: 0 }
                    ], {
                        duration: fallDuration,
                        easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
                    }).onfinish = () => {
                        confetti.remove();
                    };
                }, i * 30);
            }
        }

        // Afficher le formulaire de participation
        function showParticipationForm() {
            const formHTML = `
                <div class="participation-form">
                    <h3 style="color: var(--accent-cyan); margin-bottom: 20px;">Formulaire de participation</h3>
                    
                    <form id="participationForm" enctype="multipart/form-data">
                        <div class="form-field">
                            <label>Nom *</label>
                            <input type="text" id="partNom" required>
                        </div>
                        
                        <div class="form-field">
                            <label>Prénom *</label>
                            <input type="text" id="partPrenom" required>
                        </div>
                        
                        <div class="form-field">
                            <label>Email *</label>
                            <input type="email" id="partEmail" required>
                        </div>
                        
                        <div class="form-field">
                            <label>Commentaire *</label>
                            <textarea id="partCommentaire" rows="5" placeholder="Pourquoi souhaitez-vous participer ?" required></textarea>
                        </div>
                        
                        <div class="form-field">
                            <label>Fichier (optionnel)</label>
                            <div class="file-upload">
                                <input type="file" id="partFichier" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip">
                                <div class="file-upload-label">
                                    <span>📎</span>
                                    <span id="fileName">Choisir un fichier</span>
                                </div>
                            </div>
                            <small style="color: var(--text-dim); display: block; margin-top: 5px;">
                                Formats acceptés: PDF, DOC, DOCX, JPG, PNG, ZIP (max 5MB)
                            </small>
                        </div>
                    </form>
                    
                    <div class="action-buttons">
                        <button class="btn-action secondary" onclick="showDetails(${currentEvent.id})">Retour</button>
                        <button class="btn-action" id="btnEnregistrerPart" onclick="submitParticipation()">Enregistrer</button>
                    </div>
                </div>
            `;
            
            document.getElementById('modalBody').innerHTML = formHTML;
            
            // Listener pour le nom du fichier
            document.getElementById('partFichier').addEventListener('change', function(e) {
                const fileName = e.target.files[0] ? e.target.files[0].name : 'Choisir un fichier';
                document.getElementById('fileName').textContent = fileName;
            });
        }

        // Soumettre la participation
        function submitParticipation() {
            const nom = document.getElementById('partNom').value.trim();
            const prenom = document.getElementById('partPrenom').value.trim();
            const email = document.getElementById('partEmail').value.trim();
            const commentaire = document.getElementById('partCommentaire').value.trim();
            
            if(!nom || !prenom || !email || !commentaire) {
                alert('Veuillez remplir tous les champs obligatoires');
                return;
            }
            
            // Créer FormData
            const formData = new FormData();
            formData.append('nom', nom);
            formData.append('prenom', prenom);
            formData.append('email', email);
            formData.append('commentaire', commentaire);
            formData.append('evenement_id', currentEvent.id);
            
            // Ajouter le fichier s'il existe
            const fichier = document.getElementById('partFichier').files[0];
            if(fichier) {
                formData.append('fichier', fichier);
            }
            
            // Log pour debug
            console.log('Envoi des données:', {
                nom, prenom, email, 
                commentaire: commentaire.substring(0, 50),
                evenement_id: currentEvent.id,
                fichier: fichier ? fichier.name : 'aucun'
            });
            
            // Désactiver le bouton pendant l'envoi
            const btnEnregistrer = document.getElementById('btnEnregistrerPart');
            if(btnEnregistrer) {
                const originalText = btnEnregistrer.textContent;
                btnEnregistrer.textContent = 'Envoi en cours...';
                btnEnregistrer.disabled = true;
                
                // Envoyer avec l'action dans l'URL
                fetch(PARTICIPATION_URL + '?action=soumettreParticipation', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Statut de la réponse:', response.status);
                    return response.text();
                })
                .then(text => {
                    console.log('Réponse brute:', text);
                    try {
                        const result = JSON.parse(text);
                        console.log('Réponse JSON:', result);
                        
                        if(result.success) {
                            showSuccessMessage('Votre participation a été enregistrée avec succès !');
                        } else {
                            alert('Erreur: ' + result.message);
                        }
                    } catch(e) {
                        console.error('Erreur de parsing JSON:', e);
                        console.error('Texte reçu:', text);
                        alert('Erreur: Réponse invalide du serveur');
                    }
                })
                .catch(error => {
                    console.error('Erreur réseau:', error);
                    alert('Erreur lors de l\'enregistrement: ' + error.message);
                })
                .finally(() => {
                    btnEnregistrer.textContent = originalText;
                    btnEnregistrer.disabled = false;
                });
            }
        }

        // Afficher un message de succès
        function showSuccessMessage(message) {
            const successHTML = `
                <div class="success-notification">
                    <h3 style="color: var(--accent-green); font-size: 24px; margin-bottom: 15px;">✓ Succès !</h3>
                    <p style="color: var(--text-primary); font-size: 16px;">${message}</p>
                </div>
                
                <div class="action-buttons">
                    <button class="btn-action secondary" onclick="closeDetailsModal()">Fermer</button>
                    <button class="btn-action" onclick="showDetails(${currentEvent.id})">Voir les détails</button>
                </div>
            `;
            
            document.getElementById('modalBody').innerHTML = successHTML;
        }

        // Ouvrir/Fermer le modal
        function openModal() {
            document.getElementById('detailsModal').style.display = 'block';
            document.getElementById('modalBackdrop').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').style.display = 'none';
            document.getElementById('modalBackdrop').style.display = 'none';
            document.body.style.overflow = 'auto';
            currentEvent = null;
        }

        // Fonctions utilitaires
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('fr-FR', { 
                day: '2-digit', 
                month: 'long', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function formatDateLong(dateStr) {
            const date = new Date(dateStr);
            const options = { 
                weekday: 'long',
                day: 'numeric', 
                month: 'long', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return date.toLocaleDateString('fr-FR', options);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Fermer avec Échap
        document.addEventListener('keydown', (e) => {
            if(e.key === 'Escape') {
                closeDetailsModal();
            }
        });
    </script>
</body>
</html>