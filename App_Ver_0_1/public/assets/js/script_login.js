document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.login-form');
    const username = document.querySelector('input[name="username"]');
    const password = document.querySelector('input[name="password"]');
    const role = document.querySelector('select[name="role"]');
    const errorMessages = document.getElementById('error-messages');


    // --- mon code JS pour l'animation du message de confirmation --- //

        const message = document.getElementById('confirmation');
        if (message) {
            // On attend 5 secondes (5000ms)
            setTimeout(function() {
                // Option 1 : Le faire disparaître brutalement
                //message.style.display = 'none';
                
                // Option 2 (plus joli) : Le faire disparaître en douceur
                 message.style.transition = "opacity 1s";
                 message.style.opacity = "0";
                 setTimeout(() => message.remove(), 1000);
            }, 5000);
        }

    // --------------------------------------------------------------- //

    // Fonction pour vérifier la validité d'un champ
    function isFieldValid(field, minLength) {
        return field.value.length >= minLength;
    }

    // Fonction pour mettre à jour le message d'erreur
    function updateErrorMessage(message, isValid = false) {
        errorMessages.className = 'error-messages show' + (isValid ? ' valid' : '');
        errorMessages.textContent = message;
    }

    function validateForm(e) {

        const usernameMinLength = 3;
        const passwordMinLength = 6;

        // Vérification des champs vides
        if (!username.value && !password.value && !role.value) {
            e.preventDefault();
            updateErrorMessage('Veuillez remplir tous les champs');
            return;
        }

        // Vérification si un seul champ est rempli
        if (!username.value) {
            e.preventDefault();
            updateErrorMessage('Veuillez saisir votre nom d\'utilisateur');
            return;
        }
        if (!password.value) {
            e.preventDefault();
            updateErrorMessage('Veuillez saisir votre mot de passe');
            return;
        }
        if (!role.value) {
            e.preventDefault();
            updateErrorMessage('Veuillez sélectionner votre rôle');
            return;
        }

        // Vérification de la longueur du nom d'utilisateur
        const isUsernameValid = isFieldValid(username, usernameMinLength);
        const isPasswordValid = isFieldValid(password, passwordMinLength);

        // Différents cas de validation
        if (!isUsernameValid && !isPasswordValid) {
            updateErrorMessage(`Le nom d'utilisateur doit contenir au moins ${usernameMinLength} caractères et le mot de passe au moins ${passwordMinLength} caractères`);
            return;
        }

        if (!isUsernameValid) {
            updateErrorMessage(`Le nom d'utilisateur doit contenir au moins ${usernameMinLength} caractères`);
            return;
        }

        if (!isPasswordValid) {
            updateErrorMessage(`Le mot de passe doit contenir au moins ${passwordMinLength} caractères`);
            return;
        }

    }

    // Écouteurs d'événements
    form.addEventListener('submit', validateForm);

    // Validation en temps réel
    username.addEventListener('input', function () {
        if (username.value.length > 0 && username.value.length < 3) {
            updateErrorMessage(`Le nom d'utilisateur doit contenir au moins 3 caractères`);
        } else if (username.value.length >= 3 && password.value.length >= 6 && role.value) {
            errorMessages.className = 'error-messages';
            errorMessages.textContent = '';
        } else {
            errorMessages.className = 'error-messages';
            errorMessages.textContent = '';
        }
    });

    password.addEventListener('input', function () {
        if (password.value.length > 0 && password.value.length < 6) {
            updateErrorMessage('Le mot de passe doit contenir au moins 6 caractères');
        } else if (username.value.length >= 3 && password.value.length >= 6 && role.value) {
            errorMessages.className = 'error-messages';
            errorMessages.textContent = '';
        } else {
            errorMessages.className = 'error-messages';
            errorMessages.textContent = '';
        }
    });

    role.addEventListener('change', function () {
        if (username.value.length >= 3 && password.value.length >= 6 && role.value) {
            errorMessages.className = 'error-messages';
            errorMessages.textContent = '';
        }
    });
});
