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
            $equations = $keyword === ''
                ? $model->getAll()
                : $model->search($keyword);
        } catch (Throwable $exception) {
            $equations = [];
        }

        $this->render('phuongtrinh/index', [
            'title' => '🔬 Phương trình Hóa học Phổ Biến',
            'equations' => $equations,
            'keyword' => $keyword,
        ]);
    }
}
