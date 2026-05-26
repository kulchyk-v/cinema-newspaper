<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Components\Login;
use App\Layouts\MainLayout;
use Camezilla\Pages\Page;

$page = new class extends Page {

    public function __construct() {
        parent::__construct(new MainLayout("Login Area Riservata - Accademia del Cinema"), function () { ?>


            <div class="login-page" style="display: flex; justify-content: center; align-items: center; min-height: 80vh; padding: 20px;">
                
                <div class="login-container">
                    
                    <div class="login-header">
                        <h2>Area Riservata Docente</h2>
                        <p>Inserisci le tue credenziali per accedere al pannello di controllo.</p>
                    </div>

                    <?php if (get_action_error()): ?>
                        <div class="error-message" style="margin-bottom: 20px;">
                            <?= e(get_action_error(true)) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= action('account.php', 'login', 'tabellaAdmin.php') ?>" class="login-form">
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Inserisci la tua email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Inserisci la password" required>
                        </div>

                        <button type="submit" class="btn-login">Accedi al Pannello</button>
                    </form>
                </div>

            </div>

        <?php });
    }
};

echo $page->render();