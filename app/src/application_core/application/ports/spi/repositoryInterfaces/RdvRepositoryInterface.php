<?php

namespace toubilib\core\application\ports\spi\repositoryInterfaces;

use DateTime;
use toubilib\core\application\ports\api\dtos\InputRendezVousDTO;

interface RdvRepositoryInterface
{
    public function findAll(): array;
    public function getRdvByPraticienAndPeriod(string $praticienId, DateTime $dateDebut, DateTime $dateFin): array;
    public function isPraticienDisponible(string $praticienId, DateTime $dateDebut, int $dureeMinutes): bool;
    public function create(InputRendezVousDTO $dto): void;
}
