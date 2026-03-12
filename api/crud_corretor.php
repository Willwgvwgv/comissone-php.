<?php
// api/crud_corretor.php
require_once '../src/db.php';
require_once '../src/utils.php';
require_once '../src/auth.php';

checkAuth();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Listar
    $stmt = $pdo->query("SELECT * FROM corretores ORDER BY nome ASC");
    jsonResponse($stmt->fetchAll());

} elseif ($method === 'POST') {
    // Criar / Editar
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['id']) && !empty($data['id'])) {
        // Update
        $sql = "UPDATE corretores SET 
                nome=?, cpf_cnpj=?, creci=?, email=?, telefone=?, 
                banco=?, agencia=?, conta=?, tipo_conta=?, chave_pix=?, 
                percentual_comissao_padrao_venda=?, percentual_comissao_padrao_aluguel=?, status=?
                WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['nome'], $data['cpf_cnpj'], $data['creci'], $data['email'], $data['telefone'],
            $data['banco'], $data['agencia'], $data['conta'], $data['tipo_conta'], $data['chave_pix'],
            $data['percentual_comissao_padrao_venda'], $data['percentual_comissao_padrao_aluguel'], $data['status'],
            $data['id']
        ]);
        jsonResponse(['message' => 'Corretor atualizado!']);
    } else {
        // Insert
        $sql = "INSERT INTO corretores (
                nome, cpf_cnpj, creci, email, telefone, 
                banco, agencia, conta, tipo_conta, chave_pix, 
                percentual_comissao_padrao_venda, percentual_comissao_padrao_aluguel, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['nome'], $data['cpf_cnpj'], $data['creci'], $data['email'], $data['telefone'],
            $data['banco'], $data['agencia'], $data['conta'], $data['tipo_conta'], $data['chave_pix'],
            $data['percentual_comissao_padrao_venda'], $data['percentual_comissao_padrao_aluguel'], $data['status'] ?? 'Ativo'
        ]);
        jsonResponse(['message' => 'Corretor cadastrado!']);
    }

} elseif ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        // Soft delete could be better, but user asked for standard CRUD actions.
        // Check dependencies first? For now simple delete.
        try {
            $stmt = $pdo->prepare("DELETE FROM corretores WHERE id = ?");
            $stmt->execute([$id]);
            jsonResponse(['message' => 'Corretor excluído!']);
        } catch (PDOException $e) {
            jsonResponse(['error' => 'Não é possível excluir corretor com comissões vinculadas.'], 400);
        }
    }
}
?>
