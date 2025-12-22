<!DOCTYPE html>
<?php

require_once 'Database.php';

require_once 'auth.php';

if(auth::$user_type == 0){
    header("Location: login-page.php");
    exit();
}

$pdo = Database::getInstance()->getConnection();

if (isset($_POST['delete_id']) && auth::$user_type == 3) {
    $delete_stmt = $pdo->prepare("DELETE FROM anunturi WHERE id_anunt = :id");
    $delete_stmt->bindParam(':id', $_POST['delete_id']);
    $delete_stmt->execute();
    header("Location: read-anunturi.php");
    exit();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anunțuri</title>
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
              <a class="nav-link active" href="read-anunturi.php">Anunțuri</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="read-note.php"<?php if(auth::$user_type !=1 ):?> hidden <?php endif?>>Notele Mele</a>
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
              <a class="nav-link" href="create-user.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Înregistrare</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    
    <div class="container mt-5">
        <h1>Anunțuri Disponibile</h1>

<?php
try {
    $sql = "SELECT * FROM anunturi ORDER BY time_added DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $record = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table class='table table-bordered table-striped mt-4'>
            <thead class='table-dark'>
                <tr>
                    <th>Titlu</th>
                    <th>Continut</th>";

    if(auth::$user_type == 3) {
        echo "<th></th>";
    }
    echo "</tr>
            </thead>
            <tbody>";

    foreach($record as $anunt){
        $grupa_stmt = $pdo->prepare("SELECT grupa FROM materie WHERE id_materie = :materie AND id_profesor = :profesor");
        $grupa_stmt->execute([
                "materie"  => $anunt['materie_id'],
                "profesor" => $anunt['profesor_id']]);
        $grupa_fetch = $grupa_stmt->fetch();
        $grupa = $grupa_fetch['grupa'] ?? null;
        
        $prof_stmt = $pdo->prepare("SELECT username FROM profesor WHERE id_profesor = :profesor");
        $prof_stmt->execute([
                "profesor" => $anunt['profesor_id']]);
        $prof_fetch = $prof_stmt->fetch();
        $prof_username = $prof_fetch['username'] ?? null;

        $student_stmt = $pdo->prepare("SELECT grupa FROM student WHERE username = :user");
        $student_stmt->execute([
                "user" => auth::$username]);
        $student_fetch = $student_stmt->fetch();
        $student_grupa = $student_fetch['grupa'] ?? null;
        
        $show_anunt = false;

        if($anunt['materie_id'] == "admin" || $anunt['profesor_id'] == "admin" || auth::$user_type == 3 || (auth::$user_type == 2 && auth::$username == $prof_username) || (auth::$user_type == 1 && $grupa == $student_grupa)){
            $show_anunt = true;
        }

        if($show_anunt){
            $titlu = $anunt['titlu'];
            $content = $anunt['continut'];  
            echo "<tr>
                    <td>" . htmlspecialchars($titlu) . "</td>
                    <td>" . nl2br(htmlspecialchars($content)) . "</td>";
            
            if(auth::$user_type == 3) {
                echo "<td>
                        <form method='POST' onsubmit='return confirm(\"Sigur doriți să ștergeți acest anunț?\");'>
                            <input type='hidden' name='csrf_token' value='" . auth::$csrf_token . "'>
                            <input type='hidden' name='delete_id' value='" . htmlspecialchars($anunt['id_anunt']) . "'>
                            <button type='submit' class='btn btn-danger btn-sm'>Șterge</button>
                        </form>
                      </td>";
            }
            echo "</tr>";
        }
    }
    echo "</tbody></table>"; 

} 
catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>