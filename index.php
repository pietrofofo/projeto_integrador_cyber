<?php
require_once __DIR__ . '/config/config.php';

$categoriaSlug = $_GET['categoria'] ?? null;
$categoriaAtual = null;

if ($categoriaSlug) {
    $stmt = $pdo->prepare('SELECT * FROM categorias WHERE slug = ?');
    $stmt->execute([$categoriaSlug]);
    $categoriaAtual = $stmt->fetch();
}

$sqlBase = 'SELECT n.*, c.nome AS categoria_nome, c.slug AS categoria_slug, u.nome AS autor_nome
            FROM noticias n
            JOIN categorias c ON c.id = n.categoria_id
            JOIN usuarios u ON u.id = n.autor_id
            WHERE n.status = "publicado"';

$params = [];
if ($categoriaAtual) {
    $sqlBase .= ' AND n.categoria_id = ?';
    $params[] = $categoriaAtual['id'];
}

$stmt = $pdo->prepare($sqlBase . ' ORDER BY n.publicado_em DESC LIMIT 7');
$stmt->execute($params);
$noticias = $stmt->fetchAll();

$destaque = array_shift($noticias);

$tituloPagina = $categoriaAtual ? $categoriaAtual['nome'] . ' — ' . SITE_NAME : SITE_NAME . ' — Conscientização, prevenção e combate ao cyberbullying';
require_once __DIR__ . '/includes/header.php';
?>

<?php if ($destaque): ?>
<div class="hero">
  <div class="hero-content">
    <span class="tag"><?= e(mb_strtoupper($destaque['categoria_nome'])) ?></span>
    <h1 class="hero-title"><?= e($destaque['titulo']) ?></h1>
    <p class="hero-excerpt"><?= e($destaque['subtitulo'] ?? resumo($destaque['conteudo'], 220)) ?></p>
    <div class="byline">
      <div class="avatar"></div>
      <span><strong style="color:var(--text);"><?= e($destaque['autor_nome']) ?></strong> • <?= data_br($destaque['publicado_em']) ?> • <?= tempo_leitura($destaque['conteudo']) ?> min de leitura</span>
    </div>
  </div>
</div>
<?php else: ?>
  <div class="container" style="padding-top:60px;">
    <p style="color:var(--text-secondary);">Nenhuma matéria publicada ainda nesta categoria.</p>
  </div>
<?php endif; ?>

<section class="section">
  <div class="section-header">
    <h2><?= $categoriaAtual ? e($categoriaAtual['nome']) : 'Últimas matérias' ?></h2>
    <?php if ($categoriaAtual): ?>
      <a href="<?= BASE_URL ?>/index.php">Ver todas →</a>
    <?php endif; ?>
  </div>

  <?php if ($noticias): ?>
  <div class="card-grid">
    <?php foreach ($noticias as $n): ?>
      <a href="<?= BASE_URL ?>/article.php?slug=<?= urlencode($n['slug']) ?>" class="card" style="text-decoration:none;">
        <div class="card-thumb"></div>
        <span class="card-category"><?= e(mb_strtoupper($n['categoria_nome'])) ?></span>
        <h3><?= e($n['titulo']) ?></h3>
        <p><?= e($n['subtitulo'] ?? resumo($n['conteudo'], 110)) ?></p>
        <span class="card-meta"><?= e($n['autor_nome']) ?> • <?= data_br($n['publicado_em']) ?></span>
      </a>
    <?php endforeach; ?>
  </div>
  <?php elseif ($destaque): ?>
    <p style="color:var(--text-secondary);">Ainda não há outras matérias publicadas.</p>
  <?php endif; ?>
</section>

<div class="section">
  <div class="support-banner">
    <div class="support-banner-text">
      <div class="support-eyebrow">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-4.35-9.5-9C1 8 2.5 4 6.5 4c2 0 3.3 1 3.5 2.5C10.2 5 11.5 4 13.5 4c4 0 5.5 4 4 8-2.5 4.65-9.5 9-9.5 9z"/></svg>
        <h2>Central de Apoio e Denúncias</h2>
      </div>
      <p>Você não está sozinho(a). Encontre orientação, apoio psicológico e canais oficiais para denunciar casos de cyberbullying com segurança e sigilo.</p>
      <div class="support-actions">
        <a href="<?= BASE_URL ?>/central-denuncias.php#denunciar" class="btn btn-primary">Fazer uma denúncia</a>
        <a href="<?= BASE_URL ?>/central-denuncias.php#canais" class="btn btn-outline">Ver canais de ajuda</a>
      </div>
    </div>
    <div class="support-channels">
      <div class="support-channel">CVV — 188 (24h, gratuito)</div>
      <div class="support-channel">SaferNet Brasil</div>
      <div class="support-channel">Disque 100</div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
