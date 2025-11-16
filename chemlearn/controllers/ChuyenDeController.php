<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\BaiGiang;

class ChuyenDeController extends BaseController
{
    private BaiGiang $baiGiangModel;

    public function __construct()
    {
        parent::__construct();
        $this->baiGiangModel = new BaiGiang();
    }

    public function index(): void
    {
        $coreTopics = [
            [
                'code' => '1.1',
                'title' => 'Cấu tạo nguyên tử',
                'bullets' => [
                    'Thành phần proton, neutron, electron – liên hệ số khối A và số hiệu nguyên tử Z.',
                    'Cấu hình electron theo quy tắc Aufbau, Hund, Pauli; phân tích đồng vị.',
                    'Ví dụ: O (Z = 8) có cấu hình 1s² 2s² 2p⁴.',
                ],
                'icon' => 'core-atom.svg',
            ],
            [
                'code' => '1.2',
                'title' => 'Bảng tuần hoàn',
                'bullets' => [
                    '118 nguyên tố sắp theo 7 chu kỳ – 18 nhóm, phân loại kim loại/phi kim/khí hiếm.',
                    'Xu hướng bán kính, năng lượng ion hóa, độ âm điện, ái lực electron.',
                    'Ví dụ nhấn mạnh: F là phi kim mạnh nhất trong bảng.',
                ],
                'icon' => 'core-table.svg',
            ],
            [
                'code' => '1.3',
                'title' => 'Liên kết hóa học',
                'bullets' => [
                    'Liên kết ion, cộng hóa trị (cực/không cực), kim loại và liên kết cho – nhận.',
                    'Lực tương tác: Van der Waals, liên kết H, liên kết kim loại.',
                    'Lai hóa sp, sp², sp³ và hình học phân tử VSEPR.',
                ],
                'icon' => 'core-bond.svg',
            ],
            [
                'code' => '1.4',
                'title' => 'Phản ứng & phương trình',
                'bullets' => [
                    'Phân loại phản ứng: thế, cộng, tách, oxi hóa – khử.',
                    'Áp dụng định luật bảo toàn khối lượng, nguyên tố, electron.',
                    'Ví dụ: Fe + CuSO₄ → FeSO₄ + Cu.',
                ],
                'icon' => 'core-reaction.svg',
            ],
            [
                'code' => '1.5',
                'title' => 'Axit – bazơ – muối',
                'bullets' => [
                    'Phân loại axit/bazơ mạnh – yếu, tính tan và thủy phân của muối.',
                    'Khái niệm pH, chỉ thị màu và ứng dụng trong bài toán thực tế.',
                    'Ví dụ: dung dịch HCl 0,001 M có pH = 3.',
                ],
                'icon' => 'core-acid.svg',
            ],
            [
                'code' => '1.6',
                'title' => 'Oxi hóa – khử',
                'bullets' => [
                    'Xác định số oxi hóa, phân biệt chất oxi hóa/chất khử.',
                    'Cân bằng phản ứng bằng phương pháp ion–electron.',
                    'Ví dụ: Cu + 2Ag⁺ → Cu²⁺ + 2Ag.',
                ],
                'icon' => 'core-redox.svg',
            ],
            [
                'code' => '1.7',
                'title' => 'Dung dịch & nồng độ',
                'bullets' => [
                    'Độ tan, dung môi, chất tan cùng các khái niệm C%, CM, molan.',
                    'Các phép pha loãng, pha trộn sử dụng công thức C₁V₁ = C₂V₂.',
                ],
                'icon' => 'core-solution.svg',
            ],
            [
                'code' => '1.8',
                'title' => 'Hóa hữu cơ đại cương',
                'bullets' => [
                    'Đồng đẳng, đồng phân, danh pháp IUPAC cơ bản.',
                    'Hiệu ứng cảm ứng và liên hợp (+I/−I, +M/−M) ảnh hưởng đến phản ứng.',
                    'Ví dụ: C₄H₁₀ có 2 đồng phân cấu tạo.',
                ],
                'icon' => 'core-organic.svg',
            ],
            [
                'code' => '1.9',
                'title' => 'Hidrocacbon & dẫn xuất',
                'bullets' => [
                    'Ankan, anken, ankin, aren cùng phản ứng thế/cộng/tách.',
                    'Dẫn xuất: ancol, phenol, ete, este, aldehit, axit – phản ứng đặc trưng.',
                    'Ví dụ: CH₃CHO + Ag⁺ (phản ứng tráng bạc).',
                ],
                'icon' => 'core-hydrocarbon.svg',
            ],
            [
                'code' => '1.10',
                'title' => 'Amin – amino axit – protein',
                'bullets' => [
                    'Tính bazơ của amin và sự tạo thành muối amoni.',
                    'Cấu trúc protein (bậc 1 → 4) cùng liên kết peptit.',
                    'Sự kết hợp –NH₂ với –COOH tạo liên kết amide.',
                ],
                'icon' => 'core-protein.svg',
            ],
        ];

        $decorImages = [
            ['file' => 'decor-1.svg', 'alt' => 'Bộ thí nghiệm mini'],
            ['file' => 'decor-2.svg', 'alt' => 'Sổ tay Hóa học'],
            ['file' => 'decor-3.svg', 'alt' => 'Dụng cụ đun hóa học'],
            ['file' => 'decor-4.svg', 'alt' => 'Phòng thí nghiệm'],
            ['file' => 'decor-5.svg', 'alt' => 'Câu lạc bộ Hóa học'],
            ['file' => 'decor-6.svg', 'alt' => 'Phân tử H₂SO₄'],
            ['file' => 'decor-7.svg', 'alt' => 'Sổ tay màu sắc'],
            ['file' => 'decor-8.svg', 'alt' => 'Mô hình hạt nhân'],
            ['file' => 'decor-9.svg', 'alt' => 'Bình tam giác'],
        ];

        $laws = [
            ['name' => 'Bảo toàn khối lượng', 'desc' => 'Tổng khối lượng chất tham gia bằng tổng khối lượng sản phẩm.'],
            ['name' => 'Bảo toàn nguyên tố', 'desc' => 'Số nguyên tử mỗi nguyên tố không thay đổi sau phản ứng.'],
            ['name' => 'Bảo toàn điện tích', 'desc' => 'Trong dung dịch, tổng điện tích dương = tổng điện tích âm.'],
            ['name' => 'Bảo toàn electron', 'desc' => 'Số mol electron cho = số mol electron nhận.'],
            ['name' => 'Định luật tuần hoàn', 'desc' => 'Tính chất các nguyên tố biến đổi tuần hoàn theo Z.'],
            ['name' => 'Phương trình khí lí tưởng', 'desc' => 'pV = nRT – áp dụng cho các bài toán khí cơ bản.'],
            ['name' => 'Định luật Henry', 'desc' => 'Độ tan khí trong dung dịch tỉ lệ với áp suất riêng phần của khí.'],
            ['name' => 'Định luật Beer–Lambert', 'desc' => 'A = εlc, độ hấp thụ tỉ lệ với nồng độ và bề dày cuvet.'],
            ['name' => 'Định luật Avogadro', 'desc' => 'Cùng nhiệt độ và áp suất, V khí bằng nhau ⇒ số phân tử bằng nhau.'],
            ['name' => 'Định luật Hess', 'desc' => 'ΔH phản ứng bằng tổng entanpi các bước trung gian.'],
        ];

        $formulas = [
            ['title' => 'Công thức cơ bản', 'lines' => ['n = m/M', 'n = V/22,4 (đktc)', 'C% = (mct/mdd) × 100%', 'CM = n/V', 'C₁V₁ = C₂V₂']],
            ['title' => 'pH – pOH', 'lines' => ['pH = –log[H⁺]', 'pOH = –log[OH⁻]', 'pH + pOH = 14']],
            ['title' => 'Số oxi hóa – electron', 'lines' => ['Tổng số oxi hóa = 0 với phân tử trung hòa', 'Số e cho = số e nhận']],
            ['title' => 'Công thức hydrocarbon', 'lines' => ['Ankan: CnH2n+2', 'Anken: CnH2n', 'Ankin: CnH2n–2', 'Aren: CnH2n–6']],
            ['title' => 'Phản ứng đặc trưng', 'lines' => ['Tráng bạc: R–CHO + 2Ag⁺ → R–COO⁻ + 2Ag', 'Este hóa: Axit + Ancol ⇄ Este + H₂O', 'Xà phòng hóa: Este + NaOH → Muối + Ancol']],
            ['title' => 'Điện hóa & nhiệt hóa', 'lines' => ['m = (A·I·t)/(nF)', 'ΔG = –nFE', 'ΔH = ΣH(sp) – ΣH(tham gia)']],
        ];

        $this->render('chuyende/index', [
            'title' => 'Chuyên đề Hóa học',
            'lessons' => $this->baiGiangModel->all(),
            'coreTopics' => $coreTopics,
            'laws' => $laws,
            'formulas' => $formulas,
            'decorImages' => $decorImages,
        ]);
    }

    public function show(int $id): void
    {
        $lesson = $this->baiGiangModel->find($id);
        if ($lesson === null) {
            $this->redirect('chuyende.php');
        }

        $this->render('chuyende/detail', [
            'title' => $lesson['ten_baigiang'],
            'lesson' => $lesson,
        ]);
    }
}
