<?php

declare(strict_types=1);

namespace ChemLearn\Models;

class TienDo extends BaseModel
{
    public function ghiNhan(?int $userId, ?int $maBaiGiang, int $soCauDung, int $soCauSai, string $ngayLam, ?string $ghiChu = null): void
    {
        if (!$this->hasConnection()) {
            return;
        }

        $statement = $this->requireConnection()->prepare('INSERT INTO tien_do_hoc (ma_user, ma_baigiang, so_cau_dung, so_cau_sai, ngay_lam, ghi_chu) VALUES (:user, :lesson, :correct, :wrong, :date, :note)');
        if ($userId === null) {
            $statement->bindValue(':user', null, \PDO::PARAM_NULL);
        } else {
            $statement->bindValue(':user', $userId, \PDO::PARAM_INT);
        }

        if ($maBaiGiang === null) {
            $statement->bindValue(':lesson', null, \PDO::PARAM_NULL);
        } else {
            $statement->bindValue(':lesson', $maBaiGiang, \PDO::PARAM_INT);
        }

        $statement->bindValue(':correct', $soCauDung, \PDO::PARAM_INT);
        $statement->bindValue(':wrong', $soCauSai, \PDO::PARAM_INT);
        $statement->bindValue(':date', $ngayLam, \PDO::PARAM_STR);
        if ($ghiChu === null) {
            $statement->bindValue(':note', null, \PDO::PARAM_NULL);
        } else {
            $statement->bindValue(':note', $ghiChu, \PDO::PARAM_STR);
        }
        $statement->execute();
    }

    public function getByUser(int $userId): array
    {
        if (!$this->hasConnection()) {
            return [];
        }

        $statement = $this->requireConnection()->prepare('SELECT t.*, b.ten_baigiang FROM tien_do_hoc t LEFT JOIN baigiang b ON t.ma_baigiang = b.ma_baigiang WHERE t.ma_user = :user ORDER BY t.ngay_lam DESC, t.id DESC');
        $statement->bindValue(':user', $userId, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }
}
