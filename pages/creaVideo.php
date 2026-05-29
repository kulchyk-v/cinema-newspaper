<?php
require_once __DIR__ . '/../camezilla/camezilla.php';
require_user_authentication();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['video_title'] ?? '';
    $link = $_POST['video_link'] ?? '';
    $date = $_POST['video_date'] ?? date('Y-m-d');
    $description = $_POST['video_description'] ?? ''; // Recupera la descrizione breve

    if (!empty($title) && !empty($link)) {
        try {
            connect_database();
            $camezillaDb = get_database();

            $escaped_title = addslashes($title);
            $escaped_link = addslashes($link);
            $escaped_date = addslashes($date);
            $escaped_desc = addslashes($description);

            // Inseriamo la descrizione reale fornita dal form
            $sql = "INSERT INTO articles (title, link, date, category, description, text, priority_level, author) 
                    VALUES ('$escaped_title', '$escaped_link', '$escaped_date', 'video', '$escaped_desc', 'Link multimediale', 'low', 'Admin')";

            $camezillaDb->query($sql);
        } catch (Exception $e) {
            log_error("Errore salvataggio creaVideo: " . $e->getMessage());
        }
    }
}

header("Location: tabellaAdminVideo.php");
exit;