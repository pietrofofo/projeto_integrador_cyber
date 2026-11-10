<?php
/** Autenticação e controle de acesso por papel (leitor / jornalista / admin). */

function usuario_logado(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function esta_logado(): bool
{
    return isset($_SESSION['usuario']);
}

function tem_papel(string ...$papeis): bool
{
    $usuario = usuario_logado();
    return $usuario && in_array($usuario['papel'], $papeis, true);
}

function exigir_login(): void
{
    if (!esta_logado()) {
        flash('erro', 'Você precisa entrar para continuar.');
        redirect('/login.php');
    }
}

function exigir_papel(string ...$papeis): void
{
    exigir_login();
    if (!tem_papel(...$papeis)) {
        http_response_code(403);
        die('Acesso restrito.');
    }
}

function login_usuario(array $usuario): void
{
    session_regenerate_id(true);
    $_SESSION['usuario'] = [
        'id'    => (int) $usuario['id'],
        'nome'  => $usuario['nome'],
        'email' => $usuario['email'],
        'papel' => $usuario['papel'],
    ];
}

function logout_usuario(): void
{
    $_SESSION = [];
    session_destroy();
}
