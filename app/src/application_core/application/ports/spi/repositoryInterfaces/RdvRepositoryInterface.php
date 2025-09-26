<?php

namespace toubilib\core\application\ports\spi\repositoryInterfaces;

use toubilib\core\application\ports\api\dtos\InputRendezVousDTO;

interface RdvRepositoryInterface
{
    public function findAll(): array;
    public function create(InputRendezVousDTO $dto): void;
}
