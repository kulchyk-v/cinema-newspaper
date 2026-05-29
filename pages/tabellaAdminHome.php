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
        parent::__construct(new MainLayout2("Gestione Home - Accademia del Cinema"), function () {

            // RECUPERO RUOLO UTENTE DALLA SESSIONE
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $current_user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'viewer'; 
            $isViewer = (strtolower($current_user_role) === 'viewer');

            // Estrazione mirata per gli incontri in evidenza (Solo category 'meeting' e livelli high/medium)
            $homeList = [];
            try {
                connect_database();
                $camezillaDb = get_database();

                $sql = "SELECT * FROM articles WHERE LOWER(category) = 'meeting' AND LOWER(priority_level) IN ('high', 'medium') ORDER BY date DESC";

                $result = $camezillaDb->query($sql);
                if (is_array($result)) {
                    $homeList = $result;
                } elseif (is_object($result) && method_exists($result, 'fetchAll')) {
                    $homeList = $result->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch (Exception $e) {
                log_error("Errore estrazione articoli home: " . $e->getMessage());
                $homeList = [];
            }
            ?>

            <div class="main-container">
                <div class="tabella-wrapper">

                    <div class="page-header">
                        <h1>Incontri in Evidenza (Home)</h1>
                        <p><?= $isViewer ? "Visualizzazione degli elementi in evidenza della Home Page (Sola Lettura)" : "Gestisci gli articoli di livello Alto o Medio visibili nella Home Page pubblica" ?></p>
                    </div>

                    <?php if (!$isViewer): ?>
                        <form method="post" action="creaHome.php" class="form-aggiunta">
                            <h3><i class="fa-solid fa-star"></i> Aggiungi Nuovo Incontro in Evidenza</h3>
                            <div class="input-row-utenti">
                                <input type="text" name="title" placeholder="Titolo dell'Incontro" required>
                                <input type="text" name="author" placeholder="Autore / Relatore" required>
                                <input type="date" name="date" required value="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="input-row-utenti">
                                <input type="text" name="description" placeholder="Inserisci il sottotitolo o una breve descrizione..."
                                    required>
                            </div>
                            <div class="input-row-utenti">
                                <textarea name="text" placeholder="Testo completo dell'articolo..." rows="4" class="custom-select"
                                    style="height: auto; max-width: 100%; font-family: inherit; resize: vertical; white-space: pre-wrap; word-break: break-word;"
                                    required></textarea>
                            </div>

                            <button type="submit" class="btn-pubblica">Pubblica in Home</button>
                        </form>
                    <?php else: ?>
                        <div style="background-color: #e2f0fe; color: #185494; padding: 12px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #b8daff; display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid fa-circle-info"></i>
                            <span><strong>Modalità Lettura:</strong> Non disponi dei permessi per aggiungere nuovi incontri in evidenza.</span>
                        </div>
                    <?php endif; ?>

                    <div class="tabella-container">
                        <div class="tabella-header-utenti" style="grid-template-columns: 1.5fr 1.5fr 0.6fr 0.6fr 0.8fr;">
                            <span>TITOLO INCONTRO</span>
                            <span>DESCRIZIONE</span>
                            <span>LIVELLO</span>
                            <span>DATA</span>
                            <span>STATUS / AZIONI</span>
                        </div>

                        <?php if (!empty($homeList) && is_array($homeList)): ?>
                            <?php foreach ($homeList as $item):
                                $lvl = strtolower($item['priority_level'] ?? 'high'); ?>
                                <div class="riga-tabella-utenti" style="grid-template-columns: 1.5fr 1.5fr 0.6fr 0.6fr 0.8fr;">
                                    <div class="col-utente">
                                        <i class="fa-solid fa-star" style="color: #ffc107;"></i>
                                        <span class="nome-completo"><?= e($item['title'] ?? '') ?></span>
                                    </div>
                                    <div class="col-email">
                                        <?= e($item['description'] ?? '') ?>
                                    </div>
                                    <div>
                                        <span class="badge badge-<?= $lvl == 'high' ? 'admin' : 'editor' ?>"><?= e($lvl) ?></span>
                                    </div>
                                    <div class="col-email">
                                        <?= !empty($item['date']) ? date('d/m/Y', strtotime($item['date'])) : '' ?>
                                    </div>
                                    <div class="col-azioni">
                                        <?php if (!$isViewer): ?>
                                            <a href="modificaHome.php?id=<?= $item['id'] ?>" class="btn-pubblica"
                                                style="padding: 5px 10px; font-size: 0.8rem; text-decoration:none; width: auto; background: #28a745;">Modifica</a>

                                            <form method="post" action="<?= action('article.php', 'delete', 'tabellaAdminHome.php') ?>"
                                                onsubmit="return confirm('Rimuovere questo articolo dalla Home?')" style="margin: 0; display: inline;">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <button type="submit" class="btn-pubblica"
                                                    style="padding: 5px 10px; font-size: 0.8rem; width: auto; background: #dc3545;">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: #6c757d; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px;">
                                                <i class="fa-solid fa-lock" style="color: #ffc107;"></i> Sola lettura
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="riga-tabella-utenti" style="grid-template-columns: 1fr; justify-content: center;">
                                <span class="col-email">Nessun incontro in evidenza trovato nel database.</span>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        <?php
        });
    }
};

echo $page->render();