<?php

namespace App\Repositories;

use App\Models\Role;
use Camezilla\Repositories\Repository;
use App\Models\User;
use Camezilla\Exceptions\RepositoryErrorException;
use Exception;

class UserRepository extends Repository {

    public function __construct() {
        parent::__construct();
    }

    public function get_all(): array {
        try {
            $query = $this->database->prepare(
                "SELECT * FROM users"
            );
            $query->execute();
            $rows = $query->fetchAll();

            $users = [];
            foreach ($rows as $row) {
                $role = Role::tryFrom($row['role']);
                if ($role === null) {
                    throw new RepositoryErrorException("Invalid role value: " . ($row['role'] ?? 'null'));
                }

                $users[] = new User(
                    $row['id'],
                    $row['first_name'],
                    $row['last_name'],
                    $row['email'],
                    $row['password_hash'],
                    $role
                );
            }
            
            return $users;
        } catch (Exception $e) {
            throw new RepositoryErrorException("Error getting users: " . $e->getMessage());
        }
    }

    public function get_by_id(int $id): ?User {
        try {
            $query = $this->database->prepare(
                "SELECT * FROM users WHERE id = :id"
            );
            $query->execute([
                'id' => $id
            ]);
            $row = $query->fetch();

            if ($row !== false) {
                $role = Role::tryFrom($row['role']);
                if ($role === null) {
                    throw new RepositoryErrorException("Invalid role value: " . ($row['role'] ?? 'null'));
                }

                return new User(
                    $row['id'],
                    $row['first_name'],
                    $row['last_name'],
                    $row['email'],
                    $row['password_hash'],
                    $role
                );
            }
            
            return null;
        } catch (Exception $e) {
            throw new RepositoryErrorException("Error getting user: " . $e->getMessage());
        }
    }

    public function get_by_email(string $email): ?User {
        try {
            $query = $this->database->prepare(
                "SELECT * FROM users WHERE email = :email"
            );
            $query->execute([
                'email' => $email
            ]);
            $row = $query->fetch();

            if ($row !== false) {
                $role = Role::tryFrom($row['role']);
                if ($role === null) {
                    throw new RepositoryErrorException("Invalid role value: " . ($row['role'] ?? 'null'));
                }

                return new User(
                    $row['id'],
                    $row['first_name'],
                    $row['last_name'],
                    $row['email'],
                    $row['password_hash'],
                    $role
                );
            }
            
            return null;
        } catch (Exception $e) {
            throw new RepositoryErrorException("Error getting user: " . $e->getMessage());
        }
    }

    public function create(User $user) {
        try {
            $query = $this->database->prepare(
                "INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES (:first_name, :last_name, :email, :password_hash, :role)"
            );
            $query->execute([
                'first_name' => $user->get_first_name(),
                'last_name' => $user->get_last_name(),
                'email' => $user->get_email(),
                'password_hash' => $user->get_password_hash(),
                'role' => $user->get_role()->value
            ]);
        } catch (Exception $e) {
            throw new RepositoryErrorException("Error creating user: " . $e->getMessage());
        }
    }

    public function update(User $user) {
        try {
            $query = $this->database->prepare(
                "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, password_hash = :password_hash, role = :role WHERE id = :id"
            );
            $query->execute([
                'id' => $user->get_id(),
                'first_name' => $user->get_first_name(),
                'last_name' => $user->get_last_name(),
                'email' => $user->get_email(),
                'password_hash' => $user->get_password_hash(),
                'role' => $user->get_role()->value
            ]);
        } catch (Exception $e) {
            throw new RepositoryErrorException("Error updating user: " . $e->getMessage());
        }
    }

    public function delete_by_id(int $id) {
        try {
            $query = $this->database->prepare(
                "DELETE FROM users WHERE id = :id"
            );
            $query->execute([
                'id' => $id
            ]);
        } catch (Exception $e) {
            throw new RepositoryErrorException("Error deleting user: " . $e->getMessage());
        }
    }
}