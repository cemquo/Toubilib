<?php

namespace toubilib\core\application\usecases;

use toubilib\core\application\ports\api\ServicePatientInterface;
use toubilib\core\application\ports\api\dtos\PatientDTO;

use toubilib\core\application\ports\spi\repositoryInterfaces\AuthRepositoryInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\PatientRepositoryInterface;
use toubilib\core\application\ports\api\ServiceAuthnInterface;
use toubilib\core\application\ports\api\dtos\CredentialsDTO;
use toubilib\core\domain\entities\Patient;

class ServicePatient implements ServicePatientInterface
{
    private ServiceAuthnInterface $serviceAuthn;
    private PatientRepositoryInterface $patientRepository;

    public function __construct(
        ServiceAuthnInterface $serviceAuthn,
        PatientRepositoryInterface $patientRepository
    ) {
        $this->serviceAuthn = $serviceAuthn;
        $this->patientRepository = $patientRepository;
    }

    public function creerComptePatient(PatientDTO $patientDTO): void
    {
        $credentials = new CredentialsDTO($patientDTO->email, $patientDTO->password);
        $profile = $this->serviceAuthn->register($credentials, 1);
        
        $patient = new Patient(
            $profile->id,
            $patientDTO->nom,
            $patientDTO->prenom,
            $patientDTO->dateNaissance ? new \DateTime($patientDTO->dateNaissance) : null,
            $patientDTO->adresse,
            $patientDTO->codePostal,
            $patientDTO->ville,
            $patientDTO->email,
            $patientDTO->telephone
        );

        $this->patientRepository->createPatient($patient);
    }
}