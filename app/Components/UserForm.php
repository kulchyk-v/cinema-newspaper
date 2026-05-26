<?php

namespace App\Components;

use Camezilla\Components\Component;
use App\Models\User;

class UserForm extends Component {

    private ?User $user;

    public function __construct(?User $user = null) {
        $this->user = $user;
        parent::__construct();
    }

    protected function build(): void { ?>
        Form Utente
    <?php }
}