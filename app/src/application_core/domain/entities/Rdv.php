<?php

namespace toubilib\core\domain\entities\rdv;

use DateTime;
use Ramsey\Uuid\Uuid;

class Rdv
{
    private string $id;
    private string $praticien_id;
    private string $patient_id;
    private ?string $patient_email;
    private DateTime $date_heure_debut;
    private int $status;
    private int $duree;
    private ?DateTime $date_heure_fin;
    private DateTime $date_creation;
    private ?string $motif_visite;

    public function __construct(
        string $praticien_id,
        string $patient_id,
        DateTime $date_heure_debut,
        int $duree,
        ?string $patient_email = null,
        int $status = 1,
        ?string $motif_visite = null
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->praticien_id = $praticien_id;
        $this->patient_id = $patient_id;
        $this->patient_email = $patient_email;
        $this->date_heure_debut = $date_heure_debut;
        $this->status = $status;
        $this->duree = $duree;
        $this->calculateDateHeureFin();
        $this->date_creation = new DateTime();
        $this->motif_visite = $motif_visite;
    }

    private function calculateDateHeureFin(): void
    {
        if ($this->duree > 0) {
            $this->date_heure_fin = clone $this->date_heure_debut;
            $this->date_heure_fin->modify("+{$this->duree} minutes");
        }
    }

    /* GETTERS */

    public function getId(): string
    {
        return $this->id;
    }

    public function getPraticienId(): string
    {
        return $this->praticien_id;
    }

    public function getPatientId(): string
    {
        return $this->patient_id;
    }

    public function getPatientEmail(): ?string
    {
        return $this->patient_email;
    }

    public function getDateHeureDebut(): DateTime
    {
        return $this->date_heure_debut;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getDuree(): int
    {
        return $this->duree;
    }

    public function getDateHeureFin(): ?DateTime
    {
        return $this->date_heure_fin;
    }

    public function getDateCreation(): DateTime
    {
        return $this->date_creation;
    }

    public function getMotifVisite(): ?string
    {
        return $this->motif_visite;
    }

    /* SETTERS */

    public function setPatientEmail(?string $patient_email): void
    {
        $this->patient_email = $patient_email;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function setDuree(int $duree): void
    {
        $this->duree = $duree;
        $this->calculateDateHeureFin();
    }

    public function setMotifVisite(?string $motif_visite): void
    {
        $this->motif_visite = $motif_visite;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'praticien_id' => $this->praticien_id,
            'patient_id' => $this->patient_id,
            'patient_email' => $this->patient_email,
            'date_heure_debut' => $this->date_heure_debut->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'duree' => $this->duree,
            'date_heure_fin' => $this->date_heure_fin?->format('Y-m-d H:i:s'),
            'date_creation' => $this->date_creation->format('Y-m-d H:i:s'),
            'motif_visite' => $this->motif_visite
        ];
    }
}
