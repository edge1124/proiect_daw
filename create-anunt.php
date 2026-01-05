<?php
require_once 'auth.php';
require_once 'Database.php';
if(auth::$user_type <= 1){
    header("Location: login-page.php");
}
require_once 'IPlogger.php';
logger::logVisit('create-anunt.php');
$pdo = null;
$materii = [];
try {
    $pdo = Database::getInstance()->getConnection();
} catch (PDOException $e){
    die("Eroare de conexiune: " . $e->getMessage());
}
$sql_prof = "SELECT id_profesor FROM profesor WHERE username = :username";
$stmt_prof = $pdo->prepare($sql_prof);
$stmt_prof->bindParam(':username', auth::$username, PDO::PARAM_STR);
$stmt_prof->execute();
$profesor = $stmt_prof->fetchColumn(); 
if (auth::$user_type == 3){
    $profesor = 'admin';
}
$materii_getall = $pdo->prepare("SELECT DISTINCT id_materie, nume_materie FROM materie WHERE (id_profesor = :pid OR :pid = 'admin')");
$materii_getall->bindParam(':pid', $profesor, PDO::PARAM_STR);
$materii_getall->execute();
$materii = $materii_getall->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postat Anunțuri</title>
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
              <a class="nav-link active" href="create-anunt.php"<?php if(auth::$user_type <2):?> hidden <?php endif?>>Creează Anunț</a>
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
              <a class="nav-link" href="logout.php">Log Out</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container mt-5">
        <h1 class="mb-4">Creează Anunț</h1>
        <form action="post-anunt.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo auth::$csrf_token; ?>">
            <div class="mb-3">
                <label for="title" class="form-label">Titlu</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Conținut</label>
                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label for="materie" class="form-label">Materie:</label>
                <select name="materie" id="materie" class="form-control" required>
                    <option value="" disabled selected>-- Alegeți Materia --</option>
                    <?php foreach ($materii as $materie): 
                        $id = htmlspecialchars($materie['id_materie']);
                        $nume = htmlspecialchars($materie['nume_materie']); 
                    ?>
                        <option value="<?php echo $id; ?>">
                            <?php echo $nume;?>
                        </option>
                    <?php endforeach; ?>
                    <?php if($profesor == 'admin'): ?>
                        <option value="admin">
                            Administrativ
                        </option>
                    <?php endif; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Postați Anunț</button>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>