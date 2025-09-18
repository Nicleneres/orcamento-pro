<?php
// --- PARTE EM PHP PARA BUSCAR OS DADOS DO PRODUTO ---

// 1. VERIFICAR SE UM ID FOI PASSADO PELA URL
if (isset($_GET['id'])) {
    $produto_id = $_GET['id'];

// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

    if ($conexao->connect_error) {
        die("Falha na conexão: " . $conexao->connect_error);
    }

    // 3. BUSCAR OS DADOS DO PRODUTO ESPECÍFICO
    $sql = "SELECT * FROM produtos WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $produto_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $produto = $resultado->fetch_assoc();
    } else {
        echo "Produto não encontrado.";
        exit; // Para a execução do script se o produto não existe
    }

    $stmt->close();
    $conexao->close();
} else {
    echo "ID do produto não especificado.";
    exit; // Para a execução se nenhum ID foi passado
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Você pode copiar os estilos do seu index.php se quiser a mesma aparência */
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .card { background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 24px; margin: 20px auto; max-width: 600px; }
        .btn { padding: 12px 24px; border-radius: 8px; font-weight: 600; }
        .btn-success { background-color: #10b981; color: #ffffff; }
    </style>
</head>
<body class="p-6">
    <div class="card">
        <h1 class="text-3xl font-bold mb-6">Editar Produto: <?php echo htmlspecialchars($produto['nome']); ?></h1>

        <form action="atualizar_produto.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="nome_produto" class="block font-medium mb-1">Nome do Produto</label>
                    <input type="text" id="nome_produto" name="nome_produto" value="<?php echo htmlspecialchars($produto['nome']); ?>" class="p-3 w-full rounded-lg border border-gray-300" required>
                </div>
                <div>
                    <label for="preco_final" class="block font-medium mb-1">Preço Final (R$)</label>
                    <input type="number" id="preco_final" name="preco_final" value="<?php echo $produto['preco_final']; ?>" step="0.01" class="p-3 w-full rounded-lg border border-gray-300" required>
                </div>
                <div>
                    <label for="preco_meio" class="block font-medium mb-1">Preço do Meio (R$)</label>
                    <input type="number" id="preco_meio" name="preco_meio" value="<?php echo $produto['preco_meio']; ?>" step="0.01" class="p-3 w-full rounded-lg border border-gray-300">
                </div>
                <div>
                    <label for="preco_loja" class="block font-medium mb-1">Preço Loja (R$)</label>
                    <input type="number" id="preco_loja" name="preco_loja" value="<?php echo $produto['preco_loja']; ?>" step="0.01" class="p-3 w-full rounded-lg border border-gray-300">
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-success w-full">Salvar Alterações</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>