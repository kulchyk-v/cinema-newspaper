<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout2;
use Camezilla\Pages\Page;

// Forza l'autenticazione dell'utente amministratore
require_user_authentication();

$page = new class extends Page {

    public function __construct() {
        parent::__construct(new MainLayout2("Gestione Home - Accademia del Cinema"), function () { 
            
            // ==================================================================
            // ESTRAZIONE NOTIZIE DINAMICA COMPATIBILE CON CAMEZILLA
            // ==================================================================
            $notizie = [];
            try {
                connect_database();
                $camezillaDb = get_database();
                
                // Estrazione istanza PDO o fallback sul wrapper del framework
                $pdo = method_exists($camezillaDb, 'get_pdo') ? $camezillaDb->get_pdo() : $camezillaDb;
                
                if ($pdo instanceof PDO) {
                    $stmt = $pdo->query("SELECT * FROM articles WHERE category = 'meeting' ORDER BY date DESC, id DESC");
                    $notizie = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else if (method_exists($camezillaDb, 'query')) {
                    $result = $camezillaDb->query("SELECT * FROM articles WHERE category = 'meeting' ORDER BY date DESC, id DESC");
                    if (is_array($result)) {
                        $notizie = $result;
                    } elseif (is_object($result) && method_exists($result, 'fetchAll')) {
                        $notizie = $result->fetchAll(PDO::FETCH_ASSOC);
                    }
                }
            } catch (Exception $e) {
                log_error("Errore estrazione notizie home: " . $e->getMessage());
                $notizie = []; 
            }
            ?>

            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="modify.css"> 

            <div class="main-container">
                <div class="tabella-wrapper">
                    
                    <div class="page-header">
                        <h1>Home</h1>
                        <p>Aggiungi e gestisci Home della testata</p>
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

                    <form method="post" action="<?= action('article.php', 'create', 'tabellaAdminHome.php') ?>" class="form-aggiunta">
                        <h3><i class="fa-solid fa-plus-circle"></i> Nuova Notizia</h3>
                        
                        <div class="input-row-utenti">
                            <input type="text" name="title" placeholder="Titolo" required>
                            <input type="text" name="description" placeholder="Sottotitolo / Sommario" required>
                            <input type="date" name="date" required value="<?= date('Y-m-d') ?>">
                        </div>

                        <div style="margin-top: 15px;">
                            <textarea name="text" placeholder="Scrivi qui il corpo del testo completo..." rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;" required></textarea>
                        </div>

                        <input type="hidden" name="category" value="meeting">
                        <input type="hidden" name="priority_level" value="high">
                        <input type="hidden" name="author" value="Admin">
                        <input type="hidden" name="image" value=" "> <input type="hidden" name="views_number" value="0">
                        <input type="hidden" name="link" value="https://localhost"> <button type="submit" class="btn-pubblica" style="margin-top: 15px;">Pubblica Articolo</button>
                    </form>

                    <div class="tabella-container" style="margin-top: 30px;">
                        <div class="tabella-header-utenti">
                            <span>DATA PUBBLICAZIONE</span>
                            <span style="flex: 2;">DETTAGLI ARTICOLO</span>
                            <span>AZIONI</span>
                        </div>

                        <?php if (!empty($notizie) && is_array($notizie)): ?>
                            <?php foreach ($notizie as $notizia): ?>
                                <?php 
                                    $id          = $notizia['id'] ?? 0;
                                    $title       = $notizia['title'] ?? '';
                                    $description = $notizia['description'] ?? '';
                                    $dateValue   = $notizia['date'] ?? '';
                                ?>
                                <div class="riga-tabella-utenti">
                                    
                                    <div class="col-email" style="font-weight: bold; color: #444;">
                                        <?= !empty($dateValue) ? date('d/m/Y', strtotime($dateValue)) : '' ?>
                                    </div>
                                    
                                    <div class="col-utente" style="flex: 2; display: flex; flex-direction: column; gap: 4px;">
                                        <span class="nome-completo" style="color: #007bff; font-weight: 600;">
                                            <?= e($title) ?>
                                        </span>
                                        <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                            <?= e($description) ?>
                                        </p>
                                    </div>
                                    
                                    <div class="col-azioni" style="display: flex; gap: 15px; align-items: center;">
                                        <a href="modifica_articolo.php?id=<?= $id ?>" class="btn-modifica" style="text-decoration:none;">Modifica</a>
                                        
                                        <form method="post" action="<?= action('article.php', 'delete', 'tabellaAdminHome.php') ?>" onsubmit="return confirm('Vuoi davvero eliminare l\'articolo: <?= e($title) ?>?')" style="margin: 0; padding: 0; display: inline;">
                                            <input type="hidden" name="id" value="<?= $id ?>">
                                            <button type="submit" class="btn-elimina" style="border: none; background: none; padding: 0; cursor: pointer;">
                                                <i class="fa-solid fa-trash" style="color: #dc3545; font-size: 16px;"></i>
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="riga-tabella-utenti" style="justify-content: center; padding: 25px; color: #888;">
                                <span><i class="fa-solid fa-info-circle"></i> Nessun articolo inserito per la Home page.</span>
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