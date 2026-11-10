<?php
/**
 * Cabeçalho do painel administrativo.
 * Espera: $tituloPagina, $paginaAtiva ('dashboard'|'noticias'|'comentarios'|'denuncias')
 * Garante que o usuário esteja logado como jornalista ou admin.
 */
exigir_papel('jornalista', 'admin');

$usuario = usuario_logado();
$paginaAtiva = $paginaAtiva ?? '';
$tituloPagina = ($tituloPagina ?? 'Painel') . ' — ' . SITE_NAME;
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($tituloPagina) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<div class="admin-shell">
  <aside class="admin-sidebar">
    <a href="<?= BASE_URL ?>/index.php" class="brand">
      <span class="brand-dot"></span>
      <?= e(SITE_NAME) ?>
    </a>

    <nav class="admin-nav">
      <a href="<?= BASE_URL ?>/admin/index.php" class="<?= $paginaAtiva === 'dashboard' ? 'active' : '' ?>">Painel</a>
      <a href="<?= BASE_URL ?>/admin/noticias.php" class="<?= $paginaAtiva === 'noticias' ? 'active' : '' ?>">Matérias</a>
      <?php if (tem_papel('admin')): ?>
        <a href="<?= BASE_URL ?>/admin/comentarios.php" class="<?= $paginaAtiva === 'comentarios' ? 'active' : '' ?>">Comentários</a>
        <a href="<?= BASE_URL ?>/admin/denuncias.php" class="<?= $paginaAtiva === 'denuncias' ? 'active' : '' ?>">Denúncias</a>
      <?php endif; ?>
    </nav>

    <div style="margin-top:32px; padding-top:20px; border-top:1px solid var(--border);">
      <p style="font-size:13px; font-weight:700; margin:0;"><?= e($usuario['nome']) ?></p>
      <p style="font-size:12px; color:var(--text-muted); margin:2px 0 12px; text-transform:capitalize;"><?= e($usuario['papel']) ?></p>
      <a href="<?= BASE_URL ?>/index.php" style="font-size:13px;">← Voltar ao site</a>
      <form action="<?= BASE_URL ?>/actions/logout_action.php" method="post" style="margin-top:10px;">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-outline btn-sm btn-block">Sair</button>
      </form>
    </div>
  </aside>

  <main class="admin-content">
    <?php foreach (pegar_flashes() as $flash): ?>
      <div class="alert alert-<?= e($flash['tipo']) ?>"><?= e($flash['mensagem']) ?></div>
    <?php endforeach; ?>
