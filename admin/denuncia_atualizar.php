<?php
require_once __DIR__ . '/../config/config.php';
exigir_papel('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/denuncias.php');
}

csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
$novoStatus = $_POST['novo_status'] ?? 'pendente';
if (!in_array($novoStatus, ['pendente', 'em_analise', 'resolvida'], true)) {
    $novoStatus = 'pendente';
}

$stmt = $pdo->prepare('UPDATE denuncias SET status = ? WHERE id = ?');
$stmt->execute([$novoStatus, $id]);

flash('sucesso', 'Status da denúncia atualizado.');
redirect('/admin/denuncias.php');
