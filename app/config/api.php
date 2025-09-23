<?php
declare(strict_types=1);

use toubilib\core\presentation\actions\ListerPraticiensAction;
use toubilib\core\application\usecases\ServicePraticienInterface;

return [
    ListerPraticiensAction::class => function($c) {
        return new ListerPraticiensAction($c->get(ServicePraticienInterface::class));
    },
];
