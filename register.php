<?php
require_once 'auth.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_type'])) {

    require_once 'Database.php';

    $message = "unknown_error";
    $email = "";
    $usern = "";
    $passwd = "";

    try {
        $pdo = Database::getInstance()->getConnection();
        
        $user_type = $_POST['user_type'];

        if ($user_type == "profesor") {
            
            $hashed_password = password_hash($_POST['pwdprof'], PASSWORD_BCRYPT);
            $email = $_POST['email1prof'];
            $usern = $_POST['usernameprof'];
            $passwd = $_POST['pwdprof'];
            $data_profesor = [
                'username' => $_POST['usernameprof'],
                'nume' => $_POST['nameprof'],
                'prenume' => $_POST['name2prof'],
                'id_profesor' => $_POST['idprof'],
                'mail_personal' => $_POST['email1prof'],
                'mail_institutional' => $_POST['email2prof']
            ];
            
            $data_user = [
                'username' => $_POST['usernameprof'],
                'passwd' => $hashed_password,
                'usertype' => 2 
            ];
            
            $sql_profesor = "INSERT INTO profesor (username, id_profesor, nume, prenume, mail_institutional, mail_personal) VALUES (:username, :id_profesor, :nume, :prenume, :mail_institutional, :mail_personal)";
            $stmt = $pdo->prepare($sql_profesor);
            $stmt->execute($data_profesor);
            
            $sql_user = "INSERT INTO user (username, password, type) VALUES (:username, :passwd, :usertype)";
            $stmt = $pdo->prepare($sql_user);
            $stmt->execute($data_user);
            
            $message = "profesor_success";

        } elseif ($user_type == "student") {
        
            $hashed_password = password_hash($_POST['pwdstudent'], PASSWORD_BCRYPT);
            $email = $_POST['email1student'];
            $usern = $_POST['usernamestudent'];
            $passwd = $_POST['pwdstudent'];

            $data_student = [
                    'username' => $_POST['usernamestudent'],
                    'nume' => $_POST['namestudent'],
                    'prenume' => $_POST['name2student'],
                    'numar_matricol' => $_POST['idstudent'],
                    'cnp' => $_POST['cnpstudent'],
                    'mail_personal' => $_POST['email1student'],
                    'mail_institutional' => $_POST['email2student'],
                    'specializare' => $_POST['specstudent'],
                    'grupa' => $_POST['grupastudent'],
                    'an_studiu' => $_POST['anstudent']
            ];
            $data_user = [
                'username' => $_POST['usernamestudent'],
                'passwd' => $hashed_password,
                'usertype' => 1
            ];
            
            $sql_student = "INSERT INTO student (username, numar_matricol, nume, prenume, grupa, cnp, specializare, mail_institutional, mail_personal, student_la_facultate_din_anul) VALUES (:username, :numar_matricol, :nume, :prenume, :grupa, :cnp, :specializare, :mail_institutional, :mail_personal, :an_studiu)";
            $stmt = $pdo->prepare($sql_student);
            $stmt->execute($data_student);
            
            $sql_user = "INSERT INTO user (username, password, type) VALUES (:username, :passwd, :usertype)";
            $stmt = $pdo->prepare($sql_user);
            $stmt->execute($data_user);
            
            $message = "student_success";
            
        } else {
             $message = "invalid_type";
        }
        
    } catch (PDOException $e) {
        $message = "db_error";
    }

if (strpos($message, 'success')!==false) {

//Din curs:

require_once 'class.phpmailer.php';
require_once 'mail_config.php' ;

if (strpos($message, 'student')!==false){
$mailtext = "Buna ziua si felicitari pentru admiterea la Facultatea Mea Fictiva!
Datele dumneavoastra de login sunt urmatoarele:
Username: " . $usern . "
Password: " . $passwd . "
Va puteti loga la platforma online pe urmatorul link: https://csindelaru.daw.ssmr.ro/login-page.php 
Va asteptam cu drag la ore!";
}
if (strpos($message, 'prof')!==false){
$mailtext = "Buna ziua! Va multimim pentru colaborarea cu Facultatea Mea Fictiva!
Datele dumneavoastra de login sunt urmatoarele:
Username: " . $usern . "
Password: " . $passwd . "
Va puteti loga la platforma online pe urmatorul link: https://csindelaru.daw.ssmr.ro/login-page.php 
O zi minunata!";
}

$mailtext = nl2br($mailtext);
$mailtext = wordwrap($mailtext, 160, "<br />\n");


$mail = new PHPMailer(true); 

$mail->IsSMTP();

try {
 
  $mail->SMTPDebug  = 0;                     
  $mail->SMTPAuth   = true; 

  $to=$email;

  $mail->SMTPSecure = "ssl";                 
  $mail->Host       = "csindelaru.daw.ssmr.ro";      
  $mail->Port       = 465;                   
  $mail->Username   = $usernameconfirm; 
  $mail->Password   = $passwordconfirm;            
  $mail->AddReplyTo('stefansindelaru@gmail.com', 'Stefan Sindelaru');
  $mail->AddAddress($to);
 
  $mail->SetFrom('confirm@csindelaru.daw.ssmr.ro', 'Confirm Email');
  $mail->Subject = 'Confirmare Cont';
  $mail->AltBody = 'To view this post you need a compatible HTML viewer!'; 
  $mail->MsgHTML($mailtext);
  $mail->Send();
} catch (phpmailerException $e) {
  echo $e->errorMessage();
  $message = "mailererror";
  die();
} catch (Exception $e) {
  echo $e->getMessage(); 
  $message = "othererror";
  die();
}

}

header("Location: create-user.php?status=" . urlencode($message));
    exit; 
}
header("Location: create-user.php");
exit;
?>