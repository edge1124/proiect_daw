<?php
require_once 'auth.php';
if(auth::$user_type < 3){
    header("Location: login-page.php");
}

$status = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inregistrare Utilizator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* Stiluri personalizate pentru a suprascrie sau ajusta Bootstrap */
        .message-box { 
            padding: 15px; 
            margin-top: 20px;
            margin-bottom: 0;
            border-radius: 5px; 
            text-align: center; 
        }
        .conditional-fields { 
            /* Folosim clase Bootstrap, dar păstrăm display: none inițial */
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
        <a class="navbar-brand" href="read-anunturi.php">Proiect Note</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link" href="read-anunturi.php">Anunțuri</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="set-note.php">Setare Note</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="create-anunt.php">Creează Anunț</a>
            </li>
          </ul>
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="login-page.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="create-user.php">Înregistrare (Admin)</a>
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
                    echo "<div class='message-box " . (strpos($status, 'success') !== false ? 'alert alert-success' : 'alert alert-danger') . "'>";
                    if ($status === "profesor_success" || $status === "student_success") {
                        echo "<h3>✅ Utilizator înregistrat cu succes.</h3>";
                    } elseif ($status === "db_error") {
                        echo "<h3>❌ Eroare la înregistrare. Vă rugăm încercați din nou.</h3>";
                    } else {
                        echo "<h3>⚠️ Eroare necunoscută. Vă rugăm încercați mai târziu.</h3>";
                    }
                    echo "</div>";
                }
                ?>

                <form action="register.php" method="POST" class="bg-white p-4 p-md-5 shadow rounded">
                    <h2 class="mb-4">Inregistrare Utilizator</h2>

                    <div class="mb-3">
                        <label for="form-type" class="form-label">Tip de utilizator</label>
                        <select id="form-type" name="user_type" onchange="showHideFields()" class="form-select" required>
                            <option value="" disabled selected>select tip utilizator</option>
                            <option value="student">student</option>
                            <option value="profesor">profesor</option>
                        </select>
                    </div>

                    <div id="user-profesor" class="conditional-fields">
                        <h4 class="mb-3 text-primary">Detalii Profesor</h4>
                        <div class="mb-3">
                            <label for="usernameprof" class="form-label">Username</label>
                            <input type="text" id="usernameprof" name="usernameprof" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="pwdprof" class="form-label">Parola</label>
                            <input type="password" id="pwdprof" name="pwdprof" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="nameprof" class="form-label">Nume</label>
                            <input type="text" id="nameprof" name="nameprof" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="name2prof" class="form-label">Prenume</label>
                            <input type="text" id="name2prof" name="name2prof" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="idprof" class="form-label">ID</label>
                            <input type="text" id="idprof" name="idprof" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="email1prof" class="form-label">Email Personal</label>
                            <input type="email" id="email1prof" name="email1prof" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="email2prof" class="form-label">Email Institutional</label>
                            <input type="email" id="email2prof" name="email2prof" class="form-control">
                        </div>
                    </div>

                    <div id="user-student" class="conditional-fields">
                        <h4 class="mb-3 text-success">Detalii Student</h4>
                        <div class="mb-3">
                            <label for="usernamestudent" class="form-label">Username</label>
                            <input type="text" id="usernamestudent" name="usernamestudent" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="pwdstudent" class="form-label">Parola</label>
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
                            <label for="idstudent" class="form-label">Numar Matricol</label>
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
                            <label for="email2student" class="form-label">Email Institutional</label>
                            <input type="email" id="email2student" name="email2student" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="specstudent" class="form-label">Specializare</label>
                            <input type="text" id="specstudent" name="specstudent" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="grupastudent" class="form-label">Grupa</label>
                            <input type="number" id="grupastudent" name="grupastudent" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="anstudent" class="form-label">Primul An de Studii</label>
                            <input type="number" id="anstudent" name="anstudent" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 mt-3">Submit Form</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showHideFields() {
            const primarySelect = document.getElementById("form-type");
            const selectedValue = primarySelect.value; 

            const allFields = document.querySelectorAll(".conditional-fields");

            allFields.forEach(fieldDiv => {
                fieldDiv.style.display = 'none';
                // Elimină 'required' de pe câmpurile ascunse
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
                    // Adaugă 'required' pe câmpurile vizibile
                    const inputs = targetDiv.querySelectorAll("input");
                    inputs.forEach(input => {
                        input.setAttribute('required', 'required');
                    });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', showHideFields);
        
        // Asigură-te că funcția rulează și la refresh pentru a menține starea dacă există erori POST
        // (Deși redirecționarea ta curentă șterge statusul, e o practică bună)
        document.addEventListener('DOMContentLoaded', () => {
             const status = new URLSearchParams(window.location.search).get('status');
             if (status) {
                 // În funcție de status, poți readuce formularul vizibil
             }
             showHideFields();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>