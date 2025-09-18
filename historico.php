<?php
require_once 'proteger_pagina.php';
require_once 'conexao.php';

$usuario_cargo = $_SESSION['usuario_cargo'];
$usuario_id = $_SESSION['usuario_id'];
$orcamentos = [];

$sql = "SELECT o.id, o.cliente_nome, o.valor_total, o.data_orcamento, u.nome as vendedor_nome 
        FROM orcamentos o
        LEFT JOIN usuarios u ON o.usuario_id = u.id";

if ($usuario_cargo == 'vendedor') {
    $sql .= " WHERE o.usuario_id = ? ORDER BY o.data_orcamento DESC";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
} else {
    $sql .= " ORDER BY o.data_orcamento DESC";
    $stmt = $conexao->prepare($sql);
}

$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado->num_rows > 0) {
    while($linha = $resultado->fetch_assoc()) {
        $orcamentos[] = $linha;
    }
}
$stmt->close();
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico - Orçamento PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(-45deg, #0f0c29, #302b63, #24243e, #1c3d52);
            background-size: 400% 400%;
            animation: gradientBG 25s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body class="text-gray-200 antialiased">

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8">
            <div>
                <h1 class="text-4xl font-bold text-white">ORÇAMENTO <span class="text-indigo-400">PRO</span></h1>
                <p class="text-xl text-gray-300">Histórico de Orçamentos</p>
            </div>
            <a href="index.php" class="mt-4 md:mt-0 inline-block bg-white/10 hover:bg-white/20 text-white font-semibold py-2 px-4 rounded-lg transition">
                &larr; Voltar ao Painel Principal
            </a>
        </div>

        <div class="bg-black/20 backdrop-blur-lg rounded-2xl shadow-xl p-6 sm:p-8">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
                <h2 class="text-2xl font-bold text-white">Todos os Registros</h2>
                <div class="flex gap-4">
                    <?php if ($_SESSION['usuario_cargo'] == 'administrador'): ?>
                        <button onclick="limparHistorico()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 active:scale-95">Limpar Histórico</button>
                    <?php endif; ?>
                    <a href="index.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 active:scale-95">Novo Orçamento</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Valor Total</th>
                            <?php if ($usuario_cargo == 'administrador'): ?>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Vendedor</th>
                            <?php endif; ?>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        <?php if (count($orcamentos) > 0): ?>
                            <?php foreach ($orcamentos as $orcamento): ?>
                                <tr class="hover:bg-white/5 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">#<?php echo $orcamento['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?php echo htmlspecialchars($orcamento['cliente_nome']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?php echo date('d/m/Y H:i', strtotime($orcamento['data_orcamento'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?php echo 'R$ ' . number_format($orcamento['valor_total'], 2, ',', '.'); ?></td>
                                    <?php if ($usuario_cargo == 'administrador'): ?>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?php echo htmlspecialchars($orcamento['vendedor_nome']); ?></td>
                                    <?php endif; ?>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="ver_orcamento.php?id=<?php echo $orcamento['id']; ?>" class="text-indigo-400 hover:text-indigo-300">Ver Detalhes</a>
                                        <?php if ($_SESSION['usuario_cargo'] == 'administrador'): ?>
                                            <button onclick="excluirOrcamento(<?php echo $orcamento['id']; ?>)" class="text-red-400 hover:text-red-300 ml-4">Excluir</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-400">Nenhum orçamento salvo ainda.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script>
// Suas funções JavaScript existentes permanecem aqui, sem alteração.
function limparHistorico() {
    if (confirm('TEM CERTEZA ABSOLUTA?\nEsta ação apagará TODOS os orçamentos do banco de dados permanentemente.')) {
        window.location.href = 'limpar_historico.php';
    }
}

function excluirOrcamento(id) {
    if (confirm('Tem certeza que deseja excluir permanentemente este orçamento? Esta ação não pode ser desfeita.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'excluir_orcamento.php';
        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = 'id';
        hiddenField.value = id;
        form.appendChild(hiddenField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
</body>
</html>