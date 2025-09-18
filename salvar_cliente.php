<?php
require_once 'proteger_pagina.php';

// Apenas administradores podem cadastrar novos clientes
if ($_SESSION['usuario_cargo'] !== 'administrador') {
    die('Acesso negado.');
}

// Verifica se os dados foram enviados
if (isset($_POST['nome'])) {
    $nome = $_POST['nome'];
    $cnpj = !empty($_POST['cnpj']) ? $_POST['cnpj'] : null;
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $contato = $_POST['contato'];

// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

    $sql = "INSERT INTO clientes (nome, cnpj, endereco, telefone, contato) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssss", $nome, $cnpj, $endereco, $telefone, $contato);

    if ($stmt->execute()) {
        header('Location: clientes.php?status=sucesso');
    } else {
        header('Location: clientes.php?status=erro');
    }
    $stmt->close();
    $conexao->close();
}
exit();
?>