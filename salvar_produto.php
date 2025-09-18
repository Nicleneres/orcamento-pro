<?php
require_once 'proteger_pagina.php';
require_once 'conexao.php'; // Usando nossa conexão centralizada

header('Content-Type: application/json'); // Informa que a resposta é JSON

if ($_SESSION['usuario_cargo'] !== 'administrador') {
    http_response_code(403); // Código de "Acesso Proibido"
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado.']);
    exit();
}

$nome = $_POST['nome_produto'];
$preco_final = $_POST['preco_final'];
$preco_meio = !empty($_POST['preco_meio']) ? $_POST['preco_meio'] : null;
$preco_loja = !empty($_POST['preco_loja']) ? $_POST['preco_loja'] : null;

$sql = "INSERT INTO produtos (nome, preco_final, preco_meio, preco_loja) VALUES (?, ?, ?, ?)";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("sddd", $nome, $preco_final, $preco_meio, $preco_loja);

if ($stmt->execute()) {
    echo json_encode(['status' => 'sucesso', 'mensagem' => 'Produto salvo com sucesso!']);
} else {
    http_response_code(500); // Código de "Erro Interno do Servidor"
    echo json_encode(['status' => 'erro', 'mensagem' => $stmt->error]);
}

$stmt->close();
$conexao->close();
?>