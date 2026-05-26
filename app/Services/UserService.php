<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Utils\StringUtils;
use Camezilla\Exceptions\ServiceErrorException;
use Camezilla\Services\Service;
use Exception;

class UserService extends Service {

    private UserRepository $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function get_all(): array {
        try {
            return $this->userRepository->get_all();
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.user.error.get_all"));
        }
    }

    public function get_by_id(int $id): ?User {
        try {
            return $this->userRepository->get_by_id($id);
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.user.error.get_by_id"));
        }
    }

    public function get_by_email(string $email): ?User {
        try {
            return $this->userRepository->get_by_email($email);
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.user.error.get_by_email"));
        }
    }

    public function create(User $user) {
        $this->validate_user($user);

        if ($user->get_password_hash() === null || !StringUtils::is_valid_password($user->get_password_hash())) {
            throw new ServiceErrorException(t("validation.user.error.password"));
        }

        if ($this->userRepository->get_by_email($user->get_email()) !== null) {
            throw new ServiceErrorException(t("service.user.error.email_exists"));
        }

        try {
            $this->userRepository->create($user);
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.user.error.create"));
        }
    }

    public function update(User $user) {
        $this->validate_user($user);

        if ($this->userRepository->get_by_id($user->get_id()) === null) {
            throw new ServiceErrorException(t("service.user.error.not_found"));
        }

        try {
            $this->userRepository->update($user);
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.user.error.update"));
        }
    }

    public function delete(User $user) {
        if ($this->userRepository->get_by_id($user->get_id()) === null) {
            throw new ServiceErrorException(t("service.user.error.not_found"));
        }

        try {
            $this->userRepository->delete_by_id($user->get_id());
        } catch (Exception $e) {
            throw new ServiceErrorException(t("service.user.error.delete"));
        }
    }

    public function validate_user(User $user) {
        if ($user->get_first_name() === null || !StringUtils::is_valid_text($user->get_first_name(), 255)) {
            throw new ServiceErrorException(t("validation.user.error.first_name"));
        }
        if ($user->get_last_name() === null || !StringUtils::is_valid_text($user->get_last_name(), 255)) {
            throw new ServiceErrorException(t("validation.user.error.last_name"));
        }
        if ($user->get_email() === null || !StringUtils::is_valid_email($user->get_email())) {
            throw new ServiceErrorException(t("validation.user.error.email"));
        }
        if ($user->get_role() === null || !Role::tryFrom($user->get_role()->value)) {
            throw new ServiceErrorException(t("validation.user.error.role"));
        }
    }
}