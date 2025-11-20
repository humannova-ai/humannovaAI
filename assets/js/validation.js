/**
 * Fichier de validation côté client
 * Validations pour les formulaires d'événements
 */

// Validation en temps réel des champs
document.addEventListener('DOMContentLoaded', function() {
    setupRealTimeValidation();
});

function setupRealTimeValidation() {
    // Validation du titre
    const titreInput = document.getElementById('eventTitre');
    if(titreInput) {
        titreInput.addEventListener('blur', function() {
            validateField(this, validateTitre);
        });
    }
    
    // Validation de la description
    const descriptionInput = document.getElementById('eventDescription');
    if(descriptionInput) {
        descriptionInput.addEventListener('blur', function() {
            validateField(this, validateDescription);
        });
    }
    
    // Validation des dates
    const dateDebutInput = document.getElementById('eventDateDebut');
    const dateFinInput = document.getElementById('eventDateFin');
    
    if(dateDebutInput && dateFinInput) {
        dateDebutInput.addEventListener('change', function() {
            validateDates(dateDebutInput, dateFinInput);
        });
        
        dateFinInput.addEventListener('change', function() {
            validateDates(dateDebutInput, dateFinInput);
        });
    }
    
    // Validation URL image
    const imageInput = document.getElementById('eventImage');
    if(imageInput) {
        imageInput.addEventListener('blur', function() {
            validateField(this, validateURL);
        });
    }
}

// Valider un champ avec une fonction de validation
function validateField(input, validatorFunction) {
    removeError(input);
    
    const result = validatorFunction(input.value);
    if(!result.valid) {
        showError(input, result.message);
        return false;
    }
    
    return true;
}

// Validation du titre
function validateTitre(value) {
    if(!value || value.trim().length === 0) {
        return { valid: false, message: 'Le titre est obligatoire' };
    }
    
    if(value.trim().length < 3) {
        return { valid: false, message: 'Le titre doit contenir au moins 3 caractères' };
    }
    
    if(value.length > 150) {
        return { valid: false, message: 'Le titre ne peut pas dépasser 150 caractères' };
    }
    
    return { valid: true };
}

// Validation de la description
function validateDescription(value) {
    if(!value || value.trim().length === 0) {
        return { valid: false, message: 'La description est obligatoire' };
    }
    
    if(value.trim().length < 10) {
        return { valid: false, message: 'La description doit contenir au moins 10 caractères' };
    }
    
    if(value.length > 500) {
        return { valid: false, message: 'La description ne peut pas dépasser 500 caractères' };
    }
    
    return { valid: true };
}

// Validation des dates
function validateDates(dateDebutInput, dateFinInput) {
    const dateDebut = new Date(dateDebutInput.value);
    const dateFin = new Date(dateFinInput.value);
    const now = new Date();
    
    removeError(dateDebutInput);
    removeError(dateFinInput);
    
    // Vérifier que les dates sont remplies
    if(!dateDebutInput.value) {
        showError(dateDebutInput, 'La date de début est obligatoire');
        return false;
    }
    
    if(!dateFinInput.value) {
        showError(dateFinInput, 'La date de fin est obligatoire');
        return false;
    }
    
    // Vérifier que la date de début est dans le futur (optionnel)
    /*
    if(dateDebut < now) {
        showError(dateDebutInput, 'La date de début doit être dans le futur');
        return false;
    }
    */
    
    // Vérifier que la date de fin est après la date de début
    if(dateFin <= dateDebut) {
        showError(dateFinInput, 'La date de fin doit être postérieure à la date de début');
        return false;
    }
    
    return true;
}

// Validation URL
function validateURL(value) {
    // Si le champ est vide, c'est acceptable (optionnel)
    if(!value || value.trim().length === 0) {
        return { valid: true };
    }
    
    // Pattern pour valider une URL
    const urlPattern = /^https?:\/\/.+\..+/i;
    
    if(!urlPattern.test(value)) {
        return { valid: false, message: 'Veuillez entrer une URL valide (http:// ou https://)' };
    }
    
    return { valid: true };
}

// Validation du nombre
function validateNumber(value, min, max) {
    const num = parseInt(value);
    
    if(isNaN(num)) {
        return { valid: false, message: 'Veuillez entrer un nombre valide' };
    }
    
    if(num < min) {
        return { valid: false, message: `Le nombre doit être au moins ${min}` };
    }
    
    if(num > max) {
        return { valid: false, message: `Le nombre ne peut pas dépasser ${max}` };
    }
    
    return { valid: true };
}

// Afficher un message d'erreur
function showError(input, message) {
    // Chercher si un message d'erreur existe déjà
    let errorDiv = input.parentElement.querySelector('.error-message');
    
    if(!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        input.parentElement.appendChild(errorDiv);
    }
    
    errorDiv.textContent = message;
    input.style.borderColor = 'var(--accent-red)';
}

// Supprimer un message d'erreur
function removeError(input) {
    const errorDiv = input.parentElement.querySelector('.error-message');
    if(errorDiv) {
        errorDiv.remove();
    }
    input.style.borderColor = '';
}

// Valider tout le formulaire avant soumission
function validateEntireForm(formId) {
    const form = document.getElementById(formId);
    if(!form) return false;
    
    let isValid = true;
    
    // Valider tous les champs requis
    const requiredInputs = form.querySelectorAll('[required]');
    requiredInputs.forEach(input => {
        if(!input.value || input.value.trim().length === 0) {
            showError(input, 'Ce champ est obligatoire');
            isValid = false;
        }
    });
    
    return isValid;
}

// Nettoyer tous les messages d'erreur
function clearAllErrors() {
    const errors = document.querySelectorAll('.error-message');
    errors.forEach(error => error.remove());
    
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.style.borderColor = '';
    });
}

// Sanitize input (empêcher les scripts malveillants)
function sanitizeInput(value) {
    const div = document.createElement('div');
    div.textContent = value;
    return div.innerHTML;
}

// Exporter les fonctions pour utilisation globale
window.validateTitre = validateTitre;
window.validateDescription = validateDescription;
window.validateDates = validateDates;
window.validateURL = validateURL;
window.validateNumber = validateNumber;
window.validateField = validateField;
window.showError = showError;
window.removeError = removeError;
window.validateEntireForm = validateEntireForm;
window.clearAllErrors = clearAllErrors;
window.sanitizeInput = sanitizeInput;