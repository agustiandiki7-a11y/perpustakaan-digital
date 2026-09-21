<?php

class Database
{
    private string $host = 'localhost';
    private string $dbName = 'perpustakaan';
    private string $username = 'root';
    private string $password = '';

    private ?PDO $connection = null;

    public function connect(): PDO
    {
        if ($this->connection instanceof PDO) {
            return $this->connection;
        }

        $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";

        try {
            $this->connection = new PDO(
                $dsn,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

            return $this->connection;
        } catch (PDOException $e) {
            die('Koneksi database gagal.');
        }
    }

    public function getConnection(): PDO
    {
        return $this->connect();
    }
}