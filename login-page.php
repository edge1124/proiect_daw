<?php
require_once 'auth.php';
$error = $_GET['error'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>

        .message-box { 
            padding: 15px; 
            margin-top: 20px;
            margin-bottom: 0;
            border-radius: 5px; 
            text-align: center; 
        }
</style>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container-fluid">
        <a class="navbar-brand" href="read-anunturi.php">Proiect Facultate</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link" href="read-anunturi.php" <?php if(auth::$user_type <1):?> hidden <?php endif?> >Anunțuri</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="read-note.php"<?php if(auth::$user_type !=1):?> hidden <?php endif?>>Notele Mele</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="set-note.php" <?php if(auth::$user_type <2):?> hidden <?php endif?>>Setare Note</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="create-anunt.php"<?php if(auth::$user_type <2):?> hidden <?php endif?>>Creează Anunț</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="asociat-profesori.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Asociere Profesori</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="profesori-unibuc.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Profesori UniBuc</a>
            </li>
          </ul>
          <ul class="navbar-nav">
          <li class="nav-item">
              <a class="nav-link" href="contact.php"<?php if(auth::$user_type >=3):?> hidden <?php endif?>>Contact</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="create-user.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Înregistrare</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="reset-passwd.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Resetare Parole</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="site-analytics.php"<?php if(auth::$user_type <3):?> hidden <?php endif?> target="_blank" rel="noopener noreferrer">Site Analytics</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="login-page.php">Login</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container mt-5">
            <?php
                if ($error) {
                    echo "<div class='message-box alert alert-danger'>";
                    if ($error==='passwd') {
                        echo "<h3> Username sau parolă greșită. </h3>";
                    } elseif ($error === "captcha") {
                        echo "<h3> Eroare la Captcha. Vă rugăm încercați din nou. </h3>";
                    } else {
                        echo "<h3> Eroare de conexiune. Vă rugăm încercați mai târziu.</h3>";
                    }
                    echo "</div>";
                }
                ?>
        <h1 class="mb-4">Login User</h1>
        <form action="login-user.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo auth::$csrf_token; ?>">
            <div class="mb-3">
                <label for="email" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Parolă</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3 g-recaptcha" data-sitekey="6LcrHjYsAAAAAEtE7XC2gJvbolWxREy5sy65HzBl"></div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <br>
        <a href="contact.php?subj=lostpass"> Ți-ai uitat parola? </a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>