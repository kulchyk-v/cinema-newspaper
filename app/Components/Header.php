<?php

namespace App\Components;

use Camezilla\Components\Component;

class Header extends Component
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function build(): void
    { ?>
        <header class="dark-header">
                <div class="logo-area">
                    <img src="<?= resource('foto/logo-con_bg.png'); ?>" alt="Icona Cinema" class="logo">
                    <div class="titles">
                        <span class="academy-name">Accademia del Cinema</span>
                        <span class="sub-title">Film School Excellence</span>
                    </div>
                </div>
            </header>
    <?php }
}