<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/login.php');
}

csrf_verify();

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($email === '' || $senha === '') {
    flash('erro', 'Preencha e-mail e senha.');
    redirect('/login.php');
}

$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
    flash('erro', 'E-mail ou senha inválidos.');
    redirect('/login.php');
}

login_usuario($usuario);
flash('sucesso', 'Bem-vindo(a) de volta, ' . $usuario['nome'] . '!');
redirect('/index.php');
