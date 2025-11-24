<?php
require_once 'auth.php';
if(auth::$user_type <= 1){
    header("Location: login-page.php");
}
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container-fluid">
        <a class="navbar-brand" href="read-anunturi.php">Proiect Note</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link" href="read-anunturi.php">Anunțuri</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="set-note.php">Setare Note</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="create-anunt.php">Creează Anunț</a>
            </li>
          </ul>
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="login-page.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="create-user.php">Înregistrare (Admin)</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container mt-5">
        <h1 class="mb-4">Postare Anunturi</h1>
        <form action="create-anunt.php" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Titlu</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Continut</label>
                <input type="text" class="form-control" id="content" name="content" required>
            </div>
            <button type="submit" class="btn btn-primary">Postat Anunt</button>
        </form>
    </div>

<?php
require_once 'Database.php';

if (isset($_POST['title']) || isset($_POST['description'])) {

try {
    $pdo = Database::getInstance()->getConnection();
} catch (PDOException $e) {
    die("Conexiune esuata: " . $e->getMessage());
}

try {
    $sql = "INSERT INTO anunturi (titlu, continut, profesor_id, materie_id) 
            VALUES (:title, :continut, :profesor_id, :materie_id)";
    
    $stmt = $pdo->prepare($sql);

    $data = [
        'title' => $_POST['title'] ?? 'Sample Title',
        'continut' => $_POST['content'] ?? 'Sample Description',
        'profesor_id' => '',
        'materie_id' => ''
    ];

    $stmt->execute($data);

    header("Location: read-anunturi.php");

} catch (PDOException $e) {
    echo "Insert failed: " . $e->getMessage();
}

}
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>