<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

class PeriodicTableController extends BaseController
{
    public function index(): void
    {
        $path = BASE_PATH . '/data/elements.json';
        $json = is_file($path) ? file_get_contents($path) : '[]';
        $elements = json_decode($json, true);

        if (!is_array($elements)) {
            $elements = [];
        }

        usort($elements, static fn(array $a, array $b): int => ($a['Z'] ?? 0) <=> ($b['Z'] ?? 0));

        $this->render('periodic_table/index', [
            'title' => 'Bảng tuần hoàn – ChemLearn',
            'elementsVar' => $elements,
        ]);
    }
}
