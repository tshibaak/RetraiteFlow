<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetreatFlow</title>
    <link rel="stylesheet" href="./css/login.css">
    <script src="../script/phone-mask.js"></script>
</head>

<body>
    <div class="form-container">
        <h1>Bienvenue sur RetreatFlow</h1>
        <h2>Créez votre profil</h2>

        <form action="{{ route('auth.login') }}" method="POST">
            @csrf
            <ol class="form-list">
                <li class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" placeholder="Entrez votre email" required value="{{ old('email') }}">
                    @error('email')<span class="error">{{ $message }}</span>@enderror
                </li>

                <li class="form-group">
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
                    @error('password')<span class="error">{{ $message }}</span>@enderror
                </li>
            </ol>

            @if($errors->has('error'))
                <div class="error-message">{{ $errors->first('error') }}</div>
            @endif

            <div class="form-group submit-group">
                <button type="submit" name="envoyer">Se connecter</button>
            </div>
        </form>
    </div>
</body>

</html>