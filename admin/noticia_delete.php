<?php
require_once __DIR__ . '/../config/config.php';
exigir_papel('jornalista', 'admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/noticias.php');
}

csrf_verify();

$usuario = usuario_logado();
$ehAdmin = tem_papel('admin');
$id = (int) ($_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM noticias WHERE id = ?');
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if (!$noticia) {
    flash('erro', 'Matéria não encontrada.');
    redirect('/admin/noticias.php');
}

if (!$ehAdmin && (int) $noticia['autor_id'] !== (int) $usuario['id']) {
    http_response_code(403);
    die('Você só pode excluir suas próprias matérias.');
}

$stmt = $pdo->prepare('DELETE FROM noticias WHERE id = ?');
$stmt->execute([$id]);

flash('sucesso', 'Matéria excluída.');
redirect('/admin/noticias.php');
