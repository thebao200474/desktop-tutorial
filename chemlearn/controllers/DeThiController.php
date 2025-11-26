<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\CauHoiDeThi;
use ChemLearn\Models\DeThi;
use ChemLearn\Models\NguoiDung;
use ChemLearn\Models\TienDo;

class DeThiController extends BaseController
{
    private DeThi $deThiModel;
    private CauHoiDeThi $cauHoiDeThiModel;
    private TienDo $tienDoModel;
    private NguoiDung $nguoiDungModel;

    public function __construct()
    {
        parent::__construct();
        $this->deThiModel = new DeThi();
        $this->cauHoiDeThiModel = new CauHoiDeThi();
        $this->tienDoModel = new TienDo();
        $this->nguoiDungModel = new NguoiDung();
    }

    public function index(): void
    {
        $examId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if ($examId === 0) {
            $examId = null;
        }

        if ($examId === null) {
            $this->render('dethi/index', [
                'title' => 'Ngân hàng đề thi',
                'exams' => $this->deThiModel->all(),
            ]);
            return;
        }

        $exam = $this->deThiModel->find($examId);
        if ($exam === null) {
            $_SESSION['flash_message'] = 'Đề thi không tồn tại hoặc đã bị gỡ.';
            $this->redirect('de_thi.php');
        }

        $questions = $this->cauHoiDeThiModel->forExam($examId);
        $results = [];
        $score = null;
        $message = null;
        $showAnswers = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? null;
            if (!$this->validateCsrfToken($token)) {
                $message = 'Yêu cầu không hợp lệ.';
            } elseif ($questions === []) {
                $message = 'Đề thi đang được cập nhật nội dung.';
            } else {
                $answers = $_POST['answers'] ?? [];
                $correctCount = 0;

                foreach ($questions as $question) {
                    $questionId = (int)$question['id'];
                    $userAnswer = strtoupper(trim((string)($answers[$questionId] ?? '')));
                    $correctAnswer = strtoupper(trim((string)($question['dap_an'] ?? '')));
                    $isCorrect = $userAnswer !== '' && $correctAnswer !== '' && $userAnswer === $correctAnswer;
                    if ($isCorrect) {
                        $correctCount++;
                    }

                    $results[$questionId] = [
                        'userAnswer' => $userAnswer,
                        'correctAnswer' => $correctAnswer,
                        'isCorrect' => $isCorrect,
                    ];
                }

                $total = count($questions);
                $score = $correctCount . '/' . $total;
                $showAnswers = true;

                $currentUser = $this->getCurrentUser();
                if ($currentUser !== null) {
                    $userId = (int)$currentUser['ma_user'];
                    $this->tienDoModel->ghiNhan(
                        $userId,
                        null,
                        $correctCount,
                        $total - $correctCount,
                        date('Y-m-d'),
                        'Đề thi: ' . ($exam['ten_de'] ?? 'Không xác định')
                    );
                    $newRank = $this->nguoiDungModel->incrementRank($userId, max(5, $correctCount * 5));
                    $this->refreshUserInSession($userId, $newRank);
                    $_SESSION['flash_message'] = 'Bạn đã hoàn thành đề thi. Điểm rank hiện tại: ' . $newRank;
                }
            }
        }

        $this->render('dethi/take', [
            'title' => $exam['ten_de'] ?? 'Đề thi',
            'exam' => $exam,
            'questions' => $questions,
            'results' => $results,
            'score' => $score,
            'message' => $message,
            'showAnswers' => $showAnswers,
        ]);
    }

    private function refreshUserInSession(int $userId, int $newRank): void
    {
        if (empty($_SESSION['user']) || (int)($_SESSION['user']['ma_user'] ?? 0) !== $userId) {
            return;
        }

        $_SESSION['user']['diem_rank'] = $newRank;
    }
}
