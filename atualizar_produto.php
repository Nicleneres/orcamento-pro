<?php
// 1. VERIFICAR SE OS DADOS FORAM ENVIADOS VIA POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

    // 3. PEGAR OS DADOS DO FORMULÁRIO (INCLUINDO O ID)
    $id = $_POST['id'];
    $nome = $_POST['nome_produto'];
    $preco_final = $_POST['preco_final'];
    $preco_meio = !empty($_POST['preco_meio']) ? $_POST['preco_meio'] : $preco_final;
    $preco_loja = !empty($_POST['preco_loja']) ? $_POST['preco_loja'] : $preco_final;

    // 4. PREPARAR A INSTRUÇÃO SQL DE UPDATE
    $sql = "UPDATE produtos SET nome = ?, preco_final = ?, preco_meio = ?, preco_loja = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);

    // "sdddi" -> s=string, d=double, d=double, d=double, i=integer
    $stmt->bind_param("sdddi", $nome, $preco_final, $preco_meio, $preco_loja, $id);

    // 5. EXECUTAR E REDIRECIONAR
    if ($stmt->execute()) {
        header("Location: index.php?status=sucesso_edicao");
        exit();
    } else {
        echo "Erro ao atualizar produto: " . $stmt->error;
    }

    $stmt->close();
    $conexao->close();
} else {
    // Se alguém tentar acessar este arquivo diretamente, redireciona para a página inicial
    header("Location: index.php");
    exit();
}
?>