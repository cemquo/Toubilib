<?php

require_once __DIR__ . '/vendor/autoload.php';

use toubilib\infra\repositories\PraticienRepository;
use toubilib\core\application\usecases\ServicePraticien;

// Connexion PDO
$pdo = new PDO("pgsql:host=toubiprati.db;port=5432;dbname=toubiprat", "toubiprat", "toubiprat");

// Repo + service
$repo = new PraticienRepository($pdo);
$service = new ServicePraticien($repo);

// Récupération des praticiens
$praticiens = $service->listerPraticiens();

// Affichage brut
header("Content-Type: application/json");
echo json_encode($praticiens, JSON_PRETTY_PRINT);
