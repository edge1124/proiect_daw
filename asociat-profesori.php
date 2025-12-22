<?php
require_once 'auth.php';
require_once 'Database.php';
if(auth::$user_type <= 1){
    header("Location: login-page.php");
}
$pdo = null;
try {
    $pdo = Database::getInstance()->getConnection();
} catch (PDOException $e){
    die("Eroare de conexiune: " . $e->getMessage());
}
$sql_prof = "SELECT id_profesor, nume, prenume FROM profesor";
$stmt_prof = $pdo->prepare($sql_prof);
$stmt_prof->execute();
$profesori = $stmt_prof->fetchAll(PDO::FETCH_ASSOC); 

?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asociat Profesori</title>
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
              <a class="nav-link active" href="asociat-profesori.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Asociere Profesori</a>
            </li>
          </ul>
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="logout.php">Log Out</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="create-user.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Înregistrare</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container mt-5">
        <h1 class="mb-4">Asociat Porfesor-Materie</h1>
        <form action="asociat-profesori.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo auth::$csrf_token; ?>">
            <div class="mb-3">
                <label for="profesor" class="form-label">Profesor</label>
                <select name="profesor" id="profesor" class="form-control" required>
                    <option value="" disabled selected>-- Alegeti Profesor--</option>
                    <?php foreach ($profesori as $profesor): 
                        $id = htmlspecialchars($profesor['id_profesor']);
                        $nume = htmlspecialchars($profesor['nume']); 
                        $prenume = htmlspecialchars($profesor['prenume']); 
                    ?>
                        <option value="<?php echo $id; ?>">
                            <?php echo $nume . " " . $prenume;?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="materie" class="form-label">Materie:</label>
                <select name="materie" id="materie" class="form-control" required>
                    <option value="" disabled selected>-- Alegeti Materia--</option>
                    <option value="M1">Materie 1</option>
                    <option value="M2">Materie 2</option>
                    <option value="M3">Materie 3</option>
                    <option value="M4">Materie 4</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="grupa" class="form-label">Grupa</label>
                <input type="number" class="form-control" id="grupa" name="grupa" required>
            </div>
            <button type="submit" class="btn btn-primary">Creați Asociere</button>
        </form>
    </div>

<?php

if (isset($_POST['profesor']) || isset($_POST['grupa']) || isset($_POST['materie'])) {

try {
    $pdo = Database::getInstance()->getConnection();
} catch (PDOException $e) {
    die("Conexiune esuata: " . $e->getMessage());
}

try {
    $sql = "INSERT INTO materie (id_profesor, id_materie, nume_materie, grupa) 
            VALUES (:id_prof, :id_materie, :nume_materie, :grupa)";
    
    $stmt = $pdo->prepare($sql);
    $nume_mat = "";
    switch ($_POST['materie']) {
    case 'M1':
        $nume_mat = "Materie 1";
        break;
    case 'M2':
        $nume_mat = "Materie 2";
        break;
    case 'M3':
        $nume_mat = "Materie 3";
        break;
    case 'M4':
        $nume_mat = "Materie 4";
        break;
    }   

    $data = [
        'id_prof' => $_POST['profesor'],
        'id_materie' => $_POST['materie'],
        'nume_materie' => $nume_mat,
        'grupa' => $_POST['grupa']
    ];

    $stmt->execute($data);

    header("Location: set-note.php");

} catch (PDOException $e) {
    echo "Insert failed: " . $e->getMessage();
    die();
}

}
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>