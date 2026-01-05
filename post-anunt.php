<?php
require_once 'auth.php';
require_once 'Database.php';
if(auth::$user_type <= 1){
    header("Location: login-page.php");
}
$pdo = null;
if (isset($_POST['title']) || isset($_POST['content']) || isset($_POST['materie'])) {

try {
    $pdo = Database::getInstance()->getConnection();
} catch (PDOException $e) {
    echo "Eroare de conexiune: " . $e->getMessage();
    die();
}

try {
    $sql = "INSERT INTO anunturi (titlu, continut, profesor_id, materie_id, time_added) 
            VALUES (:title, :continut, :profesor_id, :materie_id, :time)";
    
    $stmt = $pdo->prepare($sql);

    $data = [
        'title' => $_POST['title'] ?? 'Sample Title',
        'continut' => $_POST['content'] ?? 'Sample Description',
        'profesor_id' => $profesor,
        'materie_id' => $_POST['materie'],
        'time' => time()
    ];

    $stmt->execute($data);

    header("Location: read-anunturi.php");

} catch (PDOException $e) {
    echo "Eroare de bază de date: " . $e->getMessage();
    die();
}

}
?>