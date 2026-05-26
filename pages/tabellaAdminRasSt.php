<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout2;
use Camezilla\Pages\Page;

$page = new class extends Page {

    public function __construct() {
        parent::__construct(new MainLayout2("Rassegna Stampa - Accademia del Cinema"), function () { 
            
            $rassegne = [];
            try {
                connect_database();
                $camezillaDb = get_database();
                $pdo = method_exists($camezillaDb, 'get_pdo') ? $camezillaDb->get_pdo() : $camezillaDb;
                
                if ($pdo instanceof PDO) {
                    $stmt = $pdo->query("SELECT * FROM press_reviews ORDER BY date DESC");
                    $rassegne = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch (Exception $e) {
                log_error("Errore estrazione rassegna: " . $e->getMessage());
            }
            ?>

            <link class="cssdeck" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="modify.css"> 

            <div class="main-container">
                <div class="tabella-wrapper">
                    
                    <div class="page-header">
                        <h1>Rassegna Stampa</h1>
                        <p>Gestisci le testate giornalistiche e le pubblicazioni esterne</p>
                    </div>

                    <?php if (get_action_error()): ?>
                        <div class="error-message" style="background-color: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #f5c6cb;">
                            <?= e(get_action_error(true)) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (get_action_success()): ?>
                        <div class="success-message" style="background-color: #d4edda; color: #155724; padding: 12px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #c3e6cb;">
                            <?= e(get_action_success(true)) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= action('review.php', 'create', 'tabellaAdminRasSt.php') ?>" class="form-aggiunta">
                        <h3><i class="fa-solid fa-plus-circle"></i> Aggiungi Ritaglio Stampa</h3>
                        <div class="input-row-utenti">
                            <input type="text" name="source" placeholder="Testata (es. Corriere della Sera)" required>
                            <input type="text" name="title" placeholder="Titolo articolo/estratto" required style="flex: 2;">
                            <input type="date" name="date" required>
                        </div>
                        <button type="submit" class="btn-pubblica" style="margin-top: 10px;">Inserisci</button>
                    </form>

                    <div class="tabella-container">
                        <div class="tabella-header-utenti">
                            <span>TESTATA</span>
                            <span style="flex: 2;">TITOLO CRITICA</span>
                            <span>DATA</span>
                            <span>AZIONI</span>
                        </div>

                        <?php if (!empty($rassegne)): ?>
                            <?php foreach ($rassegne as $ras): ?>
                                <div class="riga-tabella-utenti">
                                    <div class="col-email" style="font-weight: 600; color: #222;"><?= e($ras['source']) ?></div>
                                    <div class="col-utente" style="flex: 2;">
                                        <span class="nome-completo" style="font-weight: normal;"><?= e($ras['title']) ?></span>
                                    </div>
                                    <div class="col-email"><?= date('d/m/Y', strtotime($ras['date'])) ?></div>
                                    <div class="col-azioni" style="display: flex; gap: 10px; align-items: center;">
                                        <a href="modifica_rassegna.php?id=<?= $ras['id'] ?>" class="btn-modifica" style="text-decoration:none;">Modifica</a>
                                        
                                        <form method="post" action="<?= action('review.php', 'delete', 'tabellaAdminRasSt.php') ?>" onsubmit="return confirm('Eliminare questa rassegna?')" style="margin: 0; padding: 0; display: inline;">
                                            <input type="hidden" name="id" value="<?= $ras['id'] ?>">
                                            <button type="submit" class="btn-elimina" style="border: none; background: none; padding: 0; cursor: pointer;">
                                                <i class="fa-solid fa-trash" style="color: #dc3545;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="riga-tabella-utenti" style="justify-content: center; padding: 20px; color: #888;">
                                <span><i class="fa-solid fa-info-circle"></i> Nessun dato in rassegna stampa.</span>
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