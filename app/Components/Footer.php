<?php

namespace App\Components;

use Camezilla\Components\Component;
use PDO;
use Exception;

class Footer extends Component
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function build(): void
    { 
        // Valore di fallback se il database dovesse essere offline
        $visualizzazioni_totali = 1486; 

        try {
            // Connessione nativa al database
            $dsn = "mysql:host=localhost;dbname=cinema_newspaper;charset=utf8mb4";
            $pdo = new PDO($dsn, "root", "", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            // Query che somma la colonna views_number di tutte le righe della tabella articles
            $stmt = $pdo->query("SELECT SUM(views_number) AS totale_visite FROM articles");
            $data = $stmt->fetch();
            
            // Se la tabella non è vuota e il risultato non è null, aggiorniamo il contatore
            if ($data && $data['totale_visite'] !== null) {
                $visualizzazioni_totali = (int)$data['totale_visite'];
            }

        } catch (Exception $e) {
            // In caso di errore nel database, il sito del Copernico continuerà a funzionare mostrando il fallback
        }
        ?>
        <footer class="main-footer">
            <div class="footer-content">
                
                <div class="footer-col">
                    <h4>Contatti</h4>
                    <ul>
                        <li><i class="far fa-envelope"></i> domenico.allocca@iticopernico.it</li>
                        <li><i class="fas fa-map-marker-alt"></i> Iti Copernico, Via Pontegradella, 25, 44123 Ferrara FE, Italia</li>
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
                    <i class="far fa-eye"></i> Letture articoli: 
                    <?php
                    // Mostra il totale formattato correttamente (es: 2.340)
                    echo number_format($visualizzazioni_totali, 0, ',', '.');
                    ?>
                </div>
            </div>
        </footer>
    <?php }
}