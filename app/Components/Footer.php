<?php

namespace App\Components;

use Camezilla\Components\Component;

class Footer extends Component
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function build(): void
    { ?>
      <footer class="main-footer">
    <div class="footer-content">
        
        <div class="footer-col">
            <h4>Contatti</h4>
            <ul>
                <li><i class="far fa-envelope"></i> domenico.allocca@iticopernico.it</li>
                <li><i class="fas fa-map-marker-alt"></i>Iti Copernico, Via Pontegradella, 25, 44123 Ferrara FE, Italia</li>
            </ul>
        </div>

    </div>

    <div class="footer-bottom">
        <div class="credits">
            &copy; <?= date("Y") ?> Accademia del Cinema.<br> Tutti i diritti riservati.<br>
            Progetto a cura del Prof. Domenico Allocca - Scuola Iti Copernico Ferrara<br>
            Sviluppato da: Classe 5G
        </div>
       
        <div class="page-views">
            <i class="far fa-eye"></i>
            <?php
            // Variabile finta per il backend
            $visualizzazioni_reali = 1336 + 150; 
            echo number_format($visualizzazioni_reali, 0, ',', '.');
            ?>
        </div>
    </div>
</footer>

    <?php }
}