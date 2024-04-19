<?php

namespace classes;

abstract class HasConnection
{
    protected \PDO $database;

    public function __construct()
    {
        // подключение к базе данных
        $this->database = (new DBConnection())->establish();
    }
}