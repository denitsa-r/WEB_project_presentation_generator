<?php

class User extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->query("SELECT * FROM users WHERE email = ?", [$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($username, $email, $password_hash)
    {
        return $this->db->query(
            "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)",
            [$username, $email, $password_hash]
        );
    }
}
