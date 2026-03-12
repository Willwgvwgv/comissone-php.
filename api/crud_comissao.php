<?php
// api/crud_comissao.php
require_once '../src/db.php';
require_once '../src/utils.php';
require_once '../src/auth.php';

checkAuth();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list'; // 'list_receber', 'list_pagar', 'pagar'

if ($method === 'GET') {
    if ($action === 'list_pagar') {
        // Comissões que a imobiliária tem que pagar aos corretores
        // Status: 'Pendente Pagamento' ou 'Paga Parcialmente'
        // Join with receitas to get property/client info?
        $sql = "
            SELECT c.*, 
                   cat.nome as captador_nome, 
                   ven.nome as vendedor_nome,
                   r.descricao as receita_descricao,
                   i.endereco_completo as imovel
            FROM comissoes c
            LEFT JOIN corretores cat ON c.id_corretor_captador = cat.id
            LEFT JOIN corretores ven ON c.id_corretor_vendedor = ven.id
            JOIN receitas r ON c.id_transacao_original = r.id
            LEFT JOIN imoveis i ON r.imovel_id = i.id
            WHERE c.status_comissao IN ('Pendente Pagamento', 'Paga Parcialmente')
        ";
        $stmt = $pdo->query($sql);
        jsonResponse($stmt->fetchAll());
    } 
    elseif ($action === 'list_receber') {
        // A Receber: Transações (receitas) onde o dinheiro ainda não entrou, 
        // logo a comissão está "Aguardando Recebimento".
        // Isso é basicamente listar 'comissoes' com status 'Aguardando Recebimento'
        $sql = "
            SELECT c.*, 
                   cat.nome as captador_nome, 
                   ven.nome as vendedor_nome,
                   r.descricao as receita_descricao,
                   r.data as data_venda
            FROM comissoes c
            LEFT JOIN corretores cat ON c.id_corretor_captador = cat.id
            LEFT JOIN corretores ven ON c.id_corretor_vendedor = ven.id
            JOIN receitas r ON c.id_transacao_original = r.id
            WHERE c.status_comissao = 'Aguardando Recebimento'
        ";
        $stmt = $pdo->query($sql);
        jsonResponse($stmt->fetchAll());
    }
    elseif ($action === 'list_payments_reconcile') {
        // Listar pagamentos de comissão que ainda não foram conciliados
        $sql = "
            SELECT pc.*, 
                   c.nome as corretor_nome,
                   com.id as comissao_id
            FROM pagamentos_comissoes pc
            JOIN corretores c ON pc.id_corretor = c.id
            JOIN comissoes com ON pc.id_comissao = com.id
            WHERE pc.status_conciliacao = 'Pendente'
            ORDER BY pc.data_pagamento DESC
        ";
        $stmt = $pdo->query($sql);
        jsonResponse($stmt->fetchAll());
    }

} elseif ($method === 'POST') {
    // Realizar Pagamento
    $data = json_decode(file_get_contents("php://input"), true);
    
    if ($action === 'pay') {
        $id_comissao = $data['id_comissao'];
        $valor_pago = $data['valor_pago'];
        $id_corretor = $data['id_corretor']; // Quem está recebendo (captador ou vendedor)
        $data_pagamento = $data['data_pagamento'];
        $metodo = $data['metodo_pagamento'];
        $ref = $data['referencia_bancaria'];

        try {
            $pdo->beginTransaction();

            // 1. Registra Pagamento
            $stmt = $pdo->prepare("INSERT INTO pagamentos_comissoes (id_comissao, id_corretor, valor_pago, data_pagamento, metodo_pagamento, referencia_bancaria) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id_comissao, $id_corretor, $valor_pago, $data_pagamento, $metodo, $ref]);

            // 2. Atualiza Status da Comissão
            // Lógica simples: se (valor_pago_acumulado >= total_devido_aquele_corretor) -> Paga?
            // Mas a comissão tem 2 corretores. O status é geral da comissão ou individual?
            // A tabela 'comissoes' tem um status único. Isso é uma limitação se pagarmos um corretor e não o outro.
            // Para simplificar: Se ambos receberem tudo -> Paga. Se um receber -> Paga Parcialmente.
            
            // Vamos somar tudo que já foi pago dessa comissão
            $stmtSum = $pdo->prepare("SELECT SUM(valor_pago) as total_pago FROM pagamentos_comissoes WHERE id_comissao = ?");
            $stmtSum->execute([$id_comissao]);
            $sum = $stmtSum->fetch()['total_pago'];

            // Busca valor total da comissão a ser paga (captador + vendedor)
            $stmtCom = $pdo->prepare("SELECT valor_a_pagar_captador + valor_a_pagar_vendedor as total_devido FROM comissoes WHERE id = ?");
            $stmtCom->execute([$id_comissao]);
            $total_devido = $stmtCom->fetch()['total_devido'];

            $novo_status = 'Pendente Pagamento';
            if ($sum > 0 && $sum < $total_devido) {
                $novo_status = 'Paga Parcialmente';
            } elseif ($sum >= $total_devido) {
                $novo_status = 'Paga';
            }

            $stmtUp = $pdo->prepare("UPDATE comissoes SET status_comissao = ? WHERE id = ?");
            $stmtUp->execute([$novo_status, $id_comissao]);

            $pdo->commit();
            jsonResponse(['message' => 'Pagamento registrado com sucesso!']);

        } catch (Exception $e) {
            $pdo->rollBack();
            jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
?>
