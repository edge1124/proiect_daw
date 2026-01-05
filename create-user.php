<?php
require_once 'auth.php';
$status = $_GET['status'];
if(auth::$user_type <= 1){
    header("Location: login-page.php");
}
require_once 'IPlogger.php';
logger::logVisit('create-user.php');
$type = $_GET['type'];
$nume = $_GET['nume'];
$prenume = $_GET['prenume'];
$email = $_GET['email'];
$username = $_GET['username'];
$id = $_GET['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare Utilizator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>

        .message-box { 
            padding: 15px; 
            margin-top: 20px;
            margin-bottom: 0;
            border-radius: 5px; 
            text-align: center; 
        }
        .hiddenField { 

            display: none; 
            border: 1px dashed #c0e0c0; 
            background-color: #f7fff7; 
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px; 
        }
    </style>
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
              <a class="nav-link" href="read-anunturi.php">Anunțuri</a>
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
              <a class="nav-link" href="contact.php"<?php if(auth::$user_type >=3):?> hidden <?php endif?>>Contact</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="create-user.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Înregistrare</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="reset-passwd.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Resetare Parole</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="site-analytics.php"<?php if(auth::$user_type <3):?> hidden <?php endif?> target="_blank" rel="noopener noreferrer">Site Analytics</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">Log Out</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <?php
                if ($status) {
                    echo "<div class='message-box " . (strpos(htmlspecialchars($status), 'success') !== false ? 'alert alert-success' : 'alert alert-danger') . "'>";
                    if ($status === "profesor_success" || $status === "student_success") {
                        echo "<h3> Utilizator înregistrat cu succes.</h3>";
                    } elseif ($status === "db_error") {
                        echo "<h3> Eroare la înregistrare. Vă rugăm încercați din nou.</h3>";
                    } else {
                        echo "<h3> Eroare necunoscută. Vă rugăm încercați mai târziu.</h3>";
                    }
                    echo "</div>";
                }
                ?>

                <form action="register.php" method="POST" class="bg-white p-4 p-md-5 shadow rounded">
                    <input type="hidden" name="csrf_token" value="<?php echo auth::$csrf_token; ?>">
                    <h2 class="mb-4">Înregistrare Utilizator</h2>

                    <div class="mb-3">
                        <label for="form-type" class="form-label">Tip de utilizator</label>
                        <select id="form-type" name="user_type" onchange="showHideFields()" class="form-select" required>
                            <option value="" disabled <?php if(!$type):?> selected <?php endif?>>-- Alegeți tipul de utilizator --</option>
                            <option value="student">Student</option>
                            <option value="profesor"<?php if($type=='profesor'):?> selected <?php endif?>>Profesor</option>
                        </select>
                    </div>

                    <div id="user-profesor" class="hiddenField">
                        <h4 class="mb-3 text-primary">Detalii Profesor</h4>
                        <div class="mb-3">
                            <label for="usernameprof" class="form-label">Username</label>
                            <input type="text" id="usernameprof" name="usernameprof" class="form-control" <?php if($type=='profesor' && $username): echo 'value = "'. $username . '"'; endif?>>
                        </div>
                        <div class="mb-3">
                            <label for="pwdprof" class="form-label">Parolă</label>
                            <input type="password" id="pwdprof" name="pwdprof" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="nameprof" class="form-label">Nume</label>
                            <input type="text" id="nameprof" name="nameprof" class="form-control" <?php if($type=='profesor' && $nume): echo 'value = "'. $nume . '"'; endif?>>
                        </div>
                        <div class="mb-3">
                            <label for="name2prof" class="form-label">Prenume</label>
                            <input type="text" id="name2prof" name="name2prof" class="form-control" <?php if($type=='profesor' && $prenume): echo 'value = "'. $prenume . '"'; endif?>>
                        </div>
                        <div class="mb-3">
                            <label for="idprof" class="form-label">ID</label>
                            <input type="text" id="idprof" name="idprof" class="form-control" <?php if($type=='profesor' && $id): echo 'value = "'. $id . '"'; endif?>>
                        </div>
                        <div class="mb-3">
                            <label for="email1prof" class="form-label">Email Personal</label>
                            <input type="email" id="email1prof" name="email1prof" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="email2prof" class="form-label">Email Instituțional</label>
                            <input type="email" id="email2prof" name="email2prof" class="form-control" <?php if($type=='profesor' && $email): echo 'value = "'. $email . '"'; endif?>>
                        </div>
                    </div>

                    <div id="user-student" class="hiddenField">
                        <h4 class="mb-3 text-success">Detalii Student</h4>
                        <div class="mb-3">
                            <label for="usernamestudent" class="form-label">Username</label>
                            <input type="text" id="usernamestudent" name="usernamestudent" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="pwdstudent" class="form-label">Parolă</label>
                            <input type="password" id="pwdstudent" name="pwdstudent" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="namestudent" class="form-label">Nume</label>
                            <input type="text" id="namestudent" name="namestudent" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="name2student" class="form-label">Prenume</label>
                            <input type="text" id="name2student" name="name2student" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="idstudent" class="form-label">Număr Matricol</label>
                            <input type="text" id="idstudent" name="idstudent" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="cnpstudent" class="form-label">CNP</label>
                            <input type="number" id="cnpstudent" name="cnpstudent" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="email1student" class="form-label">Email Personal</label>
                            <input type="email" id="email1student" name="email1student" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="email2student" class="form-label">Email Instituțional</label>
                            <input type="email" id="email2student" name="email2student" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="specstudent" class="form-label">Specializare</label>
                            <input type="text" id="specstudent" name="specstudent" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="grupastudent" class="form-label">Grupă</label>
                            <input type="number" id="grupastudent" name="grupastudent" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="anstudent" class="form-label">Primul An de Studii</label>
                            <input type="number" id="anstudent" name="anstudent" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 mt-3">Creează Utilizator</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function showHideFields() {
        const primarySelect = document.getElementById("form-type");
        const selectedValue = primarySelect.value; 
        const allFields = document.querySelectorAll(".hiddenField");

        allFields.forEach(fieldDiv => {
            if (fieldDiv.style.display === 'block') {
                const inputs = fieldDiv.querySelectorAll("input");
                inputs.forEach(input => {
                        input.value = "";
                });
            }
            fieldDiv.style.display = 'none';
            const inputs = fieldDiv.querySelectorAll("input");
            inputs.forEach(input => {
                input.removeAttribute('required');
            });
        });
        if (selectedValue) {
            const targetId = 'user-' + selectedValue; 
            const targetDiv = document.getElementById(targetId);

            if (targetDiv) {
                targetDiv.style.display = 'block'; 

                const inputs = targetDiv.querySelectorAll("input");
                inputs.forEach(input => {
                    input.setAttribute('required', 'required');
                });
            }
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        showHideFields(); 
        document.getElementById("form-type").addEventListener('change', showHideFields);
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>