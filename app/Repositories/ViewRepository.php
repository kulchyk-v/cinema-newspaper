<?php

namespace App\Repositories;

use Camezilla\Repositories\Repository;
use App\Models\View;
use Camezilla\Exceptions\RepositoryErrorException;
use Exception;

class ViewRepository extends Repository {

    public function __construct() {
        parent::__construct();
    }

    public function get(): View {
        try {
            $query = $this->database->query(
                "SELECT * FROM views ORDER BY id DESC LIMIT 1"
            );
            $row = $query->fetch();

            if ($row !== false) {
                return new View(
                    $row['id'],
                    $row['count']
                );
            } else {
                return new View(null, null);
            }
        } catch (Exception $e) {
            throw new RepositoryErrorException("Error fetching view: " . $e->getMessage());
        }
    }

    public function update(View $view): void {
        try {
            $query = $this->database->prepare(
                "UPDATE views SET count = :count WHERE id = :id"
            );
            $query->execute([
                'count' => $view->get_count(),
                'id' => $view->get_id()
            ]);
        } catch (Exception $e) {
            throw new RepositoryErrorException("Error updating view: " . $e->getMessage());
        }
    }
}