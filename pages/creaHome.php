<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

// Forza l'autenticazione dell'utente amministratore
require_user_authentication();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title          = $_POST['title'] ?? '';
    $description    = $_POST['description'] ?? '';
    $date           = $_POST['date'] ?? date('Y-m-d');
    $text           = $_POST['text'] ?? '';
    $author         = $_POST['author'] ?? 'Admin'; // <-- ORA RECUPERA L'AUTORE DAL FORM
    $priority_level = $_POST['priority_level'] ?? 'high'; 
    
    // Default stabili richiesti dal database/struttura
    $category       = 'meeting'; 
    $link           = 'https://localhost'; 

    if (!empty($title)) {
        try {
            connect_database();
            $camezillaDb = get_database();

            // Sanificazione vecchio stile stabile
            $escaped_title    = addslashes($title);
            $escaped_desc     = addslashes($description);
            $escaped_date     = addslashes($date);
            $escaped_text     = addslashes($text);
            $escaped_author   = addslashes($author); // <-- SANIFICA L'AUTORE
            $escaped_priority = addslashes($priority_level); 
            $escaped_link     = addslashes($link);

            // Inserimento pulito inclusa la priorità e l'autore corretto
            $sql = "INSERT INTO articles (title, description, date, text, category, link, priority_level, author, views_number) 
                    VALUES ('$escaped_title', '$escaped_desc', '$escaped_date', '$escaped_text', '$category', '$escaped_link', '$escaped_priority', '$escaped_author', 0)";

            $camezillaDb->query($sql);
        } catch (Exception $e) {
            log_error("Errore inserimento creaHome: " . $e->getMessage());
        }
    }
}

// Ritorna alla tabella admin della Home
header("Location: tabellaAdminHome.php");
exit;