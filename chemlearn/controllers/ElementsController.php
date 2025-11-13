<?php

namespace ChemLearn\Controllers;

/**
 * ElementsController chịu trách nhiệm hiển thị bảng tuần hoàn với dữ liệu từ JSON.
 */
class ElementsController extends BaseController
{
    /**
     * Đọc file JSON, xử lý lỗi và render giao diện bảng tuần hoàn.
     */
    public function index(): void
    {
        $pageTitle = 'Bảng tuần hoàn các nguyên tố hóa học';
        $dataFile = __DIR__ . '/../public/data/elements.json';
        $elements = [];

        if (is_file($dataFile)) {
            $json = file_get_contents($dataFile);
            if ($json !== false) {
                $decoded = json_decode($json, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $elements = $decoded;
                }
            }
        }

        $this->view('elements/index', [
            'pageTitle' => $pageTitle,
            'elements' => $elements,
            'extraStyles' => ['/public/css/elements.css'],
            'extraScripts' => ['/public/js/elements.js'],
        ]);
    }
}
