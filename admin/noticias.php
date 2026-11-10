<?php
require_once __DIR__ . '/../config/config.php';
exigir_papel('jornalista', 'admin');

$usuario = usuario_logado();
$ehAdmin = tem_papel('admin');

$sql = 'SELECT n.*, c.nome AS categoria_nome, u.nome AS autor_nome
        FROM noticias n
        JOIN categorias c ON c.id = n.categoria_id
        JOIN usuarios u ON u.id = n.autor_id';
$params = [];
if (!$ehAdmin) {
    $sql .= ' WHERE n.autor_id = ?';
    $params[] = $usuario['id'];
}
$sql .= ' ORDER BY n.atualizado_em DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$noticias = $stmt->fetchAll();

$tituloPagina = 'Matérias';
$paginaAtiva = 'noticias';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <h1 style="font-size:22px;">Matérias</h1>
  <a href="<?= BASE_URL ?>/admin/noticia_form.php" class="btn btn-primary">+ Nova matéria</a>
</div>

<div class="table-wrapper">
  <table>
    <thead>
      <tr>
        <th>Título</th>
        <th>Categoria</th>
        <th>Autor</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($noticias as $n): ?>
        <tr>
          <td><?= e($n['titulo']) ?></td>
          <td><?= e($n['categoria_nome']) ?></td>
          <td><?= e($n['autor_nome']) ?></td>
          <td><span class="status-badge status-<?= e($n['status']) ?>"><?= $n['status'] === 'publicado' ? 'Publicado' : 'Rascunho' ?></span></td>
          <td class="table-actions">
            <a href="<?= BASE_URL ?>/admin/noticia_form.php?id=<?= $n['id'] ?>" class="btn btn-outline btn-sm">Editar</a>
            <form action="<?= BASE_URL ?>/admin/noticia_delete.php" method="post" onsubmit="return confirm('Excluir esta matéria permanentemente?');">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= $n['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$noticias): ?>
        <tr><td colspan="5" style="color:var(--text-muted);">Nenhuma matéria cadastrada.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
