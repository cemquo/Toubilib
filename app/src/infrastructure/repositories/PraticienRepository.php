<?php

namespace toubilib\infra\repositories;

use toubilib\core\application\ports\spi\repositoryInterfaces\PraticienRepositoryInterface;
use toubilib\core\domain\entities\praticien\Praticien;

class PraticienRepository implements PraticienRepositoryInterface
{

    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $sql = "SELECT id, nom, prenom, ville, email, telephone, specialite_id FROM praticien";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $praticiens = [];
        foreach ($rows as $row) {
            $praticiens[] = new Praticien(
                $row['nom'],
                $row['prenom'],
                (string)$row['specialite_id'],
                $row['ville'],
                $row['email'],
                $row['telephone'],
                '',
                ''
            );
        }

        return $praticiens;
    }

}