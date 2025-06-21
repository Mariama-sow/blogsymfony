# blogsymfony# 📝 Blog Personnel – Projet Symfony

Ce projet est un **blog personnel** développé avec **Symfony 7**, réalisé dans le cadre d’un devoir universitaire. Il permet aux utilisateurs de publier des articles, de commenter et de gérer le contenu via un système de rôles bien défini.

---

## ✨ Fonctionnalités principales

### 👤 Gestion des utilisateurs
- Inscription avec option de vérification d'email
- Connexion / Déconnexion
- Mise à jour du profil utilisateur
- Attribution de rôles : `Administrateur`, `Éditeur`, `Visiteur`

### 📰 Gestion des articles
- Création, modification et suppression d’articles
- Upload d’images pour les articles (stockage local)
- Système de publication via un champ `isPublished`
- Attribution automatique à un auteur et une catégorie

### 💬 Système de commentaires
- Ajout de commentaires par les utilisateurs connectés
- Affichage dynamique des commentaires sous chaque article
- Association de chaque commentaire à un utilisateur et un article

### 🗂️ Gestion des catégories
- CRUD complet des catégories
- Filtrage des articles par catégorie

---

## 🧰 Technologies utilisées

- **PHP 8.2** avec **Symfony 7**
- **MySQL 8** (via Docker)
- **Twig** pour le rendu des templates
- **Doctrine ORM** pour la gestion de la base de données
- **Docker** & **Docker Compose** pour l’environnement de développement
- **Composer** pour la gestion des dépendances PHP

---

## ⚙️ Installation locale avec Docker

### 1. Cloner le projet

```bash
git clone https://github.com/ton-utilisateur/mon-blog-symfony.git
cd mon-blog-symfony

```
### 2. Démarrer les conteneurs Docker
 docker-compose up -d --build

### 3. Accéder au conteneur PHP

 docker exec -it symfony_app bash

### 4. Installer les dépendances PHP

 composer install

### 5. Créer la base de données et exécuter les migrations
 php bin/console doctrine:database:create
 php bin/console doctrine:migrations:migrate


### 7. Accéder à l’application

 Ouvrez votre navigateur à l’adresse suivante :

 http://localhost:8000

