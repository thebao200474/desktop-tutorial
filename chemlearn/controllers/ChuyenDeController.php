<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\BaiGiang;
use ChemLearn\Models\NguoiDung;

class ChuyenDeController extends BaseController
{
    private BaiGiang $baiGiangModel;
    private NguoiDung $nguoiDungModel;

    public function __construct()
    {
        parent::__construct();
        $this->baiGiangModel = new BaiGiang();
        $this->nguoiDungModel = new NguoiDung();
    }

    public function index(): void
    {
        $topicDetails = $this->getTopicDetails();
        $topicGrid = array_map(
            fn(array $topic): array => ['code' => $topic['code'], 'title' => $topic['title']],
            $topicDetails
        );

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
            'topicGrid' => $topicGrid,
            'topicDetails' => $topicDetails,
            'laws' => $laws,
            'formulas' => $formulas,
        ]);
    }

    public function submitQuiz(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'message' => 'Phương thức không được hỗ trợ.']);
            return;
        }

        $token = $_POST['csrf_token'] ?? $_POST['csrf'] ?? null;
        if (!$this->validateCsrfToken(is_string($token) ? $token : null)) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'message' => 'CSRF token không hợp lệ.']);
            return;
        }

        $topicCode = trim((string)($_POST['topic'] ?? ''));
        $answers = $_POST['answers'] ?? [];
        if ($topicCode === '' || !is_array($answers)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => 'Dữ liệu gửi lên chưa đầy đủ.']);
            return;
        }

        $topic = $this->findTopicByCode($topicCode);
        if ($topic === null) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'message' => 'Không tìm thấy chuyên đề cần chấm.']);
            return;
        }

        $correctCount = 0;
        $details = [];

        foreach ($topic['quiz'] as $index => $quiz) {
            $userAnswer = strtoupper(trim((string)($answers[$index] ?? '')));
            $correctAnswer = strtoupper($quiz['answer']);
            $isCorrect = $userAnswer !== '' && $userAnswer === $correctAnswer;
            if ($isCorrect) {
                $correctCount++;
            }
            $details[] = [
                'index' => $index,
                'question' => $quiz['question'],
                'userAnswer' => $userAnswer,
                'correctAnswer' => $correctAnswer,
                'isCorrect' => $isCorrect,
            ];
        }

        $total = count($topic['quiz']);
        $rankAwarded = $correctCount > 0 ? $correctCount : 0;
        $newRank = null;
        $currentUser = $this->getCurrentUser();
        if ($currentUser !== null && $rankAwarded > 0) {
            $newRank = $this->nguoiDungModel->incrementRank((int)$currentUser['ma_user'], $rankAwarded);
            $this->refreshUserRankInSession((int)$currentUser['ma_user'], $newRank);
        }

        $message = sprintf('Bạn trả lời đúng %d/%d.', $correctCount, $total);
        if ($currentUser !== null && $rankAwarded > 0) {
            $message .= sprintf(' Đã cộng %d điểm rank%s.', $rankAwarded, $newRank !== null ? ' (Rank hiện tại: ' . $newRank . ')' : '');
        } elseif ($currentUser === null) {
            $message .= ' Đăng nhập để được cộng điểm rank.';
        }

        echo json_encode(
            [
                'ok' => true,
                'scoreLabel' => sprintf('%d/%d', $correctCount, $total),
                'correct' => $correctCount,
                'total' => $total,
                'rankAwarded' => $rankAwarded,
                'newRank' => $newRank,
                'message' => $message,
                'details' => $details,
            ],
            JSON_UNESCAPED_UNICODE
        );
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

    private function getTopicDetails(): array
    {
        return [
            [
                'code' => '1',
                'title' => 'Cấu tạo nguyên tử',
                'summary' => 'Module nền tảng',
                'content' => [
                    'Nguyên tử gồm hạt nhân (proton, neutron) và lớp vỏ electron.',
                    'Số hiệu nguyên tử Z = số proton, số khối A = Z + n.',
                    'Cấu hình electron xây dựng theo quy tắc Aufbau, Hund, Pauli.',
                    'Ion – đồng vị và ví dụ O (Z = 8) → 1s² 2s² 2p⁴.',
                ],
                'example' => 'Cho nguyên tố Mg (Z = 12): cấu hình electron 1s² 2s² 2p⁶ 3s².',
                'quiz' => [
                    ['question' => 'Nguyên tố có Z = 17 là:', 'options' => ['A. Cl', 'B. S', 'C. P', 'D. K'], 'answer' => 'A'],
                    ['question' => 'Số hạt mang điện trong nguyên tử O (Z = 8) là:', 'options' => ['A. 8', 'B. 10', 'C. 16', 'D. 18'], 'answer' => 'C'],
                    ['question' => 'Số neutron của nguyên tử Cl-37 là:', 'options' => ['A. 17', 'B. 18', 'C. 20', 'D. 37'], 'answer' => 'B'],
                    ['question' => 'Lớp electron nào có năng lượng thấp nhất?', 'options' => ['A. M', 'B. L', 'C. N', 'D. K'], 'answer' => 'D'],
                    ['question' => 'Ion Na⁺ có số electron là:', 'options' => ['A. 9', 'B. 10', 'C. 11', 'D. 12'], 'answer' => 'B'],
                ],
            ],
            [
                'code' => '2',
                'title' => 'Bảng tuần hoàn',
                'summary' => 'Module nền tảng',
                'content' => [
                    '118 nguyên tố – 7 chu kỳ – 18 nhóm, phân loại rõ kim loại, phi kim, khí hiếm.',
                    'Xu hướng bán kính nguyên tử, độ âm điện, năng lượng ion hóa.',
                    'Tính kim loại mạnh hơn khi đi xuống nhóm và yếu đi khi sang phải.',
                    'Ví dụ: Flo (F) là phi kim mạnh nhất nhờ độ âm điện cực đại.',
                ],
                'example' => 'Xu hướng độ âm điện tăng từ trái sang phải, giảm khi đi xuống.',
                'quiz' => [
                    ['question' => 'Nguyên tố nào có tính kim loại mạnh hơn?', 'options' => ['A. Na', 'B. Mg', 'C. Al', 'D. Si'], 'answer' => 'A'],
                    ['question' => 'Độ âm điện lớn nhất thuộc về:', 'options' => ['A. O', 'B. F', 'C. Cl', 'D. N'], 'answer' => 'B'],
                    ['question' => 'Nguyên tố ở ô số 11 thuộc chu kỳ:', 'options' => ['A. 1', 'B. 2', 'C. 3', 'D. 4'], 'answer' => 'C'],
                    ['question' => 'Bán kính nguyên tử tăng khi di chuyển:', 'options' => ['A. Sang phải', 'B. Lên trên', 'C. Xuống dưới', 'D. Không đổi'], 'answer' => 'C'],
                    ['question' => 'Nhóm halogen là nhóm số:', 'options' => ['A. 1', 'B. 2', 'C. 17', 'D. 18'], 'answer' => 'C'],
                ],
            ],
            [
                'code' => '3',
                'title' => 'Liên kết hóa học',
                'summary' => 'Module nền tảng',
                'content' => [
                    'Bao gồm liên kết ion, cộng hóa trị (cực và không cực) và liên kết kim loại.',
                    'Các tương tác phụ: cho – nhận electron, lực Van der Waals, liên kết H.',
                    'Lai hóa sp, sp², sp³ cùng hình học phân tử theo thuyết VSEPR.',
                    'Ví dụ: phân tử H₂O có liên kết cộng hóa trị phân cực.',
                ],
                'example' => 'H₂O mang liên kết cộng hóa trị phân cực giữa O và H.',
                'quiz' => [
                    ['question' => 'Liên kết trong NaCl là:', 'options' => ['A. Ion', 'B. Cộng hoá trị', 'C. Kim loại', 'D. Hydro'], 'answer' => 'A'],
                    ['question' => 'Phân tử nào có liên kết cộng hoá trị không phân cực?', 'options' => ['A. HCl', 'B. CO₂', 'C. O₂', 'D. NH₃'], 'answer' => 'C'],
                    ['question' => 'Lai hoá sp³ có dạng hình học:', 'options' => ['A. Thẳng', 'B. Tam giác', 'C. Tứ diện', 'D. Vuông phẳng'], 'answer' => 'C'],
                    ['question' => 'Liên kết kim loại có đặc điểm:', 'options' => ['A. Hạt nhân + e tự do', 'B. Cho – nhận e', 'C. Chia sẻ e', 'D. Không bền'], 'answer' => 'A'],
                    ['question' => 'Liên kết H–F thuộc loại:', 'options' => ['A. Ion', 'B. Phân cực', 'C. Kim loại', 'D. Không cực'], 'answer' => 'B'],
                ],
            ],
            [
                'code' => '4',
                'title' => 'Phản ứng & phương trình',
                'summary' => 'Module nền tảng',
                'content' => [
                    'Bao gồm phản ứng thế, cộng, tách và các phản ứng oxi hóa – khử.',
                    'Ứng dụng các định luật bảo toàn khối lượng, nguyên tố, electron.',
                    'Cân bằng phương trình từng bước để tránh sai số.',
                    'Ví dụ: Fe + CuSO₄ → FeSO₄ + Cu (phản ứng thế).',
                ],
                'example' => 'Fe + CuSO₄ → FeSO₄ + Cu là phản ứng thế điển hình.',
                'quiz' => [
                    ['question' => 'Phản ứng oxi hóa – khử có đặc điểm:', 'options' => ['A. Có thay đổi số oxi hóa', 'B. Sinh khí', 'C. Tạo kết tủa', 'D. Trung hòa'], 'answer' => 'A'],
                    ['question' => 'Phản ứng: 2H₂ + O₂ → 2H₂O thuộc loại:', 'options' => ['A. Thế', 'B. Phân hủy', 'C. Cộng', 'D. Tổng hợp'], 'answer' => 'D'],
                    ['question' => 'Định luật bảo toàn electron dùng cho:', 'options' => ['A. Trung hòa', 'B. Oxi hóa – khử', 'C. Nhiệt phân', 'D. Kết tủa'], 'answer' => 'B'],
                    ['question' => 'Trong phản ứng Zn → Zn²⁺ + 2e, Zn là:', 'options' => ['A. Chất oxi hóa', 'B. Chất khử', 'C. Sản phẩm', 'D. Trung tính'], 'answer' => 'B'],
                    ['question' => 'Phương trình nào được cân bằng đúng?', 'options' => ['A. Fe + 2HCl → FeCl₂ + H₂', 'B. Fe + HCl → FeCl₃ + H₂', 'C. Fe + 2HCl → FeCl₃ + H₂', 'D. Fe + 3HCl → FeCl₂ + H₂'], 'answer' => 'A'],
                ],
            ],
            [
                'code' => '5',
                'title' => 'Axit – bazơ – muối',
                'summary' => 'Module nền tảng',
                'content' => [
                    'Nhận biết axit/bazơ mạnh hay yếu và độ tan của muối.',
                    'Khái niệm pH, pOH và sử dụng chỉ thị màu.',
                    'Ứng dụng công thức pH = –log[H⁺] cho các dung dịch điển hình.',
                    'Ví dụ: dung dịch HNO₃ 0,01 M có pH = 2.',
                ],
                'example' => 'pH = –log[H⁺]; HNO₃ 0,01 M ⇒ pH = 2.',
                'quiz' => [
                    ['question' => 'Axit mạnh là:', 'options' => ['A. CH₃COOH', 'B. H₂CO₃', 'C. HCl', 'D. HF'], 'answer' => 'C'],
                    ['question' => 'Dung dịch có pH = 1 thuộc loại:', 'options' => ['A. Bazơ mạnh', 'B. Axit mạnh', 'C. Trung tính', 'D. Axit yếu'], 'answer' => 'B'],
                    ['question' => 'Muối nào tan trong nước?', 'options' => ['A. AgCl', 'B. BaSO₄', 'C. NaCl', 'D. PbSO₄'], 'answer' => 'C'],
                    ['question' => 'Khi [OH⁻] tăng, pH sẽ:', 'options' => ['A. Tăng', 'B. Giảm', 'C. Không đổi', 'D. Bằng 7'], 'answer' => 'A'],
                    ['question' => 'Nếu pH = 3 thì [H⁺] =', 'options' => ['A. 10⁻¹', 'B. 10⁻²', 'C. 10⁻³', 'D. 10⁻⁴'], 'answer' => 'C'],
                ],
            ],
            [
                'code' => '6',
                'title' => 'Oxi hóa – khử',
                'summary' => 'Module nền tảng',
                'content' => [
                    'Xác định số oxi hóa cho từng nguyên tố trong hợp chất.',
                    'Phân biệt chất oxi hóa và chất khử.',
                    'Cân bằng phản ứng bằng phương pháp ion – electron.',
                    'Ví dụ: Cu + 2Ag⁺ → Cu²⁺ + 2Ag.',
                ],
                'example' => 'Cu + 2Ag⁺ → Cu²⁺ + 2Ag thể hiện chất khử/oxi hóa rõ ràng.',
                'quiz' => [
                    ['question' => 'Số oxi hóa của S trong H₂SO₄ là:', 'options' => ['A. +2', 'B. +4', 'C. +6', 'D. –2'], 'answer' => 'C'],
                    ['question' => 'Trong phản ứng Fe + Cl₂ → FeCl₃, chất bị oxi hóa là:', 'options' => ['A. Fe', 'B. Cl₂', 'C. FeCl₃', 'D. Không có'], 'answer' => 'A'],
                    ['question' => 'Chất khử là chất:', 'options' => ['A. Nhận electron', 'B. Cho electron', 'C. Trung hòa', 'D. Tạo kết tủa'], 'answer' => 'B'],
                    ['question' => 'Ion MnO₄⁻ trong môi trường axit bị khử thành:', 'options' => ['A. Mn²⁺', 'B. MnO₂', 'C. MnO₄²⁻', 'D. Mn⁴⁺'], 'answer' => 'A'],
                    ['question' => 'Phản ứng oxi hóa – khử luôn có:', 'options' => ['A. Kết tủa', 'B. Trao đổi electron', 'C. Tạo nước', 'D. Tạo muối'], 'answer' => 'B'],
                ],
            ],
            [
                'code' => '7',
                'title' => 'Dung dịch & nồng độ',
                'summary' => 'Module nền tảng',
                'content' => [
                    'Khái niệm dung môi, chất tan, độ tan và các dạng nồng độ.',
                    'Các công thức C%, CM, molan, cùng quy tắc pha loãng C₁V₁ = C₂V₂.',
                    'Tính mol và thể tích cho khí ở điều kiện tiêu chuẩn.',
                    'Ví dụ: dung dịch HCl 2M pha thành 1M cần thêm nước gấp đôi.',
                ],
                'example' => 'Áp dụng C₁V₁ = C₂V₂ để pha loãng HCl 2M thành 1M.',
                'quiz' => [
                    ['question' => 'Công thức C% là:', 'options' => ['A. n/V', 'B. mct/mdd × 100%', 'C. n × M', 'D. m × V'], 'answer' => 'B'],
                    ['question' => 'Công thức n = m/M dùng cho:', 'options' => ['A. Kim loại', 'B. Mọi chất', 'C. Dung dịch', 'D. Khí'], 'answer' => 'B'],
                    ['question' => 'Pha loãng gấp đôi thể tích thì nồng độ:', 'options' => ['A. Gấp 2', 'B. Giảm 1/2', 'C. Không đổi', 'D. Tăng 4'], 'answer' => 'B'],
                    ['question' => 'CM = 0,5M nghĩa là:', 'options' => ['A. 0,5 mol/1 g', 'B. 0,5 mol/1 lít', 'C. 5 mol/1 lít', 'D. 1 mol/2 lít'], 'answer' => 'B'],
                    ['question' => '1 mol khí ở ĐKTC có thể tích:', 'options' => ['A. 11,2 lít', 'B. 22,4 lít', 'C. 33,6 lít', 'D. 44,8 lít'], 'answer' => 'B'],
                ],
            ],
            [
                'code' => '8',
                'title' => 'Hóa hữu cơ cơ bản',
                'summary' => 'Module nền tảng',
                'content' => [
                    'Trình bày các hydrocarbon: ankan, anken, ankin, aren.',
                    'Dẫn xuất: ancol, phenol, ete, este, aldehit, axit.',
                    'Danh pháp, đồng phân và phản ứng đặc trưng (thế, cộng, tách).',
                    'Ví dụ: C₂H₄ + Br₂ → C₂H₄Br₂ (phản ứng cộng).',
                ],
                'example' => 'C₂H₄ + Br₂ → C₂H₄Br₂ minh họa phản ứng cộng của anken.',
                'quiz' => [
                    ['question' => 'Công thức chung của ankan là:', 'options' => ['A. CnH2n', 'B. CnH2n+2', 'C. CnH2n−2', 'D. CnH2n−6'], 'answer' => 'B'],
                    ['question' => 'Chất nào tham gia phản ứng tráng bạc?', 'options' => ['A. CH₃OH', 'B. CH₃CHO', 'C. CH₄', 'D. CH₃COOH'], 'answer' => 'B'],
                    ['question' => 'Este có mùi thơm đặc trưng là:', 'options' => ['A. CH₃COONa', 'B. CH₃COOC₂H₅', 'C. C₂H₅OH', 'D. C₂H₄'], 'answer' => 'B'],
                    ['question' => 'Phản ứng cộng thường xảy ra với:', 'options' => ['A. Ankan', 'B. Anken/ankin', 'C. Aren', 'D. Ancol'], 'answer' => 'B'],
                    ['question' => 'Số đồng phân của C₄H₁₀ là:', 'options' => ['A. 1', 'B. 2', 'C. 3', 'D. 4'], 'answer' => 'B'],
                ],
            ],
        ];

    }

    private function findTopicByCode(string $code): ?array
    {
        foreach ($this->getTopicDetails() as $topic) {
            if ($topic['code'] === $code) {
                return $topic;
            }
        }

        return null;
    }

    private function refreshUserRankInSession(int $userId, int $newRank): void
    {
        if (empty($_SESSION['user']) || (int)($_SESSION['user']['ma_user'] ?? 0) !== $userId) {
            return;
        }

        $_SESSION['user']['diem_rank'] = $newRank;
    }
}
