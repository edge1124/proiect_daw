    <?php
    require_once 'Database.php';
    require_once 'auth.php';
    if ($_SERVER["REQUEST_METHOD"] === "POST" && (isset($_POST['username']) || isset($_POST['password']))) {
    
    try {
        $pdo = Database::getInstance()->getConnection();
        
        $secret = "hiddenawayforever";
        $verifyResponse = $_POST['g-recaptcha-response'];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'secret'   => $secret,
            'response' => $verifyResponse
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $responseData = json_decode($response, true);
        curl_close($ch);

        if (!$responseData["success"]) {
            header("Location: login-page.php?error=captcha");
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
           header("Location: login-page.php?error=passwd");
            exit;
        }
    } catch (PDOException $e) {
        
        header("Location: login-page.php?error=connection");
        exit;
    }
    }

    ?>