<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use ChemLearn\Controllers\CauHoiController;

$controller = new CauHoiController();
$controller->index();
