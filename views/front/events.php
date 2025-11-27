<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - Human Nova AI</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* Search Bar */
        .search-container {
            position: fixed;
            top: 90px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 600px;
            z-index: 999;
        }

        .search-bar {
            width: 100%;
            background: rgba(18, 18, 18, 0.95);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(0, 255, 255, 0.3);
            border-radius: 50px;
            padding: 15px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
        }

        .search-bar:focus-within {
            border-color: var(--accent-cyan);
            box-shadow: 0 10px 50px rgba(0, 255, 255, 0.4);
        }

        .search-icon {
            color: var(--accent-cyan);
            font-size: 20px;
        }

        .search-input {
            flex: 1;
            background: transparent;
            border: none;
            color: var(--text-primary);
            font-size: 16px;
            outline: none;
        }

        .search-input::placeholder {
            color: var(--text-dim);
        }

        .front-section {
            margin-top: 180px;
        }

        /* Modal Overlay */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 2000;
            overflow-y: auto;
            padding: 30px;
        }

        .modal-overlay.active {
            display: block;
        }

        .modal-box {
            position: relative;
            background: linear-gradient(135deg, var(--carbon-medium), var(--carbon-dark));
            max-width: 900px;
            margin: 0 auto;
            border-radius: 20px;
            padding: 40px;
            border: 2px solid var(--accent-cyan);
            box-shadow: 0 30px 80px rgba(0, 255, 255, 0.3);
            animation: modalSlideIn 0.4s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(0, 255, 255, 0.2);
        }

        .modal-title {
            font-size: 28px;
            font-weight: 900;
            color: var(--accent-cyan);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .modal-close {
            background: rgba(255, 51, 51, 0.2);
            color: var(--accent-red);
            border: 2px solid var(--accent-red);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 24px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: var(--accent-red);
            color: #fff;
            transform: rotate(90deg);
        }

        /* Event Image */
        .event-image-large {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 16px;
            margin-bottom: 25px;
            border: 2px solid rgba(0, 255, 255, 0.2);
        }

        /* Detail Items */
        .detail-item {
            background: rgba(0, 255, 255, 0.05);
            border-left: 4px solid var(--accent-cyan);
            padding: 20px;
            margin: 15px 0;
            border-radius: 0 12px 12px 0;
        }

        .detail-label {
            color: var(--accent-cyan);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .detail-value {
            color: var(--text-primary);
            font-size: 16px;
            line-height: 1.6;
        }

        /* Participation Form */
        .participation-form {
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.05), rgba(0, 168, 255, 0.05));
            border: 2px solid rgba(0, 255, 136, 0.3);
            border-radius: 16px;
            padding: 35px;
            margin-top: 30px;
        }

        .form-title {
            color: var(--accent-green);
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-field {
            margin-bottom: 25px;
        }

        .form-field label {
            display: block;
            color: var(--accent-cyan);
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-field input,
        .form-field textarea {
            width: 100%;
            padding: 16px 20px;
            background: var(--carbon-dark);
            border: 2px solid var(--metal-dark);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-field input:focus,
        .form-field textarea:focus {
            outline: none;
            border-color: var(--accent-cyan);
            box-shadow: 0 0 25px rgba(0, 255, 255, 0.3);
            background: rgba(0, 255, 255, 0.05);
        }

        .form-field textarea {
            resize: vertical;
            min-height: 120px;
        }

        /* File Upload */
        .file-upload-wrapper {
            position: relative;
        }

        .file-upload-area {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            padding: 30px;
            background: var(--carbon-dark);
            border: 2px dashed var(--metal-light);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-area:hover {
            border-color: var(--accent-cyan);
            background: rgba(0, 255, 255, 0.05);
        }

        .file-upload-area.has-file {
            border-color: var(--accent-green);
            background: rgba(0, 255, 136, 0.05);
        }

        .file-upload-area input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .file-icon {
            font-size: 32px;
        }

        .file-text {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .file-info {
            color: var(--text-dim);
            font-size: 12px;
            margin-top: 10px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-action {
            flex: 1;
            padding: 16px 30px;
            border-radius: 12px;
            border: none;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            color: #000;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 255, 255, 0.5);
        }

        .btn-secondary {
            background: var(--metal-dark);
            color: var(--text-primary);
            border: 2px solid var(--metal-light);
        }

        .btn-secondary:hover {
            background: var(--metal-light);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--accent-green), var(--accent-cyan));
            color: #000;
        }

        .btn-success:hover {
            box-shadow: 0 15px 40px rgba(0, 255, 136, 0.5);
        }

        /* Quiz Styles */
        .quiz-container {
            margin-top: 30px;
        }

        .quiz-info {
            background: rgba(153, 69, 255, 0.1);
            border: 1px solid var(--accent-purple);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            text-align: center;
        }

        .quiz-info-text {
            color: var(--text-secondary);
            font-size: 16px;
        }

        .quiz-info-text strong {
            color: var(--accent-purple);
        }

        .question-card {
            background: linear-gradient(135deg, rgba(153, 69, 255, 0.1), rgba(0, 168, 255, 0.05));
            border: 2px solid rgba(153, 69, 255, 0.3);
            border-radius: 16px;
            padding: 30px;
            margin: 25px 0;
            transition: all 0.3s ease;
        }

        .question-card:hover {
            border-color: var(--accent-purple);
            box-shadow: 0 10px 30px rgba(153, 69, 255, 0.2);
        }

        .question-card.answered {
            border-color: var(--accent-green);
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2);
        }

        .question-card.unanswered {
            border-color: var(--accent-red);
            box-shadow: 0 10px 30px rgba(255, 51, 51, 0.2);
        }

        .question-number {
            display: inline-block;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            color: #fff;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .question-text {
            color: var(--text-primary);
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        /* Custom Checkbox/Radio */
        .answer-option {
            display: flex;
            align-items: center;
            gap: 15px;
            background: var(--carbon-medium);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 18px 22px;
            margin: 12px 0;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .answer-option:hover {
            border-color: var(--accent-cyan);
            background: rgba(0, 255, 255, 0.05);
            transform: translateX(5px);
        }

        .answer-option.selected {
            border-color: var(--accent-green);
            background: rgba(0, 255, 136, 0.1);
            box-shadow: 0 5px 20px rgba(0, 255, 136, 0.3);
        }

        .answer-option input[type="radio"],
        .answer-option input[type="checkbox"] {
            display: none;
        }

        .custom-checkbox {
            width: 28px;
            height: 28px;
            border: 2px solid var(--accent-cyan);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .answer-option.selected .custom-checkbox {
            background: linear-gradient(135deg, var(--accent-green), var(--accent-cyan));
            border-color: var(--accent-green);
        }

        .custom-checkbox::after {
            content: '✓';
            color: #000;
            font-weight: 900;
            font-size: 14px;
            opacity: 0;
            transform: scale(0);
            transition: all 0.2s ease;
        }

        .answer-option.selected .custom-checkbox::after {
            opacity: 1;
            transform: scale(1);
        }

        .answer-text {
            color: var(--text-secondary);
            font-size: 15px;
            transition: color 0.3s ease;
        }

        .answer-option:hover .answer-text,
        .answer-option.selected .answer-text {
            color: var(--text-primary);
        }

        /* Result Container */
        .result-container {
            text-align: center;
            padding: 50px 30px;
        }

        .result-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .result-title {
            font-size: 32px;
            font-weight: 900;
            margin-bottom: 20px;
        }

        .score-display {
            font-size: 72px;
            font-weight: 900;
            margin: 30px 0;
        }

        .score-text {
            font-size: 24px;
            color: var(--text-secondary);
            margin-bottom: 20px;
        }

        .result-details {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin: 30px 0;
        }

        .result-item {
            text-align: center;
        }

        .result-item-value {
            font-size: 36px;
            font-weight: 700;
        }

        .result-item-label {
            color: var(--text-secondary);
            font-size: 14px;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .correct-value {
            color: var(--accent-green);
        }

        .wrong-value {
            color: var(--accent-red);
        }

        .result-message {
            color: var(--text-secondary);
            font-size: 18px;
            margin: 20px 0;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
        }

        /* Success Animation */
        .success-container {
            text-align: center;
            padding: 50px;
        }

        .success-icon {
            font-size: 100px;
            margin-bottom: 30px;
            animation: bounce 0.5s ease;
        }

        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        .success-title {
            color: var(--accent-green);
            font-size: 32px;
            font-weight: 900;
            margin-bottom: 15px;
        }

        .success-message {
            color: var(--text-secondary);
            font-size: 18px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .front-section {
                margin-top: 200px;
            }

            .modal-box {
                padding: 25px;
                margin: 10px;
            }

            .modal-title {
                font-size: 20px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .result-details {
                flex-direction: column;
                gap: 20px;
            }

            .score-display {
                font-size: 48px;
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
                <li><a href="events.php" class="nav-link active">📅 Événements</a></li>
                <li><a href="../admin/dashboard.php" class="nav-link">📊 Dashboard</a></li>
                <li><a href="../admin/manage-events.php" class="nav-link">⚙️ Administration</a></li>
            </ul>
        </nav>
    </header>

    <!-- Search Bar -->
    <div class="search-container">
        <div class="search-bar">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="searchInput" placeholder="Rechercher un événement...">
        </div>
    </div>

    <section class="admin-events-section front-section">
        <div class="section-header">
            <h2 class="section-title">Nos Événements</h2>
            <p class="section-subtitle">Découvrez nos événements et participez aux quiz interactifs</p>
        </div>
        
        <div class="events-grid" id="eventsGrid">
            <p style="text-align: center; color: var(--text-secondary);">Chargement des événements...</p>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal-overlay" id="eventModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Titre</h3>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
            <div id="modalBody"></div>
        </div>
    </div>

    <script src="../../assets/js/validation.js"></script>
    <script>
        const API_URL = '../../controllers/EvenementController.php';
        const PARTICIPATION_URL = '../../controllers/ParticipationController.php';
        let currentEvent = null;
        let allEvents = [];

        // Charger les événements
        document.addEventListener('DOMContentLoaded', loadEvents);

        function loadEvents() {
            fetch(`${API_URL}?action=getAll`)
                .then(response => response.json())
                .then(data => {
                    allEvents = data;
                    displayEvents(data);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('eventsGrid').innerHTML = 
                        '<p style="color: var(--accent-red); text-align: center; grid-column: 1/-1;">Erreur lors du chargement des événements</p>';
                });
        }

        function displayEvents(events) {
            const grid = document.getElementById('eventsGrid');
            
            if (!events || events.length === 0) {
                grid.innerHTML = '<p style="text-align: center; color: var(--text-secondary); grid-column: 1/-1;">Aucun événement disponible</p>';
                return;
            }
            
            grid.innerHTML = events.map((event, index) => `
                <div class="event-card" style="animation-delay: ${index * 0.1}s">
                    <div class="card-image">
                        <img src="${getImageUrl(event.image_url)}" 
                             alt="${escapeHtml(event.titre)}"
                             onerror="this.src='https://via.placeholder.com/600x400/1a1a1a/00ffff?text=Event'">
                        <span class="card-badge ${event.type === 'quiz' ? 'badge-quiz' : 'badge-normal'}">
                            ${event.type === 'quiz' ? '🎯 Quiz' : '📅 Événement'}
                        </span>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">${escapeHtml(event.titre)}</h3>
                        <p class="card-description">${escapeHtml(event.description).substring(0, 100)}...</p>
                        <div class="card-date">
                            📅 ${formatDate(event.date_debut)} - ${formatDate(event.date_fin)}
                        </div>
                        <button class="card-cta" onclick="showEventDetails(${event.id})">
                            ${event.type === 'quiz' ? '🎯 Passer le Quiz' : '✍️ Participer'}
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Recherche
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const search = e.target.value.toLowerCase();
            const filtered = allEvents.filter(event => 
                event.titre.toLowerCase().includes(search) || 
                event.description.toLowerCase().includes(search)
            );
            displayEvents(filtered);
        });

        // Afficher les détails
        function showEventDetails(id) {
            fetch(`${API_URL}?action=getOne&id=${id}`)
                .then(response => response.json())
                .then(event => {
                    if (!event || event.error) {
                        alert('Événement introuvable');
                        return;
                    }
                    
                    currentEvent = event;
                    document.getElementById('modalTitle').textContent = event.titre;
                    
                    if (event.type === 'quiz') {
                        showQuizView(event);
                    } else {
                        showNormalEventView(event);
                    }
                    
                    openModal();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du chargement');
                });
        }

        // Vue événement normal
        function showNormalEventView(event) {
            document.getElementById('modalBody').innerHTML = `
                <img src="${getImageUrl(event.image_url)}" 
                     alt="${escapeHtml(event.titre)}" 
                     class="event-image-large"
                     onerror="this.src='https://via.placeholder.com/800x400/1a1a1a/00ffff?text=Event'">
                
                <div class="detail-item">
                    <div class="detail-label">📝 Description</div>
                    <div class="detail-value">${escapeHtml(event.description)}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">📅 Date de début</div>
                    <div class="detail-value">${formatDateLong(event.date_debut)}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">📅 Date de fin</div>
                    <div class="detail-value">${formatDateLong(event.date_fin)}</div>
                </div>
                
                <div class="action-buttons">
                    <button class="btn-action btn-secondary" onclick="closeModal()">Fermer</button>
                    <button class="btn-action btn-success" onclick="showParticipationForm()">✍️ Participer</button>
                </div>
            `;
        }

        // Vue Quiz
        function showQuizView(event) {
            let questionsHTML = '';
            
            if (event.questions && event.questions.length > 0) {
                questionsHTML = `
                    <div class="quiz-container">
                        <div class="quiz-info">
                            <p class="quiz-info-text">
                                🎯 Ce quiz contient <strong>${event.questions.length} question(s)</strong>. 
                                Remplissez vos informations et répondez à toutes les questions.
                            </p>
                        </div>
                        
                        <div class="participation-form">
                            <h4 class="form-title">👤 Vos informations</h4>
                            
                            <div class="form-field">
                                <label>Nom *</label>
                                <input type="text" id="quizNom" placeholder="Entrez votre nom">
                            </div>
                            
                            <div class="form-field">
                                <label>Prénom *</label>
                                <input type="text" id="quizPrenom" placeholder="Entrez votre prénom">
                            </div>
                            
                            <div class="form-field">
                                <label>Email *</label>
                                <input type="email" id="quizEmail" placeholder="Entrez votre email">
                            </div>
                        </div>
                        
                        ${event.questions.map((q, qIndex) => `
                            <div class="question-card" data-question-id="${q.id}" id="question-${q.id}">
                                <span class="question-number">Question ${qIndex + 1}/${event.questions.length}</span>
                                <div class="question-text">${escapeHtml(q.texte_question)}</div>
                                <div class="answers-container">
                                    ${q.reponses.map((r) => `
                                        <label class="answer-option" onclick="selectAnswer(this, ${q.id}, ${r.id})">
                                            <input type="radio" name="question_${q.id}" value="${r.id}">
                                            <span class="custom-checkbox"></span>
                                            <span class="answer-text">${escapeHtml(r.texte_reponse)}</span>
                                        </label>
                                    `).join('')}
                                </div>
                            </div>
                        `).join('')}
                        
                        <div class="action-buttons">
                            <button class="btn-action btn-secondary" onclick="closeModal()">Annuler</button>
                            <button class="btn-action btn-primary" onclick="submitQuiz()">📤 Soumettre le Quiz</button>
                        </div>
                    </div>
                `;
            } else {
                questionsHTML = `
                    <div style="text-align: center; padding: 50px; color: var(--text-secondary);">
                        <p style="font-size: 48px; margin-bottom: 20px;">📭</p>
                        <p>Aucune question disponible pour ce quiz</p>
                    </div>
                `;
            }
            
            document.getElementById('modalBody').innerHTML = `
                <img src="${getImageUrl(event.image_url)}" 
                     alt="${escapeHtml(event.titre)}" 
                     class="event-image-large"
                     onerror="this.src='https://via.placeholder.com/800x400/1a1a1a/9945ff?text=Quiz'">
                
                <div class="detail-item">
                    <div class="detail-label">📝 Description</div>
                    <div class="detail-value">${escapeHtml(event.description)}</div>
                </div>
                
                ${questionsHTML}
            `;
        }

        // Sélectionner une réponse
        function selectAnswer(element, questionId, reponseId) {
            const questionCard = document.getElementById(`question-${questionId}`);
            
            // Désélectionner toutes les réponses de cette question
            questionCard.querySelectorAll('.answer-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Sélectionner la réponse cliquée
            element.classList.add('selected');
            element.querySelector('input').checked = true;
            
            // Marquer la question comme répondue
            questionCard.classList.remove('unanswered');
            questionCard.classList.add('answered');
        }

        // Soumettre le quiz
        function submitQuiz() {
            // Validation avec JavaScript
            if (!validateQuizForm()) {
                return;
            }
            
            const nom = document.getElementById('quizNom').value.trim();
            const prenom = document.getElementById('quizPrenom').value.trim();
            const email = document.getElementById('quizEmail').value.trim();
            
            // Collecter les réponses
            const reponses = {};
            const questionCards = document.querySelectorAll('.question-card');
            
            questionCards.forEach(card => {
                const questionId = card.dataset.questionId;
                const selected = card.querySelector('input[type="radio"]:checked');
                
                if (selected) {
                    reponses[questionId] = parseInt(selected.value);
                }
            });
            
            const data = {
                nom: nom,
                prenom: prenom,
                email: email,
                evenement_id: currentEvent.id,
                reponses: reponses
            };
            
            // Désactiver le bouton pendant l'envoi
            const submitBtn = document.querySelector('.btn-primary');
            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Envoi en cours...';
            
            fetch(`${PARTICIPATION_URL}?action=soumettreQuiz`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showQuizResult(result);
                } else {
                    alert('Erreur: ' + result.message);
                    submitBtn.disabled = false;
                    submitBtn.textContent = '📤 Soumettre le Quiz';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la soumission');
                submitBtn.disabled = false;
                submitBtn.textContent = '📤 Soumettre le Quiz';
            });
        }

        // Afficher le résultat du quiz
        function showQuizResult(result) {
            const percentage = result.pourcentage;
            const correct = result.score;
            const wrong = result.total - result.score;
            
            let scoreColor, resultIcon, resultMessage;
            
            if (percentage >= 80) {
                scoreColor = 'var(--accent-green)';
                resultIcon = '🏆';
                resultMessage = 'Excellent travail ! Vous maîtrisez parfaitement ce sujet !';
            } else if (percentage >= 60) {
                scoreColor = 'var(--accent-cyan)';
                resultIcon = '👍';
                resultMessage = 'Bon travail ! Vous avez une bonne compréhension du sujet.';
            } else if (percentage >= 40) {
                scoreColor = 'var(--accent-orange)';
                resultIcon = '💪';
                resultMessage = 'Pas mal ! Continuez à apprendre et vous vous améliorerez.';
            } else {
                scoreColor = 'var(--accent-red)';
                resultIcon = '📚';
                resultMessage = 'Continuez à étudier ! La pratique mène à la perfection.';
            }
            
            document.getElementById('modalBody').innerHTML = `
                <div class="result-container">
                    <div class="result-icon">${resultIcon}</div>
                    <h2 class="result-title" style="color: ${scoreColor}">Quiz terminé !</h2>
                    
                    <div class="score-display" style="color: ${scoreColor}">
                        ${correct}/${result.total}
                    </div>
                    
                    <div class="score-text">
                        Vous avez obtenu <strong style="color: ${scoreColor}">${percentage}%</strong>
                    </div>
                    
                    <div class="result-details">
                        <div class="result-item">
                            <div class="result-item-value correct-value">✓ ${correct}</div>
                            <div class="result-item-label">Réponses correctes</div>
                        </div>
                        <div class="result-item">
                            <div class="result-item-value wrong-value">✗ ${wrong}</div>
                            <div class="result-item-label">Réponses incorrectes</div>
                        </div>
                    </div>
                    
                    <div class="result-message">
                        ${resultMessage}
                    </div>
                    
                    <div class="action-buttons" style="justify-content: center;">
                        <button class="btn-action btn-primary" onclick="closeModal()">Fermer</button>
                    </div>
                </div>
            `;
        }

        // Formulaire de participation
        function showParticipationForm() {
            document.getElementById('modalBody').innerHTML = `
                <div class="participation-form">
                    <h3 class="form-title">✍️ Formulaire de participation</h3>
                    
                    <div class="form-field">
                        <label>Nom *</label>
                        <input type="text" id="partNom" placeholder="Entrez votre nom">
                    </div>
                    
                    <div class="form-field">
                        <label>Prénom *</label>
                        <input type="text" id="partPrenom" placeholder="Entrez votre prénom">
                    </div>
                    
                    <div class="form-field">
                        <label>Email *</label>
                        <input type="email" id="partEmail" placeholder="Entrez votre email">
                    </div>
                    
                    <div class="form-field">
                        <label>Commentaire *</label>
                        <textarea id="partCommentaire" rows="5" placeholder="Pourquoi souhaitez-vous participer à cet événement ?"></textarea>
                    </div>
                    
                    <div class="form-field">
                        <label>Fichier (optionnel)</label>
                        <div class="file-upload-wrapper">
                            <div class="file-upload-area" id="fileUploadArea">
                                <input type="file" id="partFichier" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip">
                                <span class="file-icon">📎</span>
                                <span class="file-text" id="fileText">Cliquez pour choisir un fichier</span>
                            </div>
                            <p class="file-info">Formats acceptés: PDF, DOC, JPG, PNG, ZIP (max 5MB)</p>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button class="btn-action btn-secondary" onclick="showEventDetails(${currentEvent.id})">Retour</button>
                        <button class="btn-action btn-success" onclick="submitParticipation()">📤 Envoyer</button>
                    </div>
                </div>
            `;
            
            // Listener pour l'upload
            document.getElementById('partFichier').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const fileText = document.getElementById('fileText');
                const uploadArea = document.getElementById('fileUploadArea');
                
                if (file) {
                    fileText.textContent = file.name;
                    uploadArea.classList.add('has-file');
                } else {
                    fileText.textContent = 'Cliquez pour choisir un fichier';
                    uploadArea.classList.remove('has-file');
                }
            });
        }

        // Soumettre la participation
        function submitParticipation() {
            // Validation avec JavaScript
            if (!validateParticipationForm()) {
                return;
            }
            
            const nom = document.getElementById('partNom').value.trim();
            const prenom = document.getElementById('partPrenom').value.trim();
            const email = document.getElementById('partEmail').value.trim();
            const commentaire = document.getElementById('partCommentaire').value.trim();
            
            const formData = new FormData();
            formData.append('nom', nom);
            formData.append('prenom', prenom);
            formData.append('email', email);
            formData.append('commentaire', commentaire);
            formData.append('evenement_id', currentEvent.id);
            
            const fichier = document.getElementById('partFichier').files[0];
            if (fichier) {
                const fileValidation = validateFile(fichier);
                if (!fileValidation.valid) {
                    alert(fileValidation.message);
                    return;
                }
                formData.append('fichier', fichier);
            }
            
            // Désactiver le bouton
            const submitBtn = document.querySelector('.btn-success');
            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Envoi en cours...';
            
            fetch(`${PARTICIPATION_URL}?action=soumettreParticipation`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showSuccessMessage();
                } else {
                    let errorMsg = result.message || 'Une erreur est survenue';
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).join('\n');
                    }
                    alert(errorMsg);
                    submitBtn.disabled = false;
                    submitBtn.textContent = '📤 Envoyer';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'envoi');
                submitBtn.disabled = false;
                submitBtn.textContent = '📤 Envoyer';
            });
        }

        // Message de succès
        function showSuccessMessage() {
            document.getElementById('modalBody').innerHTML = `
                <div class="success-container">
                    <div class="success-icon">✅</div>
                    <h2 class="success-title">Participation enregistrée !</h2>
                    <p class="success-message">
                        Votre participation a été enregistrée avec succès.<br>
                        Vous recevrez une confirmation par email.
                    </p>
                    <div class="action-buttons" style="justify-content: center; margin-top: 30px;">
                        <button class="btn-action btn-primary" onclick="closeModal()">Fermer</button>
                    </div>
                </div>
            `;
        }

        // Fonctions utilitaires
        function openModal() {
            document.getElementById('eventModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('eventModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            currentEvent = null;
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('fr-FR', { 
                day: '2-digit', 
                month: 'short', 
                year: 'numeric'
            });
        }

        function formatDateLong(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('fr-FR', { 
                weekday: 'long',
                day: 'numeric', 
                month: 'long', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // دالة للحصول على مسار الصورة الصحيح
        function getImageUrl(url) {
            if (!url) {
                return 'https://via.placeholder.com/600x400/1a1a1a/00ffff?text=Event';
            }
            // إذا كان المسار يبدأ بـ uploads/ فأضف المسار النسبي
            if (url.startsWith('uploads/')) {
                return '../../' + url;
            }
            // إذا كان URL كامل أو placeholder
            return url;
        }

        // Fermer avec Échap
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });

        // Fermer en cliquant hors du modal
        document.getElementById('eventModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>
