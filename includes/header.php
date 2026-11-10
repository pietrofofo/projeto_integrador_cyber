<?php
/**
 * Cabeçalho compartilhado. Espera opcionalmente:
 *   $tituloPagina (string) — usado na tag <title>
 * Definido antes do include.
 */
$tituloPagina = $tituloPagina ?? SITE_NAME;
$usuario = usuario_logado();
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

<header>
  <div class="site-header">
    <a href="<?= BASE_URL ?>/index.php" class="brand">
      <span class="brand-dot"></span>
      <?= e(SITE_NAME) ?>
    </a>

    <nav class="main-nav">
      <a href="<?= BASE_URL ?>/index.php?categoria=prevencao">Prevenção</a>
      <a href="<?= BASE_URL ?>/index.php?categoria=legislacao">Legislação</a>
      <a href="<?= BASE_URL ?>/index.php?categoria=depoimentos">Depoimentos</a>
      <a href="<?= BASE_URL ?>/index.php?categoria=escolas">Escolas</a>
      <a href="<?= BASE_URL ?>/index.php?categoria=tecnologia">Tecnologia</a>
      <a href="<?= BASE_URL ?>/central-denuncias.php">Central de Denúncias</a>
    </nav>

    <div class="header-actions">
      <form class="search-box" action="<?= BASE_URL ?>/search.php" method="get">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" width="16" height="16"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="search" name="q" placeholder="Buscar matérias..." value="<?= e($_GET['q'] ?? '') ?>">
      </form>

      <button type="button" id="theme-toggle" class="icon-btn" title="Alternar modo escuro" aria-label="Alternar modo escuro">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
      </button>

      <?php if ($usuario): ?>
        <?php if ($usuario['papel'] !== 'leitor'): ?>
          <a href="<?= BASE_URL ?>/admin/index.php" class="btn btn-outline btn-sm">Painel</a>
        <?php endif; ?>
        <form action="<?= BASE_URL ?>/actions/logout_action.php" method="post" style="display:inline;">
          <?= csrf_field() ?>
          <button type="submit" class="btn btn-primary btn-sm">Sair</button>
        </form>
      <?php else: ?>
        <a href="<?= BASE_URL ?>/login.php">Entrar</a>
        <a href="<?= BASE_URL ?>/cadastro.php" class="btn btn-primary btn-sm">Cadastrar</a>
      <?php endif; ?>

      <button type="button" id="menu-toggle" class="hamburger" aria-label="Abrir menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" width="24" height="24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
    </div>
  </div>

  <nav id="mobile-nav" class="mobile-nav">
    <a href="<?= BASE_URL ?>/index.php?categoria=prevencao">Prevenção</a>
    <a href="<?= BASE_URL ?>/index.php?categoria=legislacao">Legislação</a>
    <a href="<?= BASE_URL ?>/index.php?categoria=depoimentos">Depoimentos</a>
    <a href="<?= BASE_URL ?>/index.php?categoria=escolas">Escolas</a>
    <a href="<?= BASE_URL ?>/index.php?categoria=tecnologia">Tecnologia</a>
    <a href="<?= BASE_URL ?>/central-denuncias.php">Central de Denúncias</a>
    <form class="search-box" action="<?= BASE_URL ?>/search.php" method="get" style="margin-top:10px;">
      <input type="search" name="q" placeholder="Buscar matérias..." style="width:100%;">
    </form>
  </nav>
</header>

<?php foreach (pegar_flashes() as $flash): ?>
  <div class="container" style="padding-top:20px;">
    <div class="alert alert-<?= e($flash['tipo']) ?>"><?= e($flash['mensagem']) ?></div>
  </div>
<?php endforeach; ?>
