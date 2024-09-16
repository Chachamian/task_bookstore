<?php

namespace App;
use App\Mapper\User;

class Authorize
{
    private FileSystem $_fileSystem;
    private ?string $_username;
    private ?string $_password;
    private array $_errors;

    public function __construct(?string $username = null, ?string $password = null)
    {
        $this->_username  = $username;
        $this->_password  = $password;
        $this->_fileSystem = new FileSystem();
        $this->_errors    = [];
    }

    public function auth()
    {
        $this->clearError();
        $users = $this->_fileSystem->getUsersFileData();
        try {
            $this->_checkAuthData();
        } catch (\Exception $e) {
            $this->_errors[] = $e->getMessage();
            return;
        }

        foreach ($users as $user) {
            if ($user->userName === $this->_username && password_verify($this->_password, $user->password)) {
                $_SESSION['username'] = $this->_username;
                echo "Авторизация прошла успешно! Добро пожаловать, " . $_SESSION['username'] . "!";
                header('location: /');
            }
        }
        $this->_errors[] = 'Проверьте учетные данные';
    }

    public function register()
    {
        $this->clearError();
        $users = $this->_fileSystem->getUsersFileData();
        try {
            $this->_checkAuthData();
        } catch (\Exception $e) {
            $this->_errors[] = $e->getMessage();
            return;
        }

        foreach ($users as $user) {
            if ($user->userName === $this->_username) {
                $this->_errors[] = "Пользователь с таким именем уже существует!";
                return;
            }
        }

        $hashedPassword = password_hash($this->_password, PASSWORD_DEFAULT);
        $users[] = new User(
            $this->_fileSystem->GetIncrement('users'),
            $this->_username,
            $hashedPassword,
            0,
        );

        $this->_fileSystem->writeUsersFileData($users);
        $_SESSION['username'] = $this->_username;
        header('location: /');
    }

    public function isAdmin(): bool
    {
        $user = $this->user();

        if ($user !== null && $user->role === 1) {
            return true;
        }

        return false;
    }

    public function user(): ?User
    {
        if (!empty($_SESSION['username'])) {
            $users = $this->_fileSystem->getUsersFileData();
            foreach ($users as $user) {
                if ($_SESSION['username'] === $user->userName) {
                    return new User(
                        $user->id,
                        $user->userName,
                        null,
                        $user->role
                    );
                }
            }
        }

        return null;
    }

    public function getError(): array
    {
        return $this->_errors;
    }

    public function clearError(): void
    {
        $this->_errors = [];
    }

    public function clearSession(): void
    {
        session_destroy();
    }

    private function _checkAuthData()
    {
        if (empty($this->_username) || empty($this->_password)) {
            $this->_errors[] = "Все поля обязательны к заполнению";
            throw new \App\Exception\Authorize();
        }
    }
}