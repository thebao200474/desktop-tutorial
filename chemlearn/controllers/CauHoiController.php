<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\CauHoi;
use ChemLearn\Models\NguoiDung;
use ChemLearn\Models\TienDo;

class CauHoiController extends BaseController
{
    private CauHoi $cauHoiModel;
    private TienDo $tienDoModel;
    private NguoiDung $nguoiDungModel;

    public function __construct()
    {
        parent::__construct();
        $this->cauHoiModel = new CauHoi();
        $this->tienDoModel = new TienDo();
        $this->nguoiDungModel = new NguoiDung();
    }

    public function index(): void
    {
        $questions = $this->cauHoiModel->all();
        $results = [];
        $score = null;
        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? null;
            if (!$this->validateCsrfToken($token)) {
                $message = 'Yêu cầu không hợp lệ.';
            } elseif ($questions === []) {
                $message = 'Ngân hàng câu hỏi đang được cập nhật, vui lòng thử lại sau.';
            } else {
                $answers = $_POST['answers'] ?? [];
                $correctCount = 0;

                foreach ($questions as $question) {
                    $questionId = (int)$question['ma_cauhoi'];
                    $userAnswer = strtoupper(trim((string)($answers[$questionId] ?? '')));
                    $isCorrect = $userAnswer !== '' && $userAnswer === strtoupper($question['dapandung']);
                    if ($isCorrect) {
                        $correctCount++;
                    }
                    $results[$questionId] = [
                        'userAnswer' => $userAnswer,
                        'isCorrect' => $isCorrect,
                        'correctAnswer' => strtoupper($question['dapandung']),
                    ];
                }

                $score = $correctCount . '/' . count($questions);
                $currentUser = $this->getCurrentUser();
                if ($currentUser !== null) {
                    $firstLesson = $questions[0]['ma_baigiang'] ?? null;
                    $this->tienDoModel->ghiNhan(
                        (int)$currentUser['ma_user'],
                        $firstLesson !== null ? (int)$firstLesson : null,
                        $correctCount,
                        count($questions) - $correctCount,
                        date('Y-m-d'),
                        'Luyện tập trắc nghiệm'
                    );
                    $newRank = $this->nguoiDungModel->incrementRank((int)$currentUser['ma_user'], max(1, $correctCount * 2));
                    $this->refreshUserRankInSession((int)$currentUser['ma_user'], $newRank);
                    $_SESSION['flash_message'] = 'Hoàn thành bài luyện tập! Điểm rank hiện tại: ' . $newRank;
                }
            }
        }

        $this->render('cauhoi/index', [
            'title' => 'Làm câu hỏi trắc nghiệm',
            'questions' => $questions,
            'results' => $results,
            'score' => $score,
            'message' => $message,
        ]);
    }

    private function refreshUserRankInSession(int $userId, int $newRank): void
    {
        if (empty($_SESSION['user']) || (int)($_SESSION['user']['ma_user'] ?? 0) !== $userId) {
            return;
        }

        $_SESSION['user']['diem_rank'] = $newRank;
    }
}
