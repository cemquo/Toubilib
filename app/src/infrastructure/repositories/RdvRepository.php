<?php

namespace toubilib\infra\repositories;

use PDO;
use DateTime;
use toubilib\core\application\ports\api\dtos\RdvDTO;
use Ramsey\Uuid\Uuid;
use toubilib\core\application\ports\api\dtos\InputRendezVousDTO;
use toubilib\core\application\ports\spi\repositoryInterfaces\RdvRepositoryInterface;

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

        $rdvs[] = new RdvDTO(
            $row['id'],
            $row['praticien_id'],
            $row['patient_id'],
            $row['patient_email'],
            $row['date_heure_debut'],
            $row['status'],
            $row['duree'],
            $row['date_heure_fin'],
            $row['date_creation'],
            $row['motif_visite']
        );
    }

        return $rdvs;
    }

    public function getRdvByPraticienAndPeriod(string $praticienId, DateTime $dateDebut, DateTime $dateFin): array
    {
        try {
            $sql = "SELECT * FROM rdv 
            WHERE praticien_id = :praticien_id 
            AND date_heure_debut >= :date_debut 
            AND date_heure_debut <= :date_fin
            AND status > 0
            ORDER BY date_heure_debut ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'praticien_id' => $praticienId,
                'date_debut'   => $dateDebut->format('Y-m-d H:i:s'),
                'date_fin'     => $dateFin->format('Y-m-d H:i:s')
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Conversion en array de données formatées
            $rdvs = [];
            foreach ($results as $row) {
                $rdvs[] = new RdvDTO(
                    $row['id'],
                    $row['praticien_id'],
                    $row['patient_id'],
                    $row['patient_email'],
                    $row['date_heure_debut'],
                    $row['status'],
                    $row['duree'],
                    $row['date_heure_fin'],
                    $row['date_creation'],
                    $row['motif_visite']
                );
            }

            return $rdvs;

        } catch (\PDOException $e) {
            error_log("Erreur SQL getRdvByPraticienAndPeriod: " . $e->getMessage());
            throw new \Exception("Erreur lors de la récupération des rendez-vous du praticien");
        }
    }

    public function create(InputRendezVousDTO $dto): void
    {
        $sql = "INSERT INTO rdv
            (id, praticien_id, patient_id, patient_email, date_heure_debut, duree, motif_visite, date_creation, status)
            VALUES (:id, :praticien_id, :patient_id, :patient_email, :date_heure_debut, :duree, :motif_visite, NOW(), 0)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => Uuid::uuid4()->toString(),
            'praticien_id' => $dto->getPraticienId(),
            'patient_id' => $dto->getPatientId(),
            'patient_email' => $dto->getPatientEmail(),
            'date_heure_debut' => $dto->getDateHeureDebut()->format('Y-m-d H:i:s'),
            'duree' => $dto->getDuree(),
            'motif_visite' => $dto->getMotifVisite(),
        ]);
    }

}