<?php
// api/register.php
require_once '../src/db.php';
require_once '../src/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Método inválido'], 405);
}

$data = json_decode(file_get_contents("php://input"), true);
$nome = $data['nome'] ?? '';
$email = $data['email'] ?? '';
$senha = $data['senha'] ?? '';

if (empty($nome) || empty($email) || empty($senha)) {
    jsonResponse(['error' => 'Preencha todos os campos'], 400);
}

// Verifica se email já existe
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    jsonResponse(['error' => 'Email já cadastrado'], 400);
}

// Cria usuário
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (nome, email, senha_hash) VALUES (?, ?, ?)");

if ($stmt->execute([$nome, $email, $senha_hash])) {
    jsonResponse(['message' => 'Usuário cadastrado com sucesso!']);
} else {
    jsonResponse(['error' => 'Erro ao cadastrar'], 500);
}
?>
