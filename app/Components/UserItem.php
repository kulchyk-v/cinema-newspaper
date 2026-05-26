<?php

namespace App\Components;

use Camezilla\Components\Component;
use App\Models\User;

class UserItem extends Component {

    private User $user;

    public function __construct(User $user) {
        parent::__construct();
        $this->user = $user;
    }

    protected function build(): void { ?>
        <div>
            Utente
        </div>
    <?php }
}