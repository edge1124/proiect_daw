<?php
require_once 'auth.php';
require_once 'Database.php';
if(auth::$user_type <= 2){
    header("Location: login-page.php");
}
require_once 'IPlogger.php';
logger::logVisit('reset-passwd.php');
$pdo = null;
try {
    $pdo = Database::getInstance()->getConnection();
} catch (PDOException $e){
    header("Location: reset-passwd.php?status=pdoerror");
}
$sql_user = "SELECT username FROM user";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute();
$useri = $stmt_user->fetchAll(PDO::FETCH_ASSOC); 
$status = $_GET['status'];
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resetare Parole</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

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
              <a class="nav-link" href="read-anunturi.php">Anunțuri</a>
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
              <a class="nav-link active" href="reset-passwd.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Resetare Parole</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="site-analytics.php"<?php if(auth::$user_type <3):?> hidden <?php endif?> target="_blank" rel="noopener noreferrer">Site Analytics</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">Log Out</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container mt-5">
        <?php
                if ($status) {
                    echo "<div class='message-box " . (strpos(htmlspecialchars($status), 'success') !== false ? 'alert alert-success' : 'alert alert-danger') . "'>";
                    if ($status === "success") {
                        echo "<h3> Parola a fost resetată. </h3>";
                    } elseif ($status === "dberror") {
                        echo "<h3> Eroare de bază de date.</h3>";
                    } elseif ($status === "pdoerror") {
                        echo "<h3> Eroare de conexiune. Vă rugăm încercați mai târziu.</h3>";
                    } else {
                        echo "<h3> Eroare necunoscută. Vă rugăm încercați mai târziu.</h3>";
                    }
                    echo "</div>";
                }
        ?>
        <h1 class="mb-4">Resetare Parolă</h1>
        <form action="newpasswd.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo auth::$csrf_token; ?>">
            <div class="mb-3">
                <label for="username" class="form-label">User</label>
                <select name="username" id="username" class="form-control" required>
                    <option value="" disabled selected>-- Alegeți User --</option>
                    <?php foreach ($useri as $user): 
                        $username = htmlspecialchars($user['username']);
                    ?>
                        <option value="<?php echo $username; ?>">
                            <?php echo $username?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="passwd" class="form-label">Parolă Nouă</label>
                <input type="password" class="form-control" id="passwd" name="passwd" required>
            </div>
            <button type="submit" class="btn btn-primary">Resetați Parola</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>