    <?php
    require_once 'Database.php';
    require_once 'auth.php';

    if ($_SERVER["REQUEST_METHOD"] === "POST" && (isset($_POST['username']) || isset($_POST['password']))) {
        
    try {
        $pdo = Database::getInstance()->getConnection();

        $sql = "SELECT * FROM user WHERE username = :username";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $_POST['username'], PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($_POST['password'], $user['password'])) {
            if($user['type'] == 1){
                ("Location: read-anunturi.php");
                exit;
            } elseif($user['type'] == 2){
                header("Location: set-note.php");
                exit;
            }
        } else {
           header("Location: login-page.php");
            exit;
        }
    } catch (PDOException $e) {
        header("Location: login-page.php");
        exit;
    }
    }
    ?>