<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * PDO singleton kết nối MySQL. Mọi truy vấn dùng prepared statements.
 */
class Database
{
    private static ?PDO $instance = null;

    public static function conn(): PDO
    {
        if (self::$instance === null) {
            $cfg = require dirname(__DIR__, 2) . '/config/config.php';
            $db  = $cfg['db'];
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $db['host'], $db['port'], $db['name']
            );
            try {
                self::$instance = new PDO($dsn, $db['user'], $db['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                exit('Lỗi kết nối CSDL: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
