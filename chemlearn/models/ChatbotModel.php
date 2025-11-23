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
            ['tu_khoa' => 'axit; bronsted; proton', 'cau_tra_loi' => 'Axit theo Bronsted–Lowry là chất có khả năng nhường proton (H⁺).'],
            ['tu_khoa' => 'số oxi hóa; h2o; o', 'cau_tra_loi' => 'Số oxi hóa của O trong phân tử H₂O là −2.'],
            ['tu_khoa' => 'liên kết ion; ion; cation; anion', 'cau_tra_loi' => 'Liên kết ion là lực hút tĩnh điện giữa ion dương (cation) và ion âm (anion).'],
            ['tu_khoa' => 'axit; arrhenius; định nghĩa', 'cau_tra_loi' => 'Axit theo Arrhenius là chất khi tan trong nước phân li ra H⁺.'],
            ['tu_khoa' => 'bazơ; arrhenius; định nghĩa', 'cau_tra_loi' => 'Bazơ theo Arrhenius là chất khi tan trong nước phân li ra OH⁻.'],
            ['tu_khoa' => 'pH; tính toán; nồng độ H+', 'cau_tra_loi' => 'pH là đại lượng đo nồng độ ion H⁺, pH = −log[H⁺].'],
            ['tu_khoa' => 'cấu hình electron; natri; Na', 'cau_tra_loi' => 'Cấu hình electron của Na (Z = 11) là 1s² 2s² 2p⁶ 3s¹.'],
            ['tu_khoa' => 'kim loại; mạnh nhất; hoạt động hóa học', 'cau_tra_loi' => 'Kim loại hoạt động mạnh nhất là Francium (Fr), trong thực tế thường xét Cesium (Cs).'],
            ['tu_khoa' => 'phi kim; mạnh nhất; độ âm điện', 'cau_tra_loi' => 'Fluor (F) là phi kim mạnh nhất và có độ âm điện lớn nhất.'],
            ['tu_khoa' => 'oxit lưỡng tính; Al2O3; ZnO; ví dụ', 'cau_tra_loi' => 'Oxit lưỡng tính phản ứng được với cả axit mạnh và bazơ mạnh, ví dụ Al₂O₃, ZnO.'],
            ['tu_khoa' => 'nước cứng; định nghĩa; Ca2+; Mg2+', 'cau_tra_loi' => 'Nước cứng là nước chứa nhiều ion Ca²⁺ và Mg²⁺.'],
            ['tu_khoa' => 'alkane; công thức chung; CnH2n+2; hydrocacbon no', 'cau_tra_loi' => 'Alkane là hydrocacbon no, mạch hở với công thức chung CnH2n+2 (n ≥ 1).'],
            ['tu_khoa' => 'este; định nghĩa; nhóm chức; COO', 'cau_tra_loi' => 'Este là hợp chất hữu cơ có nhóm chức COO, tạo từ axit và ancol.'],
            ['tu_khoa' => 'phản ứng; xà phòng hóa; ester; NaOH; xà phòng', 'cau_tra_loi' => 'Xà phòng hóa là phản ứng thủy phân este trong môi trường kiềm tạo muối axit béo (xà phòng) và alcohol.'],
            ['tu_khoa' => 'polime; định nghĩa; mắt xích; phân tử khối lớn', 'cau_tra_loi' => 'Polime là hợp chất có phân tử khối rất lớn gồm nhiều mắt xích lặp lại liên kết với nhau.'],
            ['tu_khoa' => 'hiện tượng Tindall; keo; ánh sáng; nhận biết', 'cau_tra_loi' => 'Hiện tượng Tyndall là sự tán xạ ánh sáng khi truyền qua dung dịch keo.'],
            ['tu_khoa' => 'công thức; formaldehyde; HCHO; metanal', 'cau_tra_loi' => 'Formaldehyde (metanal) có công thức HCHO.'],
            ['tu_khoa' => 'thành phần; không khí; N2; O2', 'cau_tra_loi' => 'Không khí chủ yếu gồm N₂ (~78%) và O₂ (~21%).'],
            ['tu_khoa' => 'số oxi hóa; lưu huỳnh; H2SO4; tối đa', 'cau_tra_loi' => 'Trong H₂SO₄, lưu huỳnh có số oxi hóa +6.'],
            ['tu_khoa' => 'nguyên tắc Le Chatelier; cân bằng; yếu tố; dịch chuyển', 'cau_tra_loi' => 'Nguyên tắc Le Chatelier mô tả cân bằng dịch chuyển khi hệ chịu tác động thay đổi nồng độ, áp suất hoặc nhiệt độ.'],
            ['tu_khoa' => 'phản ứng tráng bạc; glucose; aldehyde; nhận biết', 'cau_tra_loi' => 'Phản ứng tráng bạc nhận biết glucose hoặc aldehyde với AgNO₃/NH₃ tạo gương bạc.'],
            ['tu_khoa' => 'ứng dụng; kim cương; độ cứng; mũi khoan', 'cau_tra_loi' => 'Kim cương dùng làm đá quý và mũi khoan nhờ độ cứng rất cao.'],
            ['tu_khoa' => 'phân biệt; alkene; bromine; mất màu', 'cau_tra_loi' => 'Alkene làm mất màu dung dịch brom, giúp phân biệt với alkane.'],
            ['tu_khoa' => 'chất điện li; mạnh; ví dụ; phân li hoàn toàn', 'cau_tra_loi' => 'Chất điện li mạnh phân li hoàn toàn trong nước (axit mạnh, bazơ mạnh, muối tan).'],
            ['tu_khoa' => 'nhiệt luyện; điều chế; kim loại; sau Al', 'cau_tra_loi' => 'Nhiệt luyện dùng điều chế các kim loại có tính khử trung bình và yếu (đứng sau Al trong dãy hoạt động).'],
        ];
    }
}
