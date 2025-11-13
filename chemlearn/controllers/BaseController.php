<?php

namespace ChemLearn\Controllers;

/**
 * BaseController cung cấp tiện ích render view + layout.
 */
abstract class BaseController
{
    /**
     * Tên layout mặc định cho các trang.
     */
    protected string $layout = 'layouts/default';

    /**
     * Render một view cụ thể và bọc bằng layout mặc định.
     *
     * @param string $view  Đường dẫn view tương đối (ví dụ: 'topics/show').
     * @param array  $data  Dữ liệu truyền xuống view.
     */
    protected function view(string $view, array $data = []): void
    {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View không tồn tại';
            return;
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        $layoutFile = __DIR__ . '/../views/' . $this->layout . '.php';
        if (!is_file($layoutFile)) {
            http_response_code(500);
            echo 'Layout không tồn tại';
            return;
        }

        $pageTitle = $data['pageTitle'] ?? 'ChemLearn';
        require $layoutFile;
    }
}
