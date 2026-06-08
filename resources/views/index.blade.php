<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetraiteFlow - Connexion</title>
    <link rel="stylesheet" href="./css/style.css">
    <script src="script/script.js"></script>
</head>

<body>
    <!-- Formulaire de connexion simple -->
    <div class="login-wrapper">
        <div class="login-container">
            <form action="{{route('auth.login')}}" method="POST" class="login-form" id="loginForm" name="loginForm">
              
                <h1>RetraiteFlow</h1>
                <p class="login-subtitle">Connectez-vous à votre espace</p>
                <!-- Champ utilisateur avec icône -->
                @csrf

                    @error('email')
                      <div class="text-danger">{{ $message }}</div>
                    @enderror

                    @error('password')
                          <div class="text-danger">{{ $message }}</div>
                    @enderror

                <div class="input-icon aesthetic">
                    <span class="icon-user">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z"
                                stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M20 21C20 18.8783 19.1571 16.8434 17.6569 15.3431C16.1566 13.8429 14.1217 13 12 13C9.87827 13 7.84344 13.8429 6.34315 15.3431C4.84285 16.8434 4 18.8783 4 21"
                                stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <input type="email" id="username" name="email" placeholder="Email de l' utilisateur" required value="{{ old('email') }}">
                </div>
                <!-- Champ mot de passe avec icône -->
                <div class="input-icon aesthetic">
                    <span class="icon-lock">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M19 11H5C3.89543 11 3 11.8954 3 13V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V13C21 11.8954 20.1046 11 19 11Z"
                                stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M7 11V7C7 5.67392 7.52678 4.40215 8.46447 3.46447C9.40215 2.52678 10.6739 2 12 2C13.3261 2 14.5979 2.52678 15.5355 3.46447C16.4732 4.40215 17 5.67392 17 7V11"
                                stroke="#0047ab" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" placeholder="Mot de passe" required>
                </div>
              
             
                <!-- Options Row -->
                <div class="options-row">
                    <!-- Case à cocher Se souvenir de moi -->
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember" value="1">
                        <label for="remember">Se souvenir de moi</label>
                    </div>
                    <!-- Lien mot de passe oublié -->
                    <div class="forgot-link aesthetic">
                        <a href="#" name="forgot_password">Mot de passe oublié ?</a>
                    </div>
                </div>
                <!-- Zone de messages d'erreur -->
                <div class="error-messages" id="error-messages"></div>
                <!-- Bouton de connexion -->
                <button type="submit" id="login" name="login" value="1" class="btn-pill aesthetic">Se connecter</button>
                <!-- Lien inscription -->
                <div class="register aesthetic">Pas de compte ? <a href="html/login.html">Créer un compte</a></div>
            </form>
        </div>
    </div>
</body>

</html>