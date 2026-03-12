<?php
// api/dashboard_stats.php
require_once '../src/db.php';
require_once '../src/auth.php';
require_once '../src/utils.php';

checkAuth();

// 1. Total Comissões a Pagar (com vencimento este mês ou atrasadas, ou total geral pendente?)
// User asked: "Total de Comissões a Pagar (Mês)" and "Comissões Atrasadas"

// Total Pagar (Geral Pendente)
$stmt = $pdo->query("
    SELECT SUM(valor_a_pagar_captador + valor_a_pagar_vendedor) as total_geral_devido 
    FROM comissoes 
    WHERE status_comissao IN ('Pendente Pagamento', 'Paga Parcialmente')
");
$row = $stmt->fetch();
$total_pending = floatval($row['total_geral_devido']);

// Total Pago de fato (para subtrair do devido se for pagamento parcial? A lógica de 'Paga Parcialmente' é complexa sem tabela de movimentos detalhada, 
// mas assumindo que 'valor_a_pagar' na tabela comissoes é o valor total original.
// Se quisermos o valor RESTANTE, deveríamos subtrair o que já foi pago tabela pagamentos_comissoes.
// Vamos fazer uma query mais inteligente:
// Total Original das Pendentes - Total Pago das Pendentes
$sqlRem = "
    SELECT 
        (SUM(c.valor_a_pagar_captador + c.valor_a_pagar_vendedor) - IFNULL((SELECT SUM(pc.valor_pago) FROM pagamentos_comissoes pc WHERE pc.id_comissao = c.id), 0)) as restante
    FROM comissoes c
    WHERE c.status_comissao IN ('Pendente Pagamento', 'Paga Parcialmente')
";
$stmt = $pdo->query($sqlRem);
$remaining = floatval($stmt->fetch()['restante']);


// Comissões Atrasadas (Vencimento < Hoje e Status != Paga)
// Assumindo que temos data_vencimento_pagamento preenchido
$sqlLate = "
    SELECT COUNT(*) as qtd, SUM(valor_a_pagar_captador + valor_a_pagar_vendedor) as valor
    FROM comissoes
    WHERE data_vencimento_pagamento < CURDATE() 
    AND status_comissao IN ('Pendente Pagamento', 'Paga Parcialmente')
"; // Note: Na criação não definimos vencimento obrigatoriamente, pode ser NULL.
$stmt = $pdo->query($sqlLate);
$late = $stmt->fetch();

// Top 5 Corretores (por valor recebido nos últimos 30 dias ou total?)
// 'Top 5 Corretores (por comissão recebida)' logicamente é ranking de quem mais vendeu/recebeu.
$sqlTop = "
    SELECT co.nome, SUM(pc.valor_pago) as total_recebido
    FROM pagamentos_comissoes pc
    JOIN corretores co ON pc.id_corretor = co.id
    GROUP BY pc.id_corretor
    ORDER BY total_recebido DESC
    LIMIT 5
";
$stmt = $pdo->query($sqlTop);
$top5 = $stmt->fetchAll(PDO::FETCH_ASSOC);

jsonResponse([
    'comissoes_pendentes' => $remaining,
    'comissoes_atrasadas' => [
        'qtd' => $late['qtd'],
        'valor' => floatval($late['valor']) 
    ],
    'top_corretores' => $top5
]);
?>
