<?php

declare(strict_types=1);

namespace ChemLearn\Models;

class NguoiDung extends BaseModel
{
    public function create(string $hoTen, string $tenDangNhap, string $matKhau): bool
    {
        $statement = $this->db->prepare('INSERT INTO nguoidung (hoten, tendangnhap, matkhau) VALUES (:hoten, :username, :password)');
        $statement->bindValue(':hoten', $hoTen, \PDO::PARAM_STR);
        $statement->bindValue(':username', $tenDangNhap, \PDO::PARAM_STR);
        $statement->bindValue(':password', password_hash($matKhau, PASSWORD_DEFAULT), \PDO::PARAM_STR);

        return $statement->execute();
    }

    public function findByUsername(string $username): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM nguoidung WHERE tendangnhap = :username LIMIT 1');
        $statement->bindValue(':username', $username, \PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetch();
        return $result !== false ? $result : null;
    }

    public function findById(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM nguoidung WHERE ma_user = :id LIMIT 1');
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch();
        return $result !== false ? $result : null;
    }
}
