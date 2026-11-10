<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/central-denuncias.php');
}

csrf_verify();

$tipo = $_POST['tipo_select'] ?? 'geral';
if (!in_array($tipo, ['geral', 'noticia', 'comentario'], true)) {
    $tipo = 'geral';
}

$noticiaId = !empty($_POST['noticia_id']) ? (int) $_POST['noticia_id'] : null;
$comentarioId = !empty($_POST['comentario_id']) ? (int) $_POST['comentario_id'] : null;
$nomeContato = trim($_POST['nome_contato'] ?? '');
$emailContato = trim($_POST['email_contato'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');

if (mb_strlen($descricao) < 10) {
    flash('erro', 'Descreva com um pouco mais de detalhe o que você quer denunciar.');
    redirect('/central-denuncias.php#denunciar');
}

if ($tipo !== 'noticia') {
    $noticiaId = null;
}
if ($tipo !== 'comentario') {
    $comentarioId = null;
}

$usuario = usuario_logado();

$stmt = $pdo->prepare(
    'INSERT INTO denuncias (tipo, noticia_id, comentario_id, denunciante_id, nome_contato, email_contato, descricao, status)
     VALUES (?, ?, ?, ?, ?, ?, ?, "pendente")'
);
$stmt->execute([
    $tipo,
    $noticiaId,
    $comentarioId,
    $usuario['id'] ?? null,
    $nomeContato !== '' ? $nomeContato : null,
    $emailContato !== '' ? $emailContato : null,
    $descricao,
]);

flash('sucesso', 'Denúncia registrada com sucesso. Nossa equipe vai analisar o mais breve possível.');
redirect('/central-denuncias.php');
