<?php

namespace toubilib\api\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use toubilib\core\application\ports\api\dtos\InputRendezVousDTO;

class CreateRdvDtoMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Vérifie content-type
        $contentType = $request->getHeaderLine('Content-Type');
        if (stripos($contentType, 'application/json') === false) {
            return $this->jsonError($request, 400, "Content-Type attendu: application/json");
        }

        $payload = json_decode((string)$request->getBody(), true);
        if (!is_array($payload)) {
            return $this->jsonError($request, 400, "Corps JSON invalide");
        }

        // Liste des champs autorisés
        $allowed = ['praticien_id', 'patient_id', 'date_heure_debut', 'duree', 'motif_visite'];
        $unknown = array_diff(array_keys($payload), $allowed);
        if (!empty($unknown)) {
            return $this->jsonError($request, 400, "Champs non supportés: " . implode(', ', $unknown));
        }

        // Contrôles de présence
        $required = ['praticien_id', 'patient_id', 'date_heure_debut', 'duree'];
        foreach ($required as $key) {
            if (!array_key_exists($key, $payload)) {
                return $this->jsonError($request, 400, "Champ manquant: $key");
            }
        }

        // Contrôles de format
        $praticienId = (string)$payload['praticien_id'];
        $patientId   = (string)$payload['patient_id'];
        $dateStr     = (string)$payload['date_heure_debut'];
        $duree       = $payload['duree'];
        $motif       = $payload['motif_visite'] ?? null;

        if (!$this->isUuid($praticienId)) {
            return $this->jsonError($request, 400, "praticien_id doit être un UUID v4");
        }
        if (!$this->isUuid($patientId)) {
            return $this->jsonError($request, 400, "patient_id doit être un UUID v4");
        }

        // ISO-8601 recommandé: "YYYY-MM-DDTHH:MM:SSZ" ou avec offset
        try {
            $date = new \DateTime($dateStr);
        } catch (\Exception $e) {
            return $this->jsonError($request, 400, "date_heure_debut invalide (ISO-8601 recommandé)");
        }

        // Normalisation en UTC (base de données souvent en UTC)
        $date->setTimezone(new \DateTimeZone('UTC'));

        if (!is_int($duree)) {
            if (is_string($duree) && ctype_digit($duree)) {
                $duree = (int)$duree;
            } else {
                return $this->jsonError($request, 400, "duree doit être un entier (minutes)");
            }
        }
        if ($duree <= 0 || $duree > 24 * 60) {
            return $this->jsonError($request, 400, "duree doit être comprise entre 1 et 1440 minutes");
        }

        if ($motif !== null) {
            if (!is_string($motif)) {
                return $this->jsonError($request, 400, "motif_visite doit être une chaîne");
            }
            $motif = trim($motif);
            if ($motif === '') {
                $motif = null;
            }
            // Option: limiter la taille
            if ($motif !== null && mb_strlen($motif) > 255) {
                return $this->jsonError($request, 400, "motif_visite ne doit pas dépasser 255 caractères");
            }
        }

        $dto = new InputRendezVousDTO(
            $praticienId,
            $patientId,
            $date,
            $duree,
            $motif
        );

        $request = $request->withAttribute('inputRdvDto', $dto);
        return $handler->handle($request);
    }

    private function jsonError(ServerRequestInterface $request, int $status, string $message): ResponseInterface
    {
        $response = $request->getAttribute('response');
        // Si non présent, on crée une réponse PSR-7 simple (selon votre stack Slim, ce sera fourni différemment)
        if (!$response instanceof \Psr\Http\Message\ResponseInterface) {
            // Slim fournit en général la Response via le handler, mais au cas où:
            $factory = new \Slim\Psr7\Factory\ResponseFactory();
            $response = $factory->createResponse($status);
        } else {
            $response = $response->withStatus($status);
        }
        $response->getBody()->write(json_encode(['error' => $message], JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function isUuid(string $uuid): bool
    {
        return (bool)preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid
        );
    }
}