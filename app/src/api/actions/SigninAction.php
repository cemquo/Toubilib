<?php

namespace toubilib\api\actions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubilib\api\provider\AuthnProvider;
use toubilib\core\application\ports\api\dtos\CredentialsDTO;

class SigninAction
{
    private AuthnProvider $authProvider;

    public function __construct(AuthnProvider $authProvider)
    {
        $this->authProvider = $authProvider;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();
        $credentials = new CredentialsDTO($body['email'], $body['password']);
        $authDTO = $this->authProvider->signin($credentials);
        $payload = [
            'profile' => $authDTO->getProfile(),
            'accessToken' => $authDTO->getAccessToken(),
            'refreshToken' => $authDTO->getRefreshToken(),
        ];
        $response->getBody()->write(json_encode($payload, JSON_PRETTY_PRINT));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}