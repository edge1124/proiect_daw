    <?php
    require_once 'Database.php';
    require_once 'auth.php';
    try {
        auth::deleteSession(auth::$username);
        header("Location: login-page.php");
    } catch (PDOException $e) {
        header("Location: login-page.php");
        exit;
    }
    ?>