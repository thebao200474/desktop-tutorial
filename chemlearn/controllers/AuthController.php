<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\NguoiDung;

class AuthController extends BaseController
{
    private NguoiDung $nguoiDungModel;

    public function __construct()
    {
        parent::__construct();
        $this->nguoiDungModel = new NguoiDung();
    }

    public function showRegister(): void
    {
        $this->render('auth/register', [
            'title' => 'Đăng ký tài khoản',
            'errors' => [],
        ]);
    }

    public function register(): void
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? null;
            if (!$this->validateCsrfToken($token)) {
                $errors[] = 'Yêu cầu không hợp lệ.';
            } else {
                $hoTen = trim((string)($_POST['hoten'] ?? ''));
                $tenDangNhap = trim((string)($_POST['tendangnhap'] ?? ''));
                $matKhau = (string)($_POST['matkhau'] ?? '');

                if ($hoTen === '' || $tenDangNhap === '' || $matKhau === '') {
                    $errors[] = 'Vui lòng điền đầy đủ thông tin.';
                }

                if ($this->nguoiDungModel->findByUsername($tenDangNhap) !== null) {
                    $errors[] = 'Tên đăng nhập đã tồn tại, vui lòng chọn tên khác.';
                }

                if ($errors === []) {
                    $this->nguoiDungModel->create($hoTen, $tenDangNhap, $matKhau);
                    $_SESSION['flash_message'] = 'Đăng ký thành công. Vui lòng đăng nhập.';
                    $this->redirect('dangnhap.php');
                    return;
                }
            }
        }

        $this->render('auth/register', [
            'title' => 'Đăng ký tài khoản',
            'errors' => $errors,
        ]);
    }

    public function showLogin(): void
    {
        $this->render('auth/login', [
            'title' => 'Đăng nhập',
            'errors' => [],
        ]);
    }

    public function login(): void
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? null;
            if (!$this->validateCsrfToken($token)) {
                $errors[] = 'Yêu cầu không hợp lệ.';
            } else {
                $tenDangNhap = trim((string)($_POST['tendangnhap'] ?? ''));
                $matKhau = (string)($_POST['matkhau'] ?? '');

                if ($tenDangNhap === '' || $matKhau === '') {
                    $errors[] = 'Vui lòng nhập tên đăng nhập và mật khẩu.';
                } else {
                    $user = $this->nguoiDungModel->findByUsername($tenDangNhap);
                    if ($user === null || !password_verify($matKhau, $user['matkhau'])) {
                        $errors[] = 'Sai tên đăng nhập hoặc mật khẩu.';
                    } else {
                        $_SESSION['user'] = $user;
                        $_SESSION['flash_message'] = 'Đăng nhập thành công!';
                        $this->redirect('index.php');
                        return;
                    }
                }
            }
        }

        $this->render('auth/login', [
            'title' => 'Đăng nhập',
            'errors' => $errors,
        ]);
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        $_SESSION['flash_message'] = 'Bạn đã đăng xuất.';
        $this->redirect('index.php');
    }
}
