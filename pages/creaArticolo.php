<?php
require_once __DIR__ . '/../camezilla/camezilla.php';
require_user_authentication();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? ''; 
    $text        = $_POST['text'] ?? '';
    $date        = $_POST['date'] ?? date('Y-m-d');

    if (!empty($title)) {
        try {
            connect_database();
            $camezillaDb = get_database();

            $escaped_title = addslashes($title);
            $escaped_desc  = addslashes($description);
            $escaped_text  = addslashes($text);
            $escaped_date  = addslashes($date);

            // Inserimento pulito senza link e immagini (lasciati vuoti)
            // Livello bloccato a 'low' e categoria bloccata a 'meeting'
            $sql = "INSERT INTO articles (title, description, text, date, category, priority_level, author, views_number, link, image) 
                    VALUES ('$escaped_title', '$escaped_desc', '$escaped_text', '$escaped_date', 'meeting', 'low', 'Admin', 0, '', '')";

            $camezillaDb->query($sql);
        } catch (Exception $e) {
            log_error("Errore salvataggio creaArticolo: " . $e->getMessage());
        }
    }
}

header("Location: tabellaAdminArticoli.php");
exit;