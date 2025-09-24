<?php

namespace toubilib\core\application\usecases;

use toubilib\core\application\ports\api\dtos\RdvDTO;
use toubilib\core\application\ports\api\ServiceRdvInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\RdvRepositoryInterface;

class ServiceRdv implements ServiceRdvInterface
{
    private RdvRepositoryInterface $rdvRepository;

    public function __construct(RdvRepositoryInterface $rdvRepository)
    {
        $this->rdvRepository = $rdvRepository;
    }

    public function listerRdv(): array
    {
        $rdvs = $this->rdvRepository->findAll();

        return array_map(function ($rdv) {
            return new RdvDTO(
                $rdv->getId(),
                $rdv->getPraticienId(),
                $rdv->getPatientId(),
                $rdv->getPatientEmail(),
                $rdv->getDateHeureDebut()->format('Y-m-d H:i:s'),
                $rdv->getStatus(),
                $rdv->getDuree(),
                $rdv->getDateHeureFin()?->format('Y-m-d H:i:s'),
                $rdv->getDateCreation()->format('Y-m-d H:i:s'),
                $rdv->getMotifVisite()
            );
        }, $rdvs);
    }

    
    public function getRdvPraticienPeriode(ServerRequestInterface $request, ResponseInterface $response, array $queryParams): ResponseInterface
    {
        try {
            $praticienId = $queryParams['praticien'];
            $debutPeriode = $queryParams['debutPeriode'];
            $finPeriode = $queryParams['finPeriode'];
            
            // Validation des paramètres
            $validation = $this->validatePraticienPeriod($praticienId, $debutPeriode, $finPeriode);
            if (!$validation['valid']) {
                $response->getBody()->write(json_encode([
                    'error' => 'Paramètres invalides',
                    'message' => $validation['message']
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            // Conversion en objets DateTime
            $dateDebut = new DateTime($debutPeriode . ' 00:00:00');
            $dateFin = new DateTime($finPeriode . ' 23:59:59');
            
            // Récupération des RDV
            $rdvs = $this->getRdvByPraticienAndPeriod($praticienId, $dateDebut, $dateFin);
            
            $result = [
                'success' => true,
                'type' => 'rdv_praticien_periode',
                'praticien_id' => $praticienId,
                'data' => $rdvs,
                'periode' => [
                    'debut' => $dateDebut->format('Y-m-d'),
                    'fin' => $dateFin->format('Y-m-d')
                ],
                'count' => count($rdvs)
            ];
            
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Erreur serveur',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
    
    public function getRdvPraticien(ServerRequestInterface $request, ResponseInterface $response, array $queryParams): ResponseInterface
    {
        $praticienId = $queryParams['praticien'];
        
        // Logique pour récupérer les RDV du praticien sans période (ex: prochains RDV)
        // Période par défaut : 30 prochains jours
        $dateDebut = new DateTime('today');
        $dateFin = new DateTime('+30 days');
        
        try {
            $rdvs = $this->getRdvByPraticienAndPeriod($praticienId, $dateDebut, $dateFin);
            
            $result = [
                'success' => true,
                'type' => 'rdv_praticien_defaut',
                'praticien_id' => $praticienId,
                'data' => $rdvs,
                'periode' => [
                    'debut' => $dateDebut->format('Y-m-d'),
                    'fin' => $dateFin->format('Y-m-d')
                ],
                'count' => count($rdvs),
                'message' => 'Période par défaut : 30 prochains jours'
            ];
            
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Erreur serveur',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
    
    public function getRdvGeneral(ServerRequestInterface $request, ResponseInterface $response, array $queryParams): ResponseInterface
    {
        // Logique pour afficher autre chose (statistiques générales, RDV du jour, etc.)
        try {
            $today = new DateTime('today');
            $tomorrow = new DateTime('tomorrow');
            
            // Exemple : RDV d'aujourd'hui pour tous les praticiens
            $rdvsToday = $this->getRdvByPeriod($today, $tomorrow);
            
            $result = [
                'success' => true,
                'type' => 'rdv_general',
                'data' => $rdvsToday,
                'date' => $today->format('Y-m-d'),
                'count' => count($rdvsToday),
                'message' => 'Rendez-vous d\'aujourd\'hui pour tous les praticiens'
            ];
            
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Erreur serveur',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
    
    public function validatePraticienPeriod($praticienId, $debutPeriode, $finPeriode): array
    {
        // Vérifier le format UUID du praticien_id
        if (!$this->isValidUuid($praticienId)) {
            return [
                'valid' => false,
                'message' => 'Format de praticien invalide. UUID attendu'
            ];
        }
        
        // Vérifier le format des dates
        if (!$this->isValidDateFormat($debutPeriode)) {
            return [
                'valid' => false,
                'message' => 'Format de debutPeriode invalide. Utilisez YYYY-MM-DD'
            ];
        }
        
        if (!$this->isValidDateFormat($finPeriode)) {
            return [
                'valid' => false,
                'message' => 'Format de finPeriode invalide. Utilisez YYYY-MM-DD'
            ];
        }
        
        try {
            $dateDebut = new DateTime($debutPeriode);
            $dateFin = new DateTime($finPeriode);
            
            // Vérifier que la date de début est antérieure ou égale à la date de fin
            if ($dateDebut > $dateFin) {
                return [
                    'valid' => false,
                    'message' => 'La date de début doit être antérieure ou égale à la date de fin'
                ];
            }
            
            // Vérifier que la période n'est pas trop longue (ex: max 6 mois)
            $interval = $dateDebut->diff($dateFin);
            if ($interval->days > 180) {
                return [
                    'valid' => false,
                    'message' => 'La période ne peut pas dépasser 180 jours'
                ];
            }
            
            // Vérifier que les dates ne sont pas trop anciennes
            $dateMin = new DateTime('-1 year');
            if ($dateDebut < $dateMin) {
                return [
                    'valid' => false,
                    'message' => 'La date de début ne peut pas être antérieure à 1 an'
                ];
            }
            
            // Vérifier que les dates ne sont pas trop dans le futur
            $dateMax = new DateTime('+2 years');
            if ($dateFin > $dateMax) {
                return [
                    'valid' => false,
                    'message' => 'La date de fin ne peut pas être supérieure à 2 ans dans le futur'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'valid' => false,
                'message' => 'Dates invalides : ' . $e->getMessage()
            ];
        }
        
        return ['valid' => true];
    }
    
    public function isValidDateFormat($date): bool
    {
        // Vérifier le format YYYY-MM-DD avec une regex
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }
        
        // Vérifier que la date est valide
        $parts = explode('-', $date);
        return checkdate($parts[1], $parts[2], $parts[0]);
    }

    public function isValidUuid($uuid): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid);
    }
    
    public function getRdvByPraticienAndPeriod(string $praticienId, DateTime $dateDebut, DateTime $dateFin): array
    {
        try {
            $sql = "SELECT 
                        id,
                        praticien_id,
                        patient_id,
                        patient_email,
                        date_heure_debut,
                        status,
                        duree,
                        date_heure_fin,
                        date_creation,
                        motif_visite
                    FROM rdv 
                    WHERE praticien_id = :praticien_id 
                    AND date_heure_debut >= :date_debut 
                    AND date_heure_debut <= :date_fin
                    AND status > 0
                    ORDER BY date_heure_debut ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'praticien_id' => $praticienId,
                'date_debut' => $dateDebut->format('Y-m-d H:i:s'),
                'date_fin' => $dateFin->format('Y-m-d H:i:s')
            ]);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Conversion en array de données formatées
            $rdvs = [];
            foreach ($results as $row) {
                $rdvs[] = [
                    'id' => $row['id'],
                    'praticien_id' => $row['praticien_id'],
                    'patient_id' => $row['patient_id'],
                    'patient_email' => $row['patient_email'],
                    'date_heure_debut' => $row['date_heure_debut'],
                    'status' => (int)$row['status'],
                    'duree' => (int)$row['duree'],
                    'date_heure_fin' => $row['date_heure_fin'],
                    'date_creation' => $row['date_creation'],
                    'motif_visite' => $row['motif_visite']
                ];
            }
            
            return $rdvs;
            
        } catch (\PDOException $e) {
            error_log("Erreur SQL getRdvByPraticienAndPeriod: " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des rendez-vous du praticien");
        }
    }
    
    public function getRdvByPeriod(DateTime $dateDebut, DateTime $dateFin): array
    {
        try {
            $sql = "SELECT 
                        id,
                        praticien_id,
                        patient_id,
                        patient_email,
                        date_heure_debut,
                        status,
                        duree,
                        date_heure_fin,
                        date_creation,
                        motif_visite
                    FROM rdv 
                    WHERE date_heure_debut >= :date_debut 
                    AND date_heure_debut < :date_fin
                    AND status > 0
                    ORDER BY date_heure_debut ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'date_debut' => $dateDebut->format('Y-m-d H:i:s'),
                'date_fin' => $dateFin->format('Y-m-d H:i:s')
            ]);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Conversion en array de données formatées
            $rdvs = [];
            foreach ($results as $row) {
                $rdvs[] = [
                    'id' => $row['id'],
                    'praticien_id' => $row['praticien_id'],
                    'patient_id' => $row['patient_id'],
                    'patient_email' => $row['patient_email'],
                    'date_heure_debut' => $row['date_heure_debut'],
                    'status' => (int)$row['status'],
                    'duree' => (int)$row['duree'],
                    'date_heure_fin' => $row['date_heure_fin'],
                    'date_creation' => $row['date_creation'],
                    'motif_visite' => $row['motif_visite']
                ];
            }
            
            return $rdvs;
            
        } catch (\PDOException $e) {
            error_log("Erreur SQL getRdvByPeriod: " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des rendez-vous");
        }
    }
}