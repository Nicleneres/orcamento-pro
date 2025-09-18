<?php
require_once 'proteger_pagina.php';

header('Content-Type: application/json');

// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

$sql = "SELECT id, nome, cnpj, endereco, telefone, contato FROM clientes ORDER BY nome ASC";
$resultado = $conexao->query($sql);
$clientes = [];
if ($resultado->num_rows > 0) {
    while($linha = $resultado->fetch_assoc()) {
        $clientes[] = $linha;
    }
}

$conexao->close();
echo json_encode($clientes);
?>