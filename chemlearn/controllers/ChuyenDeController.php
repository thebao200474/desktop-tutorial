<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\BaiGiang;

class ChuyenDeController extends BaseController
{
    private BaiGiang $baiGiangModel;

    public function __construct()
    {
        parent::__construct();
        $this->baiGiangModel = new BaiGiang();
    }

    public function index(): void
    {
        $this->render('chuyende/index', [
            'title' => 'Chuyên đề Hóa học',
            'lessons' => $this->baiGiangModel->all(),
        ]);
    }

    public function show(int $id): void
    {
        $lesson = $this->baiGiangModel->find($id);
        if ($lesson === null) {
            $this->redirect('chuyende.php');
        }

        $this->render('chuyende/detail', [
            'title' => $lesson['ten_baigiang'],
            'lesson' => $lesson,
        ]);
    }
}
