<?php
// api/import_ofx.php
require_once '../src/db.php';
require_once '../src/utils.php';
require_once '../src/auth.php';
require_once '../src/OfxParser.php';

checkAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Método inválido'], 405);
}

if (!isset($_FILES['ofx_file']) || $_FILES['ofx_file']['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(['error' => 'Erro no upload do arquivo'], 400);
}

$fileTmpPath = $_FILES['ofx_file']['tmp_name'];
$parser = new OfxParser();

try {
    $transactions = $parser->parse($fileTmpPath);
    $count = 0;

    $stmt = $pdo->prepare("INSERT INTO transacoes_ofx (data, tipo, valor, descricao_original, status) VALUES (?, ?, ?, ?, 'Pendente')");

    foreach ($transactions as $trx) {
        // Evita duplicidade simples baseada em data, valor e descricao (opcional, mas bom ter)
        $check = $pdo->prepare("SELECT id FROM transacoes_ofx WHERE data = ? AND valor = ? AND descricao_original = ?");
        $check->execute([$trx['data'], $trx['valor'], $trx['descricao']]);
        
        if (!$check->fetch()) {
            $stmt->execute([$trx['data'], $trx['tipo'], $trx['valor'], $trx['descricao']]);
            $count++;
        }
    }

    jsonResponse(['message' => "$count transações importadas com sucesso!"]);

} catch (Exception $e) {
    jsonResponse(['error' => 'Erro ao processar OFX: ' . $e->getMessage()], 500);
}
?>
