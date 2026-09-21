<?php

class User
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT 
                    id,
                    nama,
                    username,
                    password,
                    role,
                    email,
                    no_hp,
                    alamat,
                    foto,
                    status
                FROM users
                WHERE username = :username
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':username' => $username
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function verifyPassword(
        string $password,
        string $hashedPassword
    ): bool {
        return password_verify($password, $hashedPassword);
    }
}