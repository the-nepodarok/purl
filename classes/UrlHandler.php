<?php

namespace Purl;

/**
 * Класс для работы с записями в таблице URL-адресов
 */
class UrlHandler extends HasConnection
{
    public array $url;
    public function __construct(string $token = null)
    {
        parent::__construct();

        if ($token) {
            $this->url = $this->findByToken($token);
        }
    }

    public static function findAllByUser(int $uid): false|array
    {
        $connection = (new DBConnection())->establish();
        $urlSearch = $connection->prepare('SELECT * FROM urls WHERE user_id = ?');
        $urlSearch->execute([$uid]);

        return $urlSearch->fetchAll();
    }

    /**
     * Поиск адреса по токену
     * @param string $token
     * @return array
     */
    public function findByToken(string $token): array
    {
        $tokenSearch = $this->database->prepare('SELECT * FROM urls WHERE token = ?');
        $tokenSearch->execute([$token]);

        $this->url = $tokenSearch->fetch(\PDO::FETCH_ASSOC) ?: [];

        return $this->url;
    }

    /**
     * Увеличить счётчик переходов по ссылке на 1
     * @return void
     */
    public function incrementViewCount(): void
    {
        $this->database
            ->prepare('UPDATE urls SET view_count = view_count + 1 WHERE id = ?')
            ->execute([$this->url['id']]);
    }
}