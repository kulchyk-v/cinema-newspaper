<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout2;
use Camezilla\Pages\Page;
use App\Components\BackButton;

// Forza l'autenticazione dell'utente amministratore
require_user_authentication();

$page = new class extends Page {

    public function __construct() {
        parent::__construct(new MainLayout2("Gestione Rassegna Stampa - Accademia del Cinema"), function () { 
            
            // RECUPERO RUOLO UTENTE DALLA SESSIONE
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $current_user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'viewer'; 
            $isViewer = (strtolower($current_user_role) === 'viewer');

            // Estrazione diretta tramite PDO per evitare i vincoli dell'Enum del Repository
            $stampaList = [];
            try {
                connect_database();
                $camezillaDb = get_database();
                
                $sql = "SELECT * FROM articles WHERE LOWER(category) = 'press_review' ORDER BY date DESC";
                
                $result = $camezillaDb->query($sql);
                if (is_array($result)) {
                    $stampaList = $result;
                } elseif (is_object($result) && method_exists($result, 'fetchAll')) {
                    $stampaList = $result->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch (Exception $e) {
                log_error("Errore estrazione rassegna stampa: " . $e->getMessage());
                $stampaList = []; 
            }
            ?>

            <div class="main-container">

                <div class="tabella-wrapper">
                    
                    <div class="page-header">
                        <h1>Archivio Rassegna Stampa</h1>
                        <p><?= $isViewer ? "Consultazione dell'archivio degli articoli giornalistici esterni (Sola Lettura)" : "Gestisci gli articoli di giornale, i link esterni e le pubblicazioni sulla scuola" ?></p>
                    </div>

                    <?php if (!$isViewer): ?>
                        <form method="post" action="creaRassegna.php" class="form-aggiunta">
                            <h3><i class="fa-regular fa-newspaper"></i> Aggiungi Nuova Rassegna Stampa</h3>
                            <div class="input-row-utenti">
                                <input type="text" name="stampa_title" placeholder="Titolo dell'Articolo / Testata Giornalistica" required>
                                <input type="url" name="stampa_link" placeholder="URL dell'Articolo (es. https://corriere.it/... )" required>
                                <input type="date" name="stampa_date" required value="<?= date('Y-m-d') ?>">
                            </div>
                            
                            <div class="input-row-utenti">
                                <input type="text" name="stampa_description" placeholder="Inserisci un breve riassunto o sottotitolo dell'articolo..." required>
                            </div>

                            <button type="submit" class="btn-pubblica">Pubblica Rassegna</button>
                        </form>
                    <?php else: ?>
                        <div style="background-color: #e2f0fe; color: #185494; padding: 12px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #b8daff; display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid fa-circle-info"></i>
                            <span><strong>Modalità Lettura:</strong> Non disponi dei permessi per aggiungere pubblicazioni alla rassegna stampa.</span>
                        </div>
                    <?php endif; ?>

                    <div class="tabella-container">
                        <div class="tabella-header-utenti" style="grid-template-columns: 1.2fr 1.2fr 1.2fr 0.6fr 0.8fr;">
                            <span>TESTATA / TITOLO</span>
                            <span>BREVE DESCRIZIONE</span>
                            <span>LINK ARTICOLO</span>
                            <span>DATA</span>
                            <span>STATUS / AZIONI</span>
                        </div>

                        <?php if (!empty($stampaList) && is_array($stampaList)): ?>
                            <?php foreach ($stampaList as $item): ?>
                                <div class="riga-tabella-utenti" style="grid-template-columns: 1.2fr 1.2fr 1.2fr 0.6fr 0.8fr;">
                                    <div class="col-utente">
                                        📰 <span class="nome-completo"><?= e($item['title'] ?? '') ?></span>
                                    </div>
                                    <div class="col-email">
                                        <?= e(!empty($item['description']) ? $item['description'] : 'Nessuna descrizione') ?>
                                    </div>
                                    <div class="col-email">
                                        <a href="<?= e($item['link'] ?? '') ?>" target="_blank" style="color: #007bff; text-decoration: none;">
                                            Vedi Link Esterno
                                        </a>
                                    </div>
                                    <div class="col-email">
                                        <?= !empty($item['date']) ? date('d/m/Y', strtotime($item['date'])) : '' ?>
                                    </div>
                                    <div class="col-azioni">
                                        <?php if (!$isViewer): ?>
                                            <a href="modificaRassegna.php?id=<?= $item['id'] ?>" class="btn-pubblica" style="padding: 5px 10px; font-size: 0.8rem; text-decoration:none; width: auto; background: #28a745;">Modifica</a>
                                            
                                            <form method="post" action="<?= action('article.php', 'delete', 'tabellaAdminRassegna.php') ?>" onsubmit="return confirm('Eliminare definitivamente questa rassegna stampa?')" style="margin: 0; display: inline;">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <button type="submit" class="btn-pubblica" style="padding: 5px 10px; font-size: 0.8rem; width: auto; background: #dc3545;">
                                                    Elimina
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
                                <span class="col-email">Nessuna rassegna stampa trovata nel database.</span>
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