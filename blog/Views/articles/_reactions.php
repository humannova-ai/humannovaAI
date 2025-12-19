<?php
// Views/articles/_reactions.php
// DEBUG: Afficher des informations
// echo "<!-- DEBUG: Article ID = " . ($article['id'] ?? 'null') . " -->";

if (!isset($article) || !isset($article['id'])) {
    echo "<!-- DEBUG: Aucun article défini -->";
    return;
}

$articleId = (int)$article['id'];
if ($articleId <= 0) {
    echo "<!-- DEBUG: ID article invalide -->";
    return;
}
?>

<div class="reactions-section" id="reactions-section">
    <h3>Réagir à l'article</h3>
    <p>Choisissez un émoji pour exprimer votre réaction :</p>
    
    <!-- Émojis par défaut en attendant le chargement AJAX -->
    <div class="emoji-picker" id="emoji-picker">
        <div class="emoji-loading">
            <div class="loading-spinner"></div>
            <p>Chargement des réactions...</p>
        </div>
        <div class="default-emojis" style="display:none;">
            <button class="emoji-btn" data-emoji="👍" title="Like">👍</button>
            <button class="emoji-btn" data-emoji="❤️" title="Love">❤️</button>
            <button class="emoji-btn" data-emoji="😮" title="Wow">😮</button>
            <button class="emoji-btn" data-emoji="😄" title="Haha">😄</button>
            <button class="emoji-btn" data-emoji="🔥" title="Fire">🔥</button>
            <button class="emoji-btn" data-emoji="👏" title="Clap">👏</button>
        </div>
    </div>
    
    <div class="reactions-stats" id="reactions-stats">
        <p class="no-stats">Aucune statistique disponible pour le moment.</p>
    </div>
</div>

<style>
/* Styles pour les réactions */
.reactions-section {
    margin: 40px 0;
    padding: 25px;
    background: var(--bg-secondary);
    border-radius: var(--radius);
    border: 1px solid var(--border-color);
}

.emoji-picker {
    display: flex;
    gap: 15px;
    margin: 25px 0;
    flex-wrap: wrap;
    min-height: 80px;
    align-items: center;
}

.emoji-loading {
    display: flex;
    align-items: center;
    gap: 15px;
    color: var(--text-secondary);
}

.loading-spinner {
    width: 30px;
    height: 30px;
    border: 3px solid var(--border-color);
    border-top-color: var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.emoji-btn {
    font-size: 28px;
    padding: 12px;
    background: var(--bg-primary);
    border: 2px solid var(--border-color);
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 60px;
    min-height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.emoji-btn:hover {
    transform: scale(1.15);
    background: var(--bg-tertiary);
    border-color: var(--primary-color);
}

.emoji-btn.active {
    border-color: var(--primary-color);
    background: var(--bg-tertiary);
    transform: scale(1.1);
    box-shadow: 0 0 0 3px rgba(var(--primary-color-rgb), 0.1);
}

.reactions-stats {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}

.no-stats {
    color: var(--text-secondary);
    font-style: italic;
    text-align: center;
}

.stats-grid {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.reaction-stat {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: var(--bg-tertiary);
    border-radius: 20px;
    border: 1px solid var(--border-color);
}

.reaction-emoji {
    font-size: 20px;
}

.reaction-count {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-secondary);
}
</style>

<script>
// Déclarer les variables globalement
const REACTIONS_ARTICLE_ID = <?= $articleId ?>;
const REACTIONS_BASE_URL = 'index.php?controller=reaction&action=handle';

console.log('Initialisation réactions - Article ID:', REACTIONS_ARTICLE_ID);

// Fonction principale pour charger les réactions
async function loadReactions() {
    if (!REACTIONS_ARTICLE_ID || REACTIONS_ARTICLE_ID <= 0) {
        console.error('ID article invalide pour les réactions');
        return;
    }
    
    const emojiPicker = document.getElementById('emoji-picker');
    const statsDiv = document.getElementById('reactions-stats');
    
    if (!emojiPicker || !statsDiv) {
        console.error('Éléments DOM non trouvés');
        return;
    }
    
    // Afficher le chargement
    emojiPicker.innerHTML = `
        <div class="emoji-loading">
            <div class="loading-spinner"></div>
            <p>Chargement des réactions...</p>
        </div>
    `;
    
    try {
        console.log('Fetching:', `${REACTIONS_BASE_URL}&article_id=${REACTIONS_ARTICLE_ID}`);
        
        const response = await fetch(`${REACTIONS_BASE_URL}&article_id=${REACTIONS_ARTICLE_ID}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            cache: 'no-cache'
        });
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Data received:', data);
        
        if (data.success) {
            updateReactionsUI(data);
        } else {
            showErrorMessage('Erreur: ' + (data.error || 'Inconnue'));
            loadDefaultEmojis();
        }
        
    } catch (error) {
        console.error('Erreur chargement réactions:', error);
        showErrorMessage('Impossible de charger les réactions');
        loadDefaultEmojis();
    }
}

// Mettre à jour l'interface
function updateReactionsUI(data) {
    const emojiPicker = document.getElementById('emoji-picker');
    const statsDiv = document.getElementById('reactions-stats');
    
    // Mettre à jour les émojis
    if (data.available_emojis && data.available_emojis.length > 0) {
        emojiPicker.innerHTML = '';
        data.available_emojis.forEach(emoji => {
            const button = document.createElement('button');
            button.className = 'emoji-btn';
            button.textContent = emoji;
            button.title = `Réagir avec ${emoji}`;
            button.dataset.emoji = emoji;
            
            // Marquer comme actif si c'est la réaction de l'utilisateur
            if (data.user_reaction === emoji) {
                button.classList.add('active');
            }
            
            button.addEventListener('click', () => sendReaction(emoji));
            emojiPicker.appendChild(button);
        });
    } else {
        loadDefaultEmojis();
    }
    
    // Mettre à jour les statistiques
    statsDiv.innerHTML = '';
    
    if (data.reactions && data.reactions.length > 0) {
        const statsContainer = document.createElement('div');
        statsContainer.className = 'stats-grid';
        
        data.reactions.forEach(reaction => {
            const stat = document.createElement('div');
            stat.className = 'reaction-stat';
            stat.innerHTML = `
                <span class="reaction-emoji">${reaction.emoji}</span>
                <span class="reaction-count">${reaction.count}</span>
            `;
            statsContainer.appendChild(stat);
        });
        
        statsDiv.appendChild(statsContainer);
        
        // Ajouter le total
        if (data.stats && data.stats.total_reactions > 0) {
            const total = document.createElement('p');
            total.style.marginTop = '15px';
            total.style.textAlign = 'center';
            total.innerHTML = `<strong>Total: ${data.stats.total_reactions} réactions</strong>`;
            statsDiv.appendChild(total);
        }
    } else {
        statsDiv.innerHTML = '<p class="no-stats">Soyez le premier à réagir !</p>';
    }
}

// Charger les émojis par défaut si l'AJAX échoue
function loadDefaultEmojis() {
    const emojiPicker = document.getElementById('emoji-picker');
    const defaultEmojis = ['👍', '❤️', '😮', '😄', '🔥', '👏'];
    
    emojiPicker.innerHTML = '';
    defaultEmojis.forEach(emoji => {
        const button = document.createElement('button');
        button.className = 'emoji-btn';
        button.textContent = emoji;
        button.title = `Réagir avec ${emoji}`;
        button.dataset.emoji = emoji;
        button.addEventListener('click', () => sendReaction(emoji));
        emojiPicker.appendChild(button);
    });
}

// Envoyer une réaction
async function sendReaction(emoji) {
    console.log('=== DÉBUT ENVOI RÉACTION ===');
    console.log('Article ID:', REACTIONS_ARTICLE_ID);
    console.log('Émoji:', emoji);
    
    try {
        const formData = new FormData();
        formData.append('article_id', REACTIONS_ARTICLE_ID);
        formData.append('emoji', emoji);
        
        console.log('FormData créé');
        console.log('URL:', REACTIONS_BASE_URL);
        
        // Afficher ce qui est envoyé
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        const response = await fetch(REACTIONS_BASE_URL, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            mode: 'cors',
            credentials: 'same-origin'
        });
        
        console.log('Réponse reçue');
        console.log('Status:', response.status);
        console.log('Status Text:', response.statusText);
        console.log('Headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const text = await response.text();
        console.log('Texte brut:', text);
        
        let result;
        try {
            result = JSON.parse(text);
            console.log('JSON parsé:', result);
        } catch (e) {
            console.error('Erreur parsing JSON:', e);
            throw new Error('Réponse non-JSON reçue');
        }
        
        if (result.success) {
            showSuccessMessage('Réaction envoyée !');
            loadReactions(); // Recharger les réactions
        } else {
            showErrorMessage(result.error || 'Erreur lors de l\'envoi');
        }
        
    } catch (error) {
        console.error('=== ERREUR COMPLÈTE ===');
        console.error('Type:', error.name);
        console.error('Message:', error.message);
        console.error('Stack:', error.stack);
        showErrorMessage('Erreur de connexion: ' + error.message);
    }
    
    console.log('=== FIN ENVOI RÉACTION ===');
}
// Messages d'alerte
function showErrorMessage(message) {
    showAlert(message, 'error');
}

function showSuccessMessage(message) {
    showAlert(message, 'success');
}

function showAlert(message, type) {
    // Supprimer les anciennes alertes
    document.querySelectorAll('.reaction-alert').forEach(el => el.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `reaction-alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.style.cssText = `
        padding: 12px 20px;
        border-radius: 8px;
        margin: 15px 0;
        font-weight: 500;
        ${type === 'error' ? 'background: #ffebee; color: #c62828; border: 1px solid #ffcdd2;' : ''}
        ${type === 'success' ? 'background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9;' : ''}
    `;
    
    const reactionsSection = document.getElementById('reactions-section');
    if (reactionsSection) {
        reactionsSection.prepend(alertDiv);
        setTimeout(() => alertDiv.remove(), 5000);
    }
}

// Initialiser quand la page est prête
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM ready, loading reactions...');
    
    // Petit délai pour s'assurer que tout est chargé
    setTimeout(() => {
        if (REACTIONS_ARTICLE_ID && REACTIONS_ARTICLE_ID > 0) {
            loadReactions();
        } else {
            console.error('Article ID non valide pour les réactions');
        }
    }, 500);
});

// Écouter les changements de thème
document.addEventListener('themeChanged', () => {
    console.log('Theme changed, reloading reactions...');
    setTimeout(loadReactions, 100);
});
</script>