    <?php
    require_once 'Database.php';
    require_once 'auth.php';
    if ($_SERVER["REQUEST_METHOD"] === "POST" && (isset($_POST['username']) || isset($_POST['password']))) {
    
    try {
        $pdo = Database::getInstance()->getConnection();
        $iscaptchavalid = $_POST["g-recaptcha-response"];
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=6Lf8wTMsAAAAABZBIgL5KbXZSDC-gmV7HfzkveZe&response=" . $iscaptchavalid;
        $result = file_get_contents($url);
        $result = json_decode($result);
        $result = json_decode(json_encode($result), true);
        if(!($result["success"]))
        {
            header("Location: login-page.php");
            exit;
        }

        $sql = "SELECT * FROM user WHERE username = :username";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $_POST['username'], PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($_POST['password'], $user['password'])) {
            
            if($user['type'] == 1){
                header("Location: read-anunturi.php");
                exit;
            } elseif($user['type'] >= 2){
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