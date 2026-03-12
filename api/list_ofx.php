<?php
// api/list_ofx.php
require_once '../src/db.php';
require_once '../src/utils.php';
require_once '../src/auth.php';

checkAuth();

$stmt = $pdo->query("SELECT * FROM transacoes_ofx WHERE status = 'Pendente' ORDER BY data DESC");
jsonResponse($stmt->fetchAll());
?>
