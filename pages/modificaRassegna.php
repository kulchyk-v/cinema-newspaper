<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout2;
use Camezilla\Pages\Page;
use App\Services\ArticleService;
use App\Components\BackButton;

require_user_authentication();

// LOGICA DI SALVATAGGIO MODIFICA (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'salva_rassegna_modificata') {
    $id = intval($_POST['id'] ?? 0);
    $title = $_POST['stampa_title'] ?? '';
    $link = $_POST['stampa_link'] ?? '';
    $date = $_POST['stampa_date'] ?? '';
    $description = $_POST['stampa_description'] ?? '';

    if ($id > 0 && !empty($title) && !empty($link)) {
        try {
            connect_database();
            $camezillaDb = get_database();

            $escaped_title = addslashes($title);
            $escaped_link = addslashes($link);
            $escaped_date = addslashes($date);
            $escaped_desc = addslashes($description);

            // Aggiorna impostando la categoria corretta 'press_review'
            $sql = "UPDATE articles SET title = '$escaped_title', link = '$escaped_link', date = '$escaped_date', description = '$escaped_desc', category = 'press_review' WHERE id = $id";

            $camezillaDb->query($sql);
        } catch (Exception $e) {
            log_error("Errore esecuzione modificaRassegna: " . $e->getMessage());
        }
    }
    header("Location: tabellaAdminRassegna.php");
    exit;
}

// RENDERING GRAFICO DELLA PAGINA (GET)
$page = new class extends Page {

    public function __construct()
    {
        parent::__construct(new MainLayout2("Modifica Rassegna Stampa - Accademia del Cinema"), function () {

            $articleObject = null;
            $id = intval($_GET['id'] ?? 0);

            if ($id <= 0) {
                echo "<div class='main-container'><p style='color:red;'>ID rassegna non valido.</p></div>";
                return;
            }

            try {
                connect_database();
                $articleService = new ArticleService();
                $articleObject = $articleService->get_by_id($id);
            } catch (Exception $e) {
                log_error("Errore lettura tramite ArticleService in modificaRassegna: " . $e->getMessage());
            }

            if (!$articleObject) {
                echo "<div class='main-container' style='padding:20px;'><p style='color:red; font-weight:bold;'>Articolo rassegna non trovato nel database (ID: " . $id . ").</p></div>";
                return;
            }

            $stampa_title = method_exists($articleObject, 'get_title') ? $articleObject->get_title() : '';
            $stampa_link = method_exists($articleObject, 'get_link') ? $articleObject->get_link() : '';
            $stampa_description = method_exists($articleObject, 'get_description') ? $articleObject->get_description() : '';

            $stampa_date = '';
            if (method_exists($articleObject, 'get_date') && $articleObject->get_date() !== null) {
                $dateObj = $articleObject->get_date();
                $stampa_date = ($dateObj instanceof DateTime) ? $dateObj->format('Y-m-d') : $dateObj;
            }
            ?>

            <div class="main-container">

                <div class="tabella-wrapper">

                    <div class="page-header">
                        <h1>Modifica Rassegna Stampa</h1>
                        <p>Aggiorna i dettagli dell'articolo selezionato</p>
                    </div>
                    <form method="post" action="modificaRassegna.php" class="form-aggiunta">
                        <h3>[ ] Modifica Articolo #<?= $id ?></h3>

                        <input type="hidden" name="action_type" value="salva_rassegna_modificata">
                        <input type="hidden" name="id" value="<?= $id ?>">

                        <div class="input-row-utenti" style="display: flex; flex-direction: column; gap: 15px;">
                            <div style="display:flex; flex-direction:column; gap:5px;">
                                <label style="font-size:11px; color:#666; font-weight:700;">TESTATA / TITOLO ARTICOLO</label>
                                <input type="text" name="stampa_title" value="<?= e($stampa_title) ?>" required
                                    style="width: 100%; padding: 10px;">
                            </div>

                            <div style="display:flex; flex-direction:column; gap:5px;">
                                <label style="font-size:11px; color:#666; font-weight:700;">BREVE DESCRIZIONE / RIASSUNTO</label>
                                <input type="text" name="stampa_description" value="<?= e($stampa_description) ?>" required
                                    style="width: 100%; padding: 10px;">
                            </div>

                            <div style="display:flex; flex-direction:column; gap:5px;">
                                <label style="font-size:11px; color:#666; font-weight:700;">LINK ESTERNO ALL'ARTICOLO</label>
                                <input type="url" name="stampa_link" value="<?= e($stampa_link) ?>" required
                                    style="width: 100%; padding: 10px;">
                            </div>

                            <div style="display:flex; flex-direction:column; gap:5px; width: 220px;">
                                <label style="font-size:11px; color:#666; font-weight:700;">DATA DI PUBBLICAZIONE</label>
                                <input type="date" name="stampa_date" value="<?= e($stampa_date) ?>" required
                                    style="padding: 10px;">
                            </div>
                        </div>

                        <div style="margin-top: 25px; display: flex; gap: 10px;">
                            <button type="submit" class="btn-pubblica">Aggiorna Rassegna</button>
                            <a href="tabellaAdminRassegna.php" class="btn-modifica">Annulla</a>
                        </div>
                    </form>

                </div>
            </div>

        <?php
        });
    }
};

echo $page->render();