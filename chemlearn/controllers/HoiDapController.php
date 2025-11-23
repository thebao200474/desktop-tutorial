<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\CauHoi;
use ChemLearn\Models\CauTraLoi;
use ChemLearn\Models\FileDinhKem;
use RuntimeException;
use Throwable;

class HoiDapController extends BaseController
{
    private CauHoi $questions;
    private CauTraLoi $answers;
    private FileDinhKem $attachments;

    public function __construct()
    {
        parent::__construct();
        $this->questions = new CauHoi();
        $this->answers = new CauTraLoi();
        $this->attachments = new FileDinhKem();
    }

    public function index(): void
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $sort = $_GET['sort'] ?? 'newest';
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 6;
        $mine = (bool) ($_GET['mine'] ?? false);

        $currentUser = $this->getCurrentUser();
        $userId = $currentUser['ma_user'] ?? null;

        try {
            if ($mine && $userId !== null) {
                $total = $this->questions->countByUser($userId, $search);
                $questions = $this->questions->allByUser($userId, $search, $sort, $perPage, ($page - 1) * $perPage);
            } else {
                $total = $this->questions->countAll($search);
                $questions = $this->questions->all($search, $sort, $perPage, ($page - 1) * $perPage);
            }
        } catch (Throwable $exception) {
            $total = 0;
            $questions = [];
        }

        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
            $questions = $mine && $userId !== null
                ? $this->questions->allByUser($userId, $search, $sort, $perPage, ($page - 1) * $perPage)
                : $this->questions->all($search, $sort, $perPage, ($page - 1) * $perPage);
        }

        $this->render('hoidap/index', [
            'title' => 'Hỏi – Đáp Hóa học',
            'questions' => $questions,
            'search' => $search,
            'sort' => $sort,
            'page' => $page,
            'totalPages' => $totalPages,
            'mine' => $mine,
        ]);
    }

    public function create(): void
    {
        $this->render('hoidap/create', [
            'title' => 'Đặt câu hỏi mới',
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(app_url('hoi-dap/hoi'));
        }

        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            $_SESSION['flash_message'] = 'CSRF token không hợp lệ.';
            $this->redirect(app_url('hoi-dap/hoi'));
        }

        $title = trim((string) ($_POST['tieu_de'] ?? ''));
        $content = trim((string) ($_POST['noi_dung_html'] ?? ''));
        $externalLink = trim((string) ($_POST['external_link'] ?? ''));

        if ($title === '' || $content === '') {
            $_SESSION['flash_message'] = 'Vui lòng nhập tiêu đề và mô tả chi tiết.';
            $this->redirect(app_url('hoi-dap/hoi'));
        }

        $currentUser = $this->getCurrentUser();
        $userId = $currentUser['ma_user'] ?? null;

        try {
            $questionId = $this->questions->create([
                'user_id' => $userId,
                'tieu_de' => $title,
                'noi_dung_html' => $this->sanitizeRichText($content),
                'trang_thai' => 'open',
            ]);

            $items = $this->handleAttachments($externalLink);
            if ($items !== []) {
                $this->attachments->createMany($questionId, $items);
            }

            $_SESSION['flash_message'] = 'Đã đăng câu hỏi thành công!';
            $this->redirect(app_url('hoi-dap'));
        } catch (RuntimeException $exception) {
            $_SESSION['flash_message'] = $exception->getMessage();
            $this->redirect(app_url('hoi-dap/hoi'));
        }
    }

    public function show(int $id): void
    {
        $question = $this->questions->find($id);
        if (!$question) {
            http_response_code(404);
            $_SESSION['flash_message'] = 'Câu hỏi không tồn tại hoặc đã bị xóa.';
            $this->redirect(app_url('hoi-dap'));
        }

        $this->questions->increaseView($id);
        $question['luot_xem'] = (int) ($question['luot_xem'] ?? 0) + 1;

        $answers = $this->answers->findByQuestion($id);
        $files = $this->attachments->findByQuestion($id);

        $currentUser = $this->getCurrentUser();
        $canDelete = !empty($currentUser['ma_user']) && (int) $currentUser['ma_user'] === (int) ($question['user_id'] ?? 0);

        $this->render('hoidap/show', [
            'title' => $question['tieu_de'],
            'question' => $question,
            'answers' => $answers,
            'files' => $files,
            'canDelete' => $canDelete,
        ]);
    }

    public function answer(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(app_url('hoi-dap/' . $id));
        }

        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            $_SESSION['flash_message'] = 'CSRF token không hợp lệ.';
            $this->redirect(app_url('hoi-dap/' . $id));
        }

        $question = $this->questions->find($id);
        if (!$question) {
            http_response_code(404);
            $_SESSION['flash_message'] = 'Câu hỏi không tồn tại.';
            $this->redirect(app_url('hoi-dap'));
        }

        $content = trim((string) ($_POST['answer_html'] ?? ''));
        if ($content === '') {
            $_SESSION['flash_message'] = 'Vui lòng nhập nội dung trả lời.';
            $this->redirect(app_url('hoi-dap/' . $id) . '#answer-form');
        }

        $currentUser = $this->getCurrentUser();
        $userId = $currentUser['ma_user'] ?? null;
        $canMarkBest = $userId !== null && isset($question['user_id']) && (int) $question['user_id'] === (int) $userId;
        $markBest = $canMarkBest && !empty($_POST['is_best']);

        try {
            $plain = htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $plain = nl2br($plain);

            $answerId = $this->answers->create([
                'cau_hoi_id' => $id,
                'user_id' => $userId,
                'noi_dung_html' => $this->sanitizeRichText($plain),
                'is_best' => $markBest,
            ]);

            if ($markBest) {
                $this->answers->markAsBest($id, $answerId);
            }

            $this->questions->increaseAnswerCount($id);
            $_SESSION['flash_message'] = 'Đã gửi câu trả lời!';
            $this->redirect(app_url('hoi-dap/' . $id) . '#answers');
        } catch (RuntimeException $exception) {
            $_SESSION['flash_message'] = $exception->getMessage();
            $this->redirect(app_url('hoi-dap/' . $id));
        }
    }

    public function delete(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(app_url('hoi-dap/' . $id));
        }

        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            $_SESSION['flash_message'] = 'CSRF token không hợp lệ.';
            $this->redirect(app_url('hoi-dap/' . $id));
        }

        $question = $this->questions->find($id);
        if (!$question) {
            http_response_code(404);
            $_SESSION['flash_message'] = 'Câu hỏi không tồn tại hoặc đã bị xóa.';
            $this->redirect(app_url('hoi-dap'));
        }

        $currentUser = $this->getCurrentUser();
        $userId = $currentUser['ma_user'] ?? null;

        if ($userId === null || (int) $question['user_id'] !== (int) $userId) {
            http_response_code(403);
            $_SESSION['flash_message'] = 'Bạn không thể xóa câu hỏi của người khác.';
            $this->redirect(app_url('hoi-dap/' . $id));
        }

        $redirectTarget = app_url('hoi-dap');

        $candidate = (string) ($_POST['redirect'] ?? '');
        if ($candidate !== '' && !preg_match('#^https?://#i', $candidate) && str_starts_with($candidate, '/')) {
            $redirectTarget = $candidate;
        } elseif (!empty($_SERVER['HTTP_REFERER']) && str_contains($_SERVER['HTTP_REFERER'], 'mine=1')) {
            $redirectTarget = app_url('hoi-dap?mine=1');
        }

        try {
            $this->questions->delete($id, (int) $userId);
            $_SESSION['flash_message'] = 'Đã xóa câu hỏi của bạn.';
            $this->redirect($redirectTarget);
        } catch (RuntimeException $exception) {
            $_SESSION['flash_message'] = $exception->getMessage();
            $this->redirect(app_url('hoi-dap/' . $id));
        }
    }

    private function sanitizeRichText(string $html): string
    {
        $allowedTags = '<p><br><strong><em><u><ol><ul><li><a><table><thead><tbody><tr><td><th><blockquote><code><pre>'; 
        $clean = strip_tags($html, $allowedTags);
        $clean = preg_replace('/on[a-z]+="[^"]*"/i', '', $clean ?? '');
        $clean = preg_replace('/javascript:/i', '', $clean ?? '');

        return trim($clean ?? '');
    }

    private function handleAttachments(string $externalLink): array
    {
        $items = [];
        $uploadDir = BASE_PATH . '/public/uploads/questions';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        if (isset($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
            $names = $_FILES['attachments']['name'];
            $tmpNames = $_FILES['attachments']['tmp_name'];
            $errors = $_FILES['attachments']['error'];

            foreach ($names as $index => $originalName) {
                if (!isset($tmpNames[$index], $errors[$index])) {
                    continue;
                }
                if ((int) $errors[$index] !== UPLOAD_ERR_OK) {
                    continue;
                }
                $tmpPath = $tmpNames[$index];
                if (!is_uploaded_file($tmpPath)) {
                    continue;
                }

                $extension = pathinfo((string) $originalName, PATHINFO_EXTENSION);
                $safeExtension = $extension !== '' ? '.' . preg_replace('/[^a-zA-Z0-9]/', '', $extension) : '';
                $filename = uniqid('question_', true) . $safeExtension;
                $destination = $uploadDir . '/' . $filename;

                if (move_uploaded_file($tmpPath, $destination)) {
                    $items[] = [
                        'duong_dan' => $filename,
                        'ten_goc' => $originalName,
                    ];
                }
            }
        }

        if ($externalLink !== '' && filter_var($externalLink, FILTER_VALIDATE_URL)) {
            $items[] = [
                'duong_dan' => $externalLink,
                'ten_goc' => $externalLink,
            ];
        }

        return $items;
    }
}
