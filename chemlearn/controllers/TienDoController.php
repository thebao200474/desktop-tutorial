<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\TienDo;

class TienDoController extends BaseController
{
    private TienDo $tienDoModel;

    public function __construct()
    {
        parent::__construct();
        $this->tienDoModel = new TienDo();
    }

    public function index(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser === null) {
            $_SESSION['flash_message'] = 'Vui lòng đăng nhập để xem tiến độ học.';
            $this->redirect('dangnhap.php');
        }

        $records = $this->tienDoModel->getByUser((int)$currentUser['ma_user']);
        $tongDung = array_sum(array_column($records, 'so_cau_dung'));
        $tongSai = array_sum(array_column($records, 'so_cau_sai'));

        $this->render('progress/index', [
            'title' => 'Tiến độ học tập',
            'records' => $records,
            'tongDung' => $tongDung,
            'tongSai' => $tongSai,
        ]);
    }
}
