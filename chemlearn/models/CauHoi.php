<?php

declare(strict_types=1);

namespace ChemLearn\Models;

use PDO;
use PDOException;
use RuntimeException;

class CauHoi extends BaseModel
{
    private const SORT_MAPPING = [
        'newest' => 'created_at DESC',
        'views' => 'luot_xem DESC',
        'answers' => 'so_cau_tra_loi DESC',
    ];

    private static array $fallbackQuestions = [
        [
            'id' => 1,
            'tieu_de' => 'Vì sao oxi hóa – khử luôn trao đổi electron?',
            'noi_dung_html' => '<p>Cho ví dụ minh hoạ khi kim loại phản ứng với muối bạc nitrat.</p>',
            'trang_thai' => 'open',
            'luot_xem' => 24,
            'so_cau_tra_loi' => 1,
            'created_at' => '2024-01-01 09:00:00',
            'nguoi_hoi' => 'ChemLearn Mentor',
        ],
        [
            'id' => 2,
            'tieu_de' => 'Cách tính pH dung dịch sau khi pha loãng?',
            'noi_dung_html' => '<p>Giả sử có HCl 2M, muốn về 0.5M thì pha thế nào?</p>',
            'trang_thai' => 'open',
            'luot_xem' => 12,
            'so_cau_tra_loi' => 0,
            'created_at' => '2024-01-05 10:30:00',
            'nguoi_hoi' => 'Học viên A',
        ],
        [
            'id' => 3,
            'tieu_de' => 'Phân biệt ankan và anken bằng phản ứng nào?',
            'noi_dung_html' => '<p>Cần thí nghiệm đơn giản trong phòng học.</p>',
            'trang_thai' => 'solved',
            'luot_xem' => 40,
            'so_cau_tra_loi' => 2,
            'created_at' => '2024-02-01 14:15:00',
            'nguoi_hoi' => 'Bạn nhỏ yêu Hóa',
        ],
    ];

    private static bool $schemaEnsured = false;

    private function ensureSchema(): void
    {
        if (self::$schemaEnsured || !$this->hasConnection()) {
            return;
        }

        $pdo = $this->requireConnection();

        $queries = [
            "CREATE TABLE IF NOT EXISTS cau_hoi (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                tieu_de VARCHAR(255) NOT NULL,
                noi_dung_html TEXT NOT NULL,
                trang_thai ENUM('open','solved') DEFAULT 'open',
                luot_xem INT DEFAULT 0,
                so_cau_tra_loi INT DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )",
            "CREATE TABLE IF NOT EXISTS cau_tra_loi (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cau_hoi_id INT NOT NULL,
                user_id INT,
                noi_dung_html TEXT NOT NULL,
                is_best TINYINT(1) DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (cau_hoi_id) REFERENCES cau_hoi(id) ON DELETE CASCADE
            )",
            "CREATE TABLE IF NOT EXISTS file_dinh_kem (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cau_hoi_id INT,
                duong_dan VARCHAR(255),
                ten_goc VARCHAR(255),
                FOREIGN KEY (cau_hoi_id) REFERENCES cau_hoi(id) ON DELETE CASCADE
            )",
        ];

        foreach ($queries as $sql) {
            $pdo->exec($sql);
        }

        self::$schemaEnsured = true;
    }

    public function all(string $search, string $sort, int $limit, int $offset): array
    {
        if (!$this->hasConnection()) {
            return $this->filterFallback($search, $sort, $limit, $offset);
        }

        $this->ensureSchema();

        $orderBy = self::SORT_MAPPING[$sort] ?? self::SORT_MAPPING['newest'];
        $params = [];
        $conditions = [];

        if ($search !== '') {
            $conditions[] = 'tieu_de LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        $sql = 'SELECT ch.id, ch.user_id, ch.tieu_de, ch.trang_thai, ch.luot_xem, ch.so_cau_tra_loi, ch.created_at, nd.hoten AS nguoi_hoi'
            . ' FROM cau_hoi ch'
            . ' LEFT JOIN nguoidung nd ON nd.ma_user = ch.user_id';
        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY ' . $orderBy . ' LIMIT :limit OFFSET :offset';

        try {
            $pdo = $this->requireConnection();
            $stmt = $pdo->prepare($sql);

            foreach ($params as $placeholder => $value) {
                $stmt->bindValue($placeholder, $value, PDO::PARAM_STR);
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $rows !== [] ? $rows : $this->filterFallback($search, $sort, $limit, $offset);
        } catch (PDOException $exception) {
            return $this->filterFallback($search, $sort, $limit, $offset);
        }
    }

    public function allByUser(int $userId, string $search, string $sort, int $limit, int $offset): array
    {
        if (!$this->hasConnection()) {
            return [];
        }

        $this->ensureSchema();

        $orderBy = self::SORT_MAPPING[$sort] ?? self::SORT_MAPPING['newest'];
        $params = [
            ':user_id' => $userId,
        ];
        $conditions = ['ch.user_id = :user_id'];

        if ($search !== '') {
            $conditions[] = 'ch.tieu_de LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        $sql = 'SELECT ch.id, ch.user_id, ch.tieu_de, ch.trang_thai, ch.luot_xem, ch.so_cau_tra_loi, ch.created_at, nd.hoten AS nguoi_hoi'
            . ' FROM cau_hoi ch'
            . ' LEFT JOIN nguoidung nd ON nd.ma_user = ch.user_id'
            . ' WHERE ' . implode(' AND ', $conditions)
            . ' ORDER BY ' . $orderBy . ' LIMIT :limit OFFSET :offset';

        try {
            $pdo = $this->requireConnection();
            $stmt = $pdo->prepare($sql);

            foreach ($params as $placeholder => $value) {
                $stmt->bindValue($placeholder, $value, $placeholder === ':user_id' ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $exception) {
            return [];
        }
    }

    public function countAll(string $search): int
    {
        if (!$this->hasConnection()) {
            return $this->countFallback($search);
        }

        $this->ensureSchema();

        $sql = 'SELECT COUNT(*) FROM cau_hoi';
        $params = [];

        if ($search !== '') {
            $sql .= ' WHERE tieu_de LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        try {
            $pdo = $this->requireConnection();
            $stmt = $pdo->prepare($sql);

            foreach ($params as $placeholder => $value) {
                $stmt->bindValue($placeholder, $value, PDO::PARAM_STR);
            }

            $stmt->execute();

            return (int) $stmt->fetchColumn();
        } catch (PDOException $exception) {
            return $this->countFallback($search);
        }
    }

    public function countByUser(int $userId, string $search): int
    {
        if (!$this->hasConnection()) {
            return 0;
        }

        $this->ensureSchema();

        $sql = 'SELECT COUNT(*) FROM cau_hoi WHERE user_id = :user_id';
        $params = [':user_id' => $userId];

        if ($search !== '') {
            $sql .= ' AND tieu_de LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }

        try {
            $pdo = $this->requireConnection();
            $stmt = $pdo->prepare($sql);

            foreach ($params as $placeholder => $value) {
                $stmt->bindValue($placeholder, $value, $placeholder === ':user_id' ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            $stmt->execute();

            return (int) $stmt->fetchColumn();
        } catch (PDOException $exception) {
            return 0;
        }
    }

    public function create(array $data): int
    {
        if (!$this->hasConnection()) {
            throw new RuntimeException('Không thể lưu câu hỏi vì mất kết nối cơ sở dữ liệu.');
        }

        $sql = 'INSERT INTO cau_hoi (user_id, tieu_de, noi_dung_html, trang_thai) VALUES (:user_id, :tieu_de, :noi_dung_html, :trang_thai)';

        $pdo = $this->requireConnection();
        $this->ensureSchema();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $data['user_id'], $data['user_id'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':tieu_de', $data['tieu_de']);
        $stmt->bindValue(':noi_dung_html', $data['noi_dung_html']);
        $stmt->bindValue(':trang_thai', $data['trang_thai'] ?? 'open');
        $stmt->execute();

        return (int) $pdo->lastInsertId();
    }

    public function find(int $id): ?array
    {
        if (!$this->hasConnection()) {
            foreach (self::$fallbackQuestions as $question) {
                if ((int) $question['id'] === $id) {
                    return $question;
                }
            }

            return null;
        }

        try {
            $pdo = $this->requireConnection();
            $this->ensureSchema();
            $stmt = $pdo->prepare('SELECT ch.*, nd.hoten AS nguoi_hoi FROM cau_hoi ch LEFT JOIN nguoidung nd ON nd.ma_user = ch.user_id WHERE ch.id = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $question = $stmt->fetch(PDO::FETCH_ASSOC);

            return $question !== false ? $question : null;
        } catch (PDOException $exception) {
            return null;
        }
    }

    public function increaseView(int $id): void
    {
        if (!$this->hasConnection()) {
            return;
        }

        try {
            $pdo = $this->requireConnection();
            $this->ensureSchema();
            $stmt = $pdo->prepare('UPDATE cau_hoi SET luot_xem = luot_xem + 1 WHERE id = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $exception) {
            // Bỏ qua khi không cập nhật được
        }
    }

    public function increaseAnswerCount(int $id): void
    {
        if (!$this->hasConnection()) {
            return;
        }

        try {
            $pdo = $this->requireConnection();
            $this->ensureSchema();
            $stmt = $pdo->prepare('UPDATE cau_hoi SET so_cau_tra_loi = so_cau_tra_loi + 1 WHERE id = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $exception) {
            // Không làm gì thêm
        }
    }

    public function delete(int $id, int $userId): void
    {
        if (!$this->hasConnection()) {
            throw new RuntimeException('Không thể xóa câu hỏi vì mất kết nối cơ sở dữ liệu.');
        }

        $this->ensureSchema();

        try {
            $pdo = $this->requireConnection();
            $stmt = $pdo->prepare('DELETE FROM cau_hoi WHERE id = :id AND user_id = :user_id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('Không thể xóa câu hỏi này.');
            }
        } catch (PDOException $exception) {
            throw new RuntimeException('Không thể xóa câu hỏi: ' . $exception->getMessage());
        }
    }

    private function filterFallback(string $search, string $sort, int $limit, int $offset): array
    {
        $items = self::$fallbackQuestions;

        if ($search !== '') {
            $items = array_filter($items, static function (array $question) use ($search) {
                return stripos($question['tieu_de'], $search) !== false;
            });
        }

        $orderBy = $sort;
        usort($items, static function (array $a, array $b) use ($orderBy) {
            return match ($orderBy) {
                'views' => $b['luot_xem'] <=> $a['luot_xem'],
                'answers' => $b['so_cau_tra_loi'] <=> $a['so_cau_tra_loi'],
                default => strcmp($b['created_at'], $a['created_at']),
            };
        });

        return array_slice(array_values($items), $offset, $limit);
    }

    private function countFallback(string $search): int
    {
        if ($search === '') {
            return count(self::$fallbackQuestions);
        }

        $count = 0;
        foreach (self::$fallbackQuestions as $question) {
            if (stripos($question['tieu_de'], $search) !== false) {
                $count++;
            }
        }

        return $count;
    }
}
