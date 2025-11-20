-- ========================================
-- SCRIPT COMPLET - BASE DE DONNÉES (VERSION CORRIGÉE)
-- ========================================

-- 1. Créer la base de données
CREATE DATABASE IF NOT EXISTS events_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Utiliser la base de données
USE events_management;

-- ========================================
-- SUPPRESSION DES VUES ET TABLES
-- ========================================

SET FOREIGN_KEY_CHECKS = 0;

-- Supprimer les vues
DROP VIEW IF EXISTS stats_quiz;
DROP VIEW IF EXISTS stats_participations;

-- Supprimer les tables dans l'ordre inverse des dépendances
DROP TABLE IF EXISTS reponses_utilisateur;
DROP TABLE IF EXISTS fichiers;
DROP TABLE IF EXISTS participations;
DROP TABLE IF EXISTS resultats_quiz;
DROP TABLE IF EXISTS utilisateurs;
DROP TABLE IF EXISTS reponses;
DROP TABLE IF EXISTS questions;
DROP TABLE IF EXISTS evenements;

SET FOREIGN_KEY_CHECKS = 1;

-- ========================================
-- CRÉATION DES TABLES PRINCIPALES
-- ========================================

-- Table des événements
CREATE TABLE evenements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type ENUM('normal', 'quiz') NOT NULL,
    titre VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    image_url VARCHAR(500),
    nombre_questions INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des questions (pour les événements de type quiz)
CREATE TABLE questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    evenement_id INT NOT NULL,
    texte_question TEXT NOT NULL,
    ordre INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des réponses
CREATE TABLE reponses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_id INT NOT NULL,
    texte_reponse VARCHAR(255) NOT NULL,
    est_correcte BOOLEAN DEFAULT FALSE,
    ordre INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- CRÉATION DES TABLES UTILISATEURS
-- ========================================

-- Table des utilisateurs (pour suivre les participants)
CREATE TABLE utilisateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des résultats de quiz
CREATE TABLE resultats_quiz (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    evenement_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    pourcentage DECIMAL(5,2) NOT NULL,
    temps_completion INT,
    date_passage TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_evenement (evenement_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des réponses utilisateur pour quiz
CREATE TABLE reponses_utilisateur (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resultat_id INT NOT NULL,
    question_id INT NOT NULL,
    reponse_id INT NOT NULL,
    est_correcte BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resultat_id) REFERENCES resultats_quiz(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (reponse_id) REFERENCES reponses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des participations aux événements normaux
CREATE TABLE participations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    evenement_id INT NOT NULL,
    commentaire TEXT,
    fichier_url VARCHAR(500),
    statut ENUM('en_attente', 'accepte', 'refuse') DEFAULT 'en_attente',
    date_participation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE,
    INDEX idx_utilisateur_evenement (utilisateur_id, evenement_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table pour stocker les fichiers uploadés
CREATE TABLE fichiers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    participation_id INT NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(500) NOT NULL,
    type_fichier VARCHAR(100) NOT NULL,
    taille_fichier INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (participation_id) REFERENCES participations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- INDEX SUPPLÉMENTAIRES
-- ========================================

CREATE INDEX idx_evenement_type ON evenements(type);
CREATE INDEX idx_evenement_dates ON evenements(date_debut, date_fin);
CREATE INDEX idx_question_evenement ON questions(evenement_id);
CREATE INDEX idx_reponse_question ON reponses(question_id);
CREATE INDEX idx_email ON utilisateurs(email);
CREATE INDEX idx_date_passage ON resultats_quiz(date_passage);
CREATE INDEX idx_participation_date ON participations(date_participation);

-- ========================================
-- DONNÉES DE TEST
-- ========================================

-- Insérer des événements de test
INSERT INTO evenements (type, titre, description, date_debut, date_fin, image_url, nombre_questions) VALUES
('normal', 'Hackathon Innovation 2026', 'Rejoignez-nous pour un hackathon de 48 heures dédié à l''innovation technologique et aux solutions créatives.', '2026-06-01 18:00:00', '2026-06-03 18:00:00', 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?fit=crop&w=600&q=80', 0),
('quiz', 'Quiz JavaScript ES6+', 'Évaluez votre maîtrise de JavaScript moderne avec ce quiz sur ES6, ES7, et les nouvelles fonctionnalités du langage.', '2026-07-15 15:00:00', '2026-07-15 16:30:00', 'https://images.unsplash.com/photo-1516116216624-53e697fedbea?fit=crop&w=600&q=80', 3);

-- Insérer des questions pour le quiz
INSERT INTO questions (evenement_id, texte_question, ordre) VALUES
(2, 'Quelle est la différence principale entre let et var en JavaScript ?', 1),
(2, 'Que retourne une fonction fléchée sans accolades ?', 2),
(2, 'Quelle méthode permet de créer une copie superficielle d''un tableau ?', 3);

-- Insérer des réponses pour les questions
INSERT INTO reponses (question_id, texte_reponse, est_correcte, ordre) VALUES
-- Question 1
(1, 'let a une portée de bloc, var a une portée de fonction', TRUE, 1),
(1, 'let est plus rapide que var', FALSE, 2),
(1, 'var est déprécié en ES6', FALSE, 3),
(1, 'Aucune différence significative', FALSE, 4),

-- Question 2
(2, 'La valeur de l''expression automatiquement', TRUE, 1),
(2, 'undefined', FALSE, 2),
(2, 'null', FALSE, 3),
(2, 'Une erreur de syntaxe', FALSE, 4),

-- Question 3
(3, 'spread operator [...array]', TRUE, 1),
(3, 'array.clone()', FALSE, 2),
(3, 'array.copy()', FALSE, 3),
(3, 'array.duplicate()', FALSE, 4);

-- Insérer des utilisateurs de test
INSERT INTO utilisateurs (nom, prenom, email) VALUES
('Test', 'Utilisateur', 'test@example.com'),
('Dupont', 'Jean', 'jean.dupont@example.com'),
('Martin', 'Sophie', 'sophie.martin@example.com');

-- ========================================
-- VUES POUR STATISTIQUES
-- ========================================

-- Vue pour les statistiques des quiz
CREATE OR REPLACE VIEW stats_quiz AS
SELECT 
    e.id as evenement_id,
    e.titre as evenement_titre,
    COUNT(DISTINCT rq.utilisateur_id) as nombre_participants,
    ROUND(AVG(rq.pourcentage), 2) as moyenne_score,
    MAX(rq.pourcentage) as meilleur_score,
    MIN(rq.pourcentage) as moins_bon_score
FROM evenements e
LEFT JOIN resultats_quiz rq ON e.id = rq.evenement_id
WHERE e.type = 'quiz'
GROUP BY e.id, e.titre;

-- Vue pour les participations aux événements
CREATE OR REPLACE VIEW stats_participations AS
SELECT 
    e.id as evenement_id,
    e.titre as evenement_titre,
    COUNT(p.id) as nombre_participations,
    COUNT(CASE WHEN p.statut = 'accepte' THEN 1 END) as acceptees,
    COUNT(CASE WHEN p.statut = 'en_attente' THEN 1 END) as en_attente,
    COUNT(CASE WHEN p.statut = 'refuse' THEN 1 END) as refusees
FROM evenements e
LEFT JOIN participations p ON e.id = p.evenement_id
WHERE e.type = 'normal'
GROUP BY e.id, e.titre;

-- ========================================
-- VÉRIFICATION FINALE
-- ========================================

SELECT 'Base de données créée avec succès !' as message;
SHOW TABLES;