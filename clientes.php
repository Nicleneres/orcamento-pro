<?php
require_once 'proteger_pagina.php';
require_once 'conexao.php';

// Busca todos os clientes para exibir na tabela
$sql = "SELECT * FROM clientes ORDER BY nome ASC";
$resultado = $conexao->query($sql);
$clientes = [];
if ($resultado->num_rows > 0) {
    while($linha = $resultado->fetch_assoc()) {
        $clientes[] = $linha;
    }
}
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Orçamento PRO</title>
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

    <div class="max-w-5xl mx-auto p-4 sm:p-6 lg:p-8">
        
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8">
            <div>
                <h1 class="text-4xl font-bold text-white">ORÇAMENTO <span class="text-indigo-400">PRO</span></h1>
                <p class="text-xl text-gray-300">Gerenciamento de Clientes</p>
            </div>
            <a href="index.php" class="mt-4 md:mt-0 inline-block bg-white/10 hover:bg-white/20 text-white font-semibold py-2 px-4 rounded-lg transition">
                &larr; Voltar ao Painel Principal
            </a>
        </div>

        <div class="space-y-8">
            <?php if ($_SESSION['usuario_cargo'] == 'administrador'): ?>
            <div class="bg-black/20 backdrop-blur-lg rounded-2xl shadow-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-white mb-4">Adicionar Novo Cliente</h2>
                <form action="salvar_cliente.php" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="nome" placeholder="Nome do Cliente" class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" required>
                        <input type="text" name="cnpj" placeholder="CNPJ (opcional)" class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                        <input type="text" name="telefone" placeholder="Telefone" class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                        <input type="text" name="contato" placeholder="Nome do Contato" class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                        <div class="md:col-span-2">
                            <input type="text" name="endereco" placeholder="Endereço" class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                    </div>
                    <button type="submit" class="w-full mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 active:scale-95">Salvar Cliente</button>
                </form>
            </div>
            <?php endif; ?>

            <div class="bg-black/20 backdrop-blur-lg rounded-2xl shadow-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-white mb-4">Lista de Clientes</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">CNPJ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Telefone</th>
                                <?php if ($_SESSION['usuario_cargo'] == 'administrador'): ?>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Ações</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            <?php if (count($clientes) > 0): ?>
                                <?php foreach ($clientes as $cliente): ?>
                                    <tr class="hover:bg-white/5 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white"><?php echo htmlspecialchars($cliente['nome']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?php echo htmlspecialchars($cliente['cnpj']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?php echo htmlspecialchars($cliente['telefone']); ?></td>
                                        <?php if ($_SESSION['usuario_cargo'] == 'administrador'): ?>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" class="text-indigo-400 hover:text-indigo-300">Editar</a>
                                            <a href="excluir_cliente.php?id=<?php echo $cliente['id']; ?>" class="text-red-400 hover:text-red-300 ml-4" onclick="return confirm('Tem certeza que deseja excluir este cliente?');">Excluir</a>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-400">Nenhum cliente cadastrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>