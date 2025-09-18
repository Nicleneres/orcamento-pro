<?php
require_once 'proteger_pagina.php';

if ($_SESSION['usuario_cargo'] !== 'administrador') {
    die('Acesso negado.');
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $cnpj = !empty($_POST['cnpj']) ? $_POST['cnpj'] : null;
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $contato = $_POST['contato'];

// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

    $sql = "UPDATE clientes SET nome = ?, cnpj = ?, endereco = ?, telefone = ?, contato = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssssi", $nome, $cnpj, $endereco, $telefone, $contato, $id);

    if ($stmt->execute()) {
        header('Location: clientes.php?status=editado_sucesso');
    } else {
        header('Location: clientes.php?status=editado_erro');
    }
    $stmt->close();
    $conexao->close();
}
exit();
?>