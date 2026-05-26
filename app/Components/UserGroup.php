<?php

namespace App\Components;

use Camezilla\Components\Component;
use App\Components\UserItem;

class UserGroup extends Component {

    private array $users;

    public function __construct(array $users) {
        parent::__construct();
        $this->users = $users;
    }

    protected function build(): void { ?>
        <div>
            Gruppo Utenti
            
            <?php if (empty($this->users)): ?>

                <p>Nessun utente trovato.</p>

            <?php else: ?>

                <?php foreach ($this->users as $user): ?>

                    <?= new UserItem($user) ?>

                <?php endforeach; ?>

            <?php endif; ?>
        </div>
    <?php }
}