<?php
/**
 * Bootstrap da aplicação: sessão, constantes e conexão com o banco.
 * Todo arquivo público/admin deve começar com:
 *   require_once __DIR__ . '/config/config.php';
 * (ajustando o caminho relativo conforme a profundidade da pasta)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

define('BASE_URL', '/Cyber');
define('SITE_NAME', 'CyberWatch');

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
