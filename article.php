<?php
require_once __DIR__ . '/config/config.php';

$slug = $_GET['slug'] ?? '';

$stmt = $pdo->prepare(
    'SELECT n.*, c.nome AS categoria_nome, c.slug AS categoria_slug, u.nome AS autor_nome
     FROM noticias n
     JOIN categorias c ON c.id = n.categoria_id
     JOIN usuarios u ON u.id = n.autor_id
     WHERE n.slug = ? AND n.status = "publicado"'
);
$stmt->execute([$slug]);
$noticia = $stmt->fetch();

if (!$noticia) {
    http_response_code(404);
    $tituloPagina = 'Matéria não encontrada — ' . SITE_NAME;
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="container" style="padding:80px 24px; text-align:center;">
            <h1>Matéria não encontrada</h1>
            <p style="color:var(--text-secondary); margin-top:12px;">O link pode estar quebrado ou a matéria foi removida.</p>
            <a href="' . BASE_URL . '/index.php" class="btn btn-primary" style="margin-top:20px;">Voltar para a home</a>
          </div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$stmtComentarios = $pdo->prepare(
    'SELECT co.*, u.nome AS autor_nome
     FROM comentarios co
     JOIN usuarios u ON u.id = co.usuario_id
     WHERE co.noticia_id = ? AND co.status = "visivel"
     ORDER BY co.criado_em DESC'
);
$stmtComentarios->execute([$noticia['id']]);
$comentarios = $stmtComentarios->fetchAll();

$usuario = usuario_logado();
$tituloPagina = $noticia['titulo'] . ' — ' . SITE_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<article>
  <div class="article-header">
    <span class="tag"><?= e(mb_strtoupper($noticia['categoria_nome'])) ?></span>
    <h1 class="article-title"><?= e($noticia['titulo']) ?></h1>
    <?php if ($noticia['subtitulo']): ?>
      <p class="article-subtitle"><?= e($noticia['subtitulo']) ?></p>
    <?php endif; ?>

    <div class="article-toolbar">
      <div class="byline">
        <div class="avatar"></div>
        <span><strong style="color:var(--text);"><?= e($noticia['autor_nome']) ?></strong> • <?= data_br($noticia['publicado_em']) ?> • <?= tempo_leitura($noticia['conteudo']) ?> min de leitura</span>
      </div>
      <div class="share-row">
        <button type="button" class="icon-btn" data-share-native data-url="<?= e(url_atual()) ?>" data-title="<?= e($noticia['titulo']) ?>" title="Compartilhar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.6" y1="10.6" x2="15.4" y2="6.4"/><line x1="8.6" y1="13.4" x2="15.4" y2="17.6"/></svg>
        </button>
        <a class="icon-btn" data-share-social="whatsapp" data-url="<?= e(url_atual()) ?>" data-title="<?= e($noticia['titulo']) ?>" title="Compartilhar no WhatsApp">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.5 8.5 0 0 1-12.4 7.5L3 20l1.1-5.4A8.5 8.5 0 1 1 21 11.5z"/></svg>
        </a>
        <a class="icon-btn" data-share-social="twitter" data-url="<?= e(url_atual()) ?>" data-title="<?= e($noticia['titulo']) ?>" title="Compartilhar no Twitter/X">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 4s-.7 2-2 3c1.4 8-7 13-15 8 2.5.1 4-.6 5-2C6 12 4 10 4 7c1.5 1 3 1 4-1-2-3 1-6 3-4 1.5-1.5 4-1.5 5 0"/></svg>
        </a>
        <a class="icon-btn" data-share-social="facebook" data-url="<?= e(url_atual()) ?>" data-title="<?= e($noticia['titulo']) ?>" title="Compartilhar no Facebook">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h-2a5 5 0 0 0-5 5v2H6v4h2v7h4v-7h3l1-4h-4V8a1 1 0 0 1 1-1h3z"/></svg>
        </a>
      </div>
    </div>
  </div>

  <div class="article-cover"></div>

  <div class="article-body">
    <?= $noticia['conteudo'] ?>
  </div>

  <div class="container" style="max-width:760px; margin-bottom:40px;">
    <a href="<?= BASE_URL ?>/central-denuncias.php?tipo=noticia&noticia_id=<?= $noticia['id'] ?>#denunciar" class="comment-report">🚩 Denunciar esta matéria</a>
  </div>
</article>

<section id="comentarios" class="comments">
  <h3><?= count($comentarios) ?> comentário<?= count($comentarios) === 1 ? '' : 's' ?></h3>

  <?php if ($usuario): ?>
    <form class="comment-form" action="<?= BASE_URL ?>/actions/comment_action.php" method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="noticia_id" value="<?= $noticia['id'] ?>">
      <div class="form-group">
        <textarea class="form-control" name="texto" placeholder="Escreva um comentário respeitoso..." required maxlength="1000"></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Publicar comentário</button>
    </form>
  <?php else: ?>
    <p style="color:var(--text-secondary); margin-bottom:32px;">
      <a href="<?= BASE_URL ?>/login.php">Entre</a> ou <a href="<?= BASE_URL ?>/cadastro.php">cadastre-se</a> para comentar.
    </p>
  <?php endif; ?>

  <?php foreach ($comentarios as $c): ?>
    <div class="comment">
      <div class="avatar"></div>
      <div class="comment-body">
        <span class="comment-author"><?= e($c['autor_nome']) ?><span class="comment-date"><?= data_br($c['criado_em']) ?></span></span>
        <p class="comment-text"><?= nl2br(e($c['texto'])) ?></p>
        <a class="comment-report" href="<?= BASE_URL ?>/central-denuncias.php?tipo=comentario&comentario_id=<?= $c['id'] ?>#denunciar">🚩 Denunciar comentário</a>
      </div>
    </div>
  <?php endforeach; ?>

  <?php if (!$comentarios): ?>
    <p style="color:var(--text-muted); font-size:14px;">Seja o primeiro a comentar.</p>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
