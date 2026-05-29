<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout2;
use Camezilla\Pages\Page;
use App\Services\ArticleService;

require_user_authentication();

// LOGICA DI SALVATAGGIO MODIFICA (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'salva_video_modificato') {
    $id = intval($_POST['id'] ?? 0);
    $title = $_POST['video_title'] ?? '';
    $link = $_POST['video_link'] ?? '';
    $date = $_POST['video_date'] ?? '';
    $description = $_POST['video_description'] ?? ''; // Recupera descrizione aggiornata

    if ($id > 0 && !empty($title) && !empty($link)) {
        try {
            connect_database();
            $camezillaDb = get_database();

            $escaped_title = addslashes($title);
            $escaped_link = addslashes($link);
            $escaped_date = addslashes($date);
            $escaped_desc = addslashes($description);

            // Aggiorna anche il campo description
            $sql = "UPDATE articles SET title = '$escaped_title', link = '$escaped_link', date = '$escaped_date', description = '$escaped_desc', category = 'video' WHERE id = $id";

            $camezillaDb->query($sql);
        } catch (Exception $e) {
            log_error("Errore esecuzione modificaVideo: " . $e->getMessage());
        }
    }
    header("Location: tabellaAdminVideo.php");
    exit;
}

// RENDERING GRAFICO DELLA PAGINA (GET)
$page = new class extends Page {

    public function __construct()
    {
        parent::__construct(new MainLayout2("Modifica Video - Accademia del Cinema"), function () {

            $videoObject = null;
            $id = intval($_GET['id'] ?? 0);

            if ($id <= 0) {
                echo "<div class='main-container'><p style='color:red;'>ID video non valido.</p></div>";
                return;
            }

            try {
                connect_database();
                $articleService = new ArticleService();
                $videoObject = $articleService->get_by_id($id);
            } catch (Exception $e) {
                log_error("Errore lettura tramite ArticleService in modificaVideo: " . $e->getMessage());
            }

            if (!$videoObject) {
                echo "<div class='main-container' style='padding:20px;'><p style='color:red; font-weight:bold;'>Video non trovato nel database (ID: " . $id . ").</p></div>";
                return;
            }

            // Estrazione dati tramite i getter ufficiali
            $video_title = method_exists($videoObject, 'get_title') ? $videoObject->get_title() : '';
            $video_link = method_exists($videoObject, 'get_link') ? $videoObject->get_link() : '';
            $video_description = method_exists($videoObject, 'get_description') ? $videoObject->get_description() : '';

            $video_date = '';
            if (method_exists($videoObject, 'get_date') && $videoObject->get_date() !== null) {
                $dateObj = $videoObject->get_date();
                $video_date = ($dateObj instanceof DateTime) ? $dateObj->format('Y-m-d') : $dateObj;
            }
            ?>

            <div class="main-container">
                <div class="tabella-wrapper">

                    <div class="page-header">
                        <h1>Modifica Card Video</h1>
                        <p>Aggiorna i dettagli del video streaming selezionato</p>
                    </div>

                    <form method="post" action="modificaVideo.php" class="form-aggiunta">
                        <h3><i class="fa-solid fa-pen-to-square"></i> Modifica Video #<?= $id ?></h3>

                        <input type="hidden" name="action_type" value="salva_video_modificato">
                        <input type="hidden" name="id" value="<?= $id ?>">

                        <div class="input-row-utenti" style="display: flex; flex-direction: column; gap: 15px;">
                            <div style="display:flex; flex-direction:column; gap:5px;">
                                <label style="font-size:11px; color:#666; font-weight:700;">TITOLO CARD VIDEO</label>
                                <input type="text" name="video_title" value="<?= e($video_title) ?>" required
                                    style="width: 100%; padding: 10px;">
                            </div>

                            <div style="display:flex; flex-direction:column; gap:5px;">
                                <label style="font-size:11px; color:#666; font-weight:700;">DESCRIZIONE BREVE</label>
                                <input type="text" name="video_description" value="<?= e($video_description) ?>" required
                                    style="width: 100%; padding: 10px;">
                            </div>

                            <div style="display:flex; flex-direction:column; gap:5px;">
                                <label style="font-size:11px; color:#666; font-weight:700;">LINK STREAMING DEL VIDEO</label>
                                <input type="url" name="video_link" value="<?= e($video_link) ?>" required
                                    style="width: 100%; padding: 10px;">
                            </div>

                            <div style="display:flex; flex-direction:column; gap:5px; width: 220px;">
                                <label style="font-size:11px; color:#666; font-weight:700;">DATA PUBBLICAZIONE</label>
                                <input type="date" name="video_date" value="<?= e($video_date) ?>" required style="padding: 10px;">
                            </div>
                        </div>

                        <div style="margin-top: 25px; display: flex; gap: 10px;">
                            <button type="submit" class="btn-pubblica" style="flex:2; background-color: #dc3545;">Aggiorna
                                Video</button>
                            <a href="tabellaAdminVideo.php" class="btn-modifica"
                                style="flex:1; text-align:center; text-decoration:none; line-height:34px; background-color: #6c757d; color: white; border-radius: 4px;">Annulla</a>
                        </div>
                    </form>

                </div>
            </div>

        <?php
        });
    }
};

echo $page->render();