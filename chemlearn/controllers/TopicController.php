<?php

namespace ChemLearn\Controllers;

use ChemLearn\Models\LessonModel;
use ChemLearn\Models\TopicModel;

/**
 * TopicController hiển thị trang chi tiết từng chủ đề học tập.
 */
class TopicController extends BaseController
{
    public function __construct(
        private TopicModel $topicModel,
        private LessonModel $lessonModel
    ) {
    }

    /**
     * Hiển thị danh sách toàn bộ chủ đề cùng bộ đếm bài giảng và ô tìm kiếm.
     */
    public function index(): void
    {
        $q = trim($_GET['q'] ?? '');

        if ($q !== '') {
            $topics = $this->topicModel->searchByName($q);
        } else {
            $topics = $this->topicModel->getAllWithCounts();
        }

        $this->view('topics/index', [
            'pageTitle' => 'Chuyên đề Hóa học',
            'topics' => $topics,
            'q' => $q,
        ]);
    }

    /**
     * Hiển thị thông tin chủ đề và các bài giảng liên quan.
     */
    public function show(int $id): void
    {
        $topic = $this->topicModel->find((int) $id);
        if (!$topic) {
            http_response_code(404);
            echo 'Không tìm thấy chủ đề';
            return;
        }

        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $limit = 8;
        $offset = ($page - 1) * $limit;

        $lessons = $this->lessonModel->getByTopic((int) $id, $limit, $offset);
        $total = $this->lessonModel->countByTopic((int) $id);
        $totalPages = (int) ceil($total / $limit);
        if ($totalPages === 0) {
            $totalPages = 1;
        }

        $this->view('topics/show', [
            'pageTitle' => 'Chủ đề: ' . ($topic['ten_chude'] ?? ''),
            'topic' => $topic,
            'lessons' => $lessons,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalLessons' => $total,
        ]);
    }
}
