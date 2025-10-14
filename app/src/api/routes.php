<?php
declare(strict_types=1);

use Slim\App;
use toubilib\api\actions\AfficherPraticienAction;
use toubilib\api\actions\AgendaPraticienAction;
use toubilib\api\actions\AnnulerRdvAction;
use toubilib\api\actions\CreerRdvAction;
use toubilib\api\actions\ListerPraticiensAction;
use toubilib\api\actions\ListerRdvAction;
use toubilib\api\actions\SigninAction;
use toubilib\api\middlewares\CreateRdvDtoMiddleware;


return function( App $app):App {

    $app->get('/praticiens', ListerPraticiensAction::class)->setName('ListerPraticiens');

    $app->get('/praticiens/{id}', AfficherPraticienAction::class)->setName('AfficherPraticien');

    $app->get('/praticiens/{id}/rdvs', ListerRdvAction::class)->setName('ListerRdvPraticien');

    $app->post('/rdvs', CreerRdvAction::class)->add(CreateRdvDtoMiddleware::class)->setName('CreerRdv');

    $app->delete('/rdvs/{id}', AnnulerRdvAction::class)->setName('AnnulerRdv');

    $app->get('/praticiens/{id}/agenda', AgendaPraticienAction::class)->setName('AgendaPraticien');

    $app->get('/rdvs/{id}', ListerRdvAction::class)->setName('AfficherRdv');

    $app->post('/signin', SigninAction::class)->setName('Signin');

    return $app;
};