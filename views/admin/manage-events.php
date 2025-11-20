<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back Office - Gestion des événements</title>
    <link rel="stylesheet" href="../../assets/css/templatemo-prism-flux.css">
    <style>
        /* Styles spécifiques pour les formulaires dynamiques */
        .question-block {
            background: var(--carbon-dark);
            border: 1px solid rgba(0, 255, 136, 0.2);
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
        }
        
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .question-title {
            color: var(--accent-cyan);
            font-size: 16px;
            font-weight: 700;
        }
        
        .reponse-block {
            background: var(--carbon-medium);
            border-left: 3px solid var(--accent-cyan);
            padding: 10px 15px;
            margin: 8px 0;
            border-radius: 4px;
        }
        
        .radio-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 5px;
        }
        
        .radio-group input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .radio-group label {
            cursor: pointer;
            color: var(--text-secondary);
        }
        
        .questions-container {
            margin-top: 20px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        
        .hidden {
            display: none;
        }
        
        .error-message {
            color: var(--accent-red);
            font-size: 12px;
            margin-top: 5px;
        }
        
        .success-message {
            color: var(--accent-green);
            background: rgba(0, 255, 136, 0.1);
            border: 1px solid var(--accent-green);
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
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
                <li><a href="manage-events.php" class="nav-link active">Gestion des événements</a></li>
            </ul>
        </nav>
    </header>

    <section class="admin-events-section">
        <div class="section-header">
            <h2 class="section-title">Gérer les événements</h2>
            <p class="section-subtitle">Ajoutez, modifiez ou supprimez vos événements</p>
        </div>
        
        <div class="admin-actions">
            <button class="card-cta btn-add-event" onclick="showAddEventModal()">+ Ajouter un événement</button>
        </div>
        
        <div id="success-container"></div>
        <div class="events-grid" id="eventsGrid"></div>
    </section>

    <!-- Modal Ajouter/Modifier un événement -->
    <div id="eventModal" class="event-modal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Ajouter un événement</h3>
                <button class="modal-close" onclick="closeEventModal()">✕</button>
            </div>
            
            <form id="eventForm" class="event-form">
                <input type="hidden" id="eventId" value="">
                
                <div class="form-group">
                    <label for="eventType">Type d'événement *</label>
                    <select id="eventType" required>
                        <option value="">-- Sélectionner un type --</option>
                        <option value="normal">Événement Normal</option>
                        <option value="quiz">Quiz</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="eventTitre">Titre *</label>
                    <input type="text" id="eventTitre" placeholder="Titre de l'événement" required maxlength="150">
                    <small style="color: var(--text-dim);">0/150</small>
                </div>
                
                <div class="form-group">
                    <label for="eventDescription">Description *</label>
                    <textarea id="eventDescription" placeholder="Description détaillée" rows="4" required maxlength="500"></textarea>
                    <small style="color: var(--text-dim);">0/500</small>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="eventDateDebut">Date début *</label>
                        <input type="datetime-local" id="eventDateDebut" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="eventDateFin">Date fin *</label>
                        <input type="datetime-local" id="eventDateFin" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="eventImage">URL de l'image</label>
                    <input type="url" id="eventImage" placeholder="https://example.com/image.jpg">
                </div>
                
                <div class="form-group preview-group">
                    <label>Aperçu de l'image:</label>
                    <img id="imagePreview" src="https://via.placeholder.com/600x400?text=No+Image" alt="Aperçu" class="image-preview">
                </div>
                
                <!-- Section Questions (visible seulement pour les quiz) -->
                <div id="quizSection" class="hidden">
                    <div class="form-group">
                        <label for="nombreQuestions">Nombre de questions *</label>
                        <input type="number" id="nombreQuestions" min="1" max="10" placeholder="Ex: 3">
                    </div>
                    
                    <button type="button" id="genererQuestions" class="card-cta" style="margin: 15px 0;">
                        Générer les champs de questions
                    </button>
                    
                    <div id="questionsContainer" class="questions-container"></div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeEventModal()">Annuler</button>
                    <button type="submit" class="btn-submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Overlay backdrop -->
    <div id="modalBackdrop" class="modal-backdrop" style="display:none;" onclick="closeEventModal()"></div>

    <script src="../../assets/js/validation.js"></script>
    <script>
        const API_URL = '../../controllers/EvenementController.php';
        let currentEditId = null;

        // Charger les événements au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadEvenements();
            setupEventListeners();
        });

        // Configuration des écouteurs d'événements
        function setupEventListeners() {
            // Type d'événement
            document.getElementById('eventType').addEventListener('change', function() {
                const quizSection = document.getElementById('quizSection');
                quizSection.classList.toggle('hidden', this.value !== 'quiz');
                
                if(this.value !== 'quiz') {
                    document.getElementById('questionsContainer').innerHTML = '';
                    document.getElementById('nombreQuestions').value = '';
                }
            });
            
            // Générer les questions
            document.getElementById('genererQuestions').addEventListener('click', genererChampQuestions);
            
            // Prévisualisation image
            document.getElementById('eventImage').addEventListener('input', function() {
                updateImagePreview(this.value);
            });
            
            // Soumettre le formulaire
            document.getElementById('eventForm').addEventListener('submit', handleFormSubmit);
            
            // Compteurs de caractères
            document.getElementById('eventTitre').addEventListener('input', function() {
                this.nextElementSibling.textContent = `${this.value.length}/150`;
            });
            
            document.getElementById('eventDescription').addEventListener('input', function() {
                this.nextElementSibling.textContent = `${this.value.length}/500`;
            });
        }

        // Charger tous les événements
        function loadEvenements() {
            fetch(`${API_URL}?action=getAll`)
                .then(response => response.json())
                .then(data => {
                    displayEvenements(data);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showMessage('Erreur lors du chargement des événements', 'error');
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
                const typeLabel = event.type === 'quiz' ? 'QUIZ' : 'NORMAL';
                const typeColor = event.type === 'quiz' ? 'var(--accent-purple)' : 'var(--accent-cyan)';
                
                const card = document.createElement('div');
                card.className = 'event-card';
                card.innerHTML = `
                    <div class="event-image">
                        <img src="${escapeHtml(event.image_url || 'https://via.placeholder.com/600x400?text=No+Image')}" 
                             alt="${escapeHtml(event.titre)}" 
                             class="event-img" 
                             onerror="this.src='https://via.placeholder.com/600x400?text=No+Image'" />
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h3 class="event-title">${escapeHtml(event.titre)}</h3>
                        <span style="background: ${typeColor}; color: #000; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 700;">${typeLabel}</span>
                    </div>
                    <p class="event-date">📅 ${formatDate(event.date_debut)} - ${formatDate(event.date_fin)}</p>
                    <p class="event-description">${escapeHtml(event.description)}</p>
                    ${event.type === 'quiz' ? `<p style="color: var(--accent-cyan); font-size: 12px; margin-top: 8px;">📝 ${event.nombre_questions} question(s)</p>` : ''}
                    <div class="admin-controls">
                        <button onclick="editEvenement(${event.id})" class="card-cta btn-modifier">Modifier</button>
                        <button onclick="deleteEvenement(${event.id})" class="card-cta btn-delete" 
                                style="background: linear-gradient(135deg,#ff4d4d,#ff1a1a); color: #fff;">Supprimer</button>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        // Ouvrir le modal d'ajout
        function showAddEventModal() {
            currentEditId = null;
            document.getElementById('modalTitle').textContent = 'Ajouter un événement';
            document.getElementById('eventForm').reset();
            document.getElementById('eventId').value = '';
            document.getElementById('questionsContainer').innerHTML = '';
            document.getElementById('quizSection').classList.add('hidden');
            updateImagePreview('');
            showModal();
        }

        // Éditer un événement
        function editEvenement(id) {
            currentEditId = id;
            document.getElementById('modalTitle').textContent = 'Modifier l\'événement';
            
            fetch(`${API_URL}?action=getOne&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if(!data) {
                        showMessage('Événement introuvable', 'error');
                        return;
                    }
                    
                    document.getElementById('eventId').value = data.id;
                    document.getElementById('eventType').value = data.type;
                    document.getElementById('eventTitre').value = data.titre;
                    document.getElementById('eventDescription').value = data.description;
                    document.getElementById('eventDateDebut').value = data.date_debut.replace(' ', 'T');
                    document.getElementById('eventDateFin').value = data.date_fin.replace(' ', 'T');
                    document.getElementById('eventImage').value = data.image_url || '';
                    updateImagePreview(data.image_url);
                    
                    // Si c'est un quiz
                    if(data.type === 'quiz') {
                        document.getElementById('quizSection').classList.remove('hidden');
                        document.getElementById('nombreQuestions').value = data.nombre_questions;
                        genererChampQuestions(data.questions);
                    }
                    
                    showModal();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showMessage('Erreur lors du chargement de l\'événement', 'error');
                });
        }

        // Supprimer un événement
        function deleteEvenement(id) {
            if(!confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
                return;
            }
            
            fetch(`${API_URL}?action=delete&id=${id}`, { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        showMessage('Événement supprimé avec succès', 'success');
                        loadEvenements();
                    } else {
                        showMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showMessage('Erreur lors de la suppression', 'error');
                });
        }

        // Générer les champs de questions
        function genererChampQuestions(existingQuestions = null) {
            const nombre = parseInt(document.getElementById('nombreQuestions').value);
            
            if(!nombre || nombre < 1 || nombre > 10) {
                alert('Veuillez entrer un nombre de questions valide (1-10)');
                return;
            }
            
            const container = document.getElementById('questionsContainer');
            container.innerHTML = '';
            
            for(let i = 0; i < nombre; i++) {
                const questionData = existingQuestions ? existingQuestions[i] : null;
                const questionBlock = createQuestionBlock(i + 1, questionData);
                container.appendChild(questionBlock);
            }
        }

        // Créer un bloc question
        function createQuestionBlock(index, data = null) {
            const block = document.createElement('div');
            block.className = 'question-block';
            block.innerHTML = `
                <div class="question-header">
                    <h4 class="question-title">Question ${index}</h4>
                </div>
                
                <div class="form-group">
                    <label>Texte de la question *</label>
                    <textarea class="question-texte" placeholder="Entrez votre question" rows="2" required>${data?.texte_question || ''}</textarea>
                </div>
                
                <div class="form-group">
                    <label>Nombre de réponses *</label>
                    <input type="number" class="nombre-reponses" min="2" max="6" value="${data?.reponses?.length || 4}" required>
                    <button type="button" class="card-cta" style="margin-top: 10px;" onclick="genererReponses(this, ${index})">
                        Générer les champs de réponses
                    </button>
                </div>
                
                <div class="reponses-container"></div>
            `;
            
            // Générer automatiquement les réponses si c'est une édition
            if(data?.reponses) {
                setTimeout(() => {
                    const btn = block.querySelector('.card-cta');
                    genererReponses(btn, index, data.reponses);
                }, 100);
            }
            
            return block;
        }

        // Générer les champs de réponses
        function genererReponses(button, questionIndex, existingReponses = null) {
            const block = button.closest('.question-block');
            const nombre = parseInt(block.querySelector('.nombre-reponses').value);
            
            if(!nombre || nombre < 2 || nombre > 6) {
                alert('Veuillez entrer un nombre de réponses valide (2-6)');
                return;
            }
            
            const container = block.querySelector('.reponses-container');
            container.innerHTML = '';
            
            const groupName = `reponse_correcte_${questionIndex}`;
            
            for(let i = 0; i < nombre; i++) {
                const reponseData = existingReponses ? existingReponses[i] : null;
                const isCorrect = reponseData ? reponseData.est_correcte : (i === 0);
                
                const reponseBlock = document.createElement('div');
                reponseBlock.className = 'reponse-block';
                reponseBlock.innerHTML = `
                    <input type="text" class="reponse-texte" placeholder="Réponse ${i + 1}" required value="${reponseData?.texte_reponse || ''}">
                    <div class="radio-group">
                        <input type="radio" name="${groupName}" value="${i}" id="${groupName}_${i}" ${isCorrect ? 'checked' : ''}>
                        <label for="${groupName}_${i}">Réponse correcte</label>
                    </div>
                `;
                container.appendChild(reponseBlock);
            }
        }

        // Soumettre le formulaire
        function handleFormSubmit(e) {
            e.preventDefault();
            
            const type = document.getElementById('eventType').value;
            const data = {
                type: type,
                titre: document.getElementById('eventTitre').value.trim(),
                description: document.getElementById('eventDescription').value.trim(),
                date_debut: document.getElementById('eventDateDebut').value,
                date_fin: document.getElementById('eventDateFin').value,
                image_url: document.getElementById('eventImage').value.trim(),
                nombre_questions: 0,
                questions: []
            };
            
            // Validation des dates
            if(new Date(data.date_fin) <= new Date(data.date_debut)) {
                alert('La date de fin doit être postérieure à la date de début');
                return;
            }
            
            // Si c'est un quiz, récupérer les questions
            if(type === 'quiz') {
                const questionBlocks = document.querySelectorAll('.question-block');
                data.nombre_questions = questionBlocks.length;
                
                questionBlocks.forEach((block, index) => {
                    const texteQuestion = block.querySelector('.question-texte').value.trim();
                    const reponseBlocks = block.querySelectorAll('.reponse-block');
                    
                    if(!texteQuestion) {
                        alert(`Veuillez remplir la question ${index + 1}`);
                        throw new Error('Validation failed');
                    }
                    
                    const reponses = [];
                    let reponseCorrecte = null;
                    
                    reponseBlocks.forEach((repBlock, repIndex) => {
                        const texteReponse = repBlock.querySelector('.reponse-texte').value.trim();
                        const radio = repBlock.querySelector('input[type="radio"]');
                        
                        if(!texteReponse) {
                            alert(`Veuillez remplir toutes les réponses pour la question ${index + 1}`);
                            throw new Error('Validation failed');
                        }
                        
                        reponses.push({ texte: texteReponse });
                        
                        if(radio.checked) {
                            reponseCorrecte = repIndex;
                        }
                    });
                    
                    if(reponseCorrecte === null) {
                        alert(`Veuillez sélectionner la réponse correcte pour la question ${index + 1}`);
                        throw new Error('Validation failed');
                    }
                    
                    data.questions.push({
                        texte: texteQuestion,
                        reponses: reponses,
                        reponse_correcte: reponseCorrecte
                    });
                });
            }
            
            // Envoyer les données
            const action = currentEditId ? 'update' : 'create';
            const url = currentEditId ? `${API_URL}?action=update&id=${currentEditId}` : `${API_URL}?action=create`;
            
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    showMessage(result.message, 'success');
                    closeEventModal();
                    loadEvenements();
                } else {
                    showMessage(result.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showMessage('Erreur lors de l\'enregistrement', 'error');
            });
        }

        // Fonctions utilitaires
        function showModal() {
            document.getElementById('eventModal').style.display = 'block';
            document.getElementById('modalBackdrop').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeEventModal() {
            document.getElementById('eventModal').style.display = 'none';
            document.getElementById('modalBackdrop').style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('eventForm').reset();
            document.getElementById('questionsContainer').innerHTML = '';
        }

        function updateImagePreview(url) {
            const preview = document.getElementById('imagePreview');
            if(!url) {
                preview.src = 'https://via.placeholder.com/600x400?text=No+Image';
                return;
            }
            
            const img = new Image();
            img.onload = () => { preview.src = url; };
            img.onerror = () => { preview.src = 'https://via.placeholder.com/600x400?text=No+Image'; };
            img.src = url;
        }

        function showMessage(message, type) {
            const container = document.getElementById('success-container');
            const className = type === 'success' ? 'success-message' : 'error-message';
            container.innerHTML = `<div class="${className}" style="padding: 15px; border-radius: 6px; margin: 20px 0;">${message}</div>`;
            
            setTimeout(() => {
                container.innerHTML = '';
            }, 5000);
        }

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

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Fermer le modal avec Échap
        document.addEventListener('keydown', (e) => {
            if(e.key === 'Escape') {
                closeEventModal();
            }
        });
    </script>
</body>
</html>