<?php
declare(strict_types=1);

use toubilib\core\application\ports\api\ServicePraticienInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\PraticienRepositoryInterface;
use toubilib\core\application\usecases\ServicePraticien;
use toubilib\infra\repositories\PraticienRepository;

return [
    PDO::class => function($c) {
        return new PDO(
            sprintf(
                "pgsql:host=%s;port=%s;dbname=%s",
                $c->get('db.host'),
                $c->get('db.port'),
                $c->get('db.name')
            ),
            $c->get('db.user'),
            $c->get('db.password')
        );
    },

    PraticienRepositoryInterface::class => function($c) {
        return new PraticienRepository($c->get(PDO::class));
    },

    ServicePraticienInterface::class => function($c) {
        return new ServicePraticien($c->get(PraticienRepositoryInterface::class));
    },
];
