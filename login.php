<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}
$erro = isset($_GET['erro']) ? 'E-mail ou senha inválidos.' : '';
$sucesso = isset($_GET['sucesso']) ? 'Cadastro realizado com sucesso! Faça o login.' : '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Orçamento PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(-45deg, #0f0c29, #302b63, #24243e, #1c3d52);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Animação para o formulário aparecer suavemente */
        .animate-fade-in-up {
            animation: fadeInUp 0.7s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white/10 backdrop-blur-md rounded-2xl shadow-xl p-8 animate-fade-in-up">
        
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white">
                ORÇAMENTO <span class="text-indigo-400">PRO</span>
            </h1>
        </div>

        <?php if (!empty($sucesso)): ?>
            <p class="bg-green-100/80 text-green-900 font-semibold p-3 rounded-md text-sm mb-4"><?php echo $sucesso; ?></p>
        <?php endif; ?>
        
        <form action="verificar_login.php" method="POST">
            <div class="mb-4">
                <label for="email" class="block text-gray-300 text-sm font-semibold mb-2">E-mail</label>
                <input type="email" name="email" id="email" class="p-3 w-full rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
            </div>
            <div class="mb-5">
                <label for="senha" class="block text-gray-300 text-sm font-semibold mb-2">Senha</label>
                <input type="password" name="senha" id="senha" class="p-3 w-full rounded-lg bg-gray-900/50 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
            </div>
            <div class="mb-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" id="mostrarSenha" class="form-checkbox h-4 w-4 rounded bg-gray-700 border-gray-600 text-indigo-500 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-300">Mostrar Senha</span>
                </label>
            </div>
            
            <?php if (!empty($erro)): ?>
                <p class="bg-red-500/80 text-white font-semibold p-3 rounded-md text-sm mb-4"><?php echo $erro; ?></p>
            <?php endif; ?>
            
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 active:scale-95">
                Entrar
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="registrar.php" class="text-sm text-indigo-300 hover:text-indigo-100 transition">Não tem uma conta? Cadastre-se</a>
        </div>
        
    </div>

    <script>
        document.getElementById('mostrarSenha').addEventListener('change', function() {
            document.getElementById('senha').type = this.checked ? 'text' : 'password';
        });
    </script>
</body>
</html>