<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout2;
use Camezilla\Pages\Page;
use App\Components\BackButton;

// Forza l'autenticazione dell'utente amministratore
require_user_authentication();

$page = new class extends Page {

    public function __construct()
    {
        parent::__construct(new MainLayout2("Modifica Incontro Home - Accademia del Cinema"), function () {

            // Recupero connessione PDO stabile
            connect_database();
            $camezillaDb = get_database();

            // Recupero l'ID dall'URL
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
            $errore = null;

            // --- LOGICA DI SALVATAGGIO DIRETTA (UPDATE) ---
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salva_modifiche'])) {
                $title = $_POST['title'] ?? '';
                $author = $_POST['author'] ?? '';
                $date = $_POST['date'] ?? '';
                $description = $_POST['description'] ?? '';
                $text = $_POST['text'] ?? '';
                $id_articolo = (int) ($_POST['id'] ?? 0);

                if ($id_articolo > 0 && !empty($title)) {
                    try {
                        // Aggiornamento senza toccare né la foto né il priority_level esistenti
                        $stmt_up = $camezillaDb->prepare("
                            UPDATE articles 
                            SET title = ?, author = ?, date = ?, description = ?, text = ?
                            WHERE id = ?
                        ");
                        $stmt_up->execute([$title, $author, $date, $description, $text, $id_articolo]);

                        // Reindirizzamento pulito per aggiornare la tabella di controllo
                        header("Location: tabellaAdminHome.php");
                        exit;

                    } catch (Exception $e) {
                        $errore = "Errore durante il salvataggio: " . $e->getMessage();
                    }
                }
            }

            // --- ESTRAZIONE DATI PER IL FORM ---
            $articolo = null;
            if ($id > 0) {
                try {
                    $stmt = $camezillaDb->prepare("SELECT * FROM articles WHERE id = ?");
                    $stmt->execute([$id]);
                    $articolo = $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (Exception $e) {
                    log_error("Errore recupero per form: " . $e->getMessage());
                }
            }

            // Se l'articolo non esiste nel database
            if (!$articolo) {
                ?>
                <div class="main-container">
                    <div class="tabella-wrapper" style="text-align: center; padding: 40px; font-family: sans-serif;">
                        <h2><i class="fa-solid fa-triangle-exclamation" style="color: #dc3545;"></i> Articolo non trovato</h2>
                        <p>L'articolo richiesto non è presente nel database (ID inserito: <?= $id ?>).</p>
                        <a href="tabellaAdminHome.php"
                            style="display: inline-block; margin-top: 20px; padding: 10px 20px; background: #6366f1; color: #fff; text-decoration: none; border-radius: 4px; font-weight: bold;">Torna
                            alla gestione Home</a>
                    </div>
                </div>
                <?php
                return;
            }
            ?>

            <div class="main-container">
                <div class="tabella-wrapper">

                    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h1>Modifica Incontro</h1>
                            <p>Stai modificando le informazioni dell'articolo <strong>ID #<?= $articolo['id'] ?></strong></p>
                        </div>

                    </div>

                    <?php if ($errore): ?>
                        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                            <i class="fa-solid fa-triangle-exclamation"></i> <?= $errore ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="modificaHome.php?id=<?= $articolo['id'] ?>" class="form-aggiunta">

                        <input type="hidden" name="id" value="<?= $articolo['id'] ?>">

                        <h3><i class="fa-solid fa-pen-to-square"></i> Modifica i campi dell'articolo</h3>

                        <div class="input-row-utenti">
                            <div style="flex: 2; display: flex; flex-direction: column; gap: 5px;">
                                <label style="font-weight: bold; font-size: 13px; color: #555;">Titolo dell'Incontro</label>
                                <input type="text" name="title" value="<?= e($articolo['title'] ?? '') ?>" required
                                    style="width: 100%;">
                            </div>

                            <div style="flex: 1; display: flex; flex-direction: column; gap: 5px;">
                                <label style="font-weight: bold; font-size: 13px; color: #555;">Autore / Relatore</label>
                                <input type="text" name="author" value="<?= e($articolo['author'] ?? 'Admin') ?>" required
                                    style="width: 100%;">
                            </div>

                            <div style="flex: 1; display: flex; flex-direction: column; gap: 5px;">
                                <label style="font-weight: bold; font-size: 13px; color: #555;">Data Pubblicazione</label>
                                <input type="date" name="date" value="<?= e($articolo['date'] ?? '') ?>" required
                                    style="width: 100%;">
                            </div>
                        </div>

                        <div style="margin-top: 20px; margin-bottom: 20px; display: flex; flex-direction: column; gap: 5px;">
                            <label style="font-weight: bold; font-size: 13px; color: #555;">Sottotitolo / Breve descrizione</label>
                            <input type="text" name="description" value="<?= e($articolo['description'] ?? '') ?>" required
                                style="font-size: 16px; padding: 12px; width: 100%; border-radius: 8px; border: 1px solid #ccc; box-sizing: border-box;">
                        </div>

                        <div style="margin-bottom: 25px; display: flex; flex-direction: column; gap: 5px;">
                            <label style="font-weight: bold; font-size: 13px; color: #555;">Testo completo dell'articolo</label>
                            <textarea name="text" rows="6"
                                style="width: 100%; max-width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; box-sizing: border-box; font-family: inherit; resize: vertical; white-space: pre-wrap;"
                                required><?= e($articolo['text'] ?? '') ?></textarea>
                        </div>

                        <div style="display: flex; gap: 15px;">
                            <button type="submit" name="salva_modifiche" class="btn-pubblica"
                                style="background: #28a745; color: #fff; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-size: 15px; font-weight: bold;">
                                <i class="fa-solid fa-floppy-disk"></i> Salva Modifiche
                            </button>
                            <a href="tabellaAdminHome.php"
                                style="padding: 12px 25px; background: #dc3545; color: #fff; text-decoration: none; border-radius: 6px; font-size: 15px; font-weight: bold; font-family: sans-serif;">Annulla</a>
                        </div>
                    </form>

                </div>
            </div>

        <?php
        });
    }
};

echo $page->render();