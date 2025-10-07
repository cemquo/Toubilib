<?php

namespace toubilib\core\application\ports\api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubilib\core\application\ports\api\dtos\InputRendezVousDTO;

interface ServiceRdvInterface
{
    public function listerRdv(): array;
    public function creerRendezVous(InputRendezVousDTO $dto);
    public function annulerRendezVous(string $idRdv): void;
    public function getRdv(ServerRequestInterface $request, ResponseInterface $response, array $queryParams): array;
}