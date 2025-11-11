<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\NguyenTo;

class PeriodicTableController extends BaseController
{
    private NguyenTo $nguyenToModel;

    public function __construct()
    {
        parent::__construct();
        $this->nguyenToModel = new NguyenTo();
    }

    public function index(): void
    {
        $elements = $this->nguyenToModel->all();
        $this->render('periodic/index', [
            'title' => 'Bảng tuần hoàn hóa học',
            'elements' => $elements,
        ]);
    }
}
