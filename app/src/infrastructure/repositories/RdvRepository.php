<?php

namespace toubilib\infra\repositories;

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
        $dateHeureDebut = new DateTime($row['date_heure_debut']);
    
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

}