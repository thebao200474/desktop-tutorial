<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use ChemLearn\Controllers\ChatController;

$controller = new ChatController();
$controller->respond();
