<?php
require_once __DIR__ . '/../config/config.php';
exigir_papel('jornalista', 'admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/noticias.php');
}

csrf_verify();

$usuario = usuario_logado();
$ehAdmin = tem_papel('admin');

$id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
$titulo = trim($_POST['titulo'] ?? '');
$subtitulo = trim($_POST['subtitulo'] ?? '');
$categoriaId = (int) ($_POST['categoria_id'] ?? 0);
$status = ($_POST['status'] ?? 'rascunho') === 'publicado' ? 'publicado' : 'rascunho';
$conteudo = $_POST['conteudo'] ?? '';

if ($titulo === '' || $conteudo === '' || strip_tags($conteudo) === '') {
    flash('erro', 'Preencha o título e o conteúdo da matéria.');
    redirect($id ? '/admin/noticia_form.php?id=' . $id : '/admin/noticia_form.php');
}

// Permite apenas tags básicas vindas do editor rico — remove scripts e atributos perigosos.
$conteudo = strip_tags($conteudo, '<p><div><h2><h3><strong><b><em><i><ul><ol><li><a><br><blockquote>');

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM noticias WHERE id = ?');
    $stmt->execute([$id]);
    $existente = $stmt->fetch();

    if (!$existente) {
        flash('erro', 'Matéria não encontrada.');
        redirect('/admin/noticias.php');
    }
    if (!$ehAdmin && (int) $existente['autor_id'] !== (int) $usuario['id']) {
        http_response_code(403);
        die('Você só pode editar suas próprias matérias.');
    }

    $slug = slug_unico($pdo, $titulo, $id);
    $publicadoEm = $existente['publicado_em'];
    if ($status === 'publicado' && !$publicadoEm) {
        $publicadoEm = date('Y-m-d H:i:s');
    }

    $stmt = $pdo->prepare(
        'UPDATE noticias SET titulo=?, subtitulo=?, slug=?, conteudo=?, categoria_id=?, status=?, publicado_em=? WHERE id=?'
    );
    $stmt->execute([$titulo, $subtitulo ?: null, $slug, $conteudo, $categoriaId, $status, $publicadoEm, $id]);

    flash('sucesso', 'Matéria atualizada.');
    redirect('/admin/noticia_form.php?id=' . $id);
}

$slug = slug_unico($pdo, $titulo);
$publicadoEm = $status === 'publicado' ? date('Y-m-d H:i:s') : null;

$stmt = $pdo->prepare(
    'INSERT INTO noticias (titulo, subtitulo, slug, conteudo, categoria_id, autor_id, status, publicado_em)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([$titulo, $subtitulo ?: null, $slug, $conteudo, $categoriaId, $usuario['id'], $status, $publicadoEm]);

flash('sucesso', 'Matéria criada.');
redirect('/admin/noticia_form.php?id=' . $pdo->lastInsertId());
