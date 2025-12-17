# Instructions pour faire fonctionner les réactions

## 1. Créer la table dans la base de données

Ouvrez phpMyAdmin (http://localhost/phpmyadmin) et exécutez le fichier SQL:
`create_reactions_table.sql`

Ou copiez-collez ce code dans l'onglet SQL:

```sql
CREATE TABLE IF NOT EXISTS `reactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `emoji` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_reaction` (`article_id`, `user_id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## 2. Vérifier la connexion à la base de données

Assurez-vous que le fichier `shared/Core/Connection.php` contient les bonnes informations:
- Host: localhost
- Database: votre_nom_de_base_de_données
- Username: root (ou votre utilisateur)
- Password: (votre mot de passe)

## 3. Tester les réactions

1. Allez sur un article: http://localhost/nour/blog/index.php?controller=article&action=show&id=1
2. Vous devriez voir la section "Réagir à l'article" avec les emojis
3. Cliquez sur un emoji pour ajouter votre réaction
4. Les statistiques devraient s'afficher en bas

## 4. Débogage

Si les réactions ne fonctionnent pas:

1. Ouvrez la console du navigateur (F12)
2. Regardez les erreurs JavaScript
3. Vérifiez l'onglet "Network" pour voir les requêtes AJAX
4. Assurez-vous que la requête à `index.php?controller=reaction&action=handle` retourne du JSON

## 5. Structure complète

Le système de réactions comprend:
- **Model**: `shared/Models/Reaction.php` - Gère la base de données
- **Controller**: `blog/Controllers/ReactionController.php` - Gère les requêtes AJAX
- **View**: `blog/Views/articles/_reactions.php` - Interface utilisateur
- **Router**: `blog/index.php` - Route les requêtes vers le bon contrôleur

Tout est déjà en place, il suffit de créer la table dans la base de données!
