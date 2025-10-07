<?php

namespace toubilib\core\application\usecases;

use toubilib\core\application\ports\api\dtos\CredentialsDTO;
use toubilib\core\application\ports\api\dtos\ProfileDTO;
use toubilib\core\application\ports\api\ServiceAuthnInterface;

class ServiceAuthn implements ServiceAuthnInterface
{

    public function register(CredentialsDTO $credentials, int $role): profileDTO
    {
        // TODO: Implement register() method.
    }

    public function byCredentials(CredentialsDTO $credentials): profileDTO
    {
        // TODO: Implement byCredentials() method.
    }
}