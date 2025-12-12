<?php

namespace toubilib\infra\repositories;

use toubilib\core\application\ports\spi\exceptions\PatientNonTrouveException;
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
        try {

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
        } catch (\PDOException $e) {
            throw new PatientNonTrouveException($e->getMessage());
        }
    }

    public function createPatient(Patient $patient): void
    {
        try {
            $sql = "INSERT INTO patient (id, nom, prenom, date_naissance, adresse, code_postal, ville, email, telephone) 
                        VALUES (:id, :nom, :prenom, :date_naissance, :adresse, :code_postal, :ville, :email, :telephone)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $patient->getId());
            $stmt->bindValue(':nom', $patient->getNom());
            $stmt->bindValue(':prenom', $patient->getPrenom());
            $stmt->bindValue(':date_naissance', $patient->getDateNaissance() ? $patient->getDateNaissance()->format('Y-m-d') : null);
            $stmt->bindValue(':adresse', $patient->getAdresse());
            $stmt->bindValue(':code_postal', $patient->getCodePostal());
            $stmt->bindValue(':ville', $patient->getVille());
            $stmt->bindValue(':email', $patient->getEmail());
            $stmt->bindValue(':telephone', $patient->getTelephone());
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new PatientNonTrouveException($e->getMessage());
        }   
    }
}
