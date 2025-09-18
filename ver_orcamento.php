<?php
require_once 'proteger_pagina.php';
// --- PARTE 1: BUSCAR TODOS OS DADOS NO BANCO ---

// Verificamos se um ID foi passado pela URL
if (!isset($_GET['id'])) {
    die("Erro: ID do orçamento não fornecido.");
}
$orcamento_id = $_GET['id'];

// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

// Buscar dados da empresa (para o cabeçalho)
$sql_empresa = "SELECT * FROM empresa WHERE id = 1";
$resultado_empresa = $conexao->query($sql_empresa);
$empresa_info = $resultado_empresa->fetch_assoc();

// Buscar os dados principais do orçamento
$sql_orcamento = "SELECT * FROM orcamentos WHERE id = ?";
$stmt_orcamento = $conexao->prepare($sql_orcamento);
$stmt_orcamento->bind_param("i", $orcamento_id);
$stmt_orcamento->execute();
$resultado_orcamento = $stmt_orcamento->get_result();

if ($resultado_orcamento->num_rows === 0) {
    die("Erro: Orçamento não encontrado.");
}
$orcamento = $resultado_orcamento->fetch_assoc();

// Buscar os itens pertencentes a este orçamento
$sql_itens = "SELECT * FROM orcamento_itens WHERE orcamento_id = ?";
$stmt_itens = $conexao->prepare($sql_itens);
$stmt_itens->bind_param("i", $orcamento_id);
$stmt_itens->execute();
$resultado_itens = $stmt_itens->get_result();
$itens_orcamento = [];
if ($resultado_itens->num_rows > 0) {
    while($linha = $resultado_itens->fetch_assoc()) {
        $itens_orcamento[] = $linha;
    }
}

$stmt_orcamento->close();
$stmt_itens->close();
$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Orçamento #<?php echo $orcamento['id']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); font-size: 16px; line-height: 24px; color: #555; background-color: #fff; }
        .btn { padding: 8px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-secondary { background-color: #6c757d; color: #ffffff; }
    </style>
</head>
<body class="p-6">
    <div class="text-center mb-4">
        <a href="historico.php" class="btn btn-secondary">← Voltar para o Histórico</a>
    </div>

    <div class="invoice-box">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($empresa_info['nome']); ?></h1>
            <p class="text-sm"><?php echo htmlspecialchars($empresa_info['endereco']); ?></p>
            <p class="text-sm">Tel: <?php echo htmlspecialchars($empresa_info['telefone']); ?> | E-mail: <?php echo htmlspecialchars($empresa_info['email']); ?></p>
        </div>

        <div class="flex justify-between mb-6 border-t border-b py-4">
            <div>
                <h2 class="font-bold text-gray-800">CLIENTE</h2>
                <p><?php echo htmlspecialchars($orcamento['cliente_nome']); ?></p>
                <p><?php echo htmlspecialchars($orcamento['cliente_endereco']); ?></p>
                <p><?php echo htmlspecialchars($orcamento['cliente_telefone']); ?></p>
            </div>
            <div class="text-right">
                <h2 class="font-bold text-gray-800">ORÇAMENTO #<?php echo $orcamento['id']; ?></h2>
                <p>Data: <?php echo date('d/m/Y', strtotime($orcamento['data_orcamento'])); ?></p>
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200 mb-6">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qtde</th>
                    <th class="px-2 py-3 text-right text-xs font-medium text-gray-500 uppercase">Preço Unit.</th>
                    <th class="px-2 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php 
                $subtotal = 0;
                foreach ($itens_orcamento as $item): 
                    $subtotal += $item['preco_total'];
                ?>
                    <tr>
                        <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($item['produto_nome']); ?></td>
                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center text-gray-500"><?php echo $item['quantidade']; ?></td>
                        <td class="px-2 py-4 whitespace-nowrap text-sm text-right text-gray-500"><?php echo 'R$ ' . number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                        <td class="px-2 py-4 whitespace-nowrap text-sm text-right text-gray-500"><?php echo 'R$ ' . number_format($item['preco_total'], 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="flex justify-end">
            <div class="w-1/2">
                <table class="min-w-full">
                    <tbody>
                        <tr class="border-t">
                            <td class="py-2 font-medium text-gray-600">Subtotal</td>
                            <td class="py-2 text-right"><?php echo 'R$ ' . number_format($subtotal, 2, ',', '.'); ?></td>
                        </tr>
                        <?php if ($orcamento['desconto'] > 0): ?>
                        <tr>
                            <td class="py-2 font-medium text-gray-600">Desconto</td>
                            <td class="py-2 text-right">- <?php echo 'R$ ' . number_format($orcamento['desconto'], 2, ',', '.'); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr class="border-t-2 border-gray-900">
                            <td class="py-2 font-bold text-lg text-gray-900">VALOR TOTAL</td>
                            <td class="py-2 text-right font-bold text-lg text-gray-900"><?php echo 'R$ ' . number_format($orcamento['valor_total'], 2, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>