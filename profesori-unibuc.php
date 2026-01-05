<!DOCTYPE html>
<?php
require_once 'Database.php';
require_once 'auth.php';
if(auth::$user_type < 3){
    header("Location: login-page.php");
    exit();
}

$url = "https://fmi.unibuc.ro/departamente/informatica/";
$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/114.0.0.0 Safari/537.36',
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 20] );

$htmlcontent = curl_exec($ch);
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profesori UniBuc</title>
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
              <a class="nav-link active" href="profesori-unibuc.php"<?php if(auth::$user_type <3):?> hidden <?php endif?>>Profesori UniBuc</a>
            </li>
          </ul>
          <ul class="navbar-nav">
          <li class="nav-item">
              <a class="nav-link" href="contact.php"<?php if(auth::$user_type >=3):?> hidden <?php endif?>>Contact</a>
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
              <a class="nav-link" href="logout.php">Log Out</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5">
        <h1>Profesori UniBuc</h1>

        <?php
        if(curl_errno($ch)) {
            echo '<div class="alert alert-danger">cURL Error: ' . htmlspecialchars(curl_error($ch)) . '</div>';
        } else {
            $dom = new DOMDocument();
            $dom->loadHTML($htmlcontent); 

            $data = $dom->getElementsByTagName('td');
            $profesori = [];
            $emails = [];
            
            foreach ($data as $fetch) {
                $item = $fetch->nodeValue;
                if (strpos($item, '@') !== false){
                    $emails[] = $item;
                } else {
                    $profesori[] = $item;
                }
            }

            echo "<table class='table table-bordered table-striped mt-4'>
                    <thead class='table-dark'>
                        <tr>
                            <th>Profesor</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($profesori as $k => $profesor){
                $profesor = str_replace("\xc2\xa0", ' ', $profesor);
                $profesor = str_replace("\xE2\x80\x93", '-', $profesor);
                $profesor = trim($profesor);
                $toParse = explode(" ", $profesor);

                $numeIntreg = [];
                for($i = 2; $i < count($toParse); $i++){
                    if($toParse[$i] == "-"){
                        break;
                    }
                    $numeIntreg[] = $toParse[$i];
                }

                $nume = $numeIntreg[count($numeIntreg)-1];
                $prenume = "";
                $username = "";
                for($i = 0; $i < count($numeIntreg)-1; $i++){
                    $prenume = $prenume . $numeIntreg[$i] . " ";
                    $username = $username . $numeIntreg[$i][0];
                }
                $prenume = trim($prenume);
                $normalizeDiacritice = array(
                    'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
                );
                $username = $username . $nume;
                $username = strtr($username, $normalizeDiacritice);
                $username = strtolower($username);
                $email = isset($emails[$k]) ? $emails[$k] : "";

                echo "<tr>
                        <td>" . htmlspecialchars($profesor) . "</td>
                        <td> 
                            <a class='btn btn-outline-primary btn-sm' href='create-user.php?type=profesor&nume=" . urlencode($nume) . "&prenume=". urlencode($prenume) . "&email=" . urlencode($email) . "&username=" . urlencode($username) . "&id=" . urlencode($username) . "'>
                                Înregistrează utilizator
                            </a>
                        </td>
                      </tr>";
            }
            echo "</tbody></table>";
        }
        curl_close($ch);
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>