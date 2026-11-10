<?php
require_once __DIR__ . '/config/config.php';

$q = trim($_GET['q'] ?? '');
$resultados = [];

if ($q !== '') {
    $stmt = $pdo->prepare(
        'SELECT n.*, c.nome AS categoria_nome
         FROM noticias n
         JOIN categorias c ON c.id = n.categoria_id
         WHERE n.status = "publicado"
           AND (n.titulo LIKE ? OR n.subtitulo LIKE ? OR n.conteudo LIKE ?)
         ORDER BY n.publicado_em DESC
         LIMIT 30'
    );
    $termo = '%' . $q . '%';
    $stmt->execute([$termo, $termo, $termo]);
    $resultados = $stmt->fetchAll();
}

$tituloPagina = 'Busca — ' . SITE_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="search-header">
  <h1>Buscar matérias</h1>
  <?php if ($q !== ''): ?>
    <p style="color:var(--text-secondary); margin-top:8px;"><?= count($resultados) ?> resultado(s) para "<?= e($q) ?>"</p>
  <?php endif; ?>
</div>

<div class="search-list">
  <?php if ($q === ''): ?>
    <p style="color:var(--text-muted); padding-top:20px;">Digite um termo na busca do topo da página para encontrar matérias.</p>
  <?php elseif (!$resultados): ?>
    <p style="color:var(--text-muted); padding-top:20px;">Nenhuma matéria encontrada para "<?= e($q) ?>".</p>
  <?php else: ?>
    <?php foreach ($resultados as $n): ?>
      <a href="<?= BASE_URL ?>/article.php?slug=<?= urlencode($n['slug']) ?>" class="search-item" style="text-decoration:none; color:inherit;">
        <div class="card-thumb"></div>
        <div>
          <span class="card-category"><?= e(mb_strtoupper($n['categoria_nome'])) ?></span>
          <h3 style="margin:6px 0 8px; font-size:18px;"><?= e($n['titulo']) ?></h3>
          <p style="color:var(--text-secondary); font-size:14px; margin:0;"><?= e($n['subtitulo'] ?? resumo($n['conteudo'], 140)) ?></p>
          <span class="card-meta"><?= data_br($n['publicado_em']) ?></span>
        </div>
      </a>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
