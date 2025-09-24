<?php

namespace toubilib\infra\repositories;

use DateTime;
use toubilib\core\application\ports\spi\repositoryInterfaces\RdvRepositoryInterface;
use toubilib\core\domain\entities\rdv\Rdv;

class RdvRepository implements RdvRepositoryInterface
{

    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $sql = "SELECT id, praticien_id, patient_id, patient_email, date_heure_debut, status, duree, date_heure_fin, date_creation, motif_visite FROM rdv";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $rdvs = [];
        foreach ($rows as $row) {
        $dateHeureDebut = new \DateTime($row['date_heure_debut']);

        $rdvs[] = new Rdv(
            $row['praticien_id'],
            $row['patient_id'],
            $dateHeureDebut,
            (int)$row['duree'],
            $row['patient_email'],
            (int)$row['status'],
            $row['motif_visite']
        );
    }

        return $rdvs;
    }

    public function getRdvByPeriod(DateTime $dateDebut, DateTime $dateFin): array
    {
        try {
            $sql = "SELECT 
                        id,
                        praticien_id,
                        patient_id,
                        patient_email,
                        date_heure_debut,
                        status,
                        duree,
                        date_heure_fin,
                        date_creation,
                        motif_visite
                    FROM rdv 
                    WHERE date_heure_debut >= :date_debut 
                    AND date_heure_debut < :date_fin
                    AND status > 0
                    ORDER BY date_heure_debut ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'date_debut' => $dateDebut->format('Y-m-d H:i:s'),
                'date_fin' => $dateFin->format('Y-m-d H:i:s')
            ]);

            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Conversion en array de données formatées
            $rdvs = [];
            foreach ($results as $row) {
                $rdvs[] = [
                    'id' => $row['id'],
                    'praticien_id' => $row['praticien_id'],
                    'patient_id' => $row['patient_id'],
                    'patient_email' => $row['patient_email'],
                    'date_heure_debut' => $row['date_heure_debut'],
                    'status' => (int)$row['status'],
                    'duree' => (int)$row['duree'],
                    'date_heure_fin' => $row['date_heure_fin'],
                    'date_creation' => $row['date_creation'],
                    'motif_visite' => $row['motif_visite']
                ];
            }

            return $rdvs;

        } catch (\PDOException $e) {
            error_log("Erreur SQL getRdvByPeriod: " . $e->getMessage());
            throw new \Exception("Erreur lors de la récupération des rendez-vous");
        }
    }

    public function getRdvByPraticienAndPeriod(string $praticienId, DateTime $dateDebut, DateTime $dateFin): array
    {
        try {
            $sql = "SELECT 
                        id,
                        praticien_id,
                        patient_id,
                        patient_email,
                        date_heure_debut,
                        status,
                        duree,
                        date_heure_fin,
                        date_creation,
                        motif_visite
                    FROM rdv 
                    WHERE praticien_id = :praticien_id 
                    AND date_heure_debut >= :date_debut 
                    AND date_heure_debut <= :date_fin
                    AND status > 0
                    ORDER BY date_heure_debut ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'praticien_id' => $praticienId,
                'date_debut' => $dateDebut->format('Y-m-d H:i:s'),
                'date_fin' => $dateFin->format('Y-m-d H:i:s')
            ]);

            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Conversion en array de données formatées
            $rdvs = [];
            foreach ($results as $row) {
                $rdvs[] = [
                    'id' => $row['id'],
                    'praticien_id' => $row['praticien_id'],
                    'patient_id' => $row['patient_id'],
                    'patient_email' => $row['patient_email'],
                    'date_heure_debut' => $row['date_heure_debut'],
                    'status' => (int)$row['status'],
                    'duree' => (int)$row['duree'],
                    'date_heure_fin' => $row['date_heure_fin'],
                    'date_creation' => $row['date_creation'],
                    'motif_visite' => $row['motif_visite']
                ];
            }

            return $rdvs;

        } catch (\PDOException $e) {
            error_log("Erreur SQL getRdvByPraticienAndPeriod: " . $e->getMessage());
            throw new \Exception("Erreur lors de la récupération des rendez-vous du praticien");
        }
    }

}