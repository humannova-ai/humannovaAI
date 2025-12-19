<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prism Flux | Éditer l'Article #<?= $article['id'] ?></title>
    
    <!-- Google Fonts pour Orbitron et Rajdhani -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lien vers le style Prism Flux -->
    <link rel="stylesheet" href="./templatemo-prism-flux.css">
    
    <!-- Style spécifique pour l'édition d'article -->
    <style>
        /* ============================================
           ÉDITION D'ARTICLE - Style Prism Flux
           ============================================ */
        
        /* Reset et structure */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Orbitron', 'Rajdhani', sans-serif;
            background: #0a0a0a;
            color: #ffffff;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Arrière-plan carbon fiber */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                repeating-linear-gradient(0deg,
                    transparent,
                    transparent 2px,
                    rgba(255, 255, 255, 0.03) 2px,
                    rgba(255, 255, 255, 0.03) 4px),
                repeating-linear-gradient(90deg,
                    transparent,
                    transparent 2px,
                    rgba(255, 255, 255, 0.03) 2px,
                    rgba(255, 255, 255, 0.03) 4px),
                linear-gradient(135deg,
                    #0a0a0a 0%,
                    #121212 25%,
                    #1a1a1a 50%,
                    #121212 75%,
                    #0a0a0a 100%);
            z-index: -2;
        }
        
        /* Grille animée */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(255, 51, 51, 0.05) 2px, transparent 2px),
                linear-gradient(90deg, rgba(255, 51, 51, 0.05) 2px, transparent 2px);
            background-size: 150px 150px;
            animation: gridMove 20s linear infinite;
            z-index: -1;
            opacity: 0.5;
        }
        
        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(150px, 150px); }
        }
        
        /* Container principal */
        .prism-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 120px 30px 50px;
            position: relative;
            z-index: 1;
        }
        
        /* Header de page */
        .page-header {
            margin-bottom: 40px;
            padding-bottom: 25px;
            border-bottom: 2px solid #3a3a3a;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 200px;
            height: 2px;
            background: linear-gradient(90deg, #9945ff, #00a8ff, transparent);
            animation: headerGlow 3s ease-in-out infinite;
        }
        
        @keyframes headerGlow {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }
        
        .article-id-badge {
            display: inline-block;
            background: linear-gradient(135deg, #9945ff, #00a8ff);
            color: #ffffff;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 15px;
            font-family: 'Orbitron', monospace;
            box-shadow: 0 0 15px rgba(153, 69, 255, 0.3);
        }
        
        h1 {
            font-size: 42px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #ffffff, #9945ff, #00ffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% 200%;
            animation: gradientFlow 5s ease infinite;
        }
        
        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .page-subtitle {
            font-size: 16px;
            color: #b0b0b0;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        /* Formulaire d'édition */
        .edit-form-container {
            background: linear-gradient(135deg,
                rgba(42, 42, 42, 0.3),
                rgba(26, 26, 26, 0.5));
            border: 1px solid #3a3a3a;
            border-radius: 20px;
            padding: 40px;
            margin-top: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            animation: fadeIn 0.6s ease-out;
        }
        
        .form-group {
            margin-bottom: 30px;
        }
        
        .form-label {
            display: block;
            color: #b0b0b0;
            margin-bottom: 12px;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .form-label span {
            color: #ff3333;
            margin-left: 3px;
        }
        
        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 18px 20px;
            background: #121212;
            border: 1px solid #3a3a3a;
            border-radius: 10px;
            color: #ffffff;
            font-size: 16px;
            font-family: 'Rajdhani', sans-serif;
            transition: all 0.3s ease;
        }
        
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: #9945ff;
            box-shadow: 0 0 0 2px rgba(153, 69, 255, 0.2);
            background: #1a1a1a;
        }
        
        .form-textarea {
            min-height: 200px;
            resize: vertical;
            line-height: 1.6;
        }
        
        .form-input::placeholder, .form-textarea::placeholder {
            color: #4a4a4a;
        }
        
        /* Indicateur de caractères */
        .char-counter {
            text-align: right;
            font-size: 12px;
            color: #808080;
            margin-top: 8px;
            font-family: 'Orbitron', monospace;
        }
        
        .char-counter.warning {
            color: #ffaa00;
        }
        
        .char-counter.danger {
            color: #ff3333;
        }
        
        /* Boutons d'action */
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #3a3a3a;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 16px 35px;
            border: none;
            border-radius: 30px;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-family: 'Orbitron', sans-serif;
            font-size: 14px;
        }
        
        .btn-save {
            background: linear-gradient(135deg, #9945ff, #00a8ff);
            box-shadow: 0 5px 20px rgba(153, 69, 255, 0.3);
        }
        
        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(153, 69, 255, 0.5);
        }
        
        .btn-cancel {
            background: linear-gradient(135deg, #4a4a4a, #2a2a2a);
            border: 1px solid #3a3a3a;
        }
        
        .btn-cancel:hover {
            background: linear-gradient(135deg, #3a3a3a, #1a1a1a);
            transform: translateY(-3px);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #ff3333, #cc0000);
            box-shadow: 0 5px 20px rgba(255, 51, 51, 0.3);
        }
        
        .btn-delete:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 51, 51, 0.5);
        }
        
        .btn-icon {
            font-size: 18px;
        }
        
        /* Prévisualisation en temps réel */
        .preview-section {
            margin-top: 40px;
            padding: 30px;
            background: rgba(26, 26, 26, 0.5);
            border-radius: 15px;
            border: 1px dashed #3a3a3a;
            display: none;
        }
        
        .preview-section.active {
            display: block;
            animation: slideDown 0.4s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #3a3a3a;
        }
        
        .preview-title {
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #00ffff;
        }
        
        .preview-content {
            color: #b0b0b0;
            line-height: 1.6;
        }
        
        .toggle-preview {
            background: transparent;
            border: 1px solid #00ffff;
            color: #00ffff;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 12px;
            font-family: 'Orbitron', sans-serif;
        }
        
        .toggle-preview:hover {
            background: rgba(0, 255, 255, 0.1);
        }
        
        /* Informations de l'article */
        .article-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            background: #121212;
            padding: 25px;
            border-radius: 15px;
            border: 1px solid #3a3a3a;
        }
        
        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .meta-label {
            font-size: 12px;
            color: #808080;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .meta-value {
            font-size: 16px;
            color: #ffffff;
            font-family: 'Orbitron', monospace;
            font-weight: 600;
        }
        
        /* Footer */
        .prism-footer {
            margin-top: 60px;
            padding: 30px;
            text-align: center;
            color: #808080;
            font-size: 14px;
            border-top: 1px solid #3a3a3a;
        }
        
        .prism-footer .highlight {
            color: #00ffff;
            font-weight: 700;
        }
        
        /* Animation d'entrée */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .prism-container {
                padding: 100px 15px 30px;
            }
            
            h1 {
                font-size: 32px;
            }
            
            .edit-form-container {
                padding: 25px;
            }
            
            .form-input, .form-textarea, .form-select {
                padding: 15px;
                font-size: 14px;
            }
            
            .form-actions {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
                padding: 14px;
            }
            
            .article-meta {
                grid-template-columns: 1fr;
            }
        }
        
        /* État de chargement */
        .loading {
            position: relative;
            pointer-events: none;
        }
        
        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 10, 10, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            z-index: 10;
        }
        
        .loading::before {
            content: '⚡';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            z-index: 11;
            animation: pulse 1s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body>
    <div class="prism-container">
        <!-- Header -->
        <div class="page-header">
            <div class="article-id-badge">
                ARTICLE #<?= $article['id'] ?>
            </div>
            <h1>Éditer l'Article</h1>
            <p class="page-subtitle">Modifiez les informations de votre publication</p>
        </div>
        
        <!-- Informations de l'article -->
        <div class="article-meta">
            <div class="meta-item">
                <span class="meta-label">Créé le</span>
                <span class="meta-value"><?= date('d/m/Y H:i', strtotime($article['date_creation'])) ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Dernière modification</span>
                <span class="meta-value"><?= isset($article['date_modification']) ? date('d/m/Y H:i', strtotime($article['date_modification'])) : 'Jamais' ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Statut</span>
                <span class="meta-value" style="color: #00ff88;"><?= isset($article['statut']) ? ucfirst($article['statut']) : 'Actif' ?></span>
            </div>
        </div>
        
        <!-- Formulaire d'édition -->
        <form action="index.php?controller=article&action=update&id=<?= $article['id'] ?>" method="POST" class="edit-form-container" id="editForm">
            <div class="form-group">
                <label for="titre" class="form-label">
                    Titre de l'article <span>*</span>
                </label>
                <input type="text" 
                       id="titre" 
                       name="titre" 
                       class="form-input" 
                       value="<?= htmlspecialchars($article['titre']) ?>" 
                       required 
                       placeholder="Entrez le titre de l'article"
                       maxlength="200">
                <div class="char-counter" id="titleCounter">
                    <span id="titleLength"><?= mb_strlen($article['titre']) ?></span> / 200 caractères
                </div>
            </div>
            
            <div class="form-group">
                <label for="contenu" class="form-label">
                    Contenu <span>*</span>
                </label>
                <textarea id="contenu" 
                          name="contenu" 
                          class="form-textarea" 
                          required 
                          placeholder="Rédigez le contenu de votre article..."
                          rows="8"><?= htmlspecialchars($article['contenu']) ?></textarea>
                <div class="char-counter" id="contentCounter">
                    <span id="contentLength"><?= mb_strlen($article['contenu']) ?></span> caractères
                </div>
            </div>
            
            <?php if(isset($article['categorie'])): ?>
            <div class="form-group">
                <label for="categorie" class="form-label">Catégorie</label>
                <select id="categorie" name="categorie" class="form-select">
                    <option value="technologie" <?= $article['categorie'] == 'technologie' ? 'selected' : '' ?>>Technologie</option>
                    <option value="science" <?= $article['categorie'] == 'science' ? 'selected' : '' ?>>Science</option>
                    <option value="design" <?= $article['categorie'] == 'design' ? 'selected' : '' ?>>Design</option>
                    <option value="business" <?= $article['categorie'] == 'business' ? 'selected' : '' ?>>Business</option>
                    <option value="lifestyle" <?= $article['categorie'] == 'lifestyle' ? 'selected' : '' ?>>Lifestyle</option>
                </select>
            </div>
            <?php endif; ?>
            
            <?php if(isset($article['statut'])): ?>
            <div class="form-group">
                <label for="statut" class="form-label">Statut</label>
                <select id="statut" name="statut" class="form-select">
                    <option value="brouillon" <?= $article['statut'] == 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                    <option value="publie" <?= $article['statut'] == 'publie' ? 'selected' : '' ?>>Publié</option>
                    <option value="archive" <?= $article['statut'] == 'archive' ? 'selected' : '' ?>>Archivé</option>
                </select>
            </div>
            <?php endif; ?>
            
            <!-- Bouton de prévisualisation -->
            <button type="button" class="toggle-preview" id="togglePreview">
                Aperçu en direct
            </button>
            
            <!-- Zone de prévisualisation -->
            <div class="preview-section" id="previewSection">
                <div class="preview-header">
                    <div class="preview-title">APERÇU</div>
                    <button type="button" class="toggle-preview" id="closePreview">
                        Masquer
                    </button>
                </div>
                <div class="preview-content" id="previewContent">
                    <!-- Le contenu sera inséré ici par JavaScript -->
                </div>
            </div>
            
            <!-- Actions du formulaire -->
            <div class="form-actions">
                <div class="btn-group-left">
                    <button type="submit" class="btn btn-save" id="saveBtn">
                        <span class="btn-icon">💾</span>
                        Enregistrer les modifications
                    </button>
                </div>
                
                <div class="btn-group-right">
                    <a href="index.php?controller=article&action=index" class="btn btn-cancel">
                        <span class="btn-icon">↩️</span>
                        Annuler
                    </a>
                    <a href="index.php?controller=article&action=delete&id=<?= $article['id'] ?>" 
                       class="btn btn-delete"
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.')">
                        <span class="btn-icon">🗑️</span>
                        Supprimer
                    </a>
                </div>
            </div>
        </form>
        
        <!-- Footer -->
        <div class="prism-footer">
            Système de Gestion de Contenu Prism Flux | 
            Contrôleur: <span class="highlight">article</span> | 
            Action: <span class="highlight">edit</span> |
            ID: <span class="highlight"><?= $article['id'] ?></span>
        </div>
    </div>
    
    <!-- Script pour les fonctionnalités interactives -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Compteurs de caractères
            const titleInput = document.getElementById('titre');
            const contentInput = document.getElementById('contenu');
            const titleCounter = document.getElementById('titleCounter');
            const contentCounter = document.getElementById('contentCounter');
            const titleLength = document.getElementById('titleLength');
            const contentLength = document.getElementById('contentLength');
            
            function updateCounters() {
                const titleLen = titleInput.value.length;
                const contentLen = contentInput.value.length;
                
                titleLength.textContent = titleLen;
                contentLength.textContent = contentLen;
                
                // Mettre à jour les couleurs des compteurs
                titleCounter.className = 'char-counter';
                if (titleLen > 180) {
                    titleCounter.classList.add('warning');
                }
                if (titleLen >= 200) {
                    titleCounter.classList.add('danger');
                }
                
                contentCounter.className = 'char-counter';
                if (contentLen > 5000) {
                    contentCounter.classList.add('warning');
                }
                if (contentLen > 10000) {
                    contentCounter.classList.add('danger');
                }
            }
            
            titleInput.addEventListener('input', updateCounters);
            contentInput.addEventListener('input', updateCounters);
            updateCounters();
            
            // Prévisualisation en temps réel
            const togglePreviewBtn = document.getElementById('togglePreview');
            const closePreviewBtn = document.getElementById('closePreview');
            const previewSection = document.getElementById('previewSection');
            const previewContent = document.getElementById('previewContent');
            
            function updatePreview() {
                const title = titleInput.value || 'Titre de l\'article';
                const content = contentInput.value || 'Contenu de l\'article...';
                
                previewContent.innerHTML = `
                    <h3 style="color: #ffffff; margin-bottom: 15px;">${title}</h3>
                    <div style="color: #b0b0b0; line-height: 1.6; white-space: pre-wrap;">${content.replace(/\n/g, '<br>')}</div>
                `;
            }
            
            togglePreviewBtn.addEventListener('click', function() {
                updatePreview();
                previewSection.classList.toggle('active');
                togglePreviewBtn.textContent = previewSection.classList.contains('active') 
                    ? 'Masquer l\'aperçu' 
                    : 'Aperçu en direct';
            });
            
            closePreviewBtn.addEventListener('click', function() {
                previewSection.classList.remove('active');
                togglePreviewBtn.textContent = 'Aperçu en direct';
            });
            
            titleInput.addEventListener('input', updatePreview);
            contentInput.addEventListener('input', updatePreview);
            
            // Validation du formulaire
            const form = document.getElementById('editForm');
            const saveBtn = document.getElementById('saveBtn');
            
            form.addEventListener('submit', function(e) {
                if (!titleInput.value.trim()) {
                    e.preventDefault();
                    alert('Le titre est obligatoire.');
                    titleInput.focus();
                    return;
                }
                
                if (!contentInput.value.trim()) {
                    e.preventDefault();
                    alert('Le contenu est obligatoire.');
                    contentInput.focus();
                    return;
                }
                
                // Animation de chargement
                saveBtn.innerHTML = '<span class="btn-icon">⚡</span> Enregistrement...';
                saveBtn.disabled = true;
                form.classList.add('loading');
            });
            
            // Protection contre la perte de données
            let formChanged = false;
            
            function checkFormChanges() {
                formChanged = titleInput.value !== '<?= addslashes($article['titre']) ?>' || 
                             contentInput.value !== '<?= addslashes($article['contenu']) ?>';
            }
            
            titleInput.addEventListener('input', checkFormChanges);
            contentInput.addEventListener('input', checkFormChanges);
            
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = 'Vous avez des modifications non enregistrées. Voulez-vous vraiment quitter ?';
                }
            });
            
            // Initialisation
            checkFormChanges();
            
            // Focus sur le premier champ
            titleInput.focus();
            
            // Animation d'entrée
            const animatedElements = document.querySelectorAll('.edit-form-container, .article-meta');
            animatedElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            });
            
            setTimeout(() => {
                animatedElements.forEach(el => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                });
            }, 100);
        });
    </script>
</body>
</html>