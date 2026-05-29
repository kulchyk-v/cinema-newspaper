<?php
require_once __DIR__ . '/../camezilla/camezilla.php';
require_user_authentication();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['stampa_title'] ?? '';
    $link = $_POST['stampa_link'] ?? '';
    $date = $_POST['stampa_date'] ?? date('Y-m-d');
    $description = $_POST['stampa_description'] ?? '';

    if (!empty($title) && !empty($link)) {
        try {
            connect_database();
            $camezillaDb = get_database();

            $escaped_title = addslashes($title);
            $escaped_link = addslashes($link);
            $escaped_date = addslashes($date);
            $escaped_desc = addslashes($description);

            // Salviamo usando 'press_review'
            $sql = "INSERT INTO articles (title, link, date, category, description, text, priority_level, author) 
                    VALUES ('$escaped_title', '$escaped_link', '$escaped_date', 'press_review', '$escaped_desc', 'Rassegna stampa esterna', 'low', 'Admin')";

            $camezillaDb->query($sql);
        } catch (Exception $e) {
            log_error("Errore salvataggio creaRassegna: " . $e->getMessage());
        }
    }
}

header("Location: tabellaAdminRassegna.php");
exit;