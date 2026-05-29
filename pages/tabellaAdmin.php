<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout2;
use Camezilla\Pages\Page;

$page = new class extends Page {

    public function __construct() {
        // Controllo e avvio della sessione per leggere il ruolo e l'ID utente
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Recuperiamo il ruolo (se non esiste, per sicurezza impostiamo 'viewer')
        $current_user_role = "viewer";
        $isViewer = (strtolower($current_user_role) === 'viewer');
        
        // Recuperiamo l'ID utente dalla sessione per estrarre i dati anagrafici dal DB
        $current_user_id = $_SESSION['authentication.user-id'] ?? 0;

        // Inizializzazione variabili per il blocco di benvenuto
        $user_full_name = "Utente";
        $user_display_role = ucfirst($current_user_role);

        if ($current_user_id > 0) {
            try {
                connect_database();
                $camezillaDb = get_database();

                $stmt = $camezillaDb->prepare("SELECT first_name, last_name, role FROM users WHERE id = ? LIMIT 1");
                $stmt->execute([$current_user_id]);
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user_data) {
                    $user_full_name = trim(($user_data['first_name'] ?? '') . ' ' . ($user_data['last_name'] ?? ''));
                    $current_user_role = $user_data['role'] ?? 'viewer';
                    $user_display_role = ucfirst($current_user_role);
                    $isViewer = (strtolower($current_user_role) === 'viewer');
                }
            } catch (Exception $e) {
                log_error("Errore recupero info utente loggato: " . $e->getMessage());
            }
        }

        // Esegue il layout della pagina master inserendo il titolo globale del portale
        parent::__construct(new MainLayout2("Pannello di Controllo Admin - Accademia del Cinema"), function () use ($isViewer, $user_full_name, $user_display_role) { 
            ?>

            <div class="main-container">
                <div class="tabella-wrapper">

                    <div class="page-header-main" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 20px;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div class="admin-icon-box">
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                            <div>
                                <h1 style="margin: 0; font-size: 2rem;">Pannello di Controllo Admin</h1>
                                <p style="margin: 5px 0 0 0;">
                                    <?= $isViewer ? "Modalità Sola Lettura — Consulta le sezioni del portale" : "Seleziona la sezione del portale che desideri configurare o aggiornare" ?>
                                </p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                            
                            <div class="user-welcome-widget" style="
                                display: flex;
                                align-items: center;
                                gap: 12px;
                                background: #f8f9fa;
                                padding: 8px 16px;
                                border-radius: 8px;
                                border: 1px solid #e9ecef;
                            ">
                                <div style="
                                    background: #007bff;
                                    color: #fff;
                                    width: 36px;
                                    height: 36px;
                                    border-radius: 50%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 1.1rem;
                                ">
                                    <i class="fa-solid fa-user-gear"></i>
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 0.85rem; color: #6c757d; margin: 0;">Benvenuto,</span>
                                    <span style="font-size: 0.95rem; font-weight: bold; color: #212529; margin: 0;">
                                        <?= e($user_full_name) ?>
                                    </span>
                                    <span style="
                                        font-size: 0.75rem;
                                        font-weight: bold;
                                        color: #fff;
                                        background-color: <?= (strtolower($user_display_role) === 'admin') ? '#007bff' : ((strtolower($user_display_role) === 'editor') ? '#28a745' : '#ffc107') ?>;
                                        padding: 1px 6px;
                                        border-radius: 4px;
                                        width: max-content;
                                        margin-top: 3px;
                                        text-transform: uppercase;
                                    ">
                                        <?= e($user_display_role) ?>
                                    </span>
                                </div>
                            </div>

                            <a href="login.php" class="btn-logout" style="
                                display: inline-flex;
                                align-items: center;
                                gap: 8px;
                                background-color: #dc3545;
                                color: #ffffff;
                                padding: 10px 20px;
                                text-decoration: none;
                                border-radius: 6px;
                                font-weight: bold;
                                font-size: 0.95rem;
                                height: 40px;
                                box-sizing: border-box;
                                transition: background-color 0.3s ease;
                            " onmouseover="this.style.backgroundColor='#bd2130';" onmouseout="this.style.backgroundColor='#dc3545';">
                                <i class="fa-solid fa-right-from-bracket"></i> Esci
                            </a>
                        </div>
                    </div>

                    <div class="tabella-container">
                        
                        <div class="riga-tabella">
                            <span class="sezione"><i class="fa-solid fa-house" style="margin-right: 8px; color: #007bff;"></i> Home</span>
                            <?php if ($isViewer): ?>
                                <a href="tabellaAdminHome.php" class="btn-modifica" style="background-color: #17a2b8; border-color: #17a2b8;">Visualizza</a>
                            <?php else: ?>
                                <a href="tabellaAdminHome.php" class="btn-modifica">Modifica</a>
                            <?php endif; ?>
                        </div>

                        <div class="riga-tabella">
                            <span class="sezione"><i class="fa-solid fa-newspaper" style="margin-right: 8px; color: #007bff;"></i> Rassegna Stampa</span>
                            <?php if ($isViewer): ?>
                                <a href="tabellaAdminRassegna.php" class="btn-modifica" style="background-color: #17a2b8; border-color: #17a2b8;">Visualizza</a>
                            <?php else: ?>
                                <a href="tabellaAdminRassegna.php" class="btn-modifica">Modifica</a>
                            <?php endif; ?>
                        </div>

                        <div class="riga-tabella">
                            <span class="sezione"><i class="fa-solid fa-file-lines" style="margin-right: 8px; color: #007bff;"></i> Tutti gli Articoli</span>
                            <?php if ($isViewer): ?>
                                <a href="tabellaAdminArticoli.php" class="btn-modifica" style="background-color: #17a2b8; border-color: #17a2b8;">Visualizza</a>
                            <?php else: ?>
                                <a href="tabellaAdminArticoli.php" class="btn-modifica">Modifica</a>
                            <?php endif; ?>
                        </div>

                        <div class="riga-tabella">
                            <span class="sezione"><i class="fa-solid fa-video" style="margin-right: 8px; color: #007bff;"></i> Video</span>
                            <?php if ($isViewer): ?>
                                <a href="tabellaAdminVideo.php" class="btn-modifica" style="background-color: #17a2b8; border-color: #17a2b8;">Visualizza</a>
                            <?php else: ?>
                                <a href="tabellaAdminVideo.php" class="btn-modifica">Modifica</a>
                            <?php endif; ?>
                        </div>

                        <div class="riga-tabella">
                            <span class="sezione"><i class="fa-solid fa-users-gear" style="margin-right: 8px; color: #007bff;"></i> Gestione Utenti</span>
                            <?php if ($isViewer): ?>
                                <a href="gestione_utenti.php" class="btn-modifica" style="background-color: #17a2b8; border-color: #17a2b8;">Visualizza</a>
                            <?php else: ?>
                                <a href="gestione_utenti.php" class="btn-modifica">Modifica</a>
                            <?php endif; ?>
                        </div>
                        
                    </div>

                </div>
            </div>
            
        <?php 
        });
    }
};

echo $page->render();