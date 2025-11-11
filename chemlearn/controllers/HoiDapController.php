<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\HoiDap;
use ChemLearn\Services\TutorBot;

class HoiDapController extends BaseController
{
    private HoiDap $hoiDapModel;
    private TutorBot $tutorBot;

    public function __construct()
    {
        parent::__construct();
        $this->hoiDapModel = new HoiDap();
        $this->tutorBot = new TutorBot();
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
                    $history = $_SESSION['chat_history'] ?? [];
                    $answer = $this->tutorBot->respond($questionText, $history);
                    $currentUser = $this->getCurrentUser();
                    $userId = is_array($currentUser) && isset($currentUser['ma_user']) ? (int)$currentUser['ma_user'] : null;
                    $this->hoiDapModel->store($userId, $questionText, $answer);
                    $history[] = ['role' => 'user', 'message' => $questionText];
                    $history[] = ['role' => 'assistant', 'message' => $answer];
                    $_SESSION['chat_history'] = array_slice($history, -20);
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
}
