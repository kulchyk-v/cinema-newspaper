<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout2;
use Camezilla\Pages\Page;

// Forza il login dell'amministratore
require_user_authentication();

$page = new class extends Page {

    public function __construct() {
        parent::__construct(new MainLayout2("Modifica Articolo - Accademia del Cinema"), function () { 
            
            $idArticolo = isset($_GET['id']) ? (int)$_GET['id'] : null;
            $articolo = null;

            if (!$idArticolo || $idArticolo <= 0) {
                echo "<div style='padding:20px; color:#721c24; background:#f8d7da; border:1px solid #f5c6cb; border-radius:4px; margin:20px;'>
                        <i class='fa-solid fa-exclamation-triangle'></i> <strong>Errore:</strong> ID Articolo mancante o non valido nell'URL.
                      </div>";
                return;
            }

            // ==================================================================
            // ESTRAZIONE SICURA DELL'ARTICOLO (STILE GESTIONE UTENTI)
            // ==================================================================
            try {
                connect_database();
                $camezillaDb = get_database();
                
                // Estraiamo l'istanza PDO interna o usiamo il wrapper custom
                $pdo = method_exists($camezillaDb, 'get_pdo') ? $camezillaDb->get_pdo() : $camezillaDb;

                if ($pdo instanceof PDO) {
                    // Estratto tramite PDO nativo con Named Parameter sicuro
                    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id LIMIT 1");
                    $stmt->execute([':id' => $idArticolo]);
                    $articolo = $stmt->fetch(PDO::FETCH_ASSOC);
                } else if (method_exists($camezillaDb, 'query')) {
                    // Fallback sul metodo query del framework Camezilla
                    $result = $camezillaDb->query("SELECT * FROM articles WHERE id = $idArticolo LIMIT 1");
                    if (is_array($result) && isset($result[0])) {
                        $articolo = $result[0];
                    } elseif (is_object($result) && method_exists($result, 'fetch')) {
                        $articolo = $result->fetch(PDO::FETCH_ASSOC);
                    }
                }
            } catch (Exception $e) {
                log_error("Errore recupero articolo ID $idArticolo: " . $e->getMessage());
            }

            // Se dopo tutti i tentativi l'articolo non c'è, mostriamo un errore pulito
            if (!$articolo) {
                echo "<div style='padding:20px; color:#721c24; background:#f8d7da; border:1px solid #f5c6cb; border-radius:4px; margin:20px;'>
                        <i class='fa-solid fa-search'></i> <strong>Errore di Sincronizzazione:</strong> L'articolo con ID <strong>" . e($idArticolo) . "</strong> non è stato trovato nel database. Verificare la tabella <code>articles</code>.
                      </div>";
                return;
            }
            ?>

            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="modify.css"> 

            <div class="main-container">
                <div class="tabella-wrapper">
                    
                    <div class="page-header">
                        <h1>Modifica Articolo #<?= e($articolo['id']) ?></h1>
                        <p>Aggiorna le informazioni del contenuto selezionato per la Home</p>
                    </div>

                    <form method="post" action="<?= action('article.php', 'update', 'tabellaAdminHome.php') ?>" class="form-aggiunta">
                        <input type="hidden" name="id" value="<?= e($articolo['id']) ?>">

                        <div class="input-row-utenti">
                            <div style="flex:2;">
                                <label style="font-weight:bold; display:block; margin-bottom:5px;">Titolo</label>
                                <input type="text" name="title" required value="<?= e($articolo['title']) ?>">
                            </div>
                            <div style="flex:2;">
                                <label style="font-weight:bold; display:block; margin-bottom:5px;">Sottotitolo / Sommario</label>
                                <input type="text" name="description" required value="<?= e($articolo['description']) ?>">
                            </div>
                            <div style="flex:1;">
                                <label style="font-weight:bold; display:block; margin-bottom:5px;">Data Pubblicazione</label>
                                <input type="date" name="date" required value="<?= e($articolo['date']) ?>">
                            </div>
                        </div>

                        <div style="margin-top: 15px;">
                            <label style="font-weight:bold; display:block; margin-bottom:5px;">Corpo del Testo dell'Articolo</label>
                            <textarea name="text" rows="6" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;" required><?= e($articolo['text']) ?></textarea>
                        </div>

                        <input type="hidden" name="category" value="<?= e($articolo['category']) ?>">
                        <input type="hidden" name="priority_level" value="<?= e($articolo['priority_level']) ?>">
                        <input type="hidden" name="author" value="<?= e($articolo['author']) ?>">
                        <input type="hidden" name="image" value=" "> <input type="hidden" name="views_number" value="<?= e($articolo['views_number']) ?>">
                        
                        <input type="hidden" name="link" value="<?= !empty($articolo['link']) ? e($articolo['link']) : 'https://localhost' ?>">

                        <div style="margin-top: 20px; display: flex; gap: 10px;">
                            <button type="submit" class="btn-pubblica" style="background:#28a745;">Salva Modifiche</button>
                            <a href="tabellaAdminHome.php" class="btn-modifica" style="text-align:center; text-decoration:none; line-height:38px; background:#6c757d; color:white; padding: 0 20px; border-radius:4px;">Annulla</a>
                        </div>
                    </form>

                </div>
            </div>
            
        <?php 
        });
    }
};

echo $page->render();