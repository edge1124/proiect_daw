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
    header("Location: reset-passwd.php?status=pdoerror");
    exit;
}
if (isset($_POST['username']) || isset($_POST['passwd'])) {

try {
    $sql = "UPDATE user
            SET password = :newpass
            WHERE username = :user";
    
    $stmt = $pdo->prepare($sql);

    $data = [
        'newpass' => password_hash($_POST['passwd'], PASSWORD_BCRYPT),
        'user' => $_POST['username']
    ];

    $stmt->execute($data);

    header("Location: reset-passwd.php?status=success");
    exit;
} catch (PDOException $e) {
    header("Location: reset-passwd.php?status=dberror");
    exit;
}

}
header("Location: reset-passwd.php?status=unknownerror");
exit;
?>