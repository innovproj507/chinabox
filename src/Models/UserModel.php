<?php
namespace App\Models;

class UserModel extends BaseModel {
    protected string $table = 'auth_user'; // Usamos la misma de Django
    protected string $primaryKey = 'id';

    public function getActiveUsers(): array {
        return $this->where('is_active', 1);
    }

    public function createDjangoUser(array $data) {
        // Enlazar los campos necesarios para Django
        $now = date('Y-m-d H:i:s');
        
        $userData = [
            'password' => $this->hashDjangoPassword($data['password']),
            'last_login' => null,
            'is_superuser' => $data['is_superuser'] ?? 0,
            'username' => $data['username'],
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'email' => $data['email'] ?? '',
            'is_staff' => $data['is_superuser'] ?? 0,
            'is_active' => $data['is_active'] ?? 1,
            'date_joined' => $now,
        ];
        
        return $this->create($userData);
    }
    
    public function updateDjangoUser($id, array $data) {
        $userData = [
            'is_superuser' => $data['is_superuser'] ?? 0,
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'email' => $data['email'] ?? '',
            'is_staff' => $data['is_superuser'] ?? 0,
            'is_active' => $data['is_active'] ?? 1,
        ];
        
        if (!empty($data['password'])) {
            $userData['password'] = $this->hashDjangoPassword($data['password']);
        }
        
        return $this->update($id, $userData);
    }

    private function hashDjangoPassword(string $password): string {
        // Generar hash en formato pbkdf2_sha256 de Django
        $iterations = 390000;
        $salt = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 5)), 0, 22);
        
        $hash = hash_pbkdf2("sha256", $password, $salt, $iterations, 32, true);
        $hashBase64 = base64_encode($hash);
        
        return "pbkdf2_sha256\${$iterations}\${$salt}\${$hashBase64}";
    }
}
