<?php
require_once __DIR__ . '/../config/config.php';
exigir_papel('admin');

$stmt = $pdo->query(
    'SELECT d.*, u.nome AS denunciante_nome, n.titulo AS noticia_titulo, n.slug AS noticia_slug
     FROM denuncias d
     LEFT JOIN usuarios u ON u.id = d.denunciante_id
     LEFT JOIN noticias n ON n.id = d.noticia_id
     ORDER BY FIELD(d.status, "pendente", "em_analise", "resolvida"), d.criado_em DESC'
);
$denuncias = $stmt->fetchAll();

$tituloPagina = 'Denúncias';
$paginaAtiva = 'denuncias';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <h1 style="font-size:22px;">Denúncias</h1>
</div>

<div class="table-wrapper">
  <table>
    <thead>
      <tr>
        <th>Tipo</th>
        <th>Descrição</th>
        <th>Origem</th>
        <th>Contato</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($denuncias as $d): ?>
        <tr>
          <td style="text-transform:capitalize;"><?= e($d['tipo']) ?></td>
          <td style="max-width:280px;"><?= e(resumo($d['descricao'], 100)) ?></td>
          <td>
            <?php if ($d['tipo'] === 'noticia' && $d['noticia_titulo']): ?>
              <a href="<?= BASE_URL ?>/article.php?slug=<?= urlencode($d['noticia_slug']) ?>" target="_blank"><?= e(resumo($d['noticia_titulo'], 30)) ?></a>
            <?php elseif ($d['tipo'] === 'comentario' && $d['comentario_id']): ?>
              Comentário #<?= (int) $d['comentario_id'] ?>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
          <td>
            <?= e($d['denunciante_nome'] ?? $d['nome_contato'] ?? 'Anônimo') ?>
            <?php if ($d['email_contato']): ?><br><span style="color:var(--text-muted); font-size:12px;"><?= e($d['email_contato']) ?></span><?php endif; ?>
          </td>
          <td><span class="status-badge status-<?= e($d['status']) ?>"><?= ucfirst(str_replace('_', ' ', $d['status'])) ?></span></td>
          <td>
            <form action="<?= BASE_URL ?>/admin/denuncia_atualizar.php" method="post" style="display:flex; gap:6px;">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= $d['id'] ?>">
              <select name="novo_status" class="form-control" style="padding:6px 8px; font-size:12.5px;">
                <option value="pendente" <?= $d['status'] === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                <option value="em_analise" <?= $d['status'] === 'em_analise' ? 'selected' : '' ?>>Em análise</option>
                <option value="resolvida" <?= $d['status'] === 'resolvida' ? 'selected' : '' ?>>Resolvida</option>
              </select>
              <button type="submit" class="btn btn-outline btn-sm">Salvar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$denuncias): ?>
        <tr><td colspan="6" style="color:var(--text-muted);">Nenhuma denúncia registrada.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
