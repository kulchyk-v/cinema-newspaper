<?php

namespace App\Components;

use Camezilla\Components\Component;

class Navbar extends Component
{
    private string $activePage;

    // Il costruttore accetta il nome della pagina attiva (es. 'home' o 'rassegna')
    public function __construct(string $activePage = 'home')
    {
        parent::__construct();
        $this->activePage = $activePage;
    }

    protected function build(): void
    { ?>
        <nav class="filter-menu">
            <div class="header-container menu-wrapper">
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <ul id="nav-links">
                    <li>
                        <a href="<?= page('index.php') ?>" class="<?= $this->activePage === 'home' ? 'active' : '' ?>">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li>
                        <a href="<?= page('rassegna.php') ?>" class="<?= $this->activePage === 'rassegna' ? 'active' : '' ?>">
                            <i class="far fa-newspaper"></i> Rassegna Stampa
                        </a>
                    </li>
                    <li>
                        <a href="<?= page('tuttiArticoli.php') ?>" class="<?= $this->activePage === 'tutti' ? 'active' : '' ?>">
                            <i class="fas fa-book-open"></i> Tutti gli Articoli
                        </a>
                    </li>
                    <li>
                        <a href="<?= page('video.php') ?>" class="<?= $this->activePage === 'video' ? 'active' : '' ?>">
                            <i class="fas fa-video"></i> Video
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    <?php }
}