<?php

namespace App\Models;

class View {

    private ?int $id;
    private ?int $count;
    
    public function __construct(?int $id, ?int $count) {
        $this->id = $id;
        $this->count = $count;
    }

    public function get_id(): ?int {
        return $this->id;
    }

    public function get_count(): ?int {
        return $this->count;
    }
}