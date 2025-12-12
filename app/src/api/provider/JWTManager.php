<?php

namespace toubilib\api\provider;

use Firebase\JWT\JWT;

class JWTManager
{
    public static function createAccessToken(array $payload): string
    {
        return JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');
    }

    public static function decodeToken(string $token): array
    {
        $res = JWT::decode($token, new \Firebase\JWT\Key(getenv('JWT_SECRET'), 'HS256'));
        return (array)$res;
    }

    public static function createRefreshToken(array $payload): string
    {
        return JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');
    }
}