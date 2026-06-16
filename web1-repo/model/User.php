<?php
// model/User.php — digunakan Web1 & Web2
require_once __DIR__ . '/../config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    public function getAll(): array {
        return $this->db->query('SELECT id, nama, nim, foto FROM user ORDER BY id DESC')->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare('SELECT * FROM user WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Hanya dipanggil oleh Web1
    public function create(string $nama, string $nim, string $fotoUrl): int {
        $stmt = $this->db->prepare('INSERT INTO user (nama, nim, foto) VALUES (?, ?, ?)');
        $stmt->execute([$nama, $nim, $fotoUrl]);
        return (int) $this->db->lastInsertId();
    }

    // Hanya dipanggil oleh Web1
    public function update(int $id, string $nama, string $nim, ?string $fotoUrl): bool {
        if ($fotoUrl) {
            $stmt = $this->db->prepare('UPDATE user SET nama=?, nim=?, foto=? WHERE id=?');
            return $stmt->execute([$nama, $nim, $fotoUrl, $id]);
        }
        $stmt = $this->db->prepare('UPDATE user SET nama=?, nim=? WHERE id=?');
        return $stmt->execute([$nama, $nim, $id]);
    }

    // Hanya dipanggil oleh Web1
    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM user WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
