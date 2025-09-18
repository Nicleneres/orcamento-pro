<?php
require_once 'proteger_pagina.php';

// Apenas administradores podem excluir
if ($_SESSION['usuario_cargo'] !== 'administrador') {
    die('Acesso negado.');
}

if (isset($_POST['id'])) {
    $orcamento_id = $_POST['id'];

// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

    // Graças ao "ON DELETE CASCADE" que definimos, ao deletar o orçamento,
    // o banco de dados automaticamente deletará todos os itens associados.
    $sql = "DELETE FROM orcamentos WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $orcamento_id);
    
    if ($stmt->execute()) {
        header('Location: historico.php?status=excluido');
    } else {
        echo "Erro ao excluir orçamento.";
    }
    
    $stmt->close();
    $conexao->close();
} else {
    header('Location: historico.php');
}
exit();
?>