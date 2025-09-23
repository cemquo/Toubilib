<?php
declare(strict_types=1);

use toubilib\api\actions\ListerPraticiensAction;
use toubilib\core\application\ports\api\ServicePraticienInterface;

return [
    ListerPraticiensAction::class => function($c) {
        return new ListerPraticiensAction($c->get(ServicePraticienInterface::class));
    },
];
