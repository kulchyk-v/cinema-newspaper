<?php

namespace App\Components;

use Camezilla\Components\Component;

class Login extends Component {

    private string $mode;

    public function __construct(string $mode = 'login') {
        parent::__construct();
        $this->mode = $mode;
    }

    protected function build(): void { ?>
        <?php if ($this->mode === 'register'): ?>

            Registrati

        <?php else: ?>

            Accedi

        <?php endif; ?>
    <?php }
}