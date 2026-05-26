<?php

namespace App\Models;

use DateTime;

class Article {
    
    private ?int $id;
    private ?string $title;
    private ?string $description;
    private ?Category $category;
    private ?string $link;
    private ?string $text;
    private ?PriorityLevel $priority_level;
    private ?string $author;
    private ?DateTime $date;

    // Modificato il type-hint di $date per accettare anche stringhe dal form
    public function __construct(?int $id, ?string $title, ?string $description, ?Category $category, ?string $link, ?string $text, ?PriorityLevel $priority_level, ?string $author, DateTime|string|null $date) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->category = $category;
        $this->link = $link;
        $this->text = $text;
        $this->author = $author;
        $this->priority_level = $priority_level;

        // Se la data arriva come stringa dal form, la convertiamo in un oggetto DateTime
        if (is_string($date)) {
            $this->date = new DateTime($date);
        } else {
            $this->date = $date;
        }
    }

    public function get_id(): ?int {
        return $this->id;
    }

    public function get_title(): ?string {
        return $this->title;
    }

    public function get_description(): ?string {
        return $this->description;
    }

    public function get_category(): ?Category {
        return $this->category;
    }

    public function get_link(): ?string {
        return $this->link;
    }

    public function get_text(): ?string {
        return $this->text;
    }

    public function get_priority_level(): ?PriorityLevel {
        return $this->priority_level;
    }

    public function get_author(): ?string {
        return $this->author;
    }

    public function get_date(): ?DateTime {
        return $this->date;
    }
}