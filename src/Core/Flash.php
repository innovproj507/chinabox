<?php
namespace App\Core;

class Flash {
    const SUCCESS = 'success';
    const ERROR = 'error';
    const WARNING = 'warning';
    const INFO = 'info';

    public static function set(string $type, string $message) {
        $messages = Session::get('flash_messages', []);
        $messages[] = [
            'type' => $type,
            'message' => $message
        ];
        Session::set('flash_messages', $messages);
    }

    public static function success(string $message) {
        self::set(self::SUCCESS, $message);
    }

    public static function error(string $message) {
        self::set(self::ERROR, $message);
    }

    public static function warning(string $message) {
        self::set(self::WARNING, $message);
    }

    public static function info(string $message) {
        self::set(self::INFO, $message);
    }

    public static function get(): array {
        $messages = Session::get('flash_messages', []);
        Session::remove('flash_messages');
        return $messages;
    }

    public static function hasMessages(): bool {
        $messages = Session::get('flash_messages', []);
        return !empty($messages);
    }
}
