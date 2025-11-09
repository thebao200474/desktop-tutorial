<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\HoiDap;

class HoiDapController extends BaseController
{
    private HoiDap $hoiDapModel;

    public function __construct()
    {
        parent::__construct();
        $this->hoiDapModel = new HoiDap();
    }

    public function index(): void
    {
        $answer = null;
        $questionText = '';
        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? null;
            if (!$this->validateCsrfToken($token)) {
                $message = 'Yêu cầu không hợp lệ.';
            } else {
                $questionText = trim((string)($_POST['question'] ?? ''));
                if ($questionText === '') {
                    $message = 'Vui lòng nhập câu hỏi.';
                } else {
                    $answer = $this->generateAnswer($questionText);
                    $currentUser = $this->getCurrentUser();
                    $userId = is_array($currentUser) && isset($currentUser['ma_user']) ? (int)$currentUser['ma_user'] : null;
                    $this->hoiDapModel->store($userId, $questionText, $answer);
                }
            }
        }

        $history = $this->hoiDapModel->latest();
        $this->render('hoi_dap/index', [
            'title' => 'Hỏi đáp AI',
            'answer' => $answer,
            'questionText' => $questionText,
            'message' => $message,
            'history' => $history,
        ]);
    }

    private function generateAnswer(string $question): string
    {
        $questionLower = mb_strtolower($question);
        $patterns = [
            'oxi' => 'Oxi (O) là phi kim phổ biến, tham gia vào nhiều phản ứng oxi hóa khử.',
            'axit' => 'Axit là hợp chất khi tan trong nước phân li ra ion H+. Ví dụ: HCl, H2SO4.',
            'bazo' => 'Bazơ là chất khi tan trong nước phân li ra ion OH−. Ví dụ: NaOH, KOH.',
            'cân bằng' => 'Để cân bằng PTHH, hãy đảm bảo số nguyên tử mỗi nguyên tố ở hai vế bằng nhau và áp dụng phương pháp thăng bằng electron hoặc đại số.',
        ];

        foreach ($patterns as $key => $response) {
            if (str_contains($questionLower, $key)) {
                return $response;
            }
        }

        return 'ChemLearn AI đang học hỏi thêm. Vui lòng tham khảo giáo trình hoặc giảng viên để có câu trả lời chính xác.';
    }
}
