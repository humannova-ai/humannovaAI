<?php
// Get interactions for this article
require_once ROOT_PATH . '/shared/Models/Interaction.php';
$interactionModel = new Interaction();
$interactions = $interactionModel->readByArticle($articleId);

// Separate likes and comments
$likes = [];
$comments = [];
if ($interactions && is_array($interactions)) {
    foreach ($interactions as $interaction) {
        if (isset($interaction['type'])) {
            if ($interaction['type'] === 'like') {
                $likes[] = $interaction;
            } elseif ($interaction['type'] === 'comment') {
                $comments[] = $interaction;
            }
        }
    }
}
?>

<style>
/* Modal Overlay */
.comments-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 9998;
    backdrop-filter: blur(5px);
}

.comments-modal-overlay.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

/* Modal Container */
.comments-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-width: 800px;
    max-height: 85vh;
    background: var(--surface-card, #ffffff);
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    z-index: 9999;
    overflow: hidden;
}

.comments-modal.active {
    display: block;
    animation: slideUp 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translate(-50%, -40%);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

/* Modal Header */
.comments-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 25px 30px;
    border-bottom: 1px solid var(--border-primary, #e0e0e0);
    background: var(--surface-secondary, #f8f9fa);
    flex-shrink: 0;
}

.modal-close-btn {
    background: none;
    border: none;
    font-size: 32px;
    cursor: pointer;
    color: var(--text-primary, #333);
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
    line-height: 1;
    padding: 0;
}

.modal-close-btn:hover {
    background: var(--surface-card, rgba(0,0,0,0.05));
    transform: rotate(90deg);
}

/* Modal Body */
.comments-modal-body {
    overflow-y: auto;
    padding: 25px 30px;
    max-height: calc(85vh - 80px);
}

/* Comment Button */
.comments-trigger-btn {
    background: var(--accent-primary);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
}

.comments-trigger-btn:hover {
    background: var(--accent-secondary);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.comments-section {
    background: transparent;
    border-radius: 0;
    padding: 0;
    margin-top: 0;
    border: none;
}

.comments-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--border-primary);
}

.comments-title {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-primary, #333);
    margin: 0;
}

.comments-count {
    color: var(--text-secondary, #666);
    font-size: 14px;
}

.comment-form {
    background: var(--surface-secondary, #f8f9fa);
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
}

.comment-form h3 {
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 18px;
    color: var(--text-primary, #333);
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: var(--text-primary);
    font-weight: 600;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid var(--border-primary, #ddd);
    border-radius: 8px;
    background: var(--surface-card, #fff);
    color: var(--text-primary, #333);
    font-family: inherit;
    font-size: 14px;
    box-sizing: border-box;
}

.form-group textarea {
    min-height: 120px;
    resize: vertical;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--accent-primary, #007bff);
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.btn-submit {
    background: var(--accent-primary, #007bff);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 15px;
}

.btn-submit:hover {
    background: var(--accent-secondary, #0056b3);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
}

.comments-list {
    margin-top: 20px;
}

.comment-item {
    background: var(--surface-secondary, #f8f9fa);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    border-left: 4px solid var(--accent-primary, #007bff);
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.comment-author {
    font-weight: 700;
    color: var(--text-primary, #333);
    font-size: 15px;
}

.comment-date {
    color: var(--text-secondary, #666);
    font-size: 13px;
}

.comment-message {
    color: var(--text-primary, #333);
    line-height: 1.6;
    font-size: 14px;
}

.likes-section {
    background: var(--surface-secondary, #f8f9fa);
    border-radius: 12px;
    padding: 15px 20px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.likes-icon {
    font-size: 22px;
}

.likes-text {
    color: var(--text-primary, #333);
    font-weight: 600;
    font-size: 15px;
}

.no-comments {
    text-align: center;
    padding: 50px 20px;
    color: var(--text-secondary, #666);
}

.no-comments p {
    margin: 8px 0;
    font-size: 15px;
}

.error-message {
    background: #ff4444;
    color: white;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 15px;
}

.success-message {
    background: #00ff88;
    color: #0a0a0a;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 15px;
}
</style>

<!-- Trigger Button (Hidden - opened via Commenter button in show.php) -->
<button class="comments-trigger-btn" onclick="openCommentsModal()" style="display: none;">
    💬 Commentaires (<?= count($comments) ?>)
</button>

<!-- Modal Overlay -->
<div class="comments-modal-overlay" id="commentsModalOverlay" onclick="closeCommentsModal()"></div>

<!-- Modal -->
<div class="comments-modal" id="commentsModal">
    <div class="comments-modal-header">
        <h2 class="comments-title">💬 Commentaires (<?= count($comments) ?>)</h2>
        <button class="modal-close-btn" onclick="closeCommentsModal()" aria-label="Fermer">&times;</button>
    </div>
    
    <div class="comments-modal-body">
    <?php if (!empty($likes)): ?>
    <div class="likes-section">
        <span class="likes-icon">👍</span>
        <span class="likes-text"><?= count($likes) ?> personne<?= count($likes) > 1 ? 's ont' : ' a' ?> aimé cet article</span>
    </div>
    <?php endif; ?>

    <!-- Formulaire de commentaire -->
    <div class="comment-form">
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="success-message">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?controller=interaction&action=create">
            <input type="hidden" name="article_id" value="<?= $articleId ?>">
            <input type="hidden" name="type" value="comment">
            <input type="hidden" name="auteur" value="<?= $_SESSION['user_name'] ?? 'Utilisateur' ?>">
            <input type="hidden" name="email" value="<?= $_SESSION['user_email'] ?? 'user@example.com' ?>">
            
            <textarea id="message" name="message" required placeholder="Écrivez votre commentaire ici..." style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 12px; font-family: inherit; font-size: 15px; min-height: 120px; resize: vertical; margin-bottom: 15px; box-sizing: border-box;"><?= htmlspecialchars($form_data['message'] ?? '') ?></textarea>
            
            <button type="submit" class="btn-submit" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 16px; cursor: pointer; transition: all 0.3s;">Publier le commentaire</button>
        </form>
    </div>

    <!-- Liste des commentaires -->
    <div class="comments-list">
        <?php if (empty($comments)): ?>
            <div class="no-comments">
                <p>Aucun commentaire pour le moment.</p>
                <p>Soyez le premier à réagir ! 🚀</p>
            </div>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment-item">
                    <div class="comment-header">
                        <span class="comment-author"><?= htmlspecialchars($comment['auteur']) ?></span>
                        <span class="comment-date">
                            <?= date('d/m/Y à H:i', strtotime($comment['date_creation'] ?? 'now')) ?>
                        </span>
                    </div>
                    <div class="comment-message">
                        <?= nl2br(htmlspecialchars($comment['message'] ?? '')) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    </div>
</div>

<script>
function openCommentsModal() {
    document.getElementById('commentsModal').classList.add('active');
    document.getElementById('commentsModalOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeCommentsModal() {
    document.getElementById('commentsModal').classList.remove('active');
    document.getElementById('commentsModalOverlay').classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCommentsModal();
    }
});
</script>
