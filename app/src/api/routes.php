<?php
declare(strict_types=1);

use Slim\App;
use toubilib\api\actions\AfficherPraticienAction;
use toubilib\api\actions\AgendaPraticienAction;
use toubilib\api\actions\AnnulerRdvAction;
use toubilib\api\actions\CreerRdvAction;
use toubilib\api\actions\ListerPraticiensAction;
use toubilib\api\actions\ListerRdvAction;
use toubilib\api\middlewares\CreateRdvDtoMiddleware;


return function( App $app):App {

    $app->get('/praticiens', ListerPraticiensAction::class);

    $app->get('/praticien/{id}', AfficherPraticienAction::class);

    $app->get('/praticien/{id}/rdv', ListerRdvAction::class);

    $app->post('/creer_rdv', CreerRdvAction::class)->add(CreateRdvDtoMiddleware::class);

    $app->delete('/supp_rdv/{id}', AnnulerRdvAction::class);

    $app->get('/praticien/{id}/agenda', AgendaPraticienAction::class);


    return $app;
};