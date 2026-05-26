<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout2;
use Camezilla\Pages\Page;

// Protezione della pagina: accessibile solo agli utenti autenticati
require_user_authentication();

$page = new class extends Page {

    public function __construct() {
        parent::__construct(new MainLayout2("Modifica Notizia - Accademia del Cinema"), function () { 
            
            // 1. Recupero dell'articolo corrente tramite ID passato in GET
            $articolo = null;
            $id = $_GET['id'] ?? null;

            if (!$id) {
                echo "<div class='main-container'><p style='color:red;'>ID notizia mancante o non valido.</p></div>";
                return;
            }

            try {
                connect_database();
                $pdo = method_exists(get_database(), 'get_pdo') ? get_database()->get_pdo() : get_database();
                
                if ($pdo instanceof PDO) {
                    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id LIMIT 1");
                    $stmt->execute([':id' => $id]);
                    $articolo = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            } catch (Exception $e) {
                log_error("Errore recupero notizia per modifica: " . $e->getMessage());
            }

            if (!$articolo) {
                echo "<div class='main-container'><p style='color:red;'>Notizia non trovata nel database.</p></div>";
                return;
            }
            ?>


            <div class="main-container">
                <div class="tabella-wrapper">
                    
                    <div class="page-header">
                        <h1>Modifica Notizia</h1>
                        <p>Aggiorna i dettagli della notizia selezionata per la Home</p>
                    </div>

                    <form method="post" action="<?= action('article.php', 'update', 'tabellaAdminHome.php') ?>" class="form-aggiunta">
                        <h3><i class="fa-solid fa-pen-to-square"></i> Modifica Record #<?= e($articolo['id']) ?></h3>
                        
                        <input type="hidden" name="id" value="<?= e($articolo['id']) ?>">

                        <div class="input-row-utenti">
                            <div style="flex: 2; display:flex; flex-direction:column; gap:5px;">
                                <label style="font-size:12px; color:#555; font-weight:600;">TITOLO NOTIZIA</label>
                                <input type="text" name="title" value="<?= e($articolo['title']) ?>" required>
                            </div>
                            <div style="flex: 2; display:flex; flex-direction:column; gap:5px;">
                                <label style="font-size:12px; color:#555; font-weight:600;">SOTTOTITOLO / SOMMARIO</label>
                                <input type="text" name="description" value="<?= e($articolo['description']) ?>" required>
                            </div>
                            <div style="display:flex; flex-direction:column; gap:5px;">
                                <label style="font-size:12px; color:#555; font-weight:600;">DATA</label>
                                <input type="date" name="date" value="<?= e($articolo['date']) ?>" required>
                            </div>

                            <input type="hidden" name="category" value="meeting"> 
                            <input type="hidden" name="priority_level" value="<?= e($articolo['priority_level'] ?? '1') ?>">
                            <input type="hidden" name="author" value="<?= e($articolo['author'] ?? 'Admin') ?>">
                        </div>
                        
                        <div style="margin-top: 15px; display: flex; gap: 15px; align-items: center;">
                            <div style="flex: 3; display:flex; flex-direction:column; gap:5px;">
                                <label style="font-size:12px; color:#555; font-weight:600;">FILE IMMAGINE CORRENTE o NUOVA</label>
                                <input type="text" name="image" value="<?= e($articolo['image']) ?>" placeholder="es. copertina.jpg">
                            </div>
                            <div style="flex: 2; display:flex; flex-direction:column; gap:5px;">
                                <label style="font-size:12px; color:#555; font-weight:600;">LINK ESTERNO (OPZIONALE)</label>
                                <input type="text" name="link" value="<?= e($articolo['link']) ?>">
                            </div>
                        </div>

                        <div style="margin-top: 15px; display:flex; flex-direction:column; gap:5px;">
                            <label style="font-size:12px; color:#555; font-weight:600;">TESTO COMPLETO DELL'ARTICOLO</label>
                            <textarea name="text" rows="5" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;" required><?= e($articolo['text']) ?></textarea>
                        </div>
                        
                        <div style="margin-top: 20px; display: flex; gap: 10px;">
                            <button type="submit" class="btn-pubblica" style="flex:2; background-color: #28a745;">Salva Modifiche</button>
                            <a href="tabellaAdminHome.php" class="btn-modifica" style="flex:1; text-align:center; text-decoration:none; line-height:28px;">Annulla</a>
                        </div>
                    </form>

                </div>
            </div>
            
        <?php 
        });
    }
};

echo $page->render();