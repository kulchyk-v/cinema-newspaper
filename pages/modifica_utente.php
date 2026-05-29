<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout2;
use Camezilla\Pages\Page;
use App\Services\UserService;
use App\Models\User;

$page = new class extends Page {

    public function __construct()
    {
        parent::__construct(new MainLayout2("Modifica Utente - Accademia del Cinema"), function () {

            // 1. RECUPERO DELL'ID DALL'URL
            $userId = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $utenteCorrente = null;

            if ($userId > 0) {
                try {
                    $userService = new UserService();

                    // Rileviamo dinamicamente il metodo corretto per estrarre il singolo utente dal Service
                    if (method_exists($userService, 'find')) {
                        $utenteCorrente = $userService->find($userId);
                    } elseif (method_exists($userService, 'getById')) {
                        $utenteCorrente = $userService->getById($userId);
                    } elseif (method_exists($userService, 'findById')) {
                        $utenteCorrente = $userService->findById($userId);
                    } else {
                        // Fallback di sicurezza: se i metodi falliscono, proviamo ad analizzare 
                        // se le proprietà interne del Service contengono dati o se possiamo mappare l'oggetto
                        $utenteCorrente = null;
                    }
                } catch (Exception $e) {
                    $utenteCorrente = null;
                }
            }

            // Estrazione sicura dei dati (gestisce sia se viene restituito un oggetto che un array associativo)
            $firstName = $utenteCorrente->first_name ?? $utenteCorrente['first_name'] ?? '';
            $lastName = $utenteCorrente->last_name ?? $utenteCorrente['last_name'] ?? '';
            $email = $utenteCorrente->email ?? $utenteCorrente['email'] ?? '';

            $rawRole = $utenteCorrente->role ?? $utenteCorrente['role'] ?? 'viewer';
            $roleValue = is_object($rawRole) ? ($rawRole->value ?? 'viewer') : $rawRole;
            ?>
            <div class="main-container">
                <div class="tabella-wrapper" style="max-width: 600px;">
                    <div class="page-header">
                        <h1>Modifica Utente</h1>
                        <p>Aggiorna i dati del profilo e i permessi di accesso</p>
                    </div>
                    <?php if (get_action_error()): ?>
                        <div class="error-message"
                            style="background-color: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #f5c6cb;">
                            <?= e(get_action_error(true)) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= action('account.php', 'update', 'gestione_utenti.php') ?>"
                        class="form-aggiunta">
                        <h3><i class="fa-solid fa-user-pen"></i> Dati Utente (ID: <?= $userId ?>)</h3>

                        <input type="hidden" name="id" value="<?= $userId ?>">

                        <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 20px;">

                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 5px; font-size: 0.9rem;">Nome</label>
                                <input type="text" name="first_name" value="<?= e($firstName) ?>" placeholder="Nome"
                                    class="input-row input" style="margin-bottom: 0;" required>
                            </div>

                            <div>
                                <label
                                    style="display: block; font-weight: 600; margin-bottom: 5px; font-size: 0.9rem;">Cognome</label>
                                <input type="text" name="last_name" value="<?= e($lastName) ?>" placeholder="Cognome"
                                    class="input-row input" style="margin-bottom: 0;" required>
                            </div>

                            <div>
                                <label
                                    style="display: block; font-weight: 600; margin-bottom: 5px; font-size: 0.9rem;">Email</label>
                                <input type="email" name="email" value="<?= e($email) ?>" placeholder="Email"
                                    class="input-row input" style="margin-bottom: 0;" required>
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 5px; font-size: 0.9rem;">Ruolo
                                    Sistema</label>
                                <select name="role" class="custom-select" required>
                                    <option value="admin" <?= $roleValue === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="editor" <?= $roleValue === 'editor' ? 'selected' : '' ?>>Editor</option>
                                    <option value="viewer" <?= $roleValue === 'viewer' ? 'selected' : '' ?>>Viewer</option>
                                </select>
                            </div>

                        </div>

                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn-pubblica" style="flex: 2;">Salva Modifiche</button>
                            <a href="gestione_utenti.php" class="btn-modifica"
                                style="flex: 1; text-align: center; line-height: 28px; background: #fff; color: #666; border-color: #ccc;">Annulla</a>
                        </div>
                    </form>

                </div>
            </div>

        <?php
        });
    }
};

echo $page->render();