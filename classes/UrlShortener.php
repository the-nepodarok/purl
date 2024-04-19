<?php

namespace classes;

require_once 'DBConnection.php';
require_once 'HasConnection.php';
require_once 'UrlHandler.php';

/**
 * Класс, занимающийся записью и укорачиванием ссылок
 */
class UrlShortener extends HasConnection
{
    private string $domainName = 'http://purl/';
    private int $tokenLength = 7;

    public function __construct(private readonly UrlHandler $urlHandler)
    {
        parent::__construct();
    }

    /**
     * Генерация случайного уникального токена
     * @return string
     */
    private function generate(): string
    {
        $token = (new \Random\Randomizer())->shuffleBytes(
            base64_encode(hash('whirlpool', mt_rand()))
        );
        $token = substr($token, 0, $this->tokenLength);

        if ($this->urlHandler->findByToken($token)) {
            $token = $this->generate();
        }

        return $token;
    }

    /**
     * Основной вызываемый метод
     * @param string $url
     * @throws \Exception
     * @return string
     */
    public function shorten(string $url): string
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception('Приложите действительный URL-адрес!');
        }

        error_reporting(E_ERROR);
        if (!get_headers($url)) {
            throw new \Exception('Что-то не так. Убедитесь, что ссылка правильная');
        }

        // проверка авторизованного польз-ля
        $user = $_SESSION['user'] ?? null;

        if ($user and $this->searchByFullUrl($url, $user)) {
            throw new \Exception('Короткий адрес для этого URL уже был сгенерирован!');
        }

        // генерация токена для ссылки
        $token = $this->generate();

        $insert = $this->database->prepare('INSERT INTO urls (full_url, token, user_id) VALUES (:full, :short, :uid)');
        $insert->execute([
            'full' => $url,
            'short' => $token,
            'uid' => $_SESSION['user']['id'] ?? null,
        ]);

        return $this->domainName . $token;
    }

    /**
     * Поиск уже существующих записей с переданным URL-адресом, в т.ч. от текущего пользователя
     * @param string $url
     * @param array $user
     * @return array|false
     */
    public function searchByFullUrl(string $url, array $user = []): array|false
    {
        $query = 'SELECT * FROM urls WHERE full_url = ?';

        if (!empty($user)) {
            $query .= ' AND user_id = ?';
        }

        $urlSearch = $this->database->prepare($query);
        $urlSearch->execute($user ? [$url, $user['id']] : [$url]);

        return $urlSearch->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Создать пустой экземпляр класса
     * @return self
     */
    public static function init(): self
    {
        return new self(new UrlHandler());
    }
}