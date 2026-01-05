
<?php
require_once 'auth.php';

if(auth::$user_type >= 3 ){
    header("Location: login-page.php");
}

if (isset($_POST['subject']) && isset($_POST['content']) && isset($_POST['email'])) {
    
    $secret = "iar trebuie sa o schimb..";
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