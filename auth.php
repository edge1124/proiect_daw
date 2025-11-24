<?php
    require_once 'Database.php';

    class auth {
        
        public static $user_type;
        public static $username;

        private function __construct()
        {

        }
        public static function deleteSession($username) {
            try{
                $pdo = Database::getInstance()->getConnection();
                $stmt = $pdo->prepare("DELETE FROM session WHERE username = :username");
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->execute();
            }catch (PDOException $e) {
                die();
            }
        }
        public static function createSession($username) {
                try{
                $pdo = Database::getInstance()->getConnection();
                $cookie = bin2hex(random_bytes(48));
                $stmt = $pdo->prepare("INSERT INTO session (cookie, username, expiry) VALUES (:cookie, :username, :expiry)");
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':cookie', $cookie, PDO::PARAM_STR);
                $expirydate = time() + 86400;
                $stmt->bindParam(':expiry', $expirydate, PDO::PARAM_INT);
                $stmt->execute();
                return $cookie;
                }catch (PDOException $e) {
                    die();
                }
        }
        public static function login($username, $password){
        try{
            $pdo = Database::getInstance()->getConnection();

            $stmt = $pdo->prepare("SELECT * FROM user WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $record = $stmt->fetch(PDO::FETCH_ASSOC);
            if($record){
                if(password_verify($password, $record['password'])){ 
                    self::$user_type = $record['type'];
                    self::$username = $record['username'];
                    self::deleteSession($username);
                    $cookie = self::createSession($username);
                    
                    setcookie('session_id', $cookie, time() + 86400);
                }
            }
            }catch (PDOException $e) {
                die();
            }

        }

        public static function recoverSession(){
            try{
                $pdo = Database::getInstance()->getConnection();
                $stmt = $pdo->prepare("SELECT * FROM session WHERE cookie = :cookie");
                $stmt->bindParam(':cookie', $_COOKIE["session_id"], PDO::PARAM_STR);
                $stmt->execute();
                $session_record = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($session_record){
                    if(time() < $session_record["expiry"]){
                        $stmt = $pdo->prepare("SELECT * FROM user WHERE username = :username");
                        $stmt->bindParam(':username', $session_record['username'], PDO::PARAM_STR);
                        $stmt->execute();
                        $user_record = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if($user_record){
                            self::$user_type = $user_record['type'];
                            self::$username = $user_record['username'];
                        }
                    }
                }

            }catch (PDOException $e) {
                die();
            }
        }
        
        public static function authRoutine() {

            if(isset($_POST["username"]) && isset($_POST['password'])){

                $username = $_POST["username"];
                $password = $_POST["password"];
                self::login($username, $password);
            }else if(isset($_COOKIE["session_id"])){

                self::recoverSession();
            }else{
                self::$user_type = 0;
                self::$username = null;
            }
        }

    }
    auth::authRoutine();
?>