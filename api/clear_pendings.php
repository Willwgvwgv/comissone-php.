<?php
include 'db.php';
include 'auth.php';

if (!checkAuth()) {
    http_response_code(401);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM transacoes_ofx WHERE status = 'Pendente'");
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Lançamentos pendentes removidos com sucesso.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
