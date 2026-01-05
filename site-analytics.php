<?php

    require_once 'Database.php';
    require_once 'auth.php';
    require_once 'fpdf.php';

    if(auth::$user_type < 3){
        header("Location: login-page.php");
    }

    try {
        $pdo = Database::getInstance()->getConnection();
        $sqldrop = "DELETE FROM logs WHERE timestamp <= NOW() - INTERVAL 1 DAY;";
        $stmtdrop = $pdo->prepare($sqldrop);
        $stmtdrop->execute();
        $sql = "SELECT user, IP, page, timestamp FROM logs";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $header = ["user", "IP", "page", "timestamp"];
        $pdf = new FPDF();
        $pdf->SetLeftMargin(24);
        $pdf->AddPage();
        $width = 1280;
        $height = 600;
        $image = imagecreate($width, $height);
        $white = imagecolorallocate($image, 255, 255, 255);
        $blue  = imagecolorallocate($image, 0, 0, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $bar_width = 50;
        $margin = 3;
        $x = 20;
        $max = 0;
        $pages = [
            "asociat-profesori.php" => 0,
            "profesori-unibuc.php" => 0,
            "create-anunt.php" => 0,
            "create-user.php" => 0,
            "read-anunturi.php" => 0,
            "read-note.php" => 0,
            "set-note.php" => 0
        ];
        foreach ($logs as $log) {
            $pages[$log["page"]] ++;
            $max = max($max, $pages[$log["page"]]);
        }
        
        $scaling = 0;
        switch (true){
            case $max <=25:
                $scaling = 20;
                break;
            case $max <=50:
                $scaling = 10;
                break;
            case $max <=100:
                $scaling = 5;
                break;
            case $max <=275:
                $scaling = 2;
                break;
            case $max <=550:
                $scaling = 1;
                break;
            case $max <=1100:
                $scaling = 0.5;
                break;
            case $max >=1101:
                $scaling = 0.25;
                break;
        }


        foreach ($pages as $page => $visits) {

            $bar_height = $visits*$scaling; 

            imagefilledrectangle($image, $x, $height - $bar_height - 20, $x + $bar_width, $height - 20, $blue);
            
            imagestring($image, 3, $x, $height - 15, $page . ": " . $visits, $black);
            
            $x += $bar_width + $margin + (strlen($page)*6) + (strlen((string)$visits)*6);
        }

        $temp_image_path = __DIR__ . '/temp_graph_export.png';
        imagepng($image, $temp_image_path);
        imagedestroy($image);

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont("Times", "", 15);
        $pdf->Cell(30, 10, "Reprezentare grafica a traficului fiecarei pagini, in ultimele 24 de ore:", 0, 1, "L", false);

        if (file_exists($temp_image_path)) {
            $pdf->Image($temp_image_path, 10, 20, 190);
        }

        

        $cellWidth = [
            "user" => 45,
            "IP" => 20, 
            "page" => 65, 
            "timestamp" => 35];
        $cellHeight = 6;

        $pdf->SetFillColor(240, 240, 240); 
        
        $pdf->Ln(100);

        

        $pdf->Cell(30, 10, "Tabel cu toti vizitatorii:", 0, 1, "L", false);
        
        $pdf->SetFontSize(10); 

        foreach($header as $headerCol) 
        {
            $pdf->Cell($cellWidth[$headerCol], $cellHeight, $headerCol, 1, 0, "L", true);
        }

        $pdf->Ln();
        $pdf->SetFont("Times", "", 8);
        
        $rowCount = count($logs);
        for($i = 0; $i < $rowCount; $i++) 
        {
            foreach($logs[$i] as $type => $log) 
            {
                if($log == null)
                {
                    $pdf->Cell($cellWidth[$type], $cellHeight, "NULL", 1, 0, "L", false);
                }
                else
                {
                    $pdf->Cell($cellWidth[$type], $cellHeight, $log, 1, 0, "L", false);
                }
            }

            $pdf->Ln();
        }
        
        if (file_exists($temp_image_path)) {
            unlink($temp_image_path); 
        }

        $pdf->Output();
        exit;
        
        
    } catch (PDOException $e){
        die();
    }

    
?>