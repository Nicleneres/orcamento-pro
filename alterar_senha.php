<?php
require_once 'proteger_pagina.php';

// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];
$senha_atual = $_POST['senha_atual'];
$nova_senha = $_POST['nova_senha'];
$confirma_senha = $_POST['confirma_senha'];

// 1. Validar se a nova senha e a confirmação são iguais
if ($nova_senha !== $confirma_senha) {
    header('Location: perfil.php?erro=senhas_nao_conferem');
    exit();
}

// 2. Buscar o hash da senha atual no banco de dados
$sql_busca = "SELECT senha FROM usuarios WHERE id = ?";
$stmt_busca = $conexao->prepare($sql_busca);
$stmt_busca->bind_param("i", $usuario_id);
$stmt_busca->execute();
$resultado = $stmt_busca->get_result();
$usuario = $resultado->fetch_assoc();
$hash_atual = $usuario['senha'];

// 3. Verificar se a senha atual digitada corresponde ao hash do banco
if (password_verify($senha_atual, $hash_atual)) {
    // Senha atual está correta, podemos prosseguir
    
    // 4. Gerar o hash da NOVA senha
    $novo_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
    
    // 5. Atualizar a senha no banco de dados
    $sql_update = "UPDATE usuarios SET senha = ? WHERE id = ?";
    $stmt_update = $conexao->prepare($sql_update);
    $stmt_update->bind_param("si", $novo_hash, $usuario_id);
    
    if ($stmt_update->execute()) {
        header('Location: perfil.php?sucesso=senha_alterada');
    } else {
        header('Location: perfil.php?erro=db_erro');
    }
    $stmt_update->close();
    
} else {
    // Senha atual incorreta
    header('Location: perfil.php?erro=senha_atual_invalida');
}

$stmt_busca->close();
$conexao->close();
exit();
?>