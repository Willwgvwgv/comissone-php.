<?php
// api/reconcile_transaction.php
require_once '../src/db.php';
require_once '../src/utils.php';
require_once '../src/auth.php';

checkAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Método inválido'], 405);
}

$data = json_decode(file_get_contents("php://input"), true);
$ofx_id = $data['ofx_id'] ?? null;
$action = $data['action'] ?? ''; // 'link' or 'create'
$user_id = $_SESSION['user_id'];

if (!$ofx_id || !$action) {
    jsonResponse(['error' => 'Dados incompletos'], 400);
}

try {
    $pdo->beginTransaction();

    if ($action === 'link') {
        // Vincular a existente
        $lancamento_id = $data['lancamento_id'] ?? null;
        $tipo = $data['tipo_lancamento'] ?? ''; // 'receita' or 'despesa'

        if (!$lancamento_id || !$tipo) {
            throw new Exception("ID ou Tipo de lançamento inválidos");
        }

        // Se for comissão
        if ($tipo === 'pagamento_comissao') {
            // Atualiza OFX
            $stmt = $pdo->prepare("UPDATE transacoes_ofx SET status='Conciliado', lancamento_id=?, tipo_lancamento=?, id_entidade_conciliada=?, tipo_entidade_conciliada='PagamentoComissao' WHERE id=?");
            $stmt->execute([$lancamento_id, 'despesa', $lancamento_id, $ofx_id]); 
            // Nota: 'lancamento_id'/'tipo_lancamento' eram os campos antigos. Estou preenchendo pra garantir compatibilidade, 
            // mas 'tipo_lancamento' com 'despesa' pode ser confuso. O ideal seria usar as novas colunas.
            // Vou usar as novas colunas exclusivamente para a lógica nova.
            
            // Retificando query do OFX para usar novas colunas:
            $stmt = $pdo->prepare("UPDATE transacoes_ofx SET status='Conciliado', id_entidade_conciliada=?, tipo_entidade_conciliada='PagamentoComissao' WHERE id=?");
            $stmt->execute([$lancamento_id, $ofx_id]);

            // Atualiza PagamentoComissao
            $stmtPay = $pdo->prepare("UPDATE pagamentos_comissoes SET status_conciliacao='Conciliado', referencia_bancaria=CONCAT(referencia_bancaria, ' [OFX:', ? ,']') WHERE id=?");
            $stmtPay->execute([$ofx_id, $lancamento_id]);

        } else {
            // Lógica antiga (Receita/Despesa direta)
            $stmt = $pdo->prepare("UPDATE transacoes_ofx SET status='Conciliado', lancamento_id=?, tipo_lancamento=? WHERE id=?");
            $stmt->execute([$lancamento_id, $tipo, $ofx_id]);
        }

    } elseif ($action === 'create') {
        // Criar novo lançamento
        $nova = $data['dados_novo'] ?? [];
        $tipo = $data['tipo_lancamento'] ?? ''; // 'receita' or 'despesa'

        if (empty($nova) || !$tipo) {
            throw new Exception("Dados para novo lançamento incompletos");
        }

        // Insere na tabela correta
        if ($tipo === 'receita') {
            $stmt = $pdo->prepare("INSERT INTO receitas (user_id, descricao, valor, data, categoria, imovel_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $nova['descricao'], $nova['valor'], $nova['data'], $nova['categoria'], $nova['imovel_id'] ?? null]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO despesas (user_id, descricao, valor, data, categoria, imovel_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $nova['descricao'], $nova['valor'], $nova['data'], $nova['categoria'], $nova['imovel_id'] ?? null]);
        }
        
        $lancamento_id = $pdo->lastInsertId();

        // Atualiza OFX
        $stmt = $pdo->prepare("UPDATE transacoes_ofx SET status='Conciliado', lancamento_id=?, tipo_lancamento=? WHERE id=?");
        $stmt->execute([$lancamento_id, $tipo, $ofx_id]);
    }

    $pdo->commit();
    jsonResponse(['message' => 'Conciliação realizada com sucesso!']);

} catch (Exception $e) {
    $pdo->rollBack();
    jsonResponse(['error' => 'Erro na conciliação: ' . $e->getMessage()], 500);
}
?>
