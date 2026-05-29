<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout2;
use Camezilla\Pages\Page;

// Forza l'autenticazione dell'utente amministratore
require_user_authentication();

$page = new class extends Page {

    public function __construct() {
        parent::__construct(new MainLayout2("Gestione Articoli - Accademia del Cinema"), function () { 
            
            // 1. RECUPERO RUOLO UTENTE DALLA SESSIONE
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $current_user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'viewer'; 
            $isViewer = (strtolower($current_user_role) === 'viewer');

            // Estrazione di tutti gli articoli della categoria 'meeting'
            $articoliList = [];
            try {
                connect_database();
                $camezillaDb = get_database();
                
                $sql = "SELECT * FROM articles WHERE LOWER(category) = 'meeting' ORDER BY date DESC";
                
                $result = $camezillaDb->query($sql);
                if (is_array($result)) {
                    $articoliList = $result;
                } elseif (is_object($result) && method_exists($result, 'fetchAll')) {
                    $articoliList = $result->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch (Exception $e) {
                log_error("Errore estrazione articoli: " . $e->getMessage());
                $articoliList = []; 
            }
            ?>
            
            <div class="main-container">
                <div class="tabella-wrapper">
                    
                    <div class="page-header">
                        <h1>Tutti gli Articoli</h1>
                        <p><?= $isViewer ? "Visualizzazione dell'archivio delle notizie (Sola Lettura)" : "Gestisci l'intero archivio testuale delle notizie (Livello Alto, Medio e Basso)" ?></p>
                    </div>

                    <?php if (!$isViewer): ?>
                        <form method="post" action="creaArticolo.php" class="form-aggiunta">
                            <h3><i class="fa-regular fa-newspaper"></i> Aggiungi Nuovo Articolo</h3>
                            
                            <div class="input-row-utenti">
                                <input type="text" name="title" placeholder="Titolo dell'Articolo" required>
                                <input type="date" name="date" required value="<?= date('Y-m-d') ?>">
                            </div>
                            
                            <div class="input-row-utenti">
                                <input type="text" name="description" placeholder="Inserisci una breve descrizione o sommario..." required>
                            </div>

                            <div class="input-row-utenti">
                                <textarea name="text" placeholder="Scrivi il corpo e il testo completo dell'articolo qui..." rows="5" class="custom-select" style="height: auto; font-family: inherit;" required></textarea>
                            </div>

                            <button type="submit" class="btn-pubblica">Pubblica Articolo</button>
                        </form>
                    <?php else: ?>
                        <div style="background-color: #e2f0fe; color: #185494; padding: 12px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #b8daff; display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid fa-circle-info"></i>
                            <span><strong>Modalità Lettura:</strong> Non hai i permessi per aggiungere o pubblicare nuovi articoli.</span>
                        </div>
                    <?php endif; ?>

                    <div class="tabella-container">
                        <div class="tabella-header-utenti" style="grid-template-columns: 1.5fr 1.5fr 0.6fr 0.6fr 0.8fr;">
                            <span>TITOLO ARTICOLO</span>
                            <span>DESCRIZIONE BREVE</span>
                            <span>LIVELLO</span>
                            <span>DATA</span>
                            <span>STATUS / AZIONI</span>
                        </div>

                        <?php if (!empty($articoliList) && is_array($articoliList)): ?>
                            <?php foreach ($articoliList as $item): 
                                $lvl = strtolower($item['priority_level'] ?? 'low'); 
                                $badgeClass = 'viewer';
                                if($lvl === 'high') $badgeClass = 'admin';
                                if($lvl === 'medium') $badgeClass = 'editor';
                            ?>
                                <div class="riga-tabella-utenti" style="grid-template-columns: 1.5fr 1.5fr 0.6fr 0.6fr 0.8fr;">
                                    
                                    <div class="col-utente">
                                        <i class="fa-regular fa-file-lines"></i>
                                        <span class="nome-completo"><?= e($item['title'] ?? '') ?></span>
                                    </div>
                                    
                                    <div class="col-email">
                                        <?= e($item['description'] ?? '') ?>
                                    </div>
                                    
                                    <div>
                                        <span class="badge badge-<?= $badgeClass ?>"><?= e($lvl) ?></span>
                                    </div>
                                    
                                    <div class="col-email">
                                        <?= !empty($item['date']) ? date('d/m/Y', strtotime($item['date'])) : '' ?>
                                    </div>
                                    
                                    <div class="col-azioni">
                                        <?php if (!$isViewer): ?>
                                            <a href="modifica_articolo.php?id=<?= $item['id'] ?>" class="btn-pubblica" style="padding: 5px 10px; font-size: 0.8rem; text-decoration:none; width: auto; background: #28a745;">Modifica</a>
                                            
                                            <form method="post" action="<?= action('article.php', 'delete', 'tabellaAdminArticoli.php') ?>" onsubmit="return confirm('Eliminare definitivamente questo articolo?')" style="margin: 0; display: inline;">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <button type="submit" class="btn-pubblica" style="padding: 5px 10px; font-size: 0.8rem; width: auto; background: #dc3545;">
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
                                <span class="col-email"><i class="fa-solid fa-folder-open"></i> Nessun articolo trovato nel database per questa categoria.</span>
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