<?php
namespace App\Core;

class Auth {
    public static function attempt(string $username, string $password): bool {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM auth_user WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && $user['is_active']) {
                // Verificar password de Django pbkdf2_sha256
                if (self::verifyDjangoPassword($password, $user['password'])) {
                    Session::set('user_id', $user['id']);
                    Session::set('user_data', $user);
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private static function verifyDjangoPassword(string $password, string $hash): bool {
        // Formato Django: pbkdf2_sha256$iterations$salt$hash
        $parts = explode('$', $hash);
        if (count($parts) !== 4 || $parts[0] !== 'pbkdf2_sha256') {
            return false;
        }

        $iterations = (int) $parts[1];
        $salt = rtrim($parts[2], '=');
        $hashBase64 = rtrim($parts[3], '=');

        // Generar hash con pbkdf2
        $calcHash = hash_pbkdf2("sha256", $password, $salt, $iterations, 32, true);
        $calcHashBase64 = rtrim(base64_encode($calcHash), '=');

        return hash_equals($hashBase64, $calcHashBase64);
    }

    public static function check(): bool {
        return Session::has('user_id');
    }

    public static function user() {
        return Session::get('user_data');
    }

    public static function id() {
        return Session::get('user_id');
    }

    public static function logout() {
        Session::destroy();
    }
}
