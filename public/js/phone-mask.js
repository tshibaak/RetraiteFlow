// document.addEventListener('DOMContentLoaded', function() {
//     const phoneInput = document.getElementById('telephone');

//     phoneInput.addEventListener('input', function(e) {
//         // Supprimer tous les caractères non numériques
//         let value = this.value.replace(/\D/g, '');
        
//         // S'assurer que le premier chiffre est 8
//         if (value.length > 0 && value[0] !== '8') {
//             value = '8' + value.substring(1);
//         }
        
//         // Limiter à 9 chiffres
//         value = value.substring(0, 9);
        
//         // Mettre à jour la valeur
//         this.value = value;
//     });

//     phoneInput.addEventListener('keypress', function(e) {
//         // Permettre uniquement les chiffres
//         if (!/^\d$/.test(e.key)) {
//             e.preventDefault();
//         }
//     });

//     // Validation lors de la soumission du formulaire
//     document.querySelector('form').addEventListener('submit', function(e) {
//         const phoneNumber = phoneInput.value;
//         if (phoneNumber.length !== 9 || phoneNumber[0] !== '8') {
//             e.preventDefault();
//             alert('Le numéro de téléphone doit commencer par 8 et contenir 9 chiffres');
//         }
//     });
// });