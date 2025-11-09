<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use ChemLearn\Controllers\CanBangController;

$controller = new CanBangController();
$controller->index();
