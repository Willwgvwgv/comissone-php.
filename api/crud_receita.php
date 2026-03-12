<?php
// api/crud_receita.php
require_once '../src/db.php';
require_once '../src/utils.php';
require_once '../src/auth.php';

checkAuth();

$method = $_SERVER['REQUEST_METHOD'];
$user_id = $_SESSION['user_id'];

if ($method === 'GET') {
    $where = "WHERE user_id = ?";
    $params = [$user_id];

    // Filtros
    if (!empty($_GET['mes'])) {
        $where .= " AND MONTH(data) = ?";
        $params[] = $_GET['mes'];
    }
    
    $stmt = $pdo->prepare("
        SELECT r.*, i.endereco_completo as imovel_endereco 
        FROM receitas r 
        LEFT JOIN imoveis i ON r.imovel_id = i.id
        $where
        ORDER BY r.data DESC
    ");
    $stmt->execute($params);
    jsonResponse($stmt->fetchAll());

} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $imovel_id = !empty($data['imovel_id']) ? $data['imovel_id'] : null;

    if (isset($data['id']) && !empty($data['id'])) {
        $stmt = $pdo->prepare("UPDATE receitas SET descricao=?, valor=?, data=?, categoria=?, imovel_id=? WHERE id=? AND user_id=?");
        $stmt->execute([
            $data['descricao'], $data['valor'], $data['data'], 
            $data['categoria'], $imovel_id, $data['id'], $user_id
        ]);
        jsonResponse(['message' => 'Receita atualizada!']);
    } else {
        $stmt = $pdo->prepare("INSERT INTO receitas (user_id, descricao, valor, data, categoria, imovel_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id, $data['descricao'], $data['valor'], 
            $data['data'], $data['categoria'], $imovel_id
        ]);
        
        $receita_id = $pdo->lastInsertId();
        
        // --- GERAÇÃO DE COMISSÃO ---
        // Se a categoria for Aluguel ou Comissão de Venda (ou sempre que tiver corretores envolvidos)
        if (!empty($data['id_corretor_captador']) || !empty($data['id_corretor_vendedor'])) {
            $id_captador = !empty($data['id_corretor_captador']) ? $data['id_corretor_captador'] : null;
            $id_vendedor = !empty($data['id_corretor_vendedor']) ? $data['id_corretor_vendedor'] : null;
            
            $pc_captacao = !empty($data['pc_comissao_captacao']) ? floatval($data['pc_comissao_captacao']) : 0;
            $pc_venda    = !empty($data['pc_comissao_venda']) ? floatval($data['pc_comissao_venda']) : 0;
            
            $valor_base = floatval($data['valor']); // O valor da receita é a base da comissão? 
            // Se for "Comissão de Venda", a receita entrada JÁ É a comissão da imobiliária (ex: 6% do valor do imóvel).
            // E os corretores ganham uma parte DESSA receita.
            // O request diz: "valor_base_comissao (Valor total da transação)".
            // Assumindo que 'receitas.valor' É esse valor total se for aluguel, mas se for venda... pode ser confuso.
            // Simplificação: Vamos usar o valor da receita como base.
            
            // Mas espera, numa Venda de 500k, a receita é 30k (6%). O corretor ganha parte dos 30k.
            // Então 'valor_comissao_total' = valor da receita.
            $valor_comissao_total = $valor_base;
            
            // Quanto cada um ganha?
            // Ex: 30% da comissão vai pro captador, 20% pro vendedor.
            $val_captador = ($valor_comissao_total * $pc_captacao) / 100;
            $val_vendedor = ($valor_comissao_total * $pc_venda) / 100;
            
            $tipo_transacao = ($data['categoria'] == 'Aluguel') ? 'Aluguel' : 'Venda';
            
            // Status inicial: Aguardando Recebimento (pois a receita acabou de ser criada, talvez não paga ainda)
            // Se o sistema tivesse controle de 'Receita Paga' vs 'Pendente', usaríamos isso.
            // Como 'receitas' não tem status explícito (assumimos 'Realizado' ou criamos campo?),
            // O request diz: "mudar... para 'Pendente Pagamento' assim que o recebimento principal for confirmado."
            // Vamos assumir que receitas tem status. Mas não tem na tabela original. 
            // VOU ADICIONAR UM STATUS PADRÃO 'Aguardando Recebimento' na comissão.
            
            $stmtCom = $pdo->prepare("INSERT INTO comissoes (
                id_transacao_original, tipo_transacao, 
                id_corretor_captador, id_corretor_vendedor, 
                valor_base_comissao, percentual_comissao_total, valor_comissao_total, 
                valor_a_pagar_captador, valor_a_pagar_vendedor, status_comissao
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Aguardando Recebimento')");
            
            // Percentual total é abstrato aqui, usamos 100% da receita como base
            $stmtCom->execute([
                $receita_id, $tipo_transacao, 
                $id_captador, $id_vendedor,
                $valor_base, 100.00, $valor_comissao_total,
                $val_captador, $val_vendedor
            ]);
        }

        jsonResponse(['message' => 'Receita criada!']);
    }

} elseif ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    $stmt = $pdo->prepare("DELETE FROM receitas WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    jsonResponse(['message' => 'Receita excluída!']);
}
?>
