<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout2;
use Camezilla\Pages\Page;
use App\Services\ArticleService;

require_user_authentication();

$cancelPage = 'tabellaAdminArticoli.php';

// ==================================================================
// 1. LOGICA DI SALVATAGGIO IN POST (Lanciata premendo Salva Modifiche)
// ==================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'salva_articolo_modificato') {
    $id = intval($_POST['id'] ?? 0);
    $title = $_POST['articolo_title'] ?? '';
    $description = $_POST['articolo_description'] ?? '';
    $text = $_POST['articolo_text'] ?? '';
    $date = $_POST['articolo_date'] ?? '';

    if ($id > 0 && !empty($title)) {
        try {
            connect_database();
            $camezillaDb = get_database();
            $pdo = method_exists($camezillaDb, 'get_pdo') ? $camezillaDb->get_pdo() : $camezillaDb;

            // BLOCCO DI SICUREZZA IN POST: Impedisce il salvataggio se l'articolo sul DB è HIGH
            if ($pdo instanceof PDO) {
                $checkStmt = $pdo->prepare("SELECT priority_level FROM articles WHERE id = ? LIMIT 1");
                $checkStmt->execute([$id]);
                $currentPriority = $checkStmt->fetchColumn();
                
                // Controllo flessibile nel caso in cui sul DB sia memorizzato come stringa
                if ($currentPriority && strtolower($currentPriority) === 'high') {
                    header("Location: " . $cancelPage);
                    exit;
                }
            }

            $escaped_title = addslashes($title);
            $escaped_desc = addslashes($description);
            $escaped_text = addslashes($text);
            $escaped_date = addslashes($date);

            // Aggiorna mantenendo i filtri di categoria e priorità originari di sicurezza
            $sql = "UPDATE articles 
                    SET title = '$escaped_title', 
                        description = '$escaped_desc', 
                        text = '$escaped_text', 
                        date = '$escaped_date' 
                    WHERE id = $id AND category = 'meeting' AND priority_level = 'low'";

            $camezillaDb->query($sql);
        } catch (Exception $e) {
            log_error("Errore esecuzione modifica_articolo SQL: " . $e->getMessage());
        }
    }

    header("Location: " . $cancelPage);
    exit;
}

// ==================================================================
// 2. RENDERING GRAFICO IN GET (Lanciata cliccando sul bottone Modifica)
// ==================================================================
$page = new class extends Page {

    public function __construct()
    {
        global $cancelPage;

        parent::__construct(new MainLayout2("Modifica Articolo - Accademia del Cinema"), function () use ($cancelPage) {

            $articoloObject = null;
            $id = intval($_GET['id'] ?? 0);

            if ($id <= 0) {
                echo "<div class='main-container'><p style='color:red; font-weight:bold; padding:20px;'>ID articolo non valido.</p></div>";
                return;
            }

            try {
                connect_database();
                $articleService = new ArticleService();
                $articoloObject = $articleService->get_by_id($id);
            } catch (Exception $e) {
                log_error("Errore lettura tramite ArticleService in modifica_articolo: " . $e->getMessage());
            }

            if (!$articoloObject) {
                echo "<div class='main-container' style='padding:20px;'><p style='color:red; font-weight:bold;'>Record non trovato nel database (ID: " . $id . ").</p></div>";
                return;
            }

            // Estrazione sicura dei dati testuali e delle proprietà dell'oggetto
            $title = method_exists($articoloObject, 'get_title') ? $articoloObject->get_title() : '';
            $description = method_exists($articoloObject, 'get_description') ? $articoloObject->get_description() : '';
            $text = method_exists($articoloObject, 'get_text') ? $articoloObject->get_text() : '';
            
            // ESTRAZIONE SICURA DALL'OGGETTO ENUM PriorityLevel
            $priority = 'low';
            if (method_exists($articoloObject, 'get_priority_level')) {
                $priorityObj = $articoloObject->get_priority_level();
                if (is_object($priorityObj)) {
                    // Se è un Enum, estraiamo la stringa tramite ->value o ->name
                    $priority = $priorityObj->value ?? $priorityObj->name ?? 'low';
                } else {
                    $priority = $priorityObj;
                }
            }
            $isHighPriority = (strtolower((string)$priority) === 'high');

            $date_formatted = '';
            if (method_exists($articoloObject, 'get_date') && $articoloObject->get_date() !== null) {
                $dateObj = $articoloObject->get_date();
                $date_formatted = ($dateObj instanceof DateTime) ? $dateObj->format('Y-m-d') : $dateObj;
            }
            ?>

            <div class="main-container">
                <div class="tabella-wrapper">

                    <div class="back-button-wrapper" style="margin-bottom: 20px; text-align: left;">
                    </div>

                    <div class="page-header">
                        <h1>Modifica Articolo</h1>
                        <p>Aggiorna il testo e la data dell'articolo selezionato</p>
                    </div>

                    <?php if ($isHighPriority): ?>
                        <div class="error-message" style="
                            background-color: #f8d7da; 
                            color: #721c24; 
                            padding: 15px; 
                            margin-bottom: 25px; 
                            border-radius: 6px; 
                            border: 1px solid #f5c6cb; 
                            display: flex; 
                            align-items: center; 
                            gap: 12px;
                            font-size: 0.95rem;
                        ">
                            <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.3rem;"></i>
                            <span>
                                <strong>Impossibile salvare le modifiche:</strong> Questo articolo è impostato come contenuto di <strong>Alto Livello (High)</strong> e non può essere sovrascritto o modificato da questa sezione.
                            </span>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="modifica_articolo.php" class="form-aggiunta">
                        <h3><i class="fa-solid fa-pen-to-square"></i> Modifica Articolo #<?= $id ?></h3>

                        <input type="hidden" name="action_type" value="salva_articolo_modificato">
                        <input type="hidden" name="id" value="<?= $id ?>">

                        <div class="input-row-utenti" style="display: flex; flex-direction: column; gap: 15px;">

                            <div style="display:flex; flex-direction:column; gap:5px;">
                                <label style="font-size:11px; color:#666; font-weight:700;">TITOLO ARTICOLO</label>
                                <input type="text" name="articolo_title" value="<?= e($title) ?>" required <?= $isHighPriority ? 'disabled' : '' ?>
                                    style="width: 100%; padding: 10px; box-sizing: border-box; <?= $isHighPriority ? 'background-color:#e9ecef; cursor:not-allowed;' : '' ?>">
                            </div>

                            <div style="display:flex; flex-direction:column; gap:5px;">
                                <label style="font-size:11px; color:#666; font-weight:700;">DESCRIZIONE BREVE</label>
                                <input type="text" name="articolo_description" value="<?= e($description) ?>" required <?= $isHighPriority ? 'disabled' : '' ?>
                                    style="width: 100%; padding: 10px; box-sizing: border-box; <?= $isHighPriority ? 'background-color:#e9ecef; cursor:not-allowed;' : '' ?>">
                            </div>

                            <div style="display:flex; flex-direction:column; gap:5px;">
                                <label style="font-size:11px; color:#666; font-weight:700;">TESTO DELL'ARTICOLO</label>
                                <textarea name="articolo_text" rows="8" required <?= $isHighPriority ? 'disabled' : '' ?>
                                    style="width: 100%; padding: 10px; box-sizing: border-box; font-family: inherit; <?= $isHighPriority ? 'background-color:#e9ecef; cursor:not-allowed;' : '' ?>"><?= e($text) ?></textarea>
                            </div>

                            <div style="display:flex; flex-direction:column; gap:5px; width: 220px;">
                                <label style="font-size:11px; color:#666; font-weight:700;">DATA PUBBLICAZIONE</label>
                                <input type="date" name="articolo_date" value="<?= e($date_formatted) ?>" required <?= $isHighPriority ? 'disabled' : '' ?>
                                    style="padding: 10px; <?= $isHighPriority ? 'background-color:#e9ecef; cursor:not-allowed;' : '' ?>">
                            </div>
                        </div>

                        <div style="margin-top: 25px; display: flex; gap: 10px;">
                            <?php if (!$isHighPriority): ?>
                                <button type="submit" class="btn-pubblica"
                                    style="flex:2; background-color: #007bff; color:white; border:none; padding:12px; border-radius:4px; font-weight:bold; cursor:pointer;">
                                    Salva Modifiche
                                </button>
                            <?php endif; ?>
                            
                            <a href="<?= $cancelPage ?>" class="btn-modifica"
                                style="flex:1; text-align:center; text-decoration:none; line-height:38px; background-color: #6c757d; color: white; border-radius: 4px; font-weight: bold;">
                                <?= $isHighPriority ? 'Torna Indietro' : 'Annulla' ?>
                            </a>
                        </div>
                    </form>

                </div>
            </div>

        <?php
        });
    }
};

echo $page->render();