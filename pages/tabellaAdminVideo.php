<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout2;
use Camezilla\Pages\Page;

$page = new class extends Page {

    public function __construct() {
        parent::__construct(new MainLayout2("Gestione Video - Accademia del Cinema"), function () { 
            
            $videos = [];
            try {
                connect_database();
                $camezillaDb = get_database();
                $pdo = method_exists($camezillaDb, 'get_pdo') ? $camezillaDb->get_pdo() : $camezillaDb;
                
                if ($pdo instanceof PDO) {
                    $stmt = $pdo->query("SELECT * FROM videos ORDER BY id DESC");
                    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch (Exception $e) {
                log_error("Errore estrazione video: " . $e->getMessage());
            }
            ?>

            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="modify.css"> 

            <div class="main-container">
                <div class="tabella-wrapper">
                    
                    <div class="page-header">
                        <h1>Contenuti Video</h1>
                        <p>Gestisci e associa i video multimediali del portale</p>
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

                    <form method="post" action="<?= action('video.php', 'create', 'tabellaAdminVideo.php') ?>" class="form-aggiunta">
                        <h3><i class="fa-solid fa-plus-circle"></i> Collega un Nuovo Video</h3>
                        <div class="input-row-utenti">
                            <input type="text" name="title" placeholder="Titolo del video" required style="flex: 1.5;">
                            <input type="text" name="url" placeholder="URL Link (YouTube / Vimeo)" required style="flex: 2;">
                            <input type="text" name="duration" placeholder="Durata (es. 02:15)">
                        </div>
                        <button type="submit" class="btn-pubblica" style="margin-top: 10px;">Salva Video</button>
                    </form>

                    <div class="tabella-container">
                        <div class="tabella-header-utenti">
                            <span style="flex: 1.5;">TITOLO VIDEO</span>
                            <span style="flex: 2;">URL SORGENTE</span>
                            <span>DURATA</span>
                            <span>AZIONI</span>
                        </div>

                        <?php if (!empty($videos)): ?>
                            <?php foreach ($videos as $vid): ?>
                                <div class="riga-tabella-utenti">
                                    <div class="col-utente" style="flex: 1.5;">
                                        <span class="nome-completo"><?= e($vid['title']) ?></span>
                                    </div>
                                    <div class="col-email" style="flex: 2; font-family: monospace; font-size: 13px; color: #555;">
                                        <?= e($vid['url']) ?>
                                    </div>
                                    <div class="col-email"><?= e($vid['duration'] ?: '--:--') ?></div>
                                    <div class="col-azioni" style="display: flex; gap: 10px; align-items: center;">
                                        <a href="modifica_video.php?id=<?= $vid['id'] ?>" class="btn-modifica" style="text-decoration:none;">Modifica</a>
                                        
                                        <form method="post" action="<?= action('video.php', 'delete', 'tabellaAdminVideo.php') ?>" onsubmit="return confirm('Rimuovere questo video?')" style="margin: 0; padding: 0; display: inline;">
                                            <input type="hidden" name="id" value="<?= $vid['id'] ?>">
                                            <button type="submit" class="btn-elimina" style="border: none; background: none; padding: 0; cursor: pointer;">
                                                <i class="fa-solid fa-trash" style="color: #dc3545;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="riga-tabella-utenti" style="justify-content: center; padding: 20px; color: #888;">
                                <span><i class="fa-solid fa-info-circle"></i> Nessun contenuto video presente.</span>
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