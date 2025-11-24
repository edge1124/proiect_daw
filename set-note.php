<?php

require 'Database.php';
require_once 'auth.php';

if(auth::$user_type<2){
    header("Location: login-page.php");
}

$pdo = null;
$materii = [];
$studenti = [];
$nota2 = null;
$materie_select_nume = '';
$grupa = null;
$nota_display = '';


$materie_select_id = $_GET['materie'] ?? null;
$student_select_id = $_GET['student'] ?? null;

$nota_nou = $_POST['nota'] ?? null;
$action_save = $_POST['action'] ?? null; 

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

if($student_select_id && $materie_select_id && $nota_nou !== null && $action_save == 'save_grade'){
    try{
        $sql_check_nota = "SELECT val_nota FROM nota WHERE numar_matricol_student = :sid AND materie = :mid";
        $stmt_check_nota = $pdo->prepare($sql_check_nota);
        $stmt_check_nota->bindParam(':mid', $materie_select_id, PDO::PARAM_STR);
        $stmt_check_nota->bindParam(':sid', $student_select_id, PDO::PARAM_STR);
        $stmt_check_nota->execute();
        $nota_existenta = $stmt_check_nota->fetchColumn();
        
        $stmt = null;
        $nota_float = (float)$nota_nou;
        
        if($nota_existenta !== false){

            $sql_update = "UPDATE nota SET val_nota = :new_nota WHERE numar_matricol_student = :sid AND materie = :mid";
            $stmt = $pdo->prepare($sql_update);
        } else {

            $sql_insert = "INSERT INTO nota (numar_matricol_student, materie, val_nota) VALUES (:sid, :mid, :new_nota)";
            $stmt = $pdo->prepare($sql_insert);
        }
        
        if ($stmt) {
            $stmt->bindParam(':mid', $materie_select_id, PDO::PARAM_STR);
            $stmt->bindParam(':sid', $student_select_id, PDO::PARAM_STR);
            $stmt->bindParam(':new_nota', $nota_float); 
            $stmt->execute();
        }

    } catch (PDOException $e){
        die("Eroare de baza de date la salvarea notei: " . $e->getMessage());
    }
}


$materii_getall = $pdo->prepare("SELECT DISTINCT id_materie, nume_materie FROM materie WHERE (id_profesor = :pid OR :pid = 'admin')");
$materii_getall->bindParam(':pid', $profesor, PDO::PARAM_STR);
$materii_getall->execute();
$materii = $materii_getall->fetchAll(PDO::FETCH_ASSOC);


if($materie_select_id){
    $get_grupa_stmt = $pdo->prepare("SELECT grupa, nume_materie FROM materie WHERE id_materie = :mid AND (id_profesor = :pid OR :pid = 'admin') LIMIT 1");
    $get_grupa_stmt->bindParam(':mid', $materie_select_id, PDO::PARAM_STR);
    $get_grupa_stmt->bindParam(':pid', $profesor, PDO::PARAM_STR);
    $get_grupa_stmt->execute();
    $materie_info = $get_grupa_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($materie_info) {
        $grupa = $materie_info['grupa'];
        $materie_select_nume = $materie_info['nume_materie'];
        
        if($grupa){
            $get_studenti_stmt = $pdo->prepare("SELECT numar_matricol, nume, prenume FROM student WHERE grupa = :grupa");
            $get_studenti_stmt->bindParam(':grupa', $grupa, PDO::PARAM_INT);
            $get_studenti_stmt->execute();
            $studenti = $get_studenti_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    

    if($student_select_id){
        $sql_check_nota2 = "SELECT val_nota FROM nota WHERE numar_matricol_student = :sid AND materie = :mid";
        $stmt_check_nota2 = $pdo->prepare($sql_check_nota2);
        $stmt_check_nota2->bindParam(':mid', $materie_select_id, PDO::PARAM_STR);
        $stmt_check_nota2->bindParam(':sid', $student_select_id, PDO::PARAM_STR);
        $stmt_check_nota2->execute();
        $nota2 = $stmt_check_nota2->fetchColumn();
    }
}

$nota_display = ($nota2 !== false) ? htmlspecialchars($nota2) : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Note</title>
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
              <a class="nav-link active" href="set-note.php">Setare Note</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="create-anunt.php">Creează Anunț</a>
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
        <h2>Modificare si setare note</h2>
        
        <form action="" method="GET"> 
            <div class="mb-3">
                <label for="materie" class="form-label">1. Alegeti Materia:</label>
                <select name="materie" id="materie" onchange="this.form.submit()" class="form-select" required>
                    <option value="" disabled selected>-- Alegeti Materia--</option>
                    <?php foreach ($materii as $materie): 
                        $id = htmlspecialchars($materie['id_materie']);
                        $nume = htmlspecialchars($materie['nume_materie']);
                        $selectat = ($id == $materie_select_id) ? 'selected' : ''; 
                    ?>
                        <option value="<?php echo $id; ?>" <?php echo $selectat; ?>>
                            <?php echo $nume; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($materie_select_id): ?>
            <div class="mb-3">
                <label for="student_id" class="form-label">2. Alegeti Studentul: </label>
                <select name="student" id="student" onchange="this.form.submit()" class="form-select" required>
                    <option value="" disabled <?php echo (!$student_select_id) ? 'selected' : ''; ?>>-- Alegeti Student --</option>
                    <?php if (!empty($studenti)): 
                        foreach ($studenti as $student): 
                            $id = htmlspecialchars($student['numar_matricol']);
                            $nume = htmlspecialchars($student['nume'] . ' ' . $student['prenume']);
                            $selectat = ($id == $student_select_id) ? 'selected' : ''; 
                    ?>
                            <option value="<?php echo $id; ?>" <?php echo $selectat; ?>>
                                <?php echo $nume; ?> (Numar matricol: <?php echo $id; ?>)
                            </option>
                    <?php endforeach; 
                    else: ?>
                        <option value="" disabled>Niciun student la aceasta grupa la aceasta materie.</option>
                    <?php endif; ?>
                </select>
            </div>
            <?php endif; ?>
        </form>
        
        <?php if ($student_select_id): ?>
            <h4 class="mt-4">Nota curenta pentru <?php echo htmlspecialchars($materie_select_nume); ?>:</h4>
            
            <?php if ($nota2 !== false): ?>
                <p class="alert alert-info d-inline-block">Nota: <strong><?php echo $nota_display; ?></strong></p>
            <?php else: ?>
                <p class="alert alert-warning d-inline-block">Studentul nu are nota la aceasta materie.</p>
            <?php endif; ?>
            
            <form action="" method="POST" class="mt-3">
                <div class="mb-3">
                    <label for="nota" class="form-label">Introduceți nota nouă:</label>
                    <input type="number" step="0.01" min="1" max="10" id="nota" name="nota" 
                            value="<?php echo $nota_display; ?>" class="form-control" required>
                </div>

                <input type="hidden" name="materie" value="<?php echo htmlspecialchars($materie_select_id); ?>">
                <input type="hidden" name="student" value="<?php echo htmlspecialchars($student_select_id); ?>">
                
                <button type="submit" name="action" value="save_grade" class="btn btn-success">Trimite nota</button>
            </form>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>