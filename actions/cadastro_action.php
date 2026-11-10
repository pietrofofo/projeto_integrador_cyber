<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/cadastro.php');
}

csrf_verify();

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$senhaConfirma = $_POST['senha_confirma'] ?? '';

if ($nome === '' || $email === '' || $senha === '') {
    flash('erro', 'Preencha todos os campos obrigatórios.');
    redirect('/cadastro.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('erro', 'Informe um e-mail válido.');
    redirect('/cadastro.php');
}

if (strlen($senha) < 6) {
    flash('erro', 'A senha deve ter pelo menos 6 caracteres.');
    redirect('/cadastro.php');
}

if ($senha !== $senhaConfirma) {
    flash('erro', 'As senhas não conferem.');
    redirect('/cadastro.php');
}

$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    flash('erro', 'Já existe uma conta com esse e-mail.');
    redirect('/cadastro.php');
}

$hash = password_hash($senha, PASSWORD_BCRYPT);
$stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha_hash, papel) VALUES (?, ?, ?, "leitor")');
$stmt->execute([$nome, $email, $hash]);

$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
$stmt->execute([$pdo->lastInsertId()]);
$usuario = $stmt->fetch();

login_usuario($usuario);
flash('sucesso', 'Conta criada com sucesso! Bem-vindo(a), ' . $usuario['nome'] . '.');
redirect('/index.php');
