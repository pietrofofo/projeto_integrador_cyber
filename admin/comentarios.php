<?php
require_once __DIR__ . '/../config/config.php';
exigir_papel('admin');

$stmt = $pdo->query(
    'SELECT co.*, u.nome AS autor_nome, n.titulo AS noticia_titulo, n.slug AS noticia_slug
     FROM comentarios co
     JOIN usuarios u ON u.id = co.usuario_id
     JOIN noticias n ON n.id = co.noticia_id
     ORDER BY co.criado_em DESC'
);
$comentarios = $stmt->fetchAll();

$tituloPagina = 'Moderação de comentários';
$paginaAtiva = 'comentarios';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <h1 style="font-size:22px;">Comentários</h1>
</div>

<p style="color:var(--text-secondary); font-size:14px; margin-bottom:20px;">
  Comentários ficam visíveis assim que publicados pelos leitores. Use esta tela para ocultar comentários ofensivos ou reincidir sobre denúncias recebidas.
</p>

<div class="table-wrapper">
  <table>
    <thead>
      <tr>
        <th>Comentário</th>
        <th>Autor</th>
        <th>Matéria</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($comentarios as $c): ?>
        <tr>
          <td style="max-width:320px;"><?= e(resumo($c['texto'], 100)) ?></td>
          <td><?= e($c['autor_nome']) ?></td>
          <td><a href="<?= BASE_URL ?>/article.php?slug=<?= urlencode($c['noticia_slug']) ?>" target="_blank"><?= e(resumo($c['noticia_titulo'], 40)) ?></a></td>
          <td><span class="status-badge status-<?= e($c['status']) ?>"><?= $c['status'] === 'visivel' ? 'Visível' : 'Oculto' ?></span></td>
          <td class="table-actions">
            <form action="<?= BASE_URL ?>/admin/comentario_moderar.php" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= $c['id'] ?>">
              <input type="hidden" name="novo_status" value="<?= $c['status'] === 'visivel' ? 'oculto' : 'visivel' ?>">
              <button type="submit" class="btn <?= $c['status'] === 'visivel' ? 'btn-danger' : 'btn-outline' ?> btn-sm">
                <?= $c['status'] === 'visivel' ? 'Ocultar' : 'Reexibir' ?>
              </button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$comentarios): ?>
        <tr><td colspan="5" style="color:var(--text-muted);">Nenhum comentário publicado ainda.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
