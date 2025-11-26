<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use ChemLearn\Controllers\ChuyenDeController;

$controller = new ChuyenDeController();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$controller->show($id);
