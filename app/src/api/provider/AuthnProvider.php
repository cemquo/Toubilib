<?php

namespace toubilib\api\provider;

use Exception;
use Firebase\JWT\JWT;
use toubilib\core\application\ports\api\dtos\AuthDTO;
use toubilib\core\application\ports\api\dtos\CredentialsDTO;
use toubilib\core\application\ports\api\dtos\ProfileDTO;
use toubilib\core\application\ports\api\ServiceAuthnInterface;
use toubilib\api\provider\JWTManager;

class AuthnProvider implements AuthnProviderInterface
{

    private ServiceAuthnInterface $serviceAuthn;

    public function __construct(ServiceAuthnInterface $serviceAuthn)
    {
        $this->serviceAuthn = $serviceAuthn;
    }

    public function signin(CredentialsDTO $credentials): AuthDTO
    {
        $profile = $this->serviceAuthn->signin($credentials);
        $acces_token = JWTManager::createAccessToken((array)$profile);
        $refresh_token = JWTManager::createRefreshToken((array)$profile);
        return new AuthDTO($profile, $acces_token, $refresh_token);
    }
}
