# Blog Module - Integration Guide

## 📦 What This Module Provides

A complete blog/article management system with:
- Article CRUD operations
- Comments & interactions
- Emoji reactions
- Social sharing

## 📁 Structure

```
nour/
├── blog/                    # Public blog interface
│   ├── index.php            # Blog router
│   ├── Controllers/
│   │   ├── ArticleController.php
│   │   ├── InteractionController.php
│   │   └── ReactionController.php
│   └── Views/
│       └── articles/        # Article views
│
├── blog_admin/              # Admin management (requires auth)
│   └── index.php            # Article management interface
│
└── shared/                  # Shared resources
    ├── Core/
    │   └── Connection.php   # Database connection
    └── Models/
        ├── Article.php      # Links to users table
        ├── Interaction.php
        └── Reaction.php
```

## 🔌 Integration Steps

### 1. Database Setup

Add these tables to your database:

```sql
-- Articles table (links to your users table)
CREATE TABLE articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    excerpt TEXT,
    image VARCHAR(255),
    tags VARCHAR(255),
    user_id INT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Interactions table
CREATE TABLE interactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_id INT NOT NULL,
    type ENUM('like', 'comment') NOT NULL,
    auteur VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

-- Reactions table
CREATE TABLE reactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_id INT NOT NULL,
    user_id VARCHAR(255) NOT NULL,
    emoji VARCHAR(10) NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_reaction (article_id, user_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);
```

### 2. Update Database Connection

Edit `shared/Core/Connection.php`:
```php
"mysql:host=localhost;dbname=YOUR_DATABASE_NAME"
"YOUR_USERNAME"
"YOUR_PASSWORD"
```

### 3. Authentication Setup

The module expects these session variables:
```php
$_SESSION['user_id']    // Required for blog_admin access
$_SESSION['role']       // Optional: check for 'admin' role
```

Update `blog_admin/index.php` to match your auth system (lines 12-25).

### 4. Access Points

**Public Blog:**
```
http://localhost/nour/blog/
```

**Admin Management (requires authentication):**
```
http://localhost/nour/blog_admin/
```

## 🎯 Features

### Public Interface (`blog/`)
- ✅ List all articles
- ✅ View article details
- ✅ Add comments
- ✅ React with emojis
- ✅ Social sharing

### Admin Interface (`blog_admin/`)
- ✅ Create articles (linked to logged-in user)
- ✅ Edit articles
- ✅ Delete articles
- ✅ Manage interactions

## 🔧 Customization

### Link Articles to Logged-in User

Articles automatically use `$_SESSION['user_id']` when created.

### Add Role Checking

Uncomment in `blog_admin/index.php`:
```php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php?error=not_authorized');
    exit;
}
```

### Customize Views

All views are in `blog/Views/articles/`:
- `index.php` - Article list
- `show.php` - Single article
- `create.php` - Create form
- `edit.php` - Edit form

## 📊 Integration with Your Project

### Option 1: Keep Separate
Leave structure as-is, access via `/blog/` and `/blog_admin/`

### Option 2: Merge into Existing Structure
1. Move `blog/Controllers/` into your main Controllers folder
2. Move `blog/Views/` into your main Views folder
3. Add routes to your existing router
4. Update paths in controllers

## 🚀 Quick Test

1. Ensure user is logged in (set `$_SESSION['user_id']`)
2. Visit: `http://localhost/nour/blog_admin/`
3. Create an article
4. View it at: `http://localhost/nour/blog/`

## ⚠️ Important Notes

- ✅ No separate admin authentication needed
- ✅ Articles linked to your users table via `user_id`
- ✅ Removed Admin.php model (not needed)
- ✅ Authentication handled by your existing system
- ✅ Ready to integrate into your project

## 🎉 You're Done!

The module is now ready to integrate with your existing user management system.
