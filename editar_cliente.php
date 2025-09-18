<?php
require_once 'proteger_pagina.php';
// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

// Apenas administradores podem editar
if ($_SESSION['usuario_cargo'] !== 'administrador') {
    die('Acesso negado.');
}

// Busca os dados do cliente específico para preencher o formulário
$cliente_id = intval($_GET['id']);
$servidor = "localhost"; $usuario_db = "root"; $senha_db = ""; $banco = "projeto_contato";
$conexao = new mysqli($servidor, $usuario_db, $senha_db, $banco);
$sql = "SELECT * FROM clientes WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();
$conexao->close();

if (!$cliente) {
    die("Cliente não encontrado.");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .card { background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 24px; margin: 20px auto; max-width: 600px; }
        .btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; border: none; cursor: pointer; width: 100%; }
        .btn-success { background-color: #10b981; color: #ffffff; }
    </style>
</head>
<body class="p-4 md:p-6">
    <div class="card">
        <h1 class="text-2xl font-bold mb-4">Editar Cliente: <?php echo htmlspecialchars($cliente['nome']); ?></h1>
        <form action="atualizar_cliente.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Nome</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($cliente['nome']); ?>" class="p-3 w-full rounded-lg border mt-1" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">CNPJ</label>
                    <input type="text" name="cnpj" value="<?php echo htmlspecialchars($cliente['cnpj']); ?>" class="p-3 w-full rounded-lg border mt-1">
                </div>
                <div>
                    <label class="block text-sm font-medium">Telefone</label>
                    <input type="text" name="telefone" value="<?php echo htmlspecialchars($cliente['telefone']); ?>" class="p-3 w-full rounded-lg border mt-1">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Contato</label>
                    <input type="text" name="contato" value="<?php echo htmlspecialchars($cliente['contato']); ?>" class="p-3 w-full rounded-lg border mt-1">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Endereço</label>
                    <input type="text" name="endereco" value="<?php echo htmlspecialchars($cliente['endereco']); ?>" class="p-3 w-full rounded-lg border mt-1">
                </div>
            </div>
            <button type="submit" class="btn btn-success mt-4">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>