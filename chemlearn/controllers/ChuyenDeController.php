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
        $featuredModules = [
            [
                'title' => 'Cấu tạo nguyên tử',
                'points' => [
                    'Thành phần: proton, neutron, electron',
                    'Số hiệu nguyên tử Z, số khối A – liên hệ với đồng vị',
                    'Cấu hình electron với quy tắc Aufbau/Hund/Pauli',
                    'Ion – đồng vị và ví dụ O (Z = 8) → 1s² 2s² 2p⁴',
                ],
                'icon' => 'atom.svg',
            ],
            [
                'title' => 'Bảng tuần hoàn',
                'points' => [
                    '118 nguyên tố – 7 chu kỳ – 18 nhóm',
                    'Xu hướng bán kính, độ âm điện, năng lượng ion hóa',
                    'Tính kim loại/phi kim thay đổi theo chu kỳ & nhóm',
                    'F là phi kim mạnh nhất, ví dụ cho độ âm điện',
                ],
                'icon' => 'table.svg',
            ],
            [
                'title' => 'Liên kết hóa học',
                'points' => [
                    'Liên kết ion, cộng hóa trị (cực/không cực), kim loại',
                    'Liên kết cho – nhận, lực Van der Waals, liên kết H',
                    'Lai hóa sp – sp² – sp³, hình học phân tử theo VSEPR',
                    'Ví dụ: NaCl (ion), HCl (cộng hóa trị phân cực)',
                ],
                'icon' => 'bond.svg',
            ],
            [
                'title' => 'Phản ứng & phương trình',
                'points' => [
                    'Phản ứng thế, cộng, tách, oxi hóa – khử',
                    'Áp dụng các định luật bảo toàn khối lượng/electron',
                    'Cân bằng phương trình chính xác theo từng bước',
                    'Ví dụ: Fe + CuSO₄ → FeSO₄ + Cu',
                ],
                'icon' => 'reaction.svg',
            ],
            [
                'title' => 'Axit – bazơ – muối',
                'points' => [
                    'Phân loại axit/bazơ mạnh – yếu, tính tan của muối',
                    'Khái niệm pH, chỉ thị màu, phản ứng thủy phân',
                    'Ứng dụng tính toán pH cho dung dịch điển hình',
                    'Ví dụ: HCl 0,001 M ⇒ pH = 3',
                ],
                'icon' => 'acid.svg',
            ],
            [
                'title' => 'Oxi hóa – khử',
                'points' => [
                    'Xác định số oxi hóa, chất oxi hóa – chất khử',
                    'Cân bằng ion–electron trong dung dịch',
                    'Áp dụng cho phản ứng điện hóa, pin Galvani',
                    'Ví dụ: Cu + 2Ag⁺ → Cu²⁺ + 2Ag',
                ],
                'icon' => 'redox.svg',
            ],
            [
                'title' => 'Dung dịch & nồng độ',
                'points' => [
                    'Định nghĩa dung môi, chất tan, độ tan',
                    'Công thức C%, CM, molan; quy tắc pha loãng',
                    'Áp dụng công thức C₁V₁ = C₂V₂ khi pha trộn',
                ],
                'icon' => 'solution.svg',
            ],
            [
                'title' => 'Hóa hữu cơ đại cương',
                'points' => [
                    'Đồng đẳng, đồng phân, danh pháp IUPAC cơ bản',
                    'Hiệu ứng cảm ứng/ liên hợp (+I/-I, +M/-M)',
                    'Ví dụ: C₄H₁₀ có 2 đồng phân cấu tạo',
                ],
                'icon' => 'organic.svg',
            ],
            [
                'title' => 'Hidrocacbon & dẫn xuất',
                'points' => [
                    'Ankan/anken/ankin/aren cùng phản ứng đặc trưng',
                    'Dẫn xuất: ancol, phenol, ete, este, aldehit, axit',
                    'Ví dụ: CH₃CHO + Ag⁺ → phản ứng tráng bạc',
                ],
                'icon' => 'hydrocarbon.svg',
            ],
            [
                'title' => 'Amin – amino axit – protein',
                'points' => [
                    'Tính bazơ của amin và phản ứng tạo muối amoni',
                    'Liên kết peptit và cấu trúc protein (bậc 1→4)',
                    'Kết hợp nhóm –NH₂ với –COOH tạo liên kết amide',
                ],
                'icon' => 'protein.svg',
            ],
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
            'featuredModules' => $featuredModules,
            'laws' => $laws,
            'formulas' => $formulas,
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
