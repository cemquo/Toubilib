<?php
declare(strict_types=1);

use Slim\App;
use toubilib\api\actions\AfficherPraticienAction;
use toubilib\api\actions\ListerPraticiensAction;


return function( App $app):App {

    $app->get('/praticiens', ListerPraticiensAction::class);

    $app->get('/praticien/{id}', AfficherPraticienAction::class);

    return $app;
};