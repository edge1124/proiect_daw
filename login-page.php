<?php
require_once 'auth.php';
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

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container-fluid">
        <a class="navbar-brand" href="login-page.php">Proiect Facultate</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link" href="read-anunturi.php" <?php if(auth::$user_type <1):?> hidden <?php endif?>>Anunțuri</a>
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
          </ul>
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link active" href="login-page.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="create-user.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Înregistrare</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container mt-5">
        <h1 class="mb-4">Login User</h1>
        <form action="login-user.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo auth::$csrf_token; ?>">
            <div class="mb-3">
                <label for="email" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3 g-recaptcha" data-sitekey="6Lf8wTMsAAAAAGwsOlImDF-inmeIOCIrtiUOSydU"></div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>