<?php

namespace toubilib\core\application\ports\api;

use toubilib\core\application\ports\api\dtos\InputRendezVousDTO;

interface ServiceRdvInterface
{
    public function listerRdv(): array;
    public function creerRendezVous(InputRendezVousDTO $dto);
}