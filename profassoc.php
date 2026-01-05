<?php
require_once 'auth.php';
require_once 'Database.php';
if(auth::$user_type <= 2){
    header("Location: login-page.php");
}
$pdo = null;
try {
    $pdo = Database::getInstance()->getConnection();
} catch (PDOException $e){
    header("Location: asociat-profesori.php?status=pdoerror");
    exit;
}
if (isset($_POST['profesor']) || isset($_POST['grupa']) || isset($_POST['materie'])) {

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

    header("Location: asociat-profesori.php?status=success");
    exit;
} catch (PDOException $e) {
    header("Location: asociat-profesori.php?status=dberror");
    exit;
}

}
header("Location: asociat-profesori.php?status=unknownerror");
exit;
?>