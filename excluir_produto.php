<?php
// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

// 2. CRIAR A CONEXÃO
$conexao = new mysqli($servidor, $usuario_db, $senha_db, $banco);

// 3. CHECAR A CONEXÃO
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// 4. VERIFICAR SE O ID DO PRODUTO FOI ENVIADO
if (isset($_POST['id'])) {
    $produto_id = $_POST['id'];

    // 5. PREPARAR A INSTRUÇÃO SQL PARA DELETAR O PRODUTO
    // A '?' previne injeção de SQL
    $sql = "DELETE FROM produtos WHERE id = ?";
    $stmt = $conexao->prepare($sql);

    // "i" significa que o parâmetro é um inteiro (integer)
    $stmt->bind_param("i", $produto_id);

    // 6. EXECUTAR E VERIFICAR
    if ($stmt->execute()) {
        echo "Produto excluído com sucesso!";
    } else {
        echo "Erro ao excluir produto: " . $stmt->error;
    }

    // 7. FECHAR O STATEMENT
    $stmt->close();
} else {
    echo "ID do produto não fornecido.";
}

// 8. FECHAR A CONEXÃO
$conexao->close();

?>