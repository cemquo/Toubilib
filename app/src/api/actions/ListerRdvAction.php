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
        $praticienId = $request->getAttribute('id'); // ID depuis l'URL
        $queryParams = $request->getQueryParams();
        $queryParams['praticien'] = $praticienId; // injecter l'ID dans le tableau

        if (isset($queryParams['praticien'], $queryParams['debutPeriode'], $queryParams['finPeriode'])) {
            return $this->serviceRdv->getRdvPraticienPeriode($request, $response, $queryParams);
        }

        if (isset($queryParams['praticien'])) {
            return $this->serviceRdv->getRdvPraticien($request, $response, $queryParams);
        }

        return $this->serviceRdv->getRdv($request, $response, $queryParams);

    }
}
