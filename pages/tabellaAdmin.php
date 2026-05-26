<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout2;
use Camezilla\Pages\Page;

$page = new class extends Page {

    public function __construct() {
        // Esegue il layout della pagina master inserendo il titolo globale del portale
        parent::__construct(new MainLayout2("Pannello di Controllo Admin - Accademia del Cinema"), function () { 
            ?>

            <div class="main-container">
                <div class="tabella-wrapper">

                    <div class="page-header-main">
                        <div class="admin-icon-box">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        <h1>Pannello di Controllo Admin</h1>
                        <p>Seleziona la sezione del portale che desideri configurare o aggiornare</p>
                    </div>

                    <div class="tabella-container">
                        <div class="riga-tabella">
                            <span class="sezione"><i class="fa-solid fa-house" style="margin-right: 8px; color: #007bff;"></i> Home</span>
                            <a href="tabellaAdminHome.php" class="btn-modifica">Modifica</a>
                        </div>

                        <div class="riga-tabella">
                            <span class="sezione"><i class="fa-solid fa-newspaper" style="margin-right: 8px; color: #007bff;"></i> Rassegna Stampa</span>
                            <a href="tabellaAdminRasSt.php" class="btn-modifica">Modifica</a>
                        </div>

                        <div class="riga-tabella">
                            <span class="sezione"><i class="fa-solid fa-file-lines" style="margin-right: 8px; color: #007bff;"></i> Tutti gli Articoli</span>
                            <a href="tabellaAdminArticoli.php" class="btn-modifica">Modifica</a>
                        </div>

                        <div class="riga-tabella">
                            <span class="sezione"><i class="fa-solid fa-video" style="margin-right: 8px; color: #007bff;"></i> Video</span>
                            <a href="tabellaAdminVideo.php" class="btn-modifica">Modifica</a>
                        </div>

                        <div class="riga-tabella">
                            <span class="sezione"><i class="fa-solid fa-users-gear" style="margin-right: 8px; color: #007bff;"></i> Gestione Utenti</span>
                            <a href="gestione_utenti.php" class="btn-modifica">Modifica</a>
                        </div>
                    </div>

                </div>
            </div>
            
        <?php 
        });
    }
};

echo $page->render();