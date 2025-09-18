<?php
require_once 'proteger_pagina.php';

// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];
// Marca todas as notificações não lidas do usuário como lidas
$sql = "UPDATE notificacoes SET lida = TRUE WHERE usuario_id = ? AND lida = FALSE";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->close();
$conexao->close();

// Retorna uma resposta vazia, mas com sucesso (HTTP 200)
http_response_code(200);
?>