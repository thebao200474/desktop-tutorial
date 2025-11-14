<?php

declare(strict_types=1);

namespace ChemLearn\Models;

use PDO;
use PDOException;

class ChatbotModel extends BaseModel
{
    public function findAnswer(string $message): ?string
    {
        $message = trim($message);
        if ($message === '') {
            return null;
        }

        $normalised = mb_strtolower($message, 'UTF-8');
        $bestScore = 0;
        $bestAnswer = null;

        $rows = $this->fetchFaqRows();

        foreach ($rows as $row) {
            $keywords = explode(';', $row['tu_khoa'] ?? '');
            $score = 0;

            foreach ($keywords as $keyword) {
                $keyword = trim(mb_strtolower($keyword, 'UTF-8'));
                if ($keyword !== '' && str_contains($normalised, $keyword)) {
                    $score++;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestAnswer = $row['cau_tra_loi'] ?? null;
            }
        }

        return $bestScore > 0 ? $bestAnswer : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchFaqRows(): array
    {
        if (!$this->hasConnection()) {
            return $this->fallbackFaq();
        }

        try {
            $pdo = $this->requireConnection();
            $stmt = $pdo->prepare('SELECT tu_khoa, cau_tra_loi FROM faq_hoa');
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            return $rows !== [] ? $rows : $this->fallbackFaq();
        } catch (PDOException) {
            return $this->fallbackFaq();
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function fallbackFaq(): array
    {
        return [
            [
                'tu_khoa' => 'axit;bronsted;proton',
                'cau_tra_loi' => 'Theo thuyết Bronsted–Lowry, axit là chất có khả năng cho proton (H⁺).',
            ],
            [
                'tu_khoa' => 'số oxi hóa;h2o;o',
                'cau_tra_loi' => 'Trong H₂O, số oxi hóa của nguyên tử oxy là -2.',
            ],
            [
                'tu_khoa' => 'liên kết ion;ion;cation;anion',
                'cau_tra_loi' => 'Liên kết ion được hình thành nhờ lực hút tĩnh điện giữa cation và anion.',
            ],
        ];
    }
}
