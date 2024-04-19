<?php

namespace classes;

use PDO;

require_once 'DBConnection.php';
require_once 'UrlHandler.php';
require_once 'HasConnection.php';

/**
 * Класс для работы с пользователями и их данными
 */
class UserAuth extends HasConnection
{
    /**
     * Регистрация нового пользователя
     * @param array $postData Почта и пароль из POST
     * @return void
     */
    private function register(array $postData): void
    {
        $postData['password'] = password_hash($postData['password'], PASSWORD_BCRYPT);

        $stmt = $this->database->prepare('INSERT INTO users (email, password) VALUES (:email, :password)');
        $stmt->execute($postData);

        $_SESSION['user'] = $this->searchUser($postData['email']);

        header('Location: /index.php');
    }

    /**
     * Авторизация пользователя
     * @param array $user
     * @param array $postData
     * @return void
     * @throws \Exception
     */
    private function login(array $user, array $postData): void
    {
        if (password_verify($postData['password'], $user['password'])) {
            unset($user['password']);
            $_SESSION['user'] = $user; // запись данных полльзователя в сессию

            $urls = UrlHandler::findAllByUser($user['id']);
            if (!empty($urls)) {
                // запись в сессию списка ссылок польз-ля
                $_SESSION['urls'] = $urls;
            }

            header('Location: /index.php');
            exit();
        }

        throw new \Exception('Неверный пароль');
    }

    /**
     * Логаут пользователя (очищает сессию)
     * @return void
     */
    public function logout(): void
    {
        $_SESSION = [];
        header('Location: /index.php');
    }

    /**
     * Валидация ввода
     * @param array $postData
     * @return bool
     */
    private function validate(array $postData): bool
    {
        $postData['email'] = filter_var($postData['email'], FILTER_SANITIZE_EMAIL);
        $postData['password'] = filter_var($postData['password'], FILTER_SANITIZE_SPECIAL_CHARS);

        foreach ($postData as $key => $value) {
            if (empty($value)) {
                $errors[] = $key;
            }
        }

        return empty($errors);
    }

    /**
     * Поиск пользователя по e-mail
     * @param string $email
     * @return array|false
     */
    private function searchUser(string $email): array|false
    {
        $userSearch = $this->database->prepare('SELECT * FROM users WHERE email = ?');
        $userSearch->execute([$email]);

        return $userSearch->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Основной метод, запускающий цепочку других
     * @param array $postData
     * @throws \Exception
     */
    public function handle(array $postData): void
    {
        if ($this->validate($postData)) {

            $user = $this->searchUser($postData['email']);

            if ($user) {
                $this->login($user, $postData);
            } else {
                $this->register($postData);
            }
        }
    }
}