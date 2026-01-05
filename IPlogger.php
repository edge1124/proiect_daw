<?php
    require_once 'Database.php';
    require_once 'auth.php';
    class logger {
        private function __construct()
        {

        }
        public static function logVisit($site){
            try {
                $pdo = Database::getInstance()->getConnection();
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
            try {
                $sql = "INSERT INTO logs (user, IP, page, timestamp) 
                        VALUES (:user, :IP, :site, :timestamp)";
                
                $stmt = $pdo->prepare($sql);

                $data = [
                    'user' => auth::$username,
                    'IP' => $_SERVER['REMOTE_ADDR'],
                    'site' => $site,
                    'timestamp' => date('Y-m-d H:i:s', time())
                ];

                $stmt->execute($data);

            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }
    }

?>