<?php
require_once __DIR__ . '/config/config.php';

if (esta_logado()) {
    redirect('/index.php');
}

$tituloPagina = 'Criar conta — ' . SITE_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
  <div class="auth-card">
    <h1>Criar conta</h1>
    <p class="auth-sub">Cadastre-se como leitor para comentar e favoritar matérias.</p>

    <form action="<?= BASE_URL ?>/actions/cadastro_action.php" method="post">
      <?= csrf_field() ?>
      <div class="form-group">
        <label for="nome">Nome completo</label>
        <input type="text" id="nome" name="nome" class="form-control" required autofocus>
      </div>
      <div class="form-group">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" class="form-control" minlength="6" required>
        <span class="form-hint">Mínimo de 6 caracteres.</span>
      </div>
      <div class="form-group">
        <label for="senha_confirma">Confirmar senha</label>
        <input type="password" id="senha_confirma" name="senha_confirma" class="form-control" minlength="6" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Criar conta</button>
    </form>

    <p class="form-footer">Já tem conta? <a href="<?= BASE_URL ?>/login.php">Entrar</a></p>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
