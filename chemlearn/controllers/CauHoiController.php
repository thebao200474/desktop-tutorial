<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\CauHoi;
use ChemLearn\Models\TienDo;

class CauHoiController extends BaseController
{
    private CauHoi $cauHoiModel;
    private TienDo $tienDoModel;

    public function __construct()
    {
        parent::__construct();
        $this->cauHoiModel = new CauHoi();
        $this->tienDoModel = new TienDo();
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
                        date('Y-m-d')
                    );
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
}
