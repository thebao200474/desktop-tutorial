<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\NguoiDung;
use ChemLearn\Models\TienDo;

class TienDoController extends BaseController
{
    private TienDo $tienDoModel;
    private NguoiDung $nguoiDungModel;

    public function __construct()
    {
        parent::__construct();
        $this->tienDoModel = new TienDo();
        $this->nguoiDungModel = new NguoiDung();
    }

    public function index(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser === null) {
            $_SESSION['flash_message'] = 'Vui lòng đăng nhập để xem tiến độ học.';
            $this->redirect('dangnhap.php');
        }

        $userId = (int)$currentUser['ma_user'];
        $records = $this->tienDoModel->getByUser($userId);
        $tongDung = array_sum(array_column($records, 'so_cau_dung'));
        $tongSai = array_sum(array_column($records, 'so_cau_sai'));
        $latestUser = $this->nguoiDungModel->findById($userId);
        if ($latestUser !== null) {
            $_SESSION['user'] = $latestUser;
        }

        $this->render('progress/index', [
            'title' => 'Tiến độ học tập',
            'records' => $records,
            'tongDung' => $tongDung,
            'tongSai' => $tongSai,
            'rank' => (int)($_SESSION['user']['diem_rank'] ?? 0),
        ]);
    }
}
