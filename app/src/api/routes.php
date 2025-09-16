<?php
declare(strict_types=1);

use toubilib\core\application\usecases\ServicePraticien;
use toubilib\core\presentation\actions\ListerPraticiensAction;
use toubilib\infra\repositories\PraticienRepository;


return function( \Slim\App $app):\Slim\App {

    $pdo = new PDO('pgsql:host=toubiprati.db;port=5432;dbname=toubiprat', 'toubiprat', 'toubiprat');
    $repo = new PraticienRepository($pdo);
    $service = new ServicePraticien($repo);

    $app->get('/praticiens', new ListerPraticiensAction($service));

    return $app;
};