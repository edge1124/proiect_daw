<!DOCTYPE html>
<?php
require_once 'Database.php';
require_once 'auth.php';
if(auth::$user_type != 1){
    header("Location: login-page.php");
    exit();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notele Mele</title>
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
              <a class="nav-link active" href="read-note.php"<?php if(auth::$user_type !=1):?> hidden <?php endif?>>Notele Mele</a>
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
              <a class="nav-link" href="logout.php">Log Out</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="create-user.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Înregistrare (Admin)</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    
    <div class="container mt-5">
        <h1>Notele Mele</h1>

<?php


try {
    $pdo = Database::getInstance()->getConnection();
    

    $sql = "SELECT grupa, numar_matricol FROM student WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', auth::$username, PDO::PARAM_STR);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {

        $sql = "SELECT id_materie, nume_materie FROM materie WHERE grupa = :grupa";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':grupa', $student['grupa'], PDO::PARAM_INT);
        $stmt->execute();
        $record = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<table class='table table-bordered table-striped mt-4'>
                <thead class='table-dark'>
                    <tr>
                        <th>Materie</th>
                        <th>Nota</th>
                    </tr>
                </thead>
                <tbody>";

        foreach($record as $materie){
            $nota_stmt = $pdo->prepare("SELECT val_nota FROM nota WHERE materie = :materie AND numar_matricol_student = :nr_stud");
            $nota_stmt->execute([
                    "materie"  => $materie['id_materie'],
                    "nr_stud" => $student['numar_matricol']]);
            $nota_fetch = $nota_stmt->fetch();
            $nota = $nota_fetch['val_nota'] ?? '---';

            echo "<tr>
                    <td>" . htmlspecialchars($materie['nume_materie']) . "</td>
                    <td><strong>" . htmlspecialchars($nota) . "</strong></td>
                  </tr>";
        }
        echo "</tbody></table>"; 
    } else {
        echo "<div class='alert alert-warning mt-4'>Nu au fost găsite informații pentru acest student.</div>";
    }

} 
catch (PDOException $e) {
    die("<div class='alert alert-danger'>Connection failed: " . $e->getMessage() . "</div>");
}
?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>