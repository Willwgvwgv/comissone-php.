<?php
// api/crud_despesa.php
require_once '../src/db.php';
require_once '../src/utils.php';
require_once '../src/auth.php';

checkAuth();

$method = $_SERVER['REQUEST_METHOD'];
$user_id = $_SESSION['user_id'];

if ($method === 'GET') {
    $where = "WHERE user_id = ?";
    $params = [$user_id];

    if (!empty($_GET['mes'])) {
        $where .= " AND MONTH(data) = ?";
        $params[] = $_GET['mes'];
    }

    $stmt = $pdo->prepare("
        SELECT d.*, i.endereco_completo as imovel_endereco 
        FROM despesas d 
        LEFT JOIN imoveis i ON d.imovel_id = i.id
        $where
        ORDER BY d.data DESC
    ");
    $stmt->execute($params);
    jsonResponse($stmt->fetchAll());

} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $imovel_id = !empty($data['imovel_id']) ? $data['imovel_id'] : null;

    if (isset($data['id']) && !empty($data['id'])) {
        $stmt = $pdo->prepare("UPDATE despesas SET descricao=?, valor=?, data=?, categoria=?, imovel_id=? WHERE id=? AND user_id=?");
        $stmt->execute([
            $data['descricao'], $data['valor'], $data['data'], 
            $data['categoria'], $imovel_id, $data['id'], $user_id
        ]);
        jsonResponse(['message' => 'Despesa atualizada!']);
    } else {
        $stmt = $pdo->prepare("INSERT INTO despesas (user_id, descricao, valor, data, categoria, imovel_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id, $data['descricao'], $data['valor'], 
            $data['data'], $data['categoria'], $imovel_id
        ]);
        jsonResponse(['message' => 'Despesa criada!']);
    }

} elseif ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    $stmt = $pdo->prepare("DELETE FROM despesas WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    jsonResponse(['message' => 'Despesa excluída!']);
}
?>
