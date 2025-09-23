<?php
declare(strict_types=1);

return [
    'db.host' => $_ENV['DB_HOST'] ?? 'toubiprati.db',
    'db.port' => $_ENV['DB_PORT'] ?? '5432',
    'db.name' => $_ENV['DB_NAME'] ?? 'toubiprat',
    'db.user' => $_ENV['DB_USER'] ?? 'toubiprat',
    'db.password' => $_ENV['DB_PASSWORD'] ?? 'toubiprat',
];
