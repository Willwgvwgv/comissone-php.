<?php
// src/OfxParser.php

class OfxParser {
    
    public function parse($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("Arquivo não encontrado.");
        }

        $content = file_get_contents($filePath);
        $transactions = [];

        // Extrai o bloco de transações usando regex
        // Essa regex busca blocos <STMTTRN>...</STMTTRN>
        preg_match_all('/<STMTTRN>(.*?)<\/STMTTRN>/s', $content, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $block) {
                $transaction = [];

                // Tipo (TRNTYPE)
                if (preg_match('/<TRNTYPE>(.*?)(\r|\n|<)/', $block, $type)) {
                    $trntype = trim($type[1]);
                    // Mapeia para DEBIT ou CREDIT
                    $transaction['tipo'] = ($trntype == 'DEBIT' || $trntype == 'PAYMENT' || $trntype == 'money' || strpos($block, '-') !== false) ? 'DEBIT' : 'CREDIT'; 
                    // OFX pode ser confuso, geralmente TRNAMT negativo é debito.
                    // Vamos confiar mais no TRNAMT
                }

                // Valor (TRNAMT)
                if (preg_match('/<TRNAMT>(.*?)(\r|\n|<)/', $block, $amt)) {
                    $amount = floatval(trim($amt[1]));
                    $transaction['valor'] = abs($amount);
                    $transaction['tipo'] = ($amount < 0) ? 'DEBIT' : 'CREDIT';
                }

                // Data (DTPOSTED)
                if (preg_match('/<DTPOSTED>(.*?)(\r|\n|<)/', $block, $date)) {
                    // Formato OFX geralmente é YYYYMMDDHHMMSS...
                    $rawDate = trim($date[1]);
                    $transaction['data'] = substr($rawDate, 0, 4) . '-' . substr($rawDate, 4, 2) . '-' . substr($rawDate, 6, 2);
                }

                // Descrição (MEMO)
                if (preg_match('/<MEMO>(.*?)(\r|\n|<)/', $block, $memo)) {
                    $transaction['descricao'] = trim($memo[1]);
                } else {
                    // Tenta NAME se MEMO não existir
                    if (preg_match('/<NAME>(.*?)(\r|\n|<)/', $block, $name)) {
                         $transaction['descricao'] = trim($name[1]);
                    } else {
                        $transaction['descricao'] = 'Sem descrição';
                    }
                }

                $transactions[] = $transaction;
            }
        }

        return $transactions;
    }
}
?>
