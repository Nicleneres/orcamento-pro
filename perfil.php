<?php
require_once 'proteger_pagina.php';

// --- BLOCO PARA PROCESSAR AS MENSAGENS DE FEEDBACK (COM ESTILO ATUALIZADO) ---
$mensagem = '';
$cor_mensagem = '';

// MENSAGENS DE SUCESSO
if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'senha_alterada') {
    $cor_mensagem = 'bg-green-500/80 text-white font-semibold';
    $mensagem = 'Senha alterada com sucesso!';
}
if (isset($_GET['status']) && $_GET['status'] == 'sucesso') {
    $cor_mensagem = 'bg-green-500/80 text-white font-semibold';
    $mensagem = 'Perfil atualizado com sucesso!';
}

// MENSAGENS DE ERRO
if (isset($_GET['erro'])) {
    $cor_mensagem = 'bg-red-500/80 text-white font-semibold';
    if ($_GET['erro'] == 'senhas_nao_conferem') {
        $mensagem = 'Erro: A nova senha e a confirmação não são iguais.';
    } elseif ($_GET['erro'] == 'senha_atual_invalida') {
        $mensagem = 'Erro: A senha atual está incorreta.';
    } else {
        $mensagem = 'Ocorreu um erro ao processar sua solicitação.';
    }
}

require_once 'conexao.php';
$sql = "SELECT nome, email, endereco, foto_perfil FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Orçamento PRO</title>
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

    <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
        
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8">
            <div>
                <h1 class="text-4xl font-bold text-white">ORÇAMENTO <span class="text-indigo-400">PRO</span></h1>
                <p class="text-xl text-gray-300">Meu Perfil</p>
            </div>
            <a href="index.php" class="mt-4 md:mt-0 inline-block bg-white/10 hover:bg-white/20 text-white font-semibold py-2 px-4 rounded-lg transition">
                &larr; Voltar ao Painel Principal
            </a>
        </div>

        <div class="bg-black/20 backdrop-blur-lg rounded-2xl shadow-xl p-6 sm:p-8">
            
            <?php if (!empty($mensagem)): ?>
                <div class="p-4 mb-6 text-sm rounded-lg <?php echo $cor_mensagem; ?>"><?php echo $mensagem; ?></div>
            <?php endif; ?>

            <form action="atualizar_perfil.php" method="POST" enctype="multipart/form-data" class="mb-6">
                <h2 class="text-2xl font-bold text-white mb-6">Informações Pessoais</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Foto de Perfil (JPG, PNG)</label>
                        <?php if (!empty($usuario['foto_perfil'])): ?>
                            <img src="<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" alt="Foto de Perfil" class="w-24 h-24 rounded-full my-2 object-cover border-2 border-indigo-400">
                        <?php endif; ?>
                        <input type="file" name="foto_perfil" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-500 file:text-white hover:file:bg-indigo-600 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nome</label>
                        <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome'] ?? ''); ?>" class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Endereço</label>
                        <textarea name="endereco" rows="3" class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition"><?php echo htmlspecialchars($usuario['endereco'] ?? ''); ?></textarea>
                    </div>
                </div>
                <button type="submit" class="w-full mt-6 ...">Salvar Alterações</button>
            </form>

            <hr class="my-8 border-gray-700">

            <form action="alterar_senha.php" method="POST">
                <h2 class="text-2xl font-bold text-white mb-6">Alterar Senha</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Senha Atual</label>
                        <input type="password" name="senha_atual" class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nova Senha</label>
                        <input type="password" name="nova_senha" class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Confirmar Nova Senha</label>
                        <input type="password" name="confirma_senha" class="w-full p-3 rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 transition" required>
                    </div>
                </div>
                <button type="submit" class="w-full mt-6 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 active:scale-95">Alterar Senha</button>
            </form>
        </div>
    </div>
</body>
</html>