<?php
require_once 'proteger_pagina.php';

// Apenas administradores podem excluir
if ($_SESSION['usuario_cargo'] !== 'administrador') {
    die('Acesso negado.');
}

// Verifica se o ID foi passado pela URL
if (isset($_GET['id'])) {
    $cliente_id = intval($_GET['id']);

 // Bloco novo com apenas 1 linha!
require_once 'conexao.php';
    $sql = "DELETE FROM clientes WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $cliente_id);
    
    if ($stmt->execute()) {
        header('Location: clientes.php?status=excluido_sucesso');
    } else {
        header('Location: clientes.php?status=excluido_erro');
    }
    
    $stmt->close();
    $conexao->close();
} else {
    // Se nenhum ID for fornecido, apenas redireciona de volta
    header('Location: clientes.php');
}
exit();
?>