<?php

declare(strict_types=1);

namespace ChemLearn\Models;

use PDO;
use PDOException;

class BaiGiang extends BaseModel
{
    /**
     * Danh sách bài giảng ngoại tuyến dùng để dự phòng khi chưa kết nối CSDL.
     * Nội dung được biên soạn theo các chuyên đề ChemLearn do người dùng cung cấp.
     */
    private const FALLBACK_LESSONS = [
        [
            'ten_baigiang' => 'Cơ sở 1 – Cấu tạo nguyên tử',
            'noidung' => "• Lịch sử mô hình nguyên tử (Dalton, Thomson, Rutherford, Bohr, cơ học lượng tử)\n"
                . "• Cấu tạo hạt nhân với proton, neutron; mối liên hệ giữa số khối A, số proton Z và số neutron N\n"
                . "• Electron và các lớp vỏ, phân mức năng lượng\n"
                . "• Obitan nguyên tử và bộ bốn số lượng tử (n, l, m, ms)\n"
                . "• Cấu hình electron – các quy tắc Aufbau, Hund, Pauli\n"
                . "• Đồng vị, nguyên tử khối, nguyên tử khối trung bình\n"
                . "• Khối lượng mol và số Avogadro",
        ],
        [
            'ten_baigiang' => 'Cơ sở 2 – Bảng tuần hoàn & định luật tuần hoàn',
            'noidung' => "• Nguyên tắc sắp xếp các nguyên tố trong bảng tuần hoàn hiện đại\n"
                . "• Khái niệm chu kì, nhóm và các phân loại nguyên tố (kim loại, phi kim, khí hiếm, …)\n"
                . "• Xu hướng biến đổi bán kính nguyên tử, năng lượng ion hóa, độ âm điện và ái lực electron\n"
                . "• Sự biến đổi tính kim loại – phi kim trong chu kì và trong nhóm\n"
                . "• Quan hệ vị trí – tính axit/bazơ của oxit và hiđroxit",
        ],
        [
            'ten_baigiang' => 'Cơ sở 3 – Liên kết hóa học & cấu trúc phân tử',
            'noidung' => "• Liên kết ion: cơ chế hình thành và đặc điểm tinh thể ion\n"
                . "• Liên kết cộng hóa trị (không cực, phân cực) và cặp electron chung\n"
                . "• Liên kết cho – nhận, liên kết kim loại\n"
                . "• Lực liên phân tử: Van der Waals, liên kết hydrogen, …\n"
                . "• Lai hóa (sp, sp², sp³, …) và hình học phân tử theo thuyết VSEPR\n"
                . "• Liên kết σ, π; phân biệt liên kết đơn, đôi, ba",
        ],
        [
            'ten_baigiang' => 'Cơ sở 4 – Hóa trị, số oxi hóa & công thức hóa học',
            'noidung' => "• Định nghĩa hóa trị trong hóa vô cơ và hữu cơ\n"
                . "• Khái niệm số oxi hóa và các quy tắc xác định\n"
                . "• Công thức phân tử, công thức đơn giản nhất\n"
                . "• Công thức cấu tạo: mạch thẳng, mạch nhánh, vòng\n"
                . "• Thiết lập công thức từ phần trăm khối lượng thành phần\n"
                . "• Viết và cân bằng phương trình hóa học cơ bản",
        ],
        [
            'ten_baigiang' => 'Cơ sở 5 – Phản ứng hóa học & phân loại',
            'noidung' => "• Phân loại theo sự biến đổi số oxi hóa: phản ứng oxi hóa – khử và không oxi hóa – khử\n"
                . "• Phân loại theo thành phần: tổng hợp, phân hủy, thế, trao đổi\n"
                . "• Phân loại theo cơ chế trong hóa hữu cơ: thế, cộng, tách, chuyển vị\n"
                . "• Điều kiện xảy ra phản ứng: nhiệt độ, xúc tác, dung môi, …\n"
                . "• Khái niệm phản ứng thuận nghịch và phản ứng một chiều\n"
                . "• Năng lượng hoạt hóa, đường phản ứng tổng quan",
        ],
        [
            'ten_baigiang' => 'Cơ sở 6 – Tính toán hóa học (Stoichiometry)',
            'noidung' => "• Mol, khối lượng mol và thể tích mol khí ở điều kiện tiêu chuẩn\n"
                . "• Các tỉ lệ mol, khối lượng và thể tích khí\n"
                . "• Định luật bảo toàn khối lượng, bảo toàn nguyên tố và bảo toàn electron\n"
                . "• Bài toán hỗn hợp nhiều chất, chất dư – chất hết\n"
                . "• Tính hiệu suất phản ứng và các bài toán thực tiễn liên quan",
        ],
        [
            'ten_baigiang' => 'Cơ sở 7 – Trạng thái khí, lỏng, rắn',
            'noidung' => "• Tính chất cơ bản của chất khí\n"
                . "• Định luật Boyle, Charles, Gay-Lussac và phương trình trạng thái khí lí tưởng\n"
                . "• Khí lí tưởng và khí thực (khái niệm)\n"
                . "• Đặc điểm trạng thái lỏng – vai trò lực liên phân tử\n"
                . "• Các kiểu tinh thể rắn: ion, kim loại, phân tử, nguyên tử",
        ],
        [
            'ten_baigiang' => 'Cơ sở 8 – Dung dịch & nồng độ',
            'noidung' => "• Khái niệm dung môi, chất tan, dung dịch\n"
                . "• Độ tan và đường cong độ tan\n"
                . "• Nồng độ phần trăm, mol, molan, phần mol\n"
                . "• Pha dung dịch, pha loãng, pha trộn\n"
                . "• Áp suất hơi, điểm sôi, điểm đông đặc của dung dịch\n"
                . "• Dung dịch điện li và không điện li",
        ],
        [
            'ten_baigiang' => 'Cơ sở 9 – Nhiệt hóa học',
            'noidung' => "• Nhiệt phản ứng và khái niệm entanpi (ΔH)\n"
                . "• Phân biệt phản ứng tỏa nhiệt – thu nhiệt\n"
                . "• Nhiệt hình thành, nhiệt trung hòa, nhiệt đốt cháy\n"
                . "• Định luật Hess và cách thiết lập chu trình nhiệt hóa",
        ],
        [
            'ten_baigiang' => 'Cơ sở 10 – Động hóa học',
            'noidung' => "• Khái niệm tốc độ phản ứng và đơn vị\n"
                . "• Bậc phản ứng, phương trình tốc độ ở mức khái niệm\n"
                . "• Ảnh hưởng của nồng độ, nhiệt độ, diện tích bề mặt, xúc tác\n"
                . "• Năng lượng hoạt hóa và phương trình Arrhenius (giới thiệu)\n"
                . "• Cơ chế phản ứng đơn giản",
        ],
        [
            'ten_baigiang' => 'Cơ sở 11 – Cân bằng hóa học',
            'noidung' => "• Hệ phản ứng thuận nghịch và hằng số cân bằng K (Kc, Kp)\n"
                . "• Ý nghĩa giá trị K đối với mức độ xảy ra phản ứng\n"
                . "• Nguyên lí Le Chatelier: ảnh hưởng nồng độ, áp suất, nhiệt độ\n"
                . "• Cân bằng trong hệ khí và trong dung dịch",
        ],
        [
            'ten_baigiang' => 'Cơ sở 12 – Axit, bazơ, muối',
            'noidung' => "• Các thuyết Arrhenius, Brønsted–Lowry và Lewis ở mức cơ bản\n"
                . "• Độ mạnh axit/bazơ, khái niệm pH, hằng số Ka, Kb\n"
                . "• Tính pH của dung dịch axit/bazơ mạnh và yếu ở mức đơn giản\n"
                . "• Dung dịch đệm, chuẩn độ axit–bazơ và điểm tương đương\n"
                . "• Phản ứng trung hòa, trao đổi ion và thủy phân muối",
        ],
        [
            'ten_baigiang' => 'Cơ sở 13 – Oxi hóa – khử & điện hóa',
            'noidung' => "• Khái niệm quá trình oxi hóa và quá trình khử\n"
                . "• Chất oxi hóa, chất khử và phương pháp thăng bằng electron\n"
                . "• Dãy điện hóa, pin Galvanic và suất điện động pin\n"
                . "• Điện phân và các yếu tố ảnh hưởng, ứng dụng bảo vệ kim loại",
        ],
        [
            'ten_baigiang' => 'Cơ sở 14 – Hóa học hữu cơ nền tảng',
            'noidung' => "• Liên kết C–C, C–H, C–X, C–O, C–N, C=O và vai trò của chúng\n"
                . "• Đồng đẳng, đồng phân cấu tạo/hình học/vị trí\n"
                . "• Danh pháp IUPAC cơ bản\n"
                . "• Các nhóm chức chính: hydrocarbon, ancol, phenol, ete, andehit, xeton, axit, este, amin, …\n"
                . "• Phản ứng đặc trưng trong hữu cơ: thế, cộng, tách, oxi hóa – khử",
        ],
        [
            'ten_baigiang' => 'Vô cơ 1 – Nguyên tố và hợp chất khối s, p, d, f',
            'noidung' => "• Đặc điểm của khối s: kim loại kiềm, kiềm thổ\n"
                . "• Khối p: phi kim, halogen, khí hiếm\n"
                . "• Khối d: kim loại chuyển tiếp tiêu biểu (Fe, Cu, Zn, Cr, Mn, …)\n"
                . "• Khối f: lantanide và actinide (giới thiệu)",
        ],
        [
            'ten_baigiang' => 'Vô cơ 2 – Hóa học kim loại',
            'noidung' => "• Tính chất vật lí và hóa học chung của kim loại\n"
                . "• Dãy hoạt động kim loại\n"
                . "• Kim loại kiềm, kim loại kiềm thổ và các hợp chất quan trọng\n"
                . "• Nhôm và hợp chất: Al, Al₂O₃, Al(OH)₃, muối nhôm\n"
                . "• Kim loại Fe, Cu, Zn, Cr, Mn – tính chất và ứng dụng\n"
                . "• Hợp kim (thép, gang, hợp kim nhẹ) và vấn đề ăn mòn – bảo vệ",
        ],
        [
            'ten_baigiang' => 'Vô cơ 3 – Hóa học phi kim',
            'noidung' => "• Oxi, ozon và lưu huỳnh cùng các hợp chất\n"
                . "• Nito, amoniac, muối amoni, hợp chất nitơ – oxi (NO, NO₂, HNO₃, …)\n"
                . "• Photpho và hợp chất: P₂O₅, H₃PO₄, …\n"
                . "• Cacbon, CO, CO₂, axit cacbonic và muối cacbonat/bicarbonat\n"
                . "• Silic, SiO₂, silicat, vật liệu thủy tinh – gốm sứ\n"
                . "• Halogen: F₂, Cl₂, Br₂, I₂ cùng các axit halogen và muối halogenua",
        ],
        [
            'ten_baigiang' => 'Vô cơ 4 – Phản ứng trong dung dịch nước',
            'noidung' => "• Phản ứng trao đổi ion và phản ứng kết tủa\n"
                . "• Phản ứng tạo phức và phản ứng trung hòa axit – bazơ\n"
                . "• Phản ứng oxi hóa – khử trong dung dịch\n"
                . "• Viết phương trình ion rút gọn",
        ],
        [
            'ten_baigiang' => 'Vô cơ 5 – Hóa học phức chất',
            'noidung' => "• Khái niệm ion phức, phối tử và số phối trí\n"
                . "• Ví dụ phức của Cu²⁺, Fe²⁺, Fe³⁺ với NH₃, CN⁻, …\n"
                . "• Ứng dụng của phức chất trong phân tích và công nghiệp",
        ],
        [
            'ten_baigiang' => 'Vô cơ 6 – Hóa học rắn vô cơ & vật liệu',
            'noidung' => "• Tinh thể ion, kim loại, cộng hóa trị\n"
                . "• Gốm, thủy tinh, xi măng và vật liệu chịu nhiệt\n"
                . "• Vật liệu bán dẫn đơn giản (Si, Ge) ở mức khái niệm",
        ],
        [
            'ten_baigiang' => 'Hữu cơ 1 – Hydrocacbon',
            'noidung' => "• Ankan: công thức chung, đồng phân, danh pháp, phản ứng thế và cracking\n"
                . "• Anken: đồng phân hình học, phản ứng cộng, trùng hợp\n"
                . "• Ankin: phản ứng cộng và tính axit yếu của ankin đầu mạch\n"
                . "• Hidrocacbon thơm (benzen và đồng đẳng): cấu trúc thơm và phản ứng thế\n"
                . "• Vòng no (cycloankan) ở mức giới thiệu",
        ],
        [
            'ten_baigiang' => 'Hữu cơ 2 – Dẫn xuất halogen',
            'noidung' => "• Halogenua ankyl và aryl: tính chất vật lí\n"
                . "• Phản ứng thế nucleophin và phản ứng tách (E1/E2 – mức khái niệm)\n"
                . "• Ứng dụng, độc tính và lưu ý an toàn",
        ],
        [
            'ten_baigiang' => 'Hữu cơ 3 – Ancol, phenol, ete',
            'noidung' => "• Cấu tạo và phân loại ancol (bậc 1, 2, 3; no – không no; thơm)\n"
                . "• Các phản ứng chính: oxi hóa, este hóa, thế, tách nước\n"
                . "• Phenol: tính axit, phản ứng thế vào vòng, tạo nhựa phenol–formandehit\n"
                . "• Ete: tính chất, phản ứng tách và ứng dụng dung môi",
        ],
        [
            'ten_baigiang' => 'Hữu cơ 4 – Andehit, xeton, axit cacboxylic, este',
            'noidung' => "• Đặc trưng nhóm –CHO, >C=O, –COOH, –COO–\n"
                . "• Phản ứng đặc trưng của andehit: tráng bạc, phản ứng với Cu(OH)₂/NaOH\n"
                . "• Xeton: tính khử và phản ứng với thuốc thử 2,4-DNP (giới thiệu)\n"
                . "• Axit cacboxylic: tính axit, trung hòa, este hóa, khử\n"
                . "• Este: thủy phân trong môi trường axit/bazơ, ứng dụng mùi hương và chất béo",
        ],
        [
            'ten_baigiang' => 'Hữu cơ 5 – Hợp chất chứa nitơ',
            'noidung' => "• Amin: phân loại (bậc 1,2,3; aliphatic, aromatic), tính bazơ và muối amoni\n"
                . "• Amide: tính chất và thủy phân\n"
                . "• Nitril: khái niệm và quá trình thủy phân về axit hoặc amid\n"
                . "• Ứng dụng trong dược phẩm, phẩm màu",
        ],
        [
            'ten_baigiang' => 'Hữu cơ 6 – Polyme & vật liệu polymer',
            'noidung' => "• Khái niệm monome – polymer\n"
                . "• Phản ứng trùng hợp và trùng ngưng\n"
                . "• Polyme thiên nhiên: tinh bột, xenlulozơ, protein\n"
                . "• Polyme tổng hợp: PE, PVC, PS, Nylon, …\n"
                . "• Vật liệu compozit, cao su thiên nhiên và cao su tổng hợp",
        ],
        [
            'ten_baigiang' => 'Hữu cơ 7 – Cơ chế phản ứng hữu cơ cơ bản',
            'noidung' => "• Trung gian phản ứng: carbocation, carbanion, gốc tự do (giới thiệu)\n"
                . "• Phản ứng thế electrophin/nucleophin\n"
                . "• Phản ứng cộng electrophin vào nối đôi\n"
                . "• Phản ứng tách (E1/E2 – mức khái niệm)\n"
                . "• Quy tắc Markovnikov và ngoại lệ",
        ],
        [
            'ten_baigiang' => 'Hữu cơ 8 – Tổng hợp hữu cơ & sơ đồ chuyển hóa',
            'noidung' => "• Lập sơ đồ chuyển hóa từ một chất sang chất khác\n"
                . "• Xây dựng chuỗi phản ứng xuất phát từ hydrocacbon gốc\n"
                . "• Lựa chọn thuốc thử và điều kiện phù hợp",
        ],
        [
            'ten_baigiang' => 'Chuyên sâu 1 – Hóa lý',
            'noidung' => "• Nhiệt động lực học hóa học\n"
                . "• Cân bằng pha và dung dịch\n"
                . "• Động học hóa học nâng cao\n"
                . "• Hóa học bề mặt và hiện tượng hấp phụ",
        ],
        [
            'ten_baigiang' => 'Chuyên sâu 2 – Hóa phân tích',
            'noidung' => "• Phân tích thể tích: chuẩn độ acid–base, redox, complexon, kết tủa\n"
                . "• Phân tích trọng lượng\n"
                . "• Kĩ thuật sắc kí (TLC, HPLC, GC) và phổ học (UV-Vis, IR, NMR, MS – khái niệm)",
        ],
        [
            'ten_baigiang' => 'Chuyên sâu 3 – Hóa vô cơ nâng cao',
            'noidung' => "• Hóa học phối trí và thuyết trường tinh thể\n"
                . "• Hóa học trạng thái rắn và mạng tinh thể\n"
                . "• Phân tích chi tiết các nguyên tố d và f",
        ],
        [
            'ten_baigiang' => 'Chuyên sâu 4 – Hóa hữu cơ nâng cao',
            'noidung' => "• Cơ chế phản ứng chi tiết\n"
                . "• Chiến lược tổng hợp hữu cơ\n"
                . "• Hóa học hợp chất dị vòng và hợp chất thơm đa nhân",
        ],
        [
            'ten_baigiang' => 'Chuyên sâu 5 – Hóa sinh học',
            'noidung' => "• Cấu trúc và chức năng protein, enzyme\n"
                . "• Carbohydrate và lipid\n"
                . "• DNA, RNA và chuyển hóa năng lượng (ATP, chu trình Krebs, …)",
        ],
        [
            'ten_baigiang' => 'Chuyên sâu 6 – Hóa môi trường',
            'noidung' => "• Hóa học nước, không khí, đất\n"
                . "• Các chất ô nhiễm chính: NOx, SOx, CO, chất thải hữu cơ, kim loại nặng\n"
                . "• Phương pháp xử lý và giám sát môi trường",
        ],
        [
            'ten_baigiang' => 'Chuyên sâu 7 – Hóa vật liệu',
            'noidung' => "• Vật liệu nano, vật liệu từ, siêu dẫn\n"
                . "• Pin, ắc quy, siêu tụ điện\n"
                . "• Vật liệu quang – điện tử",
        ],
        [
            'ten_baigiang' => 'Chuyên sâu 8 – Hóa dược & hóa y sinh',
            'noidung' => "• Nguyên tắc thiết kế thuốc\n"
                . "• Tương tác thuốc – receptor\n"
                . "• Dược động học cơ bản",
        ],
        [
            'ten_baigiang' => 'Chuyên sâu 9 – Hóa hạt nhân',
            'noidung' => "• Phóng xạ, các dạng phân rã và chu kì bán rã\n"
                . "• Ứng dụng trong y học và năng lượng",
        ],
        [
            'ten_baigiang' => 'Chuyên sâu 10 – Hóa tính toán & hóa lượng tử',
            'noidung' => "• Mô hình hóa phân tử và tính toán cấu trúc\n"
                . "• Ứng dụng cơ học lượng tử cơ bản cho nguyên tử và phân tử",
        ],
    ];

    private static ?array $fallbackCache = null;

    public function all(): array
    {
        if ($this->hasConnection()) {
            try {
                $statement = $this->requireConnection()->query('SELECT * FROM baigiang ORDER BY ma_baigiang DESC');
                if ($statement instanceof \PDOStatement) {
                    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
                    if ($rows !== []) {
                        return $rows;
                    }
                }
            } catch (PDOException $exception) {
                // fall back to offline data
            }
        }

        return $this->fallbackLessons();
    }

    public function find(int $id): ?array
    {
        if ($this->hasConnection()) {
            try {
                $statement = $this->requireConnection()->prepare('SELECT * FROM baigiang WHERE ma_baigiang = :id');
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                $statement->execute();
                $result = $statement->fetch(PDO::FETCH_ASSOC);
                if ($result !== false && $result !== null) {
                    return $result;
                }
            } catch (PDOException $exception) {
                // chuyển sang dữ liệu dự phòng
            }
        }

        foreach ($this->fallbackLessons() as $lesson) {
            if ((int)$lesson['ma_baigiang'] === $id) {
                return $lesson;
            }
        }

        return null;
    }

    private function fallbackLessons(): array
    {
        if (self::$fallbackCache === null) {
            $lessons = [];
            foreach (self::FALLBACK_LESSONS as $index => $lesson) {
                $lessons[] = [
                    'ma_baigiang' => $index + 1,
                    'ten_baigiang' => $lesson['ten_baigiang'],
                    'noidung' => $lesson['noidung'],
                ];
            }

            // Giữ nguyên quy ước ORDER BY ma_baigiang DESC khi chưa có dữ liệu CSDL.
            self::$fallbackCache = array_reverse($lessons);
        }

        return self::$fallbackCache;
    }
}
