<?php // Khai báo mở đầu file PHP

declare(strict_types=1); // Bật chế độ kiểm tra kiểu nghiêm ngặt

namespace ChemLearn\Controllers; // Định nghĩa namespace cho controller

use ChemLearn\Models\CauHoi; // Import model Câu hỏi
use ChemLearn\Models\CauTraLoi; // Import model Câu trả lời
use ChemLearn\Models\FileDinhKem; // Import model File đính kèm
use RuntimeException; // Sử dụng RuntimeException cho lỗi runtime
use Throwable; // Sử dụng Throwable để bắt mọi ngoại lệ

class HoiDapController extends BaseController // Lớp controller kế thừa BaseController
{
    private CauHoi $questions; // Thuộc tính model câu hỏi
    private CauTraLoi $answers; // Thuộc tính model câu trả lời
    private FileDinhKem $attachments; // Thuộc tính model file đính kèm

    public function __construct() // Hàm khởi tạo controller
    {
        parent::__construct(); // Gọi khởi tạo cha để có session, csrf...
        $this->questions = new CauHoi(); // Khởi tạo model câu hỏi
        $this->answers = new CauTraLoi(); // Khởi tạo model câu trả lời
        $this->attachments = new FileDinhKem(); // Khởi tạo model file đính kèm
    }

    public function index(): void // Trang danh sách hỏi đáp
    {
        $search = trim((string) ($_GET['q'] ?? '')); // Lấy từ khóa tìm kiếm
        $sort = $_GET['sort'] ?? 'newest'; // Lấy kiểu sắp xếp
        $page = max(1, (int) ($_GET['page'] ?? 1)); // Lấy trang hiện tại, tối thiểu 1
        $perPage = 6; // Số bản ghi mỗi trang
        $mine = (bool) ($_GET['mine'] ?? false); // Có lọc câu hỏi của tôi hay không

        $currentUser = $this->getCurrentUser(); // Lấy thông tin người dùng đang đăng nhập
        $userId = $currentUser['ma_user'] ?? null; // Lấy id người dùng nếu có

        try {
            if ($mine && $userId !== null) { // Nếu xem câu hỏi của chính mình
                $total = $this->questions->countByUser($userId, $search); // Đếm tổng bản ghi
                $questions = $this->questions->allByUser($userId, $search, $sort, $perPage, ($page - 1) * $perPage); // Lấy danh sách
            } else { // Nếu xem toàn bộ
                $total = $this->questions->countAll($search); // Đếm tổng tất cả câu hỏi
                $questions = $this->questions->all($search, $sort, $perPage, ($page - 1) * $perPage); // Lấy danh sách tất cả
            }
        } catch (Throwable $exception) { // Nếu xảy ra lỗi DB
            $total = 0; // Đặt tổng bằng 0
            $questions = []; // Danh sách rỗng
        }

        $totalPages = max(1, (int) ceil($total / $perPage)); // Tính tổng số trang
        if ($page > $totalPages) { // Nếu trang vượt quá
            $page = $totalPages; // Điều chỉnh về trang cuối
            $questions = $mine && $userId !== null
                ? $this->questions->allByUser($userId, $search, $sort, $perPage, ($page - 1) * $perPage) // Lấy lại dữ liệu nếu cần
                : $this->questions->all($search, $sort, $perPage, ($page - 1) * $perPage); // Hoặc danh sách chung
        }

        $this->render('hoidap/index', [ // Gọi view danh sách hỏi đáp
            'title' => 'Hỏi – Đáp Hóa học', // Tiêu đề trang
            'questions' => $questions, // Dữ liệu câu hỏi
            'search' => $search, // Từ khóa tìm kiếm
            'sort' => $sort, // Kiểu sắp xếp
            'page' => $page, // Trang hiện tại
            'totalPages' => $totalPages, // Tổng số trang
            'mine' => $mine, // Cờ câu hỏi của tôi
        ]); // Kết thúc render
    }

    public function create(): void // Hiển thị form đặt câu hỏi
    {
        $this->render('hoidap/create', [ // Gọi view tạo câu hỏi
            'title' => 'Đặt câu hỏi mới', // Tiêu đề trang
        ]); // Kết thúc render
    }

    public function store(): void // Xử lý lưu câu hỏi
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Chỉ chấp nhận POST
            $this->redirect(app_url('hoi-dap/hoi')); // Nếu không, quay về form
        }

        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? null)) { // Kiểm tra CSRF
            http_response_code(403); // Đặt mã lỗi 403
            $_SESSION['flash_message'] = 'CSRF token không hợp lệ.'; // Thông báo lỗi
            $this->redirect(app_url('hoi-dap/hoi')); // Quay về form
        }

        $title = trim((string) ($_POST['tieu_de'] ?? '')); // Lấy tiêu đề câu hỏi
        $content = trim((string) ($_POST['noi_dung_html'] ?? '')); // Lấy nội dung rich-text
        $externalLink = trim((string) ($_POST['external_link'] ?? '')); // Lấy liên kết ngoài nếu có

        if ($title === '' || $content === '') { // Kiểm tra dữ liệu bắt buộc
            $_SESSION['flash_message'] = 'Vui lòng nhập tiêu đề và mô tả chi tiết.'; // Thông báo
            $this->redirect(app_url('hoi-dap/hoi')); // Quay về form
        }

        $currentUser = $this->getCurrentUser(); // Lấy người dùng
        $userId = $currentUser['ma_user'] ?? null; // Id người dùng

        try {
            $questionId = $this->questions->create([ // Tạo bản ghi câu hỏi
                'user_id' => $userId, // Chủ sở hữu
                'tieu_de' => $title, // Tiêu đề
                'noi_dung_html' => $this->sanitizeRichText($content), // Nội dung đã lọc
                'trang_thai' => 'open', // Trạng thái mở
            ]); // Kết thúc tạo câu hỏi

            $items = $this->handleAttachments($externalLink); // Xử lý file đính kèm/link
            if ($items !== []) { // Nếu có file/link
                $this->attachments->createMany($questionId, $items); // Lưu file đính kèm vào DB
            }

            $_SESSION['flash_message'] = 'Đã đăng câu hỏi thành công!'; // Thông báo thành công
            $this->redirect(app_url('hoi-dap')); // Quay về danh sách hỏi đáp
        } catch (RuntimeException $exception) { // Nếu có lỗi khi lưu
            $_SESSION['flash_message'] = $exception->getMessage(); // Lưu thông báo lỗi
            $this->redirect(app_url('hoi-dap/hoi')); // Quay lại form
        }
    }

    public function show(int $id): void // Hiển thị chi tiết câu hỏi
    {
        $question = $this->questions->find($id); // Tìm câu hỏi theo id
        if (!$question) { // Nếu không tìm thấy
            http_response_code(404); // Mã lỗi 404
            $_SESSION['flash_message'] = 'Câu hỏi không tồn tại hoặc đã bị xóa.'; // Thông báo
            $this->redirect(app_url('hoi-dap')); // Quay về danh sách
        }

        $this->questions->increaseView($id); // Tăng lượt xem trong DB
        $question['luot_xem'] = (int) ($question['luot_xem'] ?? 0) + 1; // Tăng giá trị hiển thị

        $answers = $this->answers->findByQuestion($id); // Lấy danh sách câu trả lời
        $files = $this->attachments->findByQuestion($id); // Lấy file đính kèm

        $currentUser = $this->getCurrentUser(); // Lấy người dùng hiện tại
        $canDelete = !empty($currentUser['ma_user']) && (int) $currentUser['ma_user'] === (int) ($question['user_id'] ?? 0); // Kiểm tra quyền xóa

        $this->render('hoidap/show', [ // Gọi view chi tiết
            'title' => $question['tieu_de'], // Tiêu đề trang
            'question' => $question, // Dữ liệu câu hỏi
            'answers' => $answers, // Danh sách trả lời
            'files' => $files, // File đính kèm
            'canDelete' => $canDelete, // Quyền xóa
        ]); // Kết thúc render
    }

    public function answer(int $id): void // Xử lý gửi câu trả lời
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Chỉ chấp nhận POST
            $this->redirect(app_url('hoi-dap/' . $id)); // Nếu không, quay về chi tiết
        }

        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? null)) { // Kiểm tra CSRF
            http_response_code(403); // Trả về 403
            $_SESSION['flash_message'] = 'CSRF token không hợp lệ.'; // Thông báo
            $this->redirect(app_url('hoi-dap/' . $id)); // Quay lại chi tiết
        }

        $question = $this->questions->find($id); // Lấy câu hỏi để kiểm tra tồn tại
        if (!$question) { // Nếu không tồn tại
            http_response_code(404); // 404
            $_SESSION['flash_message'] = 'Câu hỏi không tồn tại.'; // Thông báo
            $this->redirect(app_url('hoi-dap')); // Quay về danh sách
        }

        $content = trim((string) ($_POST['answer_html'] ?? '')); // Nội dung trả lời
        if ($content === '') { // Nếu trống
            $_SESSION['flash_message'] = 'Vui lòng nhập nội dung trả lời.'; // Thông báo
            $this->redirect(app_url('hoi-dap/' . $id) . '#answer-form'); // Quay lại form trả lời
        }

        $currentUser = $this->getCurrentUser(); // Người dùng hiện tại
        $userId = $currentUser['ma_user'] ?? null; // Id người dùng
        $canMarkBest = $userId !== null && isset($question['user_id']) && (int) $question['user_id'] === (int) $userId; // Quyền đánh dấu hay nhất
        $markBest = $canMarkBest && !empty($_POST['is_best']); // Cờ đánh dấu hay nhất nếu được phép

        try {
            $plain = htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); // Escape HTML để an toàn
            $plain = nl2br($plain); // Giữ xuống dòng khi hiển thị

            $answerId = $this->answers->create([ // Tạo câu trả lời mới
                'cau_hoi_id' => $id, // Gắn với câu hỏi
                'user_id' => $userId, // Người trả lời
                'noi_dung_html' => $this->sanitizeRichText($plain), // Nội dung đã lọc
                'is_best' => $markBest, // Có phải hay nhất không
            ]); // Kết thúc tạo

            if ($markBest) { // Nếu đánh dấu hay nhất
                $this->answers->markAsBest($id, $answerId); // Cập nhật toàn bộ câu trả lời của câu hỏi
            }

            $this->questions->increaseAnswerCount($id); // Tăng bộ đếm số trả lời
            $_SESSION['flash_message'] = 'Đã gửi câu trả lời!'; // Thông báo
            $this->redirect(app_url('hoi-dap/' . $id) . '#answers'); // Quay về khu vực trả lời
        } catch (RuntimeException $exception) { // Nếu lỗi
            $_SESSION['flash_message'] = $exception->getMessage(); // Lưu thông báo lỗi
            $this->redirect(app_url('hoi-dap/' . $id)); // Quay lại chi tiết
        }
    }

    public function delete(int $id): void // Xóa câu hỏi
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Chỉ chấp nhận POST
            $this->redirect(app_url('hoi-dap/' . $id)); // Nếu không, quay lại chi tiết
        }

        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? null)) { // Kiểm tra CSRF
            http_response_code(403); // 403
            $_SESSION['flash_message'] = 'CSRF token không hợp lệ.'; // Thông báo
            $this->redirect(app_url('hoi-dap/' . $id)); // Quay lại chi tiết
        }

        $question = $this->questions->find($id); // Tìm câu hỏi
        if (!$question) { // Không tồn tại
            http_response_code(404); // 404
            $_SESSION['flash_message'] = 'Câu hỏi không tồn tại hoặc đã bị xóa.'; // Thông báo
            $this->redirect(app_url('hoi-dap')); // Quay về danh sách
        }

        $currentUser = $this->getCurrentUser(); // Người dùng
        $userId = $currentUser['ma_user'] ?? null; // Id người dùng

        if ($userId === null || (int) $question['user_id'] !== (int) $userId) { // Kiểm tra quyền xóa
            http_response_code(403); // 403
            $_SESSION['flash_message'] = 'Bạn không thể xóa câu hỏi của người khác.'; // Thông báo
            $this->redirect(app_url('hoi-dap/' . $id)); // Quay lại chi tiết
        }

        $redirectTarget = app_url('hoi-dap'); // Mặc định quay về danh sách chung

        $candidate = (string) ($_POST['redirect'] ?? ''); // Đường dẫn muốn quay lại
        if ($candidate !== '' && !preg_match('#^https?://#i', $candidate) && str_starts_with($candidate, '/')) { // Kiểm tra an toàn URL nội bộ
            $redirectTarget = $candidate; // Dùng đường dẫn gửi lên
        } elseif (!empty($_SERVER['HTTP_REFERER']) && str_contains($_SERVER['HTTP_REFERER'], 'mine=1')) { // Nếu referrer chứa mine=1
            $redirectTarget = app_url('hoi-dap?mine=1'); // Quay lại tab câu hỏi của tôi
        }

        try {
            $this->questions->delete($id, (int) $userId); // Thực thi xóa câu hỏi
            $_SESSION['flash_message'] = 'Đã xóa câu hỏi của bạn.'; // Thông báo thành công
            $this->redirect($redirectTarget); // Điều hướng về trang tương ứng
        } catch (RuntimeException $exception) { // Nếu lỗi khi xóa
            $_SESSION['flash_message'] = $exception->getMessage(); // Lưu thông báo
            $this->redirect(app_url('hoi-dap/' . $id)); // Quay lại chi tiết
        }
    }

    private function sanitizeRichText(string $html): string // Làm sạch nội dung rich-text
    {
        $allowedTags = '<p><br><strong><em><u><ol><ul><li><a><table><thead><tbody><tr><td><th><blockquote><code><pre>'; // Danh sách thẻ cho phép
        $clean = strip_tags($html, $allowedTags); // Loại bỏ thẻ không hợp lệ
        $clean = preg_replace('/on[a-z]+="[^"]*"/i', '', $clean ?? ''); // Xóa sự kiện JS inline
        $clean = preg_replace('/javascript:/i', '', $clean ?? ''); // Xóa javascript: trong href

        return trim($clean ?? ''); // Trả về chuỗi đã lọc và cắt trắng
    }

    private function handleAttachments(string $externalLink): array // Xử lý file đính kèm và link ngoài
    {
        $items = []; // Mảng kết quả file/link
        $uploadDir = BASE_PATH . '/public/uploads/questions'; // Thư mục lưu file
        if (!is_dir($uploadDir)) { // Nếu thư mục chưa tồn tại
            mkdir($uploadDir, 0775, true); // Tạo thư mục với quyền 775
        }

        if (isset($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) { // Nếu có upload file
            $names = $_FILES['attachments']['name']; // Tên file gốc
            $tmpNames = $_FILES['attachments']['tmp_name']; // Đường dẫn tạm
            $errors = $_FILES['attachments']['error']; // Mã lỗi upload

            foreach ($names as $index => $originalName) { // Lặp qua từng file
                if (!isset($tmpNames[$index], $errors[$index])) { // Nếu thiếu dữ liệu
                    continue; // Bỏ qua
                }
                if ((int) $errors[$index] !== UPLOAD_ERR_OK) { // Nếu upload lỗi
                    continue; // Bỏ qua
                }
                $tmpPath = $tmpNames[$index]; // Đường dẫn tạm
                if (!is_uploaded_file($tmpPath)) { // Kiểm tra file hợp lệ
                    continue; // Bỏ qua
                }

                $extension = pathinfo((string) $originalName, PATHINFO_EXTENSION); // Lấy phần mở rộng
                $safeExtension = $extension !== '' ? '.' . preg_replace('/[^a-zA-Z0-9]/', '', $extension) : ''; // Làm sạch phần mở rộng
                $filename = uniqid('question_', true) . $safeExtension; // Sinh tên file an toàn
                $destination = $uploadDir . '/' . $filename; // Đường dẫn lưu

                if (move_uploaded_file($tmpPath, $destination)) { // Di chuyển file tạm sang thư mục lưu
                    $items[] = [ // Thêm vào danh sách
                        'duong_dan' => $filename, // Tên file đã lưu
                        'ten_goc' => $originalName, // Tên gốc người dùng upload
                    ]; // Kết thúc phần tử
                }
            }
        }

        if ($externalLink !== '' && filter_var($externalLink, FILTER_VALIDATE_URL)) { // Nếu có link ngoài hợp lệ
            $items[] = [ // Thêm vào danh sách
                'duong_dan' => $externalLink, // Lưu đường dẫn
                'ten_goc' => $externalLink, // Tên hiển thị
            ]; // Kết thúc phần tử
        }

        return $items; // Trả về danh sách file/link
    }
}
