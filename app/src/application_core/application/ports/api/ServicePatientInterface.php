<?php

namespace toubilib\core\application\ports\api;

use toubilib\core\application\ports\api\dtos\PatientDTO;

interface ServicePatientInterface
{
    public function creerComptePatient(PatientDTO $patientDTO): void;
}