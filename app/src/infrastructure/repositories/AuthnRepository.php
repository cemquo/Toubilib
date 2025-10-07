<?php

namespace toubilib\infra\repositories;

class AuthnRepository
{
    private \PDO $pdo;
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

}