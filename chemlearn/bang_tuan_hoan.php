<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use ChemLearn\Controllers\PeriodicTableController;

$controller = new PeriodicTableController();
$controller->index();
