<?php

declare(strict_types=1);

namespace ChemLearn\Models;

use PDO;
use PDOException;

class PhuongTrinhModel extends BaseModel
{
    private const ASCII_TO_SUBSCRIPT = [
        '0' => '₀',
        '1' => '₁',
        '2' => '₂',
        '3' => '₃',
        '4' => '₄',
        '5' => '₅',
        '6' => '₆',
        '7' => '₇',
        '8' => '₈',
        '9' => '₉',
    ];

    private const SUBSCRIPT_TO_ASCII = [
        '₀' => '0',
        '₁' => '1',
        '₂' => '2',
        '₃' => '3',
        '₄' => '4',
        '₅' => '5',
        '₆' => '6',
        '₇' => '7',
        '₈' => '8',
        '₉' => '9',
    ];

    private static ?array $fallbackCache = null;

    public function getAll(): array
    {
        if (!$this->hasConnection()) {
            return $this->fallbackData();
        }

        try {
            $pdo = $this->requireConnection();
            $stmt = $pdo->query('SELECT id, phuong_trinh, loai_phan_ung, giai_thich, nhom_phan_ung FROM phuongtrinh ORDER BY id');
            $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

            return $rows !== [] ? $rows : $this->fallbackData();
        } catch (PDOException $exception) {
            return $this->fallbackData();
        }
    }

    public function search(string $keyword): array
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return $this->getAll();
        }

        if (!$this->hasConnection()) {
            return $this->filterFallback($keyword);
        }

        try {
            $pdo = $this->requireConnection();
            $columns = ['phuong_trinh', 'loai_phan_ung', 'giai_thich', 'nhom_phan_ung'];
            $patterns = $this->buildPatterns($keyword);

            if ($patterns === []) {
                return $this->getAll();
            }

            $conditions = [];
            $bindings = [];
            $index = 0;

            foreach ($patterns as $pattern) {
                foreach ($columns as $column) {
                    $placeholder = ':kw' . $index++;
                    $conditions[] = sprintf('%s LIKE %s', $column, $placeholder);
                    $bindings[$placeholder] = '%' . $pattern . '%';
                }
            }

            $sql = 'SELECT id, phuong_trinh, loai_phan_ung, giai_thich, nhom_phan_ung FROM phuongtrinh'
                . ' WHERE ' . implode(' OR ', $conditions)
                . ' ORDER BY id';

            $stmt = $pdo->prepare($sql);

            foreach ($bindings as $placeholder => $value) {
                $stmt->bindValue($placeholder, $value, PDO::PARAM_STR);
            }

            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results !== [] ? $results : $this->filterFallback($keyword);
        } catch (PDOException $exception) {
            return $this->filterFallback($keyword);
        }
    }

    private function buildPatterns(string $keyword): array
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return [];
        }

        $variants = [
            $keyword,
            mb_strtolower($keyword, 'UTF-8'),
            strtr($keyword, self::ASCII_TO_SUBSCRIPT),
            strtr($keyword, self::SUBSCRIPT_TO_ASCII),
        ];

        $filtered = [];
        foreach ($variants as $value) {
            $value = trim($value);
            if ($value !== '') {
                $filtered[$value] = true;
            }
        }

        return array_keys($filtered);
    }

    private function filterFallback(string $keyword): array
    {
        $patterns = $this->buildPatterns($keyword);

        if ($patterns === []) {
            return $this->fallbackData();
        }

        $results = [];

        foreach ($this->fallbackData() as $equation) {
            if ($this->matchesPatterns($equation, $patterns)) {
                $results[] = $equation;
            }
        }

        return $results;
    }

    private function matchesPatterns(array $equation, array $patterns): bool
    {
        $fields = ['phuong_trinh', 'loai_phan_ung', 'giai_thich', 'nhom_phan_ung'];

        foreach ($fields as $field) {
            if (!isset($equation[$field])) {
                continue;
            }

            $original = mb_strtolower((string) $equation[$field], 'UTF-8');
            $ascii = strtr($original, self::SUBSCRIPT_TO_ASCII);

            foreach ($patterns as $pattern) {
                $patternLower = mb_strtolower($pattern, 'UTF-8');
                $patternAscii = strtr($patternLower, self::SUBSCRIPT_TO_ASCII);

                if (mb_stripos($original, $patternLower, 0, 'UTF-8') !== false) {
                    return true;
                }

                if (mb_stripos($ascii, $patternAscii, 0, 'UTF-8') !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    private function fallbackData(): array
    {
        if (self::$fallbackCache !== null) {
            return self::$fallbackCache;
        }

        self::$fallbackCache = [
            ['id' => 1, 'phuong_trinh' => '2H₂ + O₂ → 2H₂O', 'loai_phan_ung' => 'Hóa hợp', 'giai_thich' => 'Hydro cháy trong oxy tạo thành nước – phản ứng hóa hợp tỏa nhiều nhiệt.', 'nhom_phan_ung' => 'Vô cơ'],
            ['id' => 2, 'phuong_trinh' => 'N₂ + 3H₂ → 2NH₃', 'loai_phan_ung' => 'Hóa hợp', 'giai_thich' => 'Tổng hợp amoniac (Haber) – phản ứng quan trọng trong công nghiệp phân bón.', 'nhom_phan_ung' => 'Vô cơ'],
            ['id' => 3, 'phuong_trinh' => 'CaCO₃ → CaO + CO₂', 'loai_phan_ung' => 'Phân hủy', 'giai_thich' => 'Nung đá vôi tạo vôi sống và khí cacbonic – phản ứng trong sản xuất xi măng.', 'nhom_phan_ung' => 'Vô cơ'],
            ['id' => 4, 'phuong_trinh' => 'Zn + 2HCl → ZnCl₂ + H₂', 'loai_phan_ung' => 'Thế', 'giai_thich' => 'Kẽm đẩy hydro ra khỏi axit clohiđric tạo khí H₂.', 'nhom_phan_ung' => 'Vô cơ'],
            ['id' => 5, 'phuong_trinh' => 'AgNO₃ + NaCl → AgCl + NaNO₃', 'loai_phan_ung' => 'Trao đổi', 'giai_thich' => 'Phản ứng tạo kết tủa bạc clorua màu trắng.', 'nhom_phan_ung' => 'Vô cơ'],
            ['id' => 6, 'phuong_trinh' => 'CuO + H₂ → Cu + H₂O', 'loai_phan_ung' => 'Oxi hóa – khử', 'giai_thich' => 'Đồng(II) oxit bị hydro khử thành đồng kim loại.', 'nhom_phan_ung' => 'Vô cơ'],
            ['id' => 7, 'phuong_trinh' => 'CH₄ + 2O₂ → CO₂ + 2H₂O', 'loai_phan_ung' => 'Đốt cháy', 'giai_thich' => 'Đốt cháy metan sinh năng lượng – phản ứng cơ bản trong hô hấp và công nghiệp.', 'nhom_phan_ung' => 'Hữu cơ'],
            ['id' => 8, 'phuong_trinh' => 'CH₂=CH₂ + H₂ → CH₃–CH₃', 'loai_phan_ung' => 'Cộng', 'giai_thich' => 'Hydro hóa etilen tạo etan, phản ứng đặc trưng của anken.', 'nhom_phan_ung' => 'Hữu cơ'],
            ['id' => 9, 'phuong_trinh' => 'CH₃COOH + C₂H₅OH ⇌ CH₃COOC₂H₅ + H₂O', 'loai_phan_ung' => 'Este hóa', 'giai_thich' => 'Axit axetic tác dụng với etanol tạo etyl axetat (mùi thơm).', 'nhom_phan_ung' => 'Hữu cơ'],
            ['id' => 10, 'phuong_trinh' => 'CH₃CH₂OH → CH₂=CH₂ + H₂O', 'loai_phan_ung' => 'Tách nước', 'giai_thich' => 'Tách nước từ etanol tạo etilen ở 170°C (xúc tác H₂SO₄ đặc).', 'nhom_phan_ung' => 'Hữu cơ'],
        ];

        return self::$fallbackCache;
    }
}
