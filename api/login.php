<?php
// api/login.php
require_once '../src/db.php';
require_once '../src/utils.php';
require_once '../src/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Método inválido'], 405);
}

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$senha = $data['senha'] ?? '';

$stmt = $pdo->prepare("SELECT id, nome, senha_hash, role FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($senha, $user['senha_hash'])) {
    session_regenerate_id();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nome'] = $user['nome'];
    $_SESSION['role'] = $user['role'];
    jsonResponse(['message' => 'Login realizado com sucesso', 'redirect' => 'dashboard.php']);
} else {
    jsonResponse(['error' => 'Email ou senha inválidos'], 401);
}
?>
