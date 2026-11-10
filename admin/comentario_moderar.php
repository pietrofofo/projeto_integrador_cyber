<?php
require_once __DIR__ . '/../config/config.php';
exigir_papel('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/comentarios.php');
}

csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
$novoStatus = ($_POST['novo_status'] ?? '') === 'visivel' ? 'visivel' : 'oculto';

$stmt = $pdo->prepare('UPDATE comentarios SET status = ? WHERE id = ?');
$stmt->execute([$novoStatus, $id]);

flash('sucesso', $novoStatus === 'oculto' ? 'Comentário ocultado.' : 'Comentário reexibido.');
redirect('/admin/comentarios.php');
