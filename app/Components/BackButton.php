<?php

namespace App\Components;

use Camezilla\Components\Component;

class BackButton extends Component
{
    private string $label;

    /**
     * @param string $label Il testo da mostrare accanto alla freccia (opzionale)
     */
    public function __construct(string $label = "Torna Indietro")
    {
        parent::__construct();
        $this->label = $label;
    }

    protected function build(): void
    { ?>
        <div class="back-button-container">
            <button onclick="window.history.back();" class="btn-back-component">
                <i class="fa-solid fa-arrow-left"></i> <?= htmlspecialchars($this->label) ?>
            </button>
        </div>
    <?php }
}