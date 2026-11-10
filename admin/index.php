<?php
require_once __DIR__ . '/../config/config.php';
exigir_papel('jornalista', 'admin');

$usuario = usuario_logado();
$ehAdmin = tem_papel('admin');

if ($ehAdmin) {
    $totalPublicadas = $pdo->query('SELECT COUNT(*) FROM noticias WHERE status = "publicado"')->fetchColumn();
    $totalRascunhos = $pdo->query('SELECT COUNT(*) FROM noticias WHERE status = "rascunho"')->fetchColumn();
    $comentariosVisiveis = $pdo->query('SELECT COUNT(*) FROM comentarios WHERE status = "visivel"')->fetchColumn();
    $denunciasPendentes = $pdo->query('SELECT COUNT(*) FROM denuncias WHERE status = "pendente"')->fetchColumn();
} else {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM noticias WHERE autor_id = ? AND status = "publicado"');
    $stmt->execute([$usuario['id']]);
    $totalPublicadas = $stmt->fetchColumn();

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM noticias WHERE autor_id = ? AND status = "rascunho"');
    $stmt->execute([$usuario['id']]);
    $totalRascunhos = $stmt->fetchColumn();
}

$stmt = $pdo->prepare(
    'SELECT n.*, c.nome AS categoria_nome
     FROM noticias n JOIN categorias c ON c.id = n.categoria_id'
    . ($ehAdmin ? '' : ' WHERE n.autor_id = ?')
    . ' ORDER BY n.atualizado_em DESC LIMIT 6'
);
$stmt->execute($ehAdmin ? [] : [$usuario['id']]);
$recentes = $stmt->fetchAll();

$tituloPagina = 'Painel';
$paginaAtiva = 'dashboard';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <h1 style="font-size:22px;">Olá, <?= e(explode(' ', $usuario['nome'])[0]) ?></h1>
  <a href="<?= BASE_URL ?>/admin/noticia_form.php" class="btn btn-primary">+ Nova matéria</a>
</div>

<div class="stat-cards">
  <div class="stat-card">
    <div class="stat-value"><?= (int) $totalPublicadas ?></div>
    <div class="stat-label"><?= $ehAdmin ? 'Matérias publicadas' : 'Suas matérias publicadas' ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-value"><?= (int) $totalRascunhos ?></div>
    <div class="stat-label">Rascunhos</div>
  </div>
  <?php if ($ehAdmin): ?>
  <div class="stat-card">
    <div class="stat-value"><?= (int) $comentariosVisiveis ?></div>
    <div class="stat-label">Comentários visíveis</div>
  </div>
  <div class="stat-card">
    <div class="stat-value"><?= (int) $denunciasPendentes ?></div>
    <div class="stat-label">Denúncias pendentes</div>
  </div>
  <?php endif; ?>
</div>

<h2 style="font-size:16px; margin-bottom:14px;">Atualizadas recentemente</h2>
<div class="table-wrapper">
  <table>
    <thead>
      <tr>
        <th>Título</th>
        <th>Categoria</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($recentes as $n): ?>
        <tr>
          <td><?= e($n['titulo']) ?></td>
          <td><?= e($n['categoria_nome']) ?></td>
          <td><span class="status-badge status-<?= e($n['status']) ?>"><?= $n['status'] === 'publicado' ? 'Publicado' : 'Rascunho' ?></span></td>
          <td><a href="<?= BASE_URL ?>/admin/noticia_form.php?id=<?= $n['id'] ?>">Editar</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$recentes): ?>
        <tr><td colspan="4" style="color:var(--text-muted);">Nenhuma matéria ainda. Clique em "+ Nova matéria" para começar.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
