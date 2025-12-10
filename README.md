# Human Nova AI - Event Management Platform

A comprehensive event management platform with an interactive quiz system built with PHP.

## About This Repository

This is a PHP-based web application for managing events, participants, and interactive quizzes.

### Project Structure

```
humannovaAI/
├── index.php                 # Main entry point
├── config/
│   └── database.php         # Database configuration
├── controllers/             # Application controllers
│   ├── EvenementController.php
│   └── ParticipationController.php
├── models/                  # Data models
│   ├── Evenement.php
│   ├── Participation.php
│   ├── Question.php
│   ├── Reponse.php
│   └── Utilisateur.php
├── views/                   # View templates
│   ├── admin/              # Admin interface
│   └── front/              # Public interface
├── assets/                 # CSS, JS, and static files
└── uploads/                # User uploaded files
```

## Features

- 📅 **Event Management**: Complete CRUD system for events
- 🎯 **Interactive Quizzes**: Create quizzes with multiple questions and answers
- 👥 **Participation Management**: Handle user registrations and approvals
- 📊 **Statistics Dashboard**: Real-time analytics and reporting

## Quick Answer: About Deleting the Main Branch

**Question**: "If I delete the main branch in this repo, does it affect the other files?"

**Short Answer**: No, deleting the `main` branch will NOT delete or affect files in other branches.

For a detailed explanation, see:
- [BRANCH_DELETION_IMPACT.md](BRANCH_DELETION_IMPACT.md) - Comprehensive impact analysis
- [GIT_BRANCH_REFERENCE.md](GIT_BRANCH_REFERENCE.md) - Visual guide to Git branches

### Key Points:

1. **Branches are pointers** to commits, not containers of files
2. **Other branches remain unaffected** when you delete a branch
3. **Commits stay safe** as long as they're referenced by another branch
4. **However**: It's generally not recommended to delete the `main` branch without good reason

## Technologies Used

- PHP
- MySQL
- HTML/CSS/JavaScript
- Git for version control

## License

© 2025 Human Nova AI - Event Management Project

---

## Documentation

- [Branch Deletion Impact Analysis](BRANCH_DELETION_IMPACT.md)
- [Git Branch Reference Guide](GIT_BRANCH_REFERENCE.md)
