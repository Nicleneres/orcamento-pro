<?php
require_once 'proteger_pagina.php';
require_once 'conexao.php';

// --- SEU CÓDIGO PHP EXISTENTE VEM AQUI, SEM MUDANÇAS ---
$usuario_cargo = $_SESSION['usuario_cargo'];
$usuario_id = $_SESSION['usuario_id'];
$where_clause = ($usuario_cargo == 'vendedor') ? "WHERE usuario_id = $usuario_id" : "";
$total_vendas = $conexao->query("SELECT SUM(valor_total) as total FROM orcamentos $where_clause")->fetch_assoc()['total'] ?? 0;
$total_orcamentos = $conexao->query("SELECT COUNT(id) as total FROM orcamentos $where_clause")->fetch_assoc()['total'] ?? 0;
$ticket_medio = ($total_orcamentos > 0) ? $total_vendas / $total_orcamentos : 0;
$sql_comissao = "SELECT SUM( CASE WHEN i.tipo_preco = 'final' THEN i.preco_total * 0.15 WHEN i.tipo_preco = 'meio' THEN i.preco_total * 0.10 WHEN i.tipo_preco = 'loja' THEN i.preco_total * 0.05 ELSE 0 END ) as comissao FROM orcamento_itens i LEFT JOIN orcamentos o ON i.orcamento_id = o.id";
if ($usuario_cargo == 'vendedor') {
    $sql_comissao .= " WHERE o.usuario_id = $usuario_id";
}
$projecao_comissao = $conexao->query($sql_comissao)->fetch_assoc()['comissao'] ?? 0;
$ultimos_orcamentos = [];
$sql_ultimos = "SELECT id, cliente_nome, valor_total FROM orcamentos $where_clause ORDER BY data_orcamento DESC LIMIT 5";
$resultado_ultimos = $conexao->query($sql_ultimos);
if ($resultado_ultimos->num_rows > 0) {
    while ($linha = $resultado_ultimos->fetch_assoc()) {
        $ultimos_orcamentos[] = $linha;
    }
}
$dados_grafico_vendedores = [];
if ($usuario_cargo == 'administrador') {
    $sql_grafico = "SELECT u.nome, SUM(o.valor_total) as total_vendido FROM orcamentos o JOIN usuarios u ON o.usuario_id = u.id GROUP BY u.nome ORDER BY total_vendido DESC";
    $resultado_grafico = $conexao->query($sql_grafico);
    if ($resultado_grafico->num_rows > 0) {
        while ($linha = $resultado_grafico->fetch_assoc()) {
            $dados_grafico_vendedores[] = $linha;
        }
    }
}
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Orçamento PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>
</head>

<body class="text-gray-200 antialiased">
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8">
            <div>
                <h1 class="text-4xl font-bold text-white">ORÇAMENTO <span class="text-indigo-400">PRO</span></h1>
                <p class="text-xl text-gray-300">Dashboard de Vendas</p>
            </div>
            <a href="index.php" class="mt-4 md:mt-0 inline-block bg-white/10 hover:bg-white/20 text-white font-semibold py-2 px-4 rounded-lg transition">
                &larr; Voltar ao Painel Principal
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-black/20 backdrop-blur-lg p-6 rounded-2xl border-t-4 border-indigo-500">
                <h2 class="text-sm font-semibold text-indigo-300 uppercase">Total de Vendas</h2>
                <p class="text-4xl font-bold text-white mt-2">R$ <?php echo number_format($total_vendas, 2, ',', '.'); ?></p>
            </div>
            <div class="bg-black/20 backdrop-blur-lg p-6 rounded-2xl border-t-4 border-green-500">
                <h2 class="text-sm font-semibold text-green-300 uppercase">Projeção de Comissão</h2>
                <p class="text-4xl font-bold text-white mt-2">R$ <?php echo number_format($projecao_comissao, 2, ',', '.'); ?></p>
            </div>
            <div class="bg-black/20 backdrop-blur-lg p-6 rounded-2xl border-t-4 border-yellow-500">
                <h2 class="text-sm font-semibold text-yellow-300 uppercase">Orçamentos Criados</h2>
                <p class="text-4xl font-bold text-white mt-2"><?php echo $total_orcamentos; ?></p>
            </div>
            <div class="bg-black/20 backdrop-blur-lg p-6 rounded-2xl border-t-4 border-sky-500">
                <h2 class="text-sm font-semibold text-sky-300 uppercase">Ticket Médio</h2>
                <p class="text-4xl font-bold text-white mt-2">R$ <?php echo number_format($ticket_medio, 2, ',', '.'); ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

            <?php if ($usuario_cargo == 'administrador' && !empty($dados_grafico_vendedores)): ?>
                <div class="bg-black/20 backdrop-blur-lg rounded-2xl p-6 lg:col-span-2">
                    <h2 class="text-xl font-bold text-white mb-4">Vendas por Vendedor</h2>
                    <canvas id="vendasChart"></canvas>
                </div>
            <?php endif; ?>

            <div class="bg-black/20 backdrop-blur-lg rounded-2xl p-6 <?php echo ($usuario_cargo != 'administrador' || empty($dados_grafico_vendedores)) ? 'lg:col-span-3' : 'lg:col-span-1'; ?>">
                <h2 class="text-xl font-bold text-white mb-4">Últimos Orçamentos</h2>
                <ul class="divide-y divide-gray-700">
                    <?php foreach ($ultimos_orcamentos as $orc): ?>
                        <li class="py-3 flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-white">#<?php echo $orc['id']; ?> - <?php echo htmlspecialchars($orc['cliente_nome']); ?></p>
                                <p class="text-sm text-gray-400">R$ <?php echo number_format($orc['valor_total'], 2, ',', '.'); ?></p>
                            </div>
                            <a href="ver_orcamento.php?id=<?php echo $orc['id']; ?>" class="text-sm text-indigo-400 hover:text-indigo-300 font-semibold">Ver</a>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($ultimos_orcamentos)): ?>
                        <p class="text-sm text-gray-400">Nenhum orçamento encontrado.</p>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Configuração do Gráfico para o tema escuro (só executa para admin)
        <?php if ($usuario_cargo == 'administrador' && !empty($dados_grafico_vendedores)): ?>
            const labels = <?php echo json_encode(array_column($dados_grafico_vendedores, 'nome')); ?>;
            const data = <?php echo json_encode(array_column($dados_grafico_vendedores, 'total_vendido')); ?>;

            const ctx = document.getElementById('vendasChart').getContext('2d');
            const vendasChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Vendido (R$)',
                        data: data,
                        backgroundColor: 'rgba(99, 102, 241, 0.5)', // Cor de preenchimento Indigo
                        borderColor: 'rgba(99, 102, 241, 1)', // Cor da borda Indigo
                        borderRadius: 5,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#d1d5db'
                            } // Cor do texto da legenda
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }, // Cor das linhas de grade
                            ticks: {
                                color: '#d1d5db'
                            } // Cor dos números do eixo Y
                        },
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: '#d1d5db'
                            } // Cor dos nomes do eixo X
                        }
                    }
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>