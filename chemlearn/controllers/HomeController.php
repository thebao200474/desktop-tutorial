<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\BaiGiang;

class HomeController extends BaseController
{
    private BaiGiang $baiGiangModel;

    public function __construct()
    {
        parent::__construct();
        $this->baiGiangModel = new BaiGiang();
    }

    public function index(): void
    {
        $lessons = array_slice($this->baiGiangModel->all(), 0, 3);
        $this->render('home/index', [
            'title' => 'ChemLearn - Nền tảng học Hóa học',
            'lessons' => $lessons,
        ]);
    }
}
