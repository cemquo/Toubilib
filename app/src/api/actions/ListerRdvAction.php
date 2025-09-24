<?php

namespace toubilib\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubilib\core\application\usecases\ServiceRdv;

class ListerRdvAction
{
    private ServiceRdv $serviceRdv;

    public function __construct(ServiceRdv $serviceRdv)
    {
        $this->serviceRdv = $serviceRdv;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        
        // Si on a un praticien_id avec période, on affiche les RDV du praticien
        if (isset($queryParams['praticien']) && !empty($queryParams['praticien']) &&
            isset($queryParams['debutPeriode']) && !empty($queryParams['debutPeriode']) &&
            isset($queryParams['finPeriode']) && !empty($queryParams['finPeriode'])) {
            
            return $this->getRdvPraticienPeriode($request, $response, $queryParams);
        }
        
        // Si on a seulement un praticien_id, on peut afficher autre chose
        if (isset($queryParams['praticien']) && !empty($queryParams['praticien'])) {
            return $this->getRdvPraticien($request, $response, $queryParams);
        }
        
        // Sinon, comportement par défaut (liste générale, autre logique...)
        return $this->getRdvGeneral($request, $response, $queryParams);
    }
    
}

?>