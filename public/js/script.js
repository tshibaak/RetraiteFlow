document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.login-form');
    const username = document.querySelector('input[name="username"]');
    const password = document.querySelector('input[name="password"]');
    const role = document.querySelector('select[name="role"]');
    const errorMessages = document.getElementById('error-messages');

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
        e.preventDefault();

        const usernameMinLength = 3;
        const passwordMinLength = 6;

        // Vérification des champs vides
        if (!username.value && !password.value && !role.value) {
            updateErrorMessage('Veuillez remplir tous les champs');
            return;
        }

        // Vérification si un seul champ est rempli
        if (!username.value) {
            updateErrorMessage('Veuillez saisir votre nom d\'utilisateur');
            return;
        }
        if (!password.value) {
            updateErrorMessage('Veuillez saisir votre mot de passe');
            return;
        }
        if (!role.value) {
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

        // Si tout est valide
        updateErrorMessage('Connexion en cours...', true);

        // Redirection selon le rôle
        setTimeout(() => {
            const selectedRole = role.value;
            const usernameValue = username.value.trim();

            // Stocker les informations utilisateur
            localStorage.setItem('username', usernameValue);
            localStorage.setItem('role', selectedRole);

            // Redirection vers la page dédiée en fonction du rôle
            if (selectedRole === 'encadreur') {
                window.location.href = 'html/page1/encadreur.html';
            } else if (selectedRole === 'finance') {
                window.location.href = 'html/page1/finance.html';
            } else if (selectedRole === 'logistique') {
                window.location.href = 'html/page1/logistique.html';
            } else if (selectedRole === 'discipline') {
                window.location.href = 'html/page1/discipline.html';
            } else if (selectedRole === 'cordon') {
                window.location.href = 'html/page1/cordon.html';
            } else {
                // Rôle inconnu : revenir à la page de connexion
                updateErrorMessage('Rôle inconnu, veuillez réessayer.', false);
            }
        }, 1000);
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
