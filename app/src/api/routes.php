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

    $app->get('/praticiens/{id}', AfficherPraticienAction::class);

    $app->get('/praticiens/{id}/rdv', ListerRdvAction::class);

    $app->post('/rdv', CreerRdvAction::class)->add(CreateRdvDtoMiddleware::class);

    $app->delete('/rdv/{id}', AnnulerRdvAction::class);

    $app->get('/praticiens/{id}/agenda', AgendaPraticienAction::class);


    return $app;
};