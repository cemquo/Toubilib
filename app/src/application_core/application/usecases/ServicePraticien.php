<?php

namespace toubilib\core\application\usecases;

use toubilib\core\application\dto\PraticienDTO;
use toubilib\infra\repositories\PraticienRepositoryInterface;

class ServicePraticien implements ServicePraticienInterface
{
    private PraticienRepositoryInterface $praticienRepository;

    public function __construct(PraticienRepositoryInterface $praticienRepository)
    {
        $this->praticienRepository = $praticienRepository;
    }

    public function listerPraticiens(): array
    {
        $praticiens = $this->praticienRepository->findAll();

        return array_map(function ($praticien) {
            return new PraticienDTO(
                $praticien->getId(),
                $praticien->getNom(),
                $praticien->getPrenom(),
                $praticien->getSpecialite(),
                $praticien->getVille(),
                $praticien->getEmail()
            );
        }, $praticiens);
    }
}