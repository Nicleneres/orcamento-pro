<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'proteger_pagina.php';
require_once 'conexao.php';

use Piggly\Pix\StaticPayload;
use Piggly\Pix\Parser;

header('Content-Type: application/json');

// --- CONFIGURAÇÃO MAIS IMPORTANTE ---
// Verifique se o tipo da sua chave PIX principal está correto.
// Opções: 'DOCUMENT' (para CPF/CNPJ), 'EMAIL', 'PHONE', 'RANDOM' (Aleatória/EVP)
$tipoChavePixPrincipal = 'EMAIL'; // <--- VERIFIQUE E AJUSTE SE NECESSÁRIO

$dados = json_decode(file_get_contents('php://input'), true);
$valor = $dados['valor'] ?? 0;

$sql_empresa = "SELECT nome, chave_pix FROM empresa WHERE id = 1";
$resultado_empresa = $conexao->query($sql_empresa);
$empresa_info = $resultado_empresa->fetch_assoc() ?? [];
$conexao->close();

if (empty($empresa_info['chave_pix'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'Nenhuma chave PIX configurada no perfil da empresa.']);
    exit();
}

try {
    // Limpa os dados para o padrão PIX (sem acentos, etc.)
    $chavePix = $empresa_info['chave_pix'];
    // Para CNPJ/CPF, removemos qualquer formatação (pontos, barras)
    if (strtoupper($tipoChavePixPrincipal) === 'DOCUMENT') {
        $chavePix = preg_replace('/[^0-9]/', '', $chavePix);
    }
    $nomeBeneficiario = preg_replace('/[^a-zA-Z0-9\s]/', '', $empresa_info['nome']);
    $cidadeBeneficiario = 'SAO PAULO'; // Cidade do recebedor (máx 15 chars, sem acentos)

    $payload = (new StaticPayload())
        ->setMerchantName(substr($nomeBeneficiario, 0, 25))
        ->setMerchantCity(substr($cidadeBeneficiario, 0, 15))
        ->setAmount($valor)
        ->setTid('***'); 
    
    // Define o tipo de chave PIX dinamicamente
    $payload->setPixKey(constant('Piggly\Pix\Parser::KEY_TYPE_' . strtoupper($tipoChavePixPrincipal)), $chavePix);

    // Retorna o código VÁLIDO e os dados usados para criá-lo
    echo json_encode([
        'brCode' => $payload->getPayload(),
        'debug_info' => [
            'chave_usada' => $chavePix,
            'tipo_chave' => $tipoChavePixPrincipal,
            'nome_usado' => substr($nomeBeneficiario, 0, 25),
            'cidade_usada' => substr($cidadeBeneficiario, 0, 15),
            'valor_usado' => $valor
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha ao gerar o código PIX.', 'detalhes' => $e->getMessage()]);
}
?>