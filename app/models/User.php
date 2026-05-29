<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $d): int
    {
        return $this->insert([
            'name'          => $d['name'],
            'email'         => $d['email'],
            'password_hash' => password_hash($d['password'], PASSWORD_BCRYPT),
            'role'          => $d['role'] ?? 'customer',
            'phone'         => $d['phone'] ?? null,
            'address'       => $d['address'] ?? null,
        ]);
    }

    public function updateProfile(int $id, array $d): bool
    {
        return $this->update($id, [
            'name'    => $d['name'],
            'phone'   => $d['phone'] ?? null,
            'address' => $d['address'] ?? null,
        ]);
    }

    public function setRole(int $id, string $role): bool
    {
        return $this->update($id, ['role' => $role]);
    }
}
