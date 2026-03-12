<?php
// api/crud_imovel.php
require_once '../src/db.php';
require_once '../src/utils.php';
require_once '../src/auth.php';

checkAuth(); // Protegido

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Listar imóveis
    $stmt = $pdo->query("
        SELECT i.*, p.nome as proprietario_nome 
        FROM imoveis i 
        JOIN proprietarios p ON i.proprietario_id = p.id
        ORDER BY i.created_at DESC
    ");
    $imoveis = $stmt->fetchAll();
    
    // Buscar proprietários para o select do form
    $stmt_props = $pdo->query("SELECT id, nome FROM proprietarios ORDER BY nome ASC");
    $proprietarios = $stmt_props->fetchAll();

    jsonResponse(['imoveis' => $imoveis, 'proprietarios' => $proprietarios]);

} elseif ($method === 'POST') {
    // Criar ou Editar (se ID vier)
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Se tiver proprietario_novo, cria o proprietário primeiro
    $proprietario_id = $data['proprietario_id'] ?? null;
    if (!empty($data['novo_proprietario_nome'])) {
        $stmt = $pdo->prepare("INSERT INTO proprietarios (nome, email, telefone) VALUES (?, ?, ?)");
        $stmt->execute([
            $data['novo_proprietario_nome'], 
            $data['novo_proprietario_email'] ?? null, 
            $data['novo_proprietario_telefone'] ?? null
        ]);
        $proprietario_id = $pdo->lastInsertId();
    }

    if (!$proprietario_id) {
        jsonResponse(['error' => 'Proprietário é obrigatório'], 400);
    }

    if (isset($data['id']) && !empty($data['id'])) {
        // Update
        $stmt = $pdo->prepare("UPDATE imoveis SET endereco_completo = ?, proprietario_id = ?, status = ? WHERE id = ?");
        $stmt->execute([$data['endereco'], $proprietario_id, $data['status'], $data['id']]);
        jsonResponse(['message' => 'Imóvel atualizado!']);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO imoveis (endereco_completo, proprietario_id, status) VALUES (?, ?, ?)");
        $stmt->execute([$data['endereco'], $proprietario_id, $data['status']]);
        jsonResponse(['message' => 'Imóvel cadastrado!']);
    }

} elseif ($method === 'DELETE') {
    // Excluir
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM imoveis WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse(['message' => 'Imóvel excluído!']);
    } else {
        jsonResponse(['error' => 'ID não fornecido'], 400);
    }
}
?>
