<?php

require_once 'auth.php';

if(auth::$user_type >= 3 ){
    header("Location: login-page.php");
}

require_once 'IPlogger.php';
logger::logVisit('contact.php');

$status = $_GET['status'];
$subj = $_GET['subj'];

?>
<!DOCTYPE html>
<style>

        .message-box { 
            padding: 15px; 
            margin-top: 20px;
            margin-bottom: 0;
            border-radius: 5px; 
            text-align: center; 
        }
</style>

<html lang="en">
<head>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container-fluid">
        <a class="navbar-brand" href="read-anunturi.php">Proiect Facultate</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link" href="read-anunturi.php" <?php if(auth::$user_type <1):?> hidden <?php endif?> >Anunțuri</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="read-note.php"<?php if(auth::$user_type !=1):?> hidden <?php endif?>>Notele Mele</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="set-note.php" <?php if(auth::$user_type <2):?> hidden <?php endif?>>Setare Note</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="create-anunt.php"<?php if(auth::$user_type <2):?> hidden <?php endif?>>Creează Anunț</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="asociat-profesori.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Asociere Profesori</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="profesori-unibuc.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Profesori UniBuc</a>
            </li>
          </ul>
          <ul class="navbar-nav">
          <li class="nav-item">
              <a class="nav-link active" href="contact.php"<?php if(auth::$user_type >=3):?> hidden <?php endif?>>Contact</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="create-user.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Înregistrare</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="reset-passwd.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Resetare Parole</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="site-analytics.php"<?php if(auth::$user_type <3):?> hidden <?php endif?> target="_blank" rel="noopener noreferrer">Site Analytics</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php"<?php if(auth::$user_type <1):?> hidden <?php endif?>>Log Out</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="login-page.php"<?php if(auth::$user_type >0):?> hidden <?php endif?>>Login</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container mt-5">
        <?php
                if ($status) {
                    echo "<div class='message-box " . (strpos(htmlspecialchars($status), 'success') !== false ? 'alert alert-success' : 'alert alert-danger') . "'>";
                    if ($status === "success") {
                        echo "<h3> Email-ul a fost trimis.</h3>";
                    } elseif ($status === "mailerror") {
                        echo "<h3> Eroare cu functia mailer. Email-ul pe care l-ați introdus poate fi invalid.</h3>";
                    } else {
                        echo "<h3> Eroare necunoscută. Vă rugăm încercați mai târziu.</h3>";
                    }
                    echo "</div>";
                }
            ?>
        <h1 class="mb-4">Contact Administrație Website</h1>
        <form action="contact.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo auth::$csrf_token; ?>">
            <div class="mb-3">
                <label for="title" class="form-label">Subiect</label>
                <input type="text" class="form-control" id="subject" name="subject" <?php if($subj === "lostpass"):?> value="Resetare Parolă" <?php endif?> required>
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Adresa ta de Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Conținut</label>
                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
            </div>
            <div class="mb-3 g-recaptcha" data-sitekey="6LcrHjYsAAAAAEtE7XC2gJvbolWxREy5sy65HzBl"></div>
            <button type="submit" class="btn btn-primary">Trimite mail</button>
        </form>
    </div>

<?php
    

if (isset($_POST['subject']) && isset($_POST['content']) && isset($_POST['email'])) {
    
    $secret = "notmakingthesamemistaketwice";
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
    
    require_once 'class.phpmailer.php';
    require_once 'mail_config.php' ;

    $mailtext = htmlspecialchars($_POST['content']) . "<br />\n<br />\n" . "Trimis de " . htmlspecialchars($_POST['email']);
    $mailtext = wordwrap($mailtext, 160, "<br />\n");
    $status = "";

    $mail = new PHPMailer(true); 

    $mail->IsSMTP();

    try {
    
        $mail->SMTPDebug  = 0;                     
        $mail->SMTPAuth   = true; 

        $to="stefansindelaru@gmail.com";

        $mail->SMTPSecure = "ssl";                 
        $mail->Host       = "csindelaru.daw.ssmr.ro";      
        $mail->Port       = 465;                   
        $mail->Username   = $usernamecontact;
        $mail->Password   = $passwordcontact;
        $mail->AddReplyTo(htmlspecialchars($_POST['email']));
        $mail->AddAddress($to);
        
        $mail->SetFrom('contact@csindelaru.daw.ssmr.ro', 'Platforma Contact');
        $mail->Subject = htmlspecialchars($_POST['subject']);
        $mail->AltBody = 'To view this post you need a compatible HTML viewer!'; 
        $mail->MsgHTML($mailtext);
        $mail->Send();
        $status = "success";
    } catch (phpmailerException $e) {
        echo $e->errorMessage();
        $status = "mailerror";
    } catch (Exception $e) {
        echo $e->getMessage();
        $status = "othererror";
    }

    header("Location: contact.php?status=" . urlencode($status));
    exit; 
}
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>