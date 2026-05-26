<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout2;
use Camezilla\Pages\Page;

function getInitials($firstName, $lastName) {
    $i1 = !empty($firstName) ? strtoupper($firstName[0]) : '';
    $i2 = !empty($lastName) ? strtoupper($lastName[0]) : '';
    return $i1 . $i2;
}

function getAvatarClass($role) {
    switch ($role) {
        case 'admin': return 'av-blue';
        case 'editor': return 'av-green';
        case 'viewer': return 'av-amber';
        default: return 'av-purple';
    }
}

$page = new class extends Page {

    public function __construct() {
        parent::__construct(new MainLayout2("Gestione Utenti - Accademia del Cinema"), function () { 
            
            // ==================================================================
            // ESTRAZIONE UTENTI COMPATIBILE CON L'OGGETTO DATABASE DI CAMEZILLA
            // ==================================================================
            $utenti = [];
            try {
                connect_database();
                $camezillaDb = get_database();
                
                // Se get_database() restituisce l'oggetto wrapper custom, estraiamo l'istanza PDO interna
                $pdo = method_exists($camezillaDb, 'get_pdo') ? $camezillaDb->get_pdo() : $camezillaDb;
                
                if ($pdo instanceof PDO) {
                    $stmt = $pdo->query("SELECT * FROM users ORDER BY first_name ASC");
                    $utenti = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else if (method_exists($camezillaDb, 'query')) {
                    $result = $camezillaDb->query("SELECT * FROM users ORDER BY first_name ASC");
                    if (is_array($result)) {
                        $utenti = $result;
                    } elseif (is_object($result) && method_exists($result, 'fetchAll')) {
                        $utenti = $result->fetchAll(PDO::FETCH_ASSOC);
                    }
                }
            } catch (Exception $e) {
                log_error("Errore estrazione utenti: " . $e->getMessage());
                $utenti = []; 
            }
            ?>


            <div class="main-container">
                <div class="tabella-wrapper">
                    
                    <div class="page-header">
                        <h1>Gestione Utenti</h1>
                        <p>Aggiungi e gestisci gli utenti del sistema</p>
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

                    <form method="post" action="<?= action('account.php', 'register', 'gestione_utenti.php') ?>" class="form-aggiunta">
                        <h3><i class="fa-solid fa-plus-circle"></i> Nuovo Utente</h3>
                        <div class="input-row-utenti">
                            <input type="text" name="first_name" placeholder="Nome" required>
                            <input type="text" name="last_name" placeholder="Cognome" required>
                            <input type="email" name="email" placeholder="Email" required>
                            <select name="role" class="custom-select" required>
                                <option value="" disabled selected>Scegli Ruolo...</option>
                                <option value="admin">Admin</option>
                                <option value="editor">Editor</option>
                                <option value="viewer">Viewer</option>
                            </select>
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn-pubblica">Crea Utente</button>
                    </form>

                    <div class="tabella-container">
                        <div class="tabella-header-utenti">
                            <span>UTENTE</span>
                            <span>EMAIL</span>
                            <span>RUOLO</span>
                            <span>AZIONI</span>
                        </div>

                        <?php if (!empty($utenti) && is_array($utenti)): ?>
                            <?php foreach ($utenti as $utente): ?>
                                <?php 
                                    $id        = $utente['id'] ?? 0;
                                    $firstName = $utente['first_name'] ?? '';
                                    $lastName  = $utente['last_name'] ?? '';
                                    $email     = $utente['email'] ?? '';
                                    $roleValue = $utente['role'] ?? 'viewer';
                                ?>
                                <div class="riga-tabella-utenti">
                                    <div class="col-utente">
                                        <div class="avatar <?= getAvatarClass($roleValue); ?>">
                                            <?= e(getInitials($firstName, $lastName)) ?>
                                        </div>
                                        <span class="nome-completo">
                                            <?= e($firstName . ' ' . $lastName) ?>
                                        </span>
                                    </div>
                                    <div class="col-email"><?= e($email) ?></div>
                                    <div class="col-ruolo">
                                        <span class="badge badge-<?= e($roleValue) ?>">
                                            <?= e(ucfirst($roleValue)) ?>
                                        </span>
                                    </div>
                                    <div class="col-azioni" style="display: flex; gap: 10px; align-items: center;">
                                        <a href="modifica_utente.php?id=<?= $id ?>" class="btn-modifica" style="text-decoration:none;">Modifica</a>
                                        
                                        <form method="post" action="<?= action('account.php', 'delete', 'gestione_utenti.php') ?>" onsubmit="return confirm('Vuoi davvero eliminare l\'utente <?= e($firstName) ?>?')" style="margin: 0; padding: 0; display: inline;">
                                            <input type="hidden" name="id" value="<?= $id ?>">
                                            <button type="submit" class="btn-elimina" style="border: none; background: none; padding: 0; cursor: pointer;">
                                                <i class="fa-solid fa-trash" style="color: #dc3545;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="riga-tabella-utenti" style="justify-content: center; padding: 20px; color: #888;">
                                <span><i class="fa-solid fa-info-circle"></i> Nessun utente trovato nel database del portale.</span>
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