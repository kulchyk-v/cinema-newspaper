<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\PriorityLevel;
use App\Repositories\ArticleRepository;
use App\Utils\StringUtils;
use Camezilla\Exceptions\ServiceErrorException;
use Camezilla\Services\Service;
use Exception;
use InvalidArgumentException;

class ArticleService extends Service {

    private ArticleRepository $articleRepository;

    public function __construct() {
        $this->articleRepository = new ArticleRepository();
    }

    public function get_all(): array {
        try {
            return $this->articleRepository->get_all();
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.article.error.get_all"));
        }
    }

    public function get_by_id(int $id): ?Article {
        try {
            return $this->articleRepository->get_by_id($id);
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.article.error.get_by_id"));
        }
    }

    public function get_recent(int $limit = 5): array {
        try {
            return $this->articleRepository->get_recent($limit);
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.article.error.get_recent"));
        }
    }

    public function get_by_category(Category $category): array {
        try {
            return $this->articleRepository->get_by_category($category);
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.article.error.get_by_category"));
        }
    }

    public function create(Article $article) {
        $this->validate_article($article);

        try {
            $this->articleRepository->create($article);
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.article.error.create"));
        }
    }

    public function update(Article $article) {
        $this->validate_article($article);

        if ($this->articleRepository->get_by_id($article->get_id()) === null) {
            throw new ServiceErrorException(t("service.article.error.not_found"));
        }

        try {
            $this->articleRepository->update($article);
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.article.error.update"));
        }
    }

    public function delete(Article $article) {
        if ($this->articleRepository->get_by_id($article->get_id()) === null) {
            throw new ServiceErrorException(t("service.article.error.not_found"));
        }

        try {
            $this->articleRepository->delete_by_id($article->get_id());
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.article.error.delete"));
        }
    }

    public function validate_article(Article $article) {
        if ($article->get_title() === null || !StringUtils::is_valid_text($article->get_title(), 255)) {
            throw new InvalidArgumentException(t("validation.article.error.title"));
        }
        if ($article->get_description() !== null && !StringUtils::is_valid_text($article->get_description(), 500)) {
            throw new InvalidArgumentException(t("validation.article.error.description"));
        }
        if ($article->get_category() === null || Category::tryFrom($article->get_category()->value) === null) {
            throw new InvalidArgumentException(t("validation.article.error.category"));
        }
        if ($article->get_link() !== null && !StringUtils::is_valid_text($article->get_link(), 2048)) {
            throw new InvalidArgumentException(t("validation.article.error.link"));
        }
        if ($article->get_text() !== null && !StringUtils::is_valid_text($article->get_text(), 65535)) {
            throw new InvalidArgumentException(t("validation.article.error.text"));
        }
        if ($article->get_priority_level() === null || !PriorityLevel::tryFrom($article->get_priority_level()->value)) {
            throw new InvalidArgumentException(t("validation.article.error.priority_level"));
        }
        if ($article->get_author() === null || !StringUtils::is_valid_text($article->get_author(), 255)) {
            throw new InvalidArgumentException(t("validation.article.error.author"));
        }
        if ($article->get_date() === null || $article->get_date() > new \DateTime()) {
            throw new InvalidArgumentException(t("validation.article.error.date"));
        }
    }
}