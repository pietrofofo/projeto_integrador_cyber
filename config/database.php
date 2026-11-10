<?php
/**
 * Conexão PDO com o MySQL. Ajuste as credenciais conforme seu ambiente
 * (XAMPP/WAMP/Laragon costumam usar usuário "root" e senha vazia).
 */

$DB_HOST = 'localhost';
$DB_NAME = 'cyberwatch';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die('Erro ao conectar ao banco de dados. Verifique se o MySQL está ativo e se o banco "cyberwatch" foi criado (importe database/schema.sql).');
}
