<?php

namespace App\Services;

use Camezilla\Exceptions\ServiceErrorException;
use Camezilla\Services\Service;
use App\Repositories\ViewRepository;
use App\Models\View;
use Exception;

class ViewService extends Service {

    private ViewRepository $viewRepository;

    public function __construct() {
        $this->viewRepository = new ViewRepository();
    }

    public function get_count(): ?int {
        try {
            $view = $this->viewRepository->get();
            return $view->get_count();
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.view.error.get_count"));
        }
    }

    public function increase(): void {
        if (get_session_item("view_increased")) {
            return;
        }

        try {
            $view = $this->viewRepository->get();
            $current_count = $view->get_count() ?? 0;
            $view = new View($view->get_id(), $current_count + 1);
            $this->viewRepository->update($view);
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.view.error.increase"));
        }

        add_session_item("view_increased", true);
    }

    public function decrease(): void {
        try {
            $view = $this->viewRepository->get();
            $current_count = $view->get_count() ?? 0;
            if ($current_count > 0) {
                $view = new View($view->get_id(), $current_count - 1);
                $this->viewRepository->update($view);
            }
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.view.error.decrease"));
        }
    }
}