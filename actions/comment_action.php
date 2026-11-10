<?php
require_once __DIR__ . '/../config/config.php';

exigir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/index.php');
}

csrf_verify();

$noticiaId = (int) ($_POST['noticia_id'] ?? 0);
$texto = trim($_POST['texto'] ?? '');

$stmt = $pdo->prepare('SELECT slug FROM noticias WHERE id = ? AND status = "publicado"');
$stmt->execute([$noticiaId]);
$noticia = $stmt->fetch();

if (!$noticia) {
    flash('erro', 'Matéria não encontrada.');
    redirect('/index.php');
}

if ($texto === '') {
    flash('erro', 'Escreva algo antes de publicar o comentário.');
    redirect('/article.php?slug=' . urlencode($noticia['slug']));
}

// Comentário fica visível imediatamente; moderação (ocultar) é feita depois pelo admin.
$stmt = $pdo->prepare('INSERT INTO comentarios (noticia_id, usuario_id, texto, status) VALUES (?, ?, ?, "visivel")');
$stmt->execute([$noticiaId, usuario_logado()['id'], $texto]);

flash('sucesso', 'Comentário publicado.');
redirect('/article.php?slug=' . urlencode($noticia['slug']) . '#comentarios');
