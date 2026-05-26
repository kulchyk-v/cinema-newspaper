<?php

namespace App\Repositories;

use Camezilla\Repositories\Repository;
use App\Models\Article;
use App\Models\Category;
use App\Models\PriorityLevel;
use Camezilla\Exceptions\RepositoryErrorException;
use Exception;
use DateTime;

class ArticleRepository extends Repository{

    public function __construct() {
        parent::__construct();
    }

    public function get_all(): array {
        try {
            $query = $this->database->query(
				"SELECT * FROM articles"
			);

            $articles = [];
            while (($row = $query->fetch()) !== false) {
                $articles[] = new Article(
                    $row['id'],
                    $row['title'],
                    $row['description'],
                    Category::from($row['category']),
                    $row['link'],
                    $row['text'],
                    PriorityLevel::from($row['priority_level']),
                    $row['author'],
                    new DateTime($row['date'])
                );
            }

            return $articles;
        } catch (Exception $e) {
            throw new RepositoryErrorException("Error fetching articles: " . $e->getMessage());
        }
    }
    
    public function get_by_id(int $id): ?Article {
        try {
            $query = $this->database->prepare(
				"SELECT * FROM articles WHERE id = :id"
			);
            $query->execute([
				'id' => $id
			]);
            $row = $query->fetch();

            if ($row !== false) {
                return new Article(
                    $row['id'],
                    $row['title'],
                    $row['description'],
                    Category::from($row['category']),
                    $row['link'],
                    $row['text'],
                    PriorityLevel::from($row['priority_level']),
                    $row['author'],
                    new DateTime($row['date'])
                );
            }

            return null;
        } catch (Exception $e) {
            throw new RepositoryErrorException("Error fetching article by ID: " . $e->getMessage());
        }
    }

	public function get_recent(int $limit = 5): array {
		try {
			$query = $this->database->prepare(
				"SELECT * FROM articles ORDER BY date DESC LIMIT :limit"
			);
			$query->execute([
				'limit' => $limit
			]);

			$articles = [];
			while (($row = $query->fetch()) !== false) {
				$articles[] = new Article(
					$row['id'],
					$row['title'],
					$row['description'],
					Category::from($row['category']),
					$row['link'],
					$row['text'],
					PriorityLevel::from($row['priority_level']),
					$row['author'],
					new DateTime($row['date'])
				);
			}

			return $articles;
		} catch (Exception $e) {
			throw new RepositoryErrorException("Error fetching recent articles: " . $e->getMessage());
		}
	}

	public function get_by_category(Category $category): array {
		try {
			$query = $this->database->prepare(
				"SELECT * FROM articles WHERE category = :category"
			);
			$query->execute([
				'category' => $category->value
			]);

			$articles = [];
			while (($row = $query->fetch()) !== false) {
				$articles[] = new Article(
					$row['id'],
					$row['title'],
					$row['description'],
					Category::from($row['category']),
					$row['link'],
					$row['text'],
					PriorityLevel::from($row['priority_level']),
					$row['author'],
					new DateTime($row['date'])
				);
			}

			return $articles;
		} catch (Exception $e) {
			throw new RepositoryErrorException("Error fetching articles by category: " . $e->getMessage());
		}
	}

	public function create(Article $article): void {
		try {
			$query = $this->database->prepare(
				"INSERT INTO articles (title, description, category, link, text, priority_level, author, date) 
				VALUES (:title, :description, :category, :link, :text, :priority_level, :author, :date)");
			$query->execute([
				'title' => $article->get_title(),
				'description' => $article->get_description(),
				'category' => $article->get_category() ? $article->get_category()->value : null,
				'link' => $article->get_link(),
				'text' => $article->get_text(),
				'priority_level' => $article->get_priority_level()->value,
				'author' => $article->get_author(),
				'date' => $article->get_date()->format('Y-m-d H:i:s')
			]);
		} catch (Exception $e) {
			throw new RepositoryErrorException("Error creating article: " . $e->getMessage());
		}
	}

	public function update(Article $article): void {
		try {
			$query = $this->database->prepare(
				"UPDATE articles 
				SET title = :title, description = :description, category = :category, link = :link, text = :text, priority_level = :priority_level, author = :author, date = :date
				WHERE id = :id");
			$query->execute([
				'id' => $article->get_id(),
				'title' => $article->get_title(),
				'description' => $article->get_description(),
				'category' => $article->get_category() ? $article->get_category()->value : null,
				'link' => $article->get_link(),
				'text' => $article->get_text(),
				'priority_level' => $article->get_priority_level()->value,
				'author' => $article->get_author(),
				'date' => $article->get_date()->format('Y-m-d H:i:s')
			]);
		} catch (Exception $e) {
			throw new RepositoryErrorException("Error updating article: " . $e->getMessage());
		}
	}

	public function delete_by_id(int $id): void {
		try {
			$query = $this->database->prepare(
				"DELETE FROM articles 
				WHERE id = :id"
			);
			$query->execute([
				'id' => $id
			]);

			log_debug($id);
		} catch (Exception $e) {
			throw new RepositoryErrorException("Error deleting article: " . $e->getMessage());
		}
	}
}
