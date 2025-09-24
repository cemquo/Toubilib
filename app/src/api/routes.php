<?php
declare(strict_types=1);

use Slim\App;
use toubilib\api\actions\AfficherPraticienAction;
use toubilib\api\actions\ListerPraticiensAction;
use toubilib\api\actions\ListerRdvAction;


return function( App $app):App {

    $app->get('/praticiens', ListerPraticiensAction::class);

    $app->get('/praticien/{id}', AfficherPraticienAction::class);

    $app->get('/rdv', ListerRdvAction::class);

    return $app;
};