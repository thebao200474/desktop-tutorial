<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use ChemLearn\Controllers\DeThiController;

$controller = new DeThiController();
$controller->index();
