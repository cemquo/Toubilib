<?php

namespace toubilib\infra\repositories;

use toubilib\core\application\ports\spi\repositoryInterfaces\PatientRepositoryInterface;
use toubilib\core\domain\entities\Patient;

class PatientRepository implements PatientRepositoryInterface
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findById(string $id): ?Patient
    {
        $sql = "SELECT id, nom, prenom, date_naissance, adresse, code_postal, ville, email, telephone 
                FROM patient WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Patient(
            $row['id'],
            $row['nom'],
            $row['prenom'],
            $row['date_naissance'] ? new \DateTime($row['date_naissance']) : null,
            $row['adresse'],
            $row['code_postal'],
            $row['ville'],
            $row['email'],
            $row['telephone']
        );
    }
}
