<?php
// Views/articles/_reactions.php
// Partielle pour inclure dans show.php
?>
<div class="reactions-section" id="reactions-section">
    <h3>Réagir à l'article</h3>
    <p>Choisissez un émoji pour exprimer votre réaction :</p>
    
    <div class="emoji-picker" id="emoji-picker">
        <!-- Émojis seront chargés par JavaScript -->
    </div>
    
    <div class="reactions-stats" id="reactions-stats">
        <!-- Statistiques seront chargées par JavaScript -->
    </div>
</div>

<script>
// Fonctions pour les réactions
async function loadReactions(articleId) {
    try {
        const response = await fetch(`index.php?controller=reaction&action=handle&article_id=${articleId}`);
        const data = await response.json();
        
        if (data.success) {
            updateReactionsUI(data);
        }
    } catch (error) {
        console.error('Erreur chargement réactions:', error);
    }
}

function updateReactionsUI(data) {
    const emojiPicker = document.getElementById('emoji-picker');
    const statsDiv = document.getElementById('reactions-stats');
    
    // Mettre à jour le sélecteur d'émojis
    emojiPicker.innerHTML = '';
    data.available_emojis.forEach(emoji => {
        const button = document.createElement('button');
        button.className = 'emoji-btn';
        button.textContent = emoji;
        button.title = `Réagir avec ${emoji}`;
        
        if (data.user_reaction === emoji) {
            button.classList.add('active');
        }
        
        button.onclick = () => reactToArticle(articleId, emoji);
        emojiPicker.appendChild(button);
    });
    
    // Mettre à jour les statistiques
    statsDiv.innerHTML = '';
    if (data.reactions && data.reactions.length > 0) {
        data.reactions.forEach(reaction => {
            const stat = document.createElement('div');
            stat.className = 'reaction-stat';
            stat.innerHTML = `
                <span class="reaction-emoji">${reaction.emoji}</span>
                <span class="reaction-count">${reaction.count}</span>
            `;
            statsDiv.appendChild(stat);
        });
    }
}

async function reactToArticle(articleId, emoji) {
    try {
        const formData = new FormData();
        formData.append('article_id', articleId);
        formData.append('emoji', emoji);
        
        const response = await fetch('index.php?controller=reaction&action=handle', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        if (result.success) {
            loadReactions(articleId);
        }
    } catch (error) {
        console.error('Erreur réaction:', error);
    }
}

// Initialiser au chargement
document.addEventListener('DOMContentLoaded', () => {
    const articleId = <?= $article['id'] ?? 0 ?>;
    if (articleId) {
        loadReactions(articleId);
    }
});
</script>