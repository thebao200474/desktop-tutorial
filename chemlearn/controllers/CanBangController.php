<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\PhanUng;

class CanBangController extends BaseController
{
    private PhanUng $phanUngModel;

    public function __construct()
    {
        parent::__construct();
        $this->phanUngModel = new PhanUng();
    }

    public function index(): void
    {
        $result = null;
        $message = null;
        $inputEquation = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? null;
            if (!$this->validateCsrfToken($token)) {
                $message = 'Yêu cầu không hợp lệ.';
            } else {
                $inputEquation = trim((string)($_POST['equation'] ?? ''));
                if ($inputEquation === '') {
                    $message = 'Vui lòng nhập phương trình cần cân bằng.';
                } else {
                    $result = $this->phanUngModel->findByEquation($inputEquation);
                    if ($result === null) {
                        $message = 'Đang cập nhật dữ liệu cho phương trình này.';
                    }
                }
            }
        }

        $this->render('canbang/index', [
            'title' => 'Cân bằng phương trình hóa học',
            'result' => $result,
            'message' => $message,
            'inputEquation' => $inputEquation,
        ]);
    }
}
