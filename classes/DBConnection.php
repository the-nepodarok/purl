<?php

namespace classes;
use PDO;
use PDOException;

class DBConnection
{
    private string $host = 'localhost';
    private string $dbname = 'shorts';
    private string $username = 'food_ninja';
    private string $password = '';

    public function establish()
    {
        try {
            $DB = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
        } catch (PDOException $e) {
            die($e->getMessage());
        }

        return $DB;
    }
}

