<?php
// api/list_usuarios.php
require_once '../src/db.php';
require_once '../src/utils.php';
require_once '../src/auth.php';

checkAdmin();

$stmt = $pdo->query("SELECT id, nome, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

jsonResponse($users);
?>
