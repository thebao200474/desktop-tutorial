<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\PhuongTrinhModel;
use Throwable;

class PhuongTrinhController extends BaseController
{
    public function index(): void
    {
        $model = new PhuongTrinhModel();
        $keyword = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

        try {
            $allEquations = $model->getAll();
            $equations = $keyword === ''
                ? $allEquations
                : $model->search($keyword);
        } catch (Throwable $exception) {
            $allEquations = $model->getAll();
            $equations = $keyword === '' ? $allEquations : [];
        }

        $this->render('phuongtrinh/index', [
            'title' => '🔬 Phương trình Hóa học Phổ Biến',
            'equations' => $equations,
            'keyword' => $keyword,
            'allEquations' => $allEquations ?? [],
        ]);
    }
}
