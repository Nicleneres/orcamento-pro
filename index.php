<?php
require_once 'proteger_pagina.php';
require_once 'conexao.php';

// ... [TODO O SEU CÓDIGO PHP EXISTENTE VAI AQUI, SEM MUDANÇAS] ...
// ... (Busca de usuário, notificações, lógica de boas-vindas, etc.) ...
// O código PHP no topo do arquivo permanece exatamente o mesmo. Apenas a parte HTML/CSS/JS será alterada.

// Busca dados do usuário logado
$usuario_id = $_SESSION['usuario_id'];
$sql_usuario = "SELECT nome, foto_perfil, primeiro_login FROM usuarios WHERE id = ?";
$stmt_usuario = $conexao->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $usuario_id);
$stmt_usuario->execute();
$usuario_info = $stmt_usuario->get_result()->fetch_assoc() ?? [];
$stmt_usuario->close();

// Busca as notificações NÃO LIDAS
$sql_notificacoes = "SELECT * FROM notificacoes WHERE usuario_id = ? AND lida = FALSE ORDER BY data_criacao DESC";
$stmt_notificacoes = $conexao->prepare($sql_notificacoes);
$stmt_notificacoes->bind_param("i", $usuario_id);
$stmt_notificacoes->execute();
$resultado_notificacoes = $stmt_notificacoes->get_result();
$notificacoes_nao_lidas = [];
if ($resultado_notificacoes->num_rows > 0) {
    while ($linha = $resultado_notificacoes->fetch_assoc()) {
        $notificacoes_nao_lidas[] = $linha;
    }
}
$stmt_notificacoes->close();

// Lógica de Boas-Vindas
$mostrar_boas_vindas = false;
if (isset($usuario_info['primeiro_login']) && $usuario_info['primeiro_login'] == 1) {
    $mostrar_boas_vindas = true;
    $sql_update = "UPDATE usuarios SET primeiro_login = 0 WHERE id = ?";
    $stmt_update = $conexao->prepare($sql_update);
    $stmt_update->bind_param("i", $usuario_id);
    $stmt_update->execute();
    $stmt_update->close();
}

// Busca dados da empresa
$sql_empresa = "SELECT * FROM empresa WHERE id = 1";
$resultado_empresa = $conexao->query($sql_empresa);
$empresa_info = $resultado_empresa->fetch_assoc() ?? [];

$conexao->close();

// Passa variáveis do PHP para o JavaScript
echo "<script>const userRole = '" . htmlspecialchars($_SESSION['usuario_cargo'], ENT_QUOTES, 'UTF-8') . "';</script>";
echo "<script>const userId = " . intval($_SESSION['usuario_id']) . ";</script>";
echo "<script>const companyInfo = " . json_encode($empresa_info) . ";</script>";
?>
<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Orçamento PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://unpkg.com/jspdf-autotable@3.8.1/dist/jspdf.plugin.autotable.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

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

        /* Estilo para TomSelect no tema escuro */
        .ts-control {
            background-color: rgba(17, 24, 39, 0.5) !important;
            border-color: #4b5563 !important;
            color: #fff !important;
            border-radius: 0.5rem !important;
        }

        .ts-control .item,
        .ts-control input {
            color: #fff !important;
        }

        .ts-dropdown {
            background-color: #1f2937 !important;
            border-color: #4b5563 !important;
        }

        .ts-dropdown .option {
            color: #d1d5db !important;
        }

        .ts-dropdown .option:hover,
        .ts-dropdown .active {
            background-color: #374151 !important;
            color: #fff !important;
        }
    </style>
</head>

<body class="text-gray-200 antialiased">
    <div id="welcomeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm <?php echo $mostrar_boas_vindas ? '' : 'hidden'; ?>">
        <div class="bg-gray-800/80 backdrop-blur-md border border-gray-700 rounded-2xl shadow-xl max-w-lg w-full p-8 m-4">
            <h2 class="text-2xl font-bold mb-4 text-white">Bem-vindo(a) ao Orçamento PRO!</h2>
            <p class="text-gray-300 mb-4">Um guia rápido para você começar:</p>
            <ul class="list-disc list-inside text-gray-300 space-y-2 mb-6">
                <?php if ($_SESSION['usuario_cargo'] == 'administrador'): ?>
                    <li><strong class="text-indigo-400">Gerenciar:</strong> Cadastre produtos e configure sua empresa nos painéis abaixo.</li>
                <?php endif; ?>
                <li><strong class="text-indigo-400">Criar Orçamentos:</strong> Busque um cliente ou preencha os dados e adicione produtos.</li>
                <li><strong class="text-indigo-400">Histórico e Dashboard:</strong> Use o menu para ver orçamentos salvos e métricas.</li>
            </ul>
            <div class="text-right">
                <button onclick="hideWelcomeModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105">Entendido!</button>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-8">

        <header class="bg-black/20 backdrop-blur-lg rounded-2xl shadow-xl p-4 mb-8 sticky top-4 z-40" x-data="{ open: false }">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <?php if (!empty($usuario_info['foto_perfil'])): ?>
                        <img src="<?php echo htmlspecialchars($usuario_info['foto_perfil']); ?>" alt="Foto de Perfil" class="w-10 h-10 rounded-full mr-4 object-cover border-2 border-indigo-500">
                    <?php endif; ?>
                    <div>
                        <span class="text-gray-400 text-sm">Bem-vindo,</span>
                        <p class="font-semibold text-white"><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <nav class="hidden md:flex items-center space-x-2 text-sm font-semibold">
                        <a href="dashboard.php" class="px-3 py-2 text-gray-300 rounded-md hover:bg-white/10 hover:text-white transition">Dashboard</a>
                        <a href="historico.php" class="px-3 py-2 text-gray-300 rounded-md hover:bg-white/10 hover:text-white transition">Histórico</a>
                        <a href="clientes.php" class="px-3 py-2 text-gray-300 rounded-md hover:bg-white/10 hover:text-white transition">Clientes</a>
                        <a href="perfil.php" class="px-3 py-2 text-gray-300 rounded-md hover:bg-white/10 hover:text-white transition">Meu Perfil</a>
                        <a href="logout.php" class="px-3 py-2 text-red-400 rounded-md hover:bg-red-500 hover:text-white transition">Sair</a>
                    </nav>

                    <div class="relative ml-4" x-data="{ notificationsOpen: false }">
                        <button @click="notificationsOpen = true; markNotificationsAsRead()" class="text-gray-300 hover:text-white transition relative">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <?php if (count($notificacoes_nao_lidas) > 0): ?>
                                <span id="notification-badge" class="absolute -top-1 -right-1 block h-3 w-3 rounded-full bg-red-500 border-2 border-gray-800"></span>
                            <?php endif; ?>
                        </button>

                        <div x-show="notificationsOpen"
                            @click.away="notificationsOpen = false"
                            x-transition
                            class="origin-top-right absolute right-0 mt-2 w-80 rounded-2xl shadow-lg bg-gray-800/80 backdrop-blur-md border border-gray-700 ring-1 ring-black ring-opacity-5 z-50"
                            style="display: none;">

                            <div class="py-1">
                                <p class="px-4 py-2 text-sm text-white font-bold border-b border-gray-700">Notificações</p>
                                <div class="max-h-64 overflow-y-auto">
                                    <?php if (count($notificacoes_nao_lidas) > 0): ?>
                                        <?php foreach ($notificacoes_nao_lidas as $notificacao): ?>
                                            <a href="<?php echo htmlspecialchars($notificacao['link']); ?>" class="block px-4 py-3 text-sm text-gray-300 hover:bg-white/10 hover:text-white transition">
                                                <?php echo htmlspecialchars($notificacao['mensagem']); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="px-4 py-3 text-sm text-gray-400">Nenhuma nova notificação.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="md:hidden ml-4">
                        <button @click="open = !open" class="text-gray-300 hover:text-white"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg></button>
                    </div>
                </div>
            </div>
            <div x-show="open" @click.away="open = false" class="md:hidden mt-4 border-t border-gray-700">
                <nav class="flex flex-col space-y-2 pt-4 font-semibold">
                    <a href="dashboard.php" class="px-3 py-2 text-gray-300 rounded-md hover:bg-white/10 hover:text-white transition">Dashboard</a>
                    <a href="historico.php" class="px-3 py-2 text-gray-300 rounded-md hover:bg-white/10 hover:text-white transition">Histórico</a>
                    <a href="clientes.php" class="px-3 py-2 text-gray-300 rounded-md hover:bg-white/10 hover:text-white transition">Clientes</a>
                    <a href="perfil.php" class="px-3 py-2 text-gray-300 rounded-md hover:bg-white/10 hover:text-white transition">Meu Perfil</a>
                    <a href="logout.php" class="px-3 py-2 text-red-400 rounded-md hover:bg-red-500 hover:text-white transition">Sair</a>
                </nav>
            </div>
        </header>

        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold text-white">
                ORÇAMENTO <span class="text-indigo-400">PRO</span>
            </h1>
            <p class="text-gray-300 mt-2">Crie e gerencie orçamentos de forma eficiente.</p>
        </div>

        <?php if ($_SESSION['usuario_cargo'] == 'administrador'): ?>
            <div class="space-y-8">
                <div class="bg-black/20 backdrop-blur-lg rounded-2xl shadow-xl p-6">
                    <h2 class="text-2xl font-bold mb-4 text-white">Dados da Sua Empresa</h2>
                    <form id="form-empresa">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <input class="w-full col-span-2 p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" type="text" id="logoUrlInput" name="logoUrlInput" placeholder="URL da Logo" value="<?php echo htmlspecialchars($empresa_info['logo_url'] ?? ''); ?>">
                            <input class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" type="text" id="companyNameInput" name="companyNameInput" placeholder="Nome da Empresa" value="<?php echo htmlspecialchars($empresa_info['nome'] ?? ''); ?>" required>
                            <input class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" type="text" id="companyAddressInput" name="companyAddressInput" placeholder="Endereço" value="<?php echo htmlspecialchars($empresa_info['endereco'] ?? ''); ?>">
                            <input class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" type="text" id="companyPhoneInput" name="companyPhoneInput" placeholder="Telefone" value="<?php echo htmlspecialchars($empresa_info['telefone'] ?? ''); ?>">
                            <input class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" type="text" id="companyEmailInput" name="companyEmailInput" placeholder="E-mail" value="<?php echo htmlspecialchars($empresa_info['email'] ?? ''); ?>">
                            <input class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" type="text" id="companyPixInput" name="companyPixInput" placeholder="Chave PIX" value="<?php echo htmlspecialchars($empresa_info['chave_pix'] ?? ''); ?>">
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 active:scale-95">Salvar Dados da Empresa</button>
                    </form>
                </div>
                <div class="bg-black/20 backdrop-blur-lg rounded-2xl shadow-xl p-6">
                    <h2 class="text-2xl font-bold mb-4 text-white">Cadastro de Produtos</h2>
                    <form id="form-add-produto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <input class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" type="text" name="nome_produto" placeholder="Nome do Produto" required>
                            <input class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" type="number" name="preco_final" placeholder="Preço Final (R$)" step="0.01" required>
                            <input class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" type="number" name="preco_meio" placeholder="Preço do Meio (R$)" step="0.01">
                            <input class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" type="number" name="preco_loja" placeholder="Preço Loja (R$)" step="0.01">
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 active:scale-95">Adicionar Produto</button>
                    </form>
                    <div id="productsList" class="overflow-x-auto mt-6"></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-black/20 backdrop-blur-lg rounded-2xl shadow-xl p-6 mt-8">
            <h2 class="text-2xl font-bold mb-6 text-white">Novo Orçamento</h2>
            <div class="mb-6">
                <label for="clientSelect" class="block text-sm font-medium text-gray-300 mb-2">Buscar Cliente Cadastrado</label>
                <select id="clientSelect" placeholder="Digite ou selecione um cliente..."></select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <input type="text" id="cnpjInput" placeholder="CNPJ do Cliente" class="p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                <button id="btn-consultar-cnpj" onclick="fetchCompanyData()" class="w-full bg-teal-500 hover:bg-teal-600 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300 flex items-center justify-center">
    <svg id="cnpj-spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <span id="cnpj-btn-text">Consultar CNPJ</span>
</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" type="text" id="clientNameInput" placeholder="Nome do Cliente">
                <input class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" type="text" id="clientAddressInput" placeholder="Endereço">
                <input class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" type="text" id="clientPhoneInput" placeholder="Telefone">
                <input class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" type="text" id="clientContactInput" placeholder="Contato">
            </div>
            <h3 class="text-xl font-bold mt-8 mb-4 text-white">Itens do Orçamento</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 items-start">
                <div class="md:col-span-1">
                    <label for="productSelect" class="block text-sm font-medium text-gray-300 mb-2">Produto</label>
                    <select id="productSelect" class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="" disabled selected>Selecione um Produto</option>
                    </select>
                    <p id="selectedProductFeedback" class="text-sm text-indigo-300 mt-2 h-5 font-semibold transition-opacity duration-300 opacity-0"></p>
                </div>
                <div>
                    <label for="quantityInput" class="block text-sm font-medium text-gray-300 mb-2">Quantidade</label>
                    <input type="number" id="quantityInput" placeholder="Qtde" min="1" value="1" class="p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-transparent mb-2 select-none">Ação</label>
                    <button onclick="addItemToBudget()" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300">Adicionar Item</button>
                </div>
            </div>
            <div id="budgetItemList" class="overflow-x-auto"></div>
            <div class="mt-8 flex justify-between items-center">
                <div class="font-bold text-lg">
                    <span class="text-gray-300">Total:</span>
                    <span id="budgetTotal" class="ml-2 text-white">R$ 0,00</span>
                </div>
                <div class="flex space-x-4">
                    <button onclick="showClearBudgetModal()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300">Limpar</button>
                    <button onclick="showPaymentModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300">Finalizar</button>
                </div>
            </div>
        </div>
    </div>
    <div id="clearBudgetModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-gray-800/80 backdrop-blur-md border border-gray-700 rounded-2xl shadow-xl max-w-md w-full p-8 m-4 text-center">
        <h2 class="text-xl font-bold text-white mb-4">Limpar Orçamento?</h2>
        <p class="text-gray-300 mb-6">Todos os itens adicionados serão removidos. Tem certeza?</p>
        <div class="flex justify-center space-x-4">
            <button onclick="hideClearBudgetModal()" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-all">Cancelar</button>
            <button onclick="clearBudget()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-all">Limpar Tudo</button>
        </div>
    </div>
</div>

<div id="paymentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-gray-800/80 backdrop-blur-md border border-gray-700 rounded-2xl shadow-xl max-w-md w-full p-8 m-4">
        <h2 class="text-2xl font-bold mb-4 text-white text-center">Finalizar Orçamento</h2>
        <p class="text-center text-gray-300">Valor Total: <span id="paymentTotalDisplay" class="font-bold text-white">R$ 0,00</span></p>

        <div class="text-left my-4 space-y-4">
            <div>
                <label for="paymentMethod" class="block text-sm font-medium text-gray-300 mb-2">Forma de Pagamento:</label>
                <select id="paymentMethod" class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                    <option value="pix">PIX</option>
                    <option value="cartao">Cartão</option>
                    <option value="cheque">Cheque</option>
                    <option value="boleto">Boleto</option>
                </select>
            </div>
            <div id="boletoDaysInput" class="hidden">
                <label for="boletoDays" class="block text-sm font-medium text-gray-300 mb-2">Prazo de Vencimento (ex: 30, 30/60):</label>
                <input type="text" id="boletoDays" placeholder="30/60/90 dias" class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
            </div>
            <div>
                <label for="discountInput" class="block text-sm font-medium text-gray-300 mb-2">Desconto:</label>
                <div class="flex space-x-2">
                    <input type="number" id="discountInput" placeholder="Valor" class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                    <select id="discountType" class="p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="percent">%</option>
                        <option value="fixed">R$</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex justify-center space-x-4 mt-8">
            <button onclick="hidePaymentModal()" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-all">Cancelar</button>
            <button onclick="generatePDF()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition-all">Gerar PDF</button>
        </div>
    </div>
</div>
    <script>
        // Variáveis globais e seletores de elementos
        const productsListContainer = document.getElementById('productsList');
        const budgetItemList = document.getElementById('budgetItemList');
        const budgetTotal = document.getElementById('budgetTotal');
        const productSelect = document.getElementById('productSelect');
        const quantityInput = document.getElementById('quantityInput');
        let products = [];
        let budgetItems = [];
        let clientes = [];

        // --- FUNÇÕES DE INICIALIZAÇÃO E CARREGAMENTO DE DADOS ---
        window.onload = () => {
            loadBudgetFromStorage();
            carregarProdutos();
            carregarClientes();
            renderBudget();
        };

        async function carregarProdutos() {
            try {
                const response = await fetch('listar_produtos.php');
                products = await response.json();
                // A renderização da tabela de produtos só é chamada se o usuário for admin
                if (userRole === 'administrador') {
                    renderizarTabelaProdutos(products);
                }
                preencherSelectProdutos(products);
            } catch (error) {
                console.error('Erro ao buscar produtos:', error);
                if (productsListContainer) productsListContainer.innerHTML = '<p class="text-center text-red-500">Falha ao carregar produtos.</p>';
            }
        }

        async function carregarClientes() {
            try {
                const response = await fetch('listar_clientes.php');
                clientes = await response.json();
                preencherSelectClientes(clientes);
            } catch (error) {
                console.error('Erro ao buscar clientes:', error);
            }
        }

        function renderizarTabelaProdutos(produtos) {
            if (!productsListContainer) return;

            productsListContainer.innerHTML = '';
            if (produtos.length === 0) {
                productsListContainer.innerHTML = '<p class="text-center text-gray-400 italic">Nenhum produto cadastrado.</p>';
                return;
            }

            const table = document.createElement('table');
            // MODIFICADO: Classes da tabela para o tema escuro
            table.className = 'min-w-full divide-y divide-gray-700 mt-4';
            table.innerHTML = `
    <thead class="bg-gray-900/50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Produto</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Preço Final</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Preço do Meio</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Preço Loja</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Ações</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-800"></tbody> 
    `;
            const tableBody = table.querySelector('tbody');

            produtos.forEach(produto => {
                const row = document.createElement('tr');
                // MODIFICADO: Efeito de hover suave nas linhas
                row.className = 'hover:bg-white/5 transition-colors duration-200';

                // ... (código de formatação de preço continua o mesmo) ...
                const pFinal = parseFloat(produto.preco_final).toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                });
                const pMeio = produto.preco_meio ? parseFloat(produto.preco_meio).toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }) : '---';
                const pLoja = produto.preco_loja ? parseFloat(produto.preco_loja).toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }) : '---';

                row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">${produto.nome}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">${pFinal}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">${pMeio}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">${pLoja}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <a href="editar_produto.php?id=${produto.id}" class="text-indigo-400 hover:text-indigo-300">Editar</a>
                <button onclick="excluirProduto(${produto.id})" class="text-red-400 hover:text-red-300 ml-4">Excluir</button>
            </td>`;
                tableBody.appendChild(row);
            });

            productsListContainer.appendChild(table);
        }

        function preencherSelectProdutos(produtos) {
            productSelect.innerHTML = '<option value="" disabled selected>Selecione um Produto</option>';
            produtos.forEach(product => {
                const optgroup = document.createElement('optgroup');
                optgroup.label = product.nome;
                const prices = {
                    final: 'Cliente Final',
                    meio: 'Do Meio',
                    loja: 'Loja'
                };
                const priceValues = {
                    final: product.preco_final,
                    meio: product.preco_meio,
                    loja: product.preco_loja
                };
                for (const key in prices) {
                    if (priceValues[key] !== null) {
                        const option = document.createElement('option');
                        option.value = `${product.id}-${key}`;
                        const price = parseFloat(priceValues[key]).toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        });
                        option.textContent = `${prices[key]} (${price})`;
                        optgroup.appendChild(option);
                    }
                }
                productSelect.appendChild(optgroup);
            });
        }

        function preencherSelectClientes(listaClientes) {
            const clientSelect = document.getElementById('clientSelect');
            clientSelect.innerHTML = ''; // Limpa para o Tom Select
            listaClientes.forEach(cliente => {
                const option = document.createElement('option');
                option.value = cliente.id;
                option.textContent = cliente.nome;
                clientSelect.appendChild(option);
            });
            new TomSelect(clientSelect, {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: 'Digite ou selecione um cliente...',
                onChange: function(clienteId) {
                    if (!clienteId) {
                        ['clientNameInput', 'cnpjInput', 'clientAddressInput', 'clientPhoneInput', 'clientContactInput'].forEach(id => document.getElementById(id).value = '');
                        return;
                    }
                    const clienteSelecionado = clientes.find(c => c.id == clienteId);
                    if (clienteSelecionado) {
                        document.getElementById('clientNameInput').value = clienteSelecionado.nome || '';
                        document.getElementById('cnpjInput').value = clienteSelecionado.cnpj || '';
                        document.getElementById('clientAddressInput').value = clienteSelecionado.endereco || '';
                        document.getElementById('clientPhoneInput').value = clienteSelecionado.telefone || '';
                        document.getElementById('clientContactInput').value = clienteSelecionado.contato || '';
                    }
                }
            });
        }

        function renderBudget() {
            budgetItemList.innerHTML = '';
            let total = 0;
            if (budgetItems.length === 0) {
                budgetItemList.innerHTML = '<p class="text-center text-gray-400 italic">O orçamento está vazio.</p>';
                budgetTotal.textContent = 'R$ 0,00';
                return;
            }

            const table = document.createElement('table');
            // MODIFICADO: Classes da tabela para o tema escuro
            table.className = 'min-w-full divide-y divide-gray-700';
            table.innerHTML = `
    <thead class="bg-gray-900/50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Produto</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Qtde</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Preço Unit.</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Subtotal</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Ações</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-800"></tbody>
    `;
            const tableBody = table.querySelector('tbody');

            budgetItems.forEach((item, index) => {
                if (!item || typeof item.price !== 'number' || isNaN(item.price)) {
                    console.warn("Item inválido no orçamento foi ignorado:", item);
                    return;
                }
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                const row = document.createElement('tr');
                // MODIFICADO: Efeito de hover suave nas linhas
                row.className = 'hover:bg-white/5 transition-colors duration-200';

                row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">${item.name}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">${item.quantity}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">${item.price.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">${itemTotal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button onclick="removeBudgetItem(${index})" class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs py-1 px-2 rounded-md transition-all">Remover</button>
            </td>`;
                tableBody.appendChild(row);
            });

            budgetItemList.appendChild(table);
            budgetTotal.textContent = total.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        }

        // --- FUNÇÕES DE AÇÃO (EVENTOS DE CLIQUE, SUBMISSÃO, ETC.) ---
        async function excluirProduto(produtoId) {
            if (!confirm('Tem certeza?')) return;
            try {
                const formData = new FormData();
                formData.append('id', produtoId);
                await fetch('excluir_produto.php', {
                    method: 'POST',
                    body: formData
                });
                carregarProdutos();
            } catch (error) {
                console.error('Erro ao excluir produto:', error);
            }
        }

        function saveBudgetToStorage() {
            localStorage.setItem('budgetItems', JSON.stringify(budgetItems));
        }

        function loadBudgetFromStorage() {
            const saved = localStorage.getItem('budgetItems');
            if (saved) {
                try {
                    budgetItems = JSON.parse(saved);
                } catch (e) {
                    budgetItems = [];
                    console.error("Erro ao ler orçamento do localStorage:", e);
                }
            }
        }

        function addItemToBudget() {
            if (!productSelect.value || isNaN(parseInt(quantityInput.value)) || parseInt(quantityInput.value) <= 0) return;
            const [productId, priceType] = productSelect.value.split('-');
            const product = products.find(p => p.id == productId);
            if (product) {
                const prices = {
                    final: 'Cliente Final',
                    meio: 'Do Meio',
                    loja: 'Loja'
                };
                const pMap = {
                    'final': 'preco_final',
                    'meio': 'preco_meio',
                    'loja': 'preco_loja'
                };
                const pKey = pMap[priceType];
                const pValue = product[pKey];
                if (pValue === null || typeof pValue === 'undefined') {
                    alert(`Produto "${product.nome}" não tem preço do tipo "${prices[priceType]}" cadastrado.`);
                    return;
                }
                const newItem = {
                    id: product.id,
                    name: product.nome,
                    price: parseFloat(pValue),
                    quantity: parseInt(quantityInput.value),
                    priceType: priceType
                };
                budgetItems.push(newItem);
                saveBudgetToStorage();
                renderBudget();
            }
        }

        function removeBudgetItem(index) {
            budgetItems.splice(index, 1);
            saveBudgetToStorage();
            renderBudget();
        }

        function clearBudget() {
            budgetItems = [];
            saveBudgetToStorage();
            renderBudget();
            hideClearBudgetModal();
        }

        function showPaymentModal() {
            let total = budgetItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            document.getElementById('paymentTotalDisplay').textContent = total.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function hidePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }

        function showClearBudgetModal() {
            document.getElementById('clearBudgetModal').classList.remove('hidden');
        }

        function hideClearBudgetModal() {
            document.getElementById('clearBudgetModal').classList.add('hidden');
        }

        function hideWelcomeModal() {
            document.getElementById('welcomeModal').classList.add('hidden');
        }

        async function generatePDF() {
            // Esconde o modal de pagamento se estiver aberto
            hidePaymentModal();

            if (budgetItems.length === 0) {
                alert("O orçamento está vazio!");
                return;
            }

            // =================================================================
            // ETAPA 1: COLETA DE DADOS DA PÁGINA E CÁLCULOS
            // =================================================================

            // Coleta dados dos inputs do cliente
            const clientName = document.getElementById('clientNameInput').value;
            const cnpj = document.getElementById('cnpjInput').value;
            const clientAddress = document.getElementById('clientAddressInput').value;
            const clientPhone = document.getElementById('clientPhoneInput').value;
            const clientContact = document.getElementById('clientContactInput').value;
            const paymentMethod = document.getElementById('paymentMethod').value;

            // Calcula os totais
            const total = budgetItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            let discountValue = parseFloat(document.getElementById('discountInput').value) || 0;
            let finalTotal = total;

            if (document.getElementById('discountType').value === 'percent') {
                finalTotal = total * (1 - discountValue / 100);
            } else if (document.getElementById('discountType').value === 'fixed') {
                finalTotal = total - discountValue;
            }

            // =================================================================
            // ETAPA 2: SALVAR ORÇAMENTO NO BANCO DE DADOS (OPCIONAL)
            // =================================================================
            try {
                const dadosOrcamento = {
                    usuario_id: userId,
                    cliente: {
                        nome: clientName,
                        cnpj: cnpj,
                        endereco: clientAddress,
                        telefone: clientPhone,
                        contato: clientContact
                    },
                    pagamento: {
                        totalFinal: finalTotal,
                        desconto: (total - finalTotal),
                        metodo: paymentMethod
                    },
                    itens: budgetItems
                };
                const response = await fetch('salvar_orcamento.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dadosOrcamento)
                });
                const resultado = await response.json();
                if (resultado.status !== 'sucesso') {
                    throw new Error(resultado.mensagem || 'Erro desconhecido ao salvar.');
                }
                console.log('Orçamento salvo com sucesso! ID:', resultado.orcamento_id);
            } catch (error) {
                console.error('Falha ao salvar o orçamento:', error);
                alert('Houve um erro ao salvar o orçamento no banco de dados. O PDF não será gerado.');
                return; // Para a execução se não conseguir salvar
            }

            // =================================================================
            // ETAPA 3: GERAÇÃO DO PDF COM LAYOUT CORRIGIDO
            // =================================================================
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();

            // --- VARIÁVEIS DE LAYOUT ---
            let y = 15;
            const pageCenter = 105;
            const pageMargin = 10;
            const pageWidth = 210 - (pageMargin * 2);

            // --- CABEÇALHO DA EMPRESA ---
            doc.setFontSize(20);
            doc.setFont(undefined, 'bold');
            doc.text(companyInfo.nome || "Nome da Sua Empresa", pageCenter, y, {
                align: "center"
            });
            y += 10;

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');

            const addressText = companyInfo.endereco || "Endereço não informado";
            const addressLines = doc.splitTextToSize(addressText, pageWidth);
            doc.text(addressLines, pageCenter, y, {
                align: "center"
            });
            y += doc.getTextDimensions(addressLines).h;

            const contactText = `Tel: ${companyInfo.telefone || "N/I"} | E-mail: ${companyInfo.email || "N/I"}`;
            doc.text(contactText, pageCenter, y + 4, {
                align: "center"
            });
            y += 4 + doc.getTextDimensions(contactText).h;

            if (companyInfo.chave_pix) {
                const pixText = `Chave PIX: ${companyInfo.chave_pix}`;
                doc.text(pixText, pageCenter, y + 4, {
                    align: "center"
                });
                y += 4 + doc.getTextDimensions(pixText).h;
            }
            y += 8;

            // --- DADOS DO CLIENTE ---
            doc.line(pageMargin, y, 210 - pageMargin, y);
            y += 10;
            doc.setFontSize(14);
            doc.text("ORÇAMENTO", 10, y);
            doc.setFontSize(10);
            doc.text(`Data: ${new Date().toLocaleDateString('pt-BR')}`, 200, y, {
                align: "right"
            });
            y += 10;
            doc.line(10, y, 200, y);
            y += 7;

            const col1_x = 10;
            const col2_x = 105;
            const maxWidth = 90;

            const clientNameLines = doc.splitTextToSize(`Cliente: ${clientName || 'N/I'}`, maxWidth);
            doc.text(clientNameLines, col1_x, y);
            const cnpjLines = doc.splitTextToSize(`CNPJ: ${cnpj || 'N/I'}`, maxWidth);
            doc.text(cnpjLines, col2_x, y);
            y += Math.max(doc.getTextDimensions(clientNameLines).h, doc.getTextDimensions(cnpjLines).h) + 2;

            const clientAddrLines = doc.splitTextToSize(`Endereço: ${clientAddress || 'N/I'}`, maxWidth);
            doc.text(clientAddrLines, col1_x, y);
            const clientPhoneLines = doc.splitTextToSize(`Telefone: ${clientPhone || 'N/I'}`, maxWidth);
            doc.text(clientPhoneLines, col2_x, y);
            y += Math.max(doc.getTextDimensions(clientAddrLines).h, doc.getTextDimensions(clientPhoneLines).h) + 2;

            const clientContactLines = doc.splitTextToSize(`Contato: ${clientContact || 'N/I'}`, maxWidth);
            doc.text(clientContactLines, col1_x, y);
            y += doc.getTextDimensions(clientContactLines).h + 2;

            // --- TABELA DE ITENS ---
            const tableHeaders = ["Produto", "Qtde", "Preço Unitário", "Total"];
            const tableData = budgetItems.map(item => [
                item.name,
                item.quantity.toString(),
                item.price.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }),
                (item.price * item.quantity).toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                })
            ]);
            doc.autoTable({
                startY: y + 5,
                head: [tableHeaders],
                body: tableData
            });

            const finalY = doc.autoTable.previous.finalY;

            // --- TOTAIS E PAGAMENTO ---
            doc.setFontSize(12);
            doc.text(`Subtotal: ${total.toLocaleString('pt-BR', {style:'currency', currency:'BRL'})}`, 200, finalY + 10, {
                align: "right"
            });
            if (discountValue > 0) {
                const discountDisplay = document.getElementById('discountType').value === 'percent' ? `${discountValue}%` : discountValue.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                });
                doc.text(`Desconto (${discountDisplay}): - ${(total - finalTotal).toLocaleString('pt-BR', {style:'currency', currency:'BRL'})}`, 200, finalY + 17, {
                    align: "right"
                });
            }
            doc.setFontSize(14);
            doc.text(`Total Final: ${finalTotal.toLocaleString('pt-BR', {style:'currency', currency:'BRL'})}`, 200, finalY + 24, {
                align: "right"
            });

            doc.setFontSize(10);
            doc.text(`Forma de Pagamento: ${paymentMethod.toUpperCase()}`, 10, finalY + 35);
            if (paymentMethod === 'pix' && companyInfo.chave_pix) {
                const nomeFormatado = encodeURIComponent((companyInfo.nome || 'Beneficiario').substring(0, 25));
                const valorFormatado = finalTotal.toFixed(2);
                const pixUrl = `https://gerarqrcodepix.com.br/api/v1?nome=${nomeFormatado}&cidade=SAO%20PAULO&chave=${companyInfo.chave_pix}&valor=${valorFormatado}&saida=qr`;
                doc.setTextColor(40, 58, 112);
                doc.textWithLink('Clique aqui para Pagar com PIX (abrirá QR Code)', 10, finalY + 42, {
                    url: pixUrl
                });
            }

            // --- SALVAR O ARQUIVO ---
            doc.save('orcamento.pdf');
        }

        function showToast(message, type = 'sucesso') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const bgColor = type === 'sucesso' ? 'bg-green-500' : 'bg-red-500';
            toast.className = `p-4 text-white rounded-lg shadow-lg ${bgColor} toast-in`;
            toast.textContent = message;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('toast-in');
                toast.classList.add('toast-out');
                toast.addEventListener('animationend', () => {
                    toast.remove();
                });
            }, 4000);
        }

        function markNotificationsAsRead() {
            const badge = document.getElementById('notification-badge');
            if (badge) {
                fetch('marcar_notificacoes_lidas.php', {
                        method: 'POST'
                    })
                    .then(response => {
                        if (response.ok) {
                            badge.remove();
                        }
                    });
            }
        }

        async function fetchCompanyData() {
            const cnpjInput = document.getElementById('cnpjInput');
            const cnpj = cnpjInput.value.replace(/\D/g, '');

            // Seleciona os elementos que vamos manipular pelos seus IDs
            const button = document.getElementById('btn-consultar-cnpj');
            const spinner = document.getElementById('cnpj-spinner');
            const btnText = document.getElementById('cnpj-btn-text');

            if (cnpj.length !== 14) {
                alert('Por favor, digite um CNPJ válido com 14 números.');
                return;
            }

            // Ativa o modo de carregamento
            button.disabled = true;
            spinner.classList.remove('hidden');
            btnText.textContent = 'Consultando...';

            try {
                const response = await fetch(`https://brasilapi.com.br/api/cnpj/v1/${cnpj}`);

                if (!response.ok) {
                    // Se o CNPJ não for encontrado, a API retorna um erro 404
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'CNPJ não encontrado ou inválido.');
                }

                const data = await response.json();

                // Preenche os campos do formulário com os dados recebidos
                document.getElementById('clientNameInput').value = data.razao_social || '';

                const address = `${data.logradouro || ''}, ${data.numero || ''} - ${data.bairro || ''}, ${data.municipio || ''}/${data.uf || ''}`;
                document.getElementById('clientAddressInput').value = address.trim();

                document.getElementById('clientPhoneInput').value = data.ddd_telefone_1 || '';
                document.getElementById('clientContactInput').value = (data.qsa && data.qsa.length > 0) ? data.qsa[0].nome_socio : '';

            } catch (error) {
                alert(error.message);
                console.error('Erro ao consultar CNPJ:', error);
            } finally {
                // Restaura o botão ao estado original, não importa se deu certo ou errado
                button.disabled = false;
                spinner.classList.add('hidden');
                btnText.textContent = 'Consultar CNPJ';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const formAddProduto = document.getElementById('form-add-produto');
            if (formAddProduto) {
                formAddProduto.addEventListener('submit', function(event) {
                    event.preventDefault();
                    const form = this;
                    const formData = new FormData(form);
                    const button = form.querySelector('button[type="submit"]');
                    const originalButtonText = button.textContent;
                    button.textContent = 'Salvando...';
                    button.disabled = true;

                    fetch('salvar_produto.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Falha na resposta do servidor.');
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'sucesso') {
                                showToast(data.mensagem, 'sucesso');
                                form.reset();
                                carregarProdutos();
                            } else {
                                throw new Error(data.mensagem || 'Erro desconhecido.');
                            }
                        })
                        .catch(error => {
                            showToast('Erro ao salvar produto: ' + error.message, 'erro');
                        })
                        .finally(() => {
                            button.textContent = originalButtonText;
                            button.disabled = false;
                        });
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethodSelect = document.getElementById('paymentMethod');
            const boletoDaysInput = document.getElementById('boletoDaysInput');

            paymentMethodSelect.addEventListener('change', function() {
                if (this.value === 'boleto') {
                    boletoDaysInput.classList.remove('hidden');
                } else {
                    boletoDaysInput.classList.add('hidden');
                }
            });
        });
        // Adicione este bloco no final do seu script

document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('productSelect');
    const feedbackEl = document.getElementById('selectedProductFeedback');

    if (productSelect) {
        productSelect.addEventListener('change', function() {
            // Limpa o feedback se nenhuma opção for selecionada
            if (!this.value) {
                feedbackEl.textContent = '';
                feedbackEl.classList.add('opacity-0');
                return;
            }

            // Encontra a opção selecionada e o grupo a que ela pertence
            const selectedOption = this.options[this.selectedIndex];
            const optgroup = selectedOption.parentElement;

            // Pega o nome do produto do 'label' do optgroup
            if (optgroup && optgroup.tagName === 'OPTGROUP') {
                const productName = optgroup.label;
                feedbackEl.textContent = productName;
                feedbackEl.classList.remove('opacity-0');
            }
        });
    }

    // ... (Seu outro código DOMContentLoaded pode continuar aqui) ...
});

    </script>
</body>

</html>