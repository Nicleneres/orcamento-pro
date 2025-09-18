<?php
require_once 'proteger_pagina.php';

// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];
$nome = $_POST['nome'];
$endereco = $_POST['endereco'];
$caminho_foto = null;

// --- LÓGICA DE UPLOAD DA FOTO DE PERFIL ---
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
    $pasta_uploads = 'uploads/';
    // Gera um nome de arquivo único para evitar sobreposições
    $nome_arquivo = uniqid() . '-' . basename($_FILES['foto_perfil']['name']);
    $caminho_completo = $pasta_uploads . $nome_arquivo;

    // Move o arquivo temporário para a pasta de uploads
    if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $caminho_completo)) {
        $caminho_foto = $caminho_completo;
    } else {
        // Falha no upload, mas continua para salvar os outros dados
        echo "Houve um erro no upload da imagem, mas os outros dados serão salvos.";
    }
}

// --- ATUALIZAÇÃO DOS DADOS NO BANCO ---
if ($caminho_foto) {
    // Se uma nova foto foi enviada, atualiza todos os campos
    $sql = "UPDATE usuarios SET nome = ?, endereco = ?, foto_perfil = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssi", $nome, $endereco, $caminho_foto, $usuario_id);
} else {
    // Se não há foto nova, atualiza apenas nome e endereço
    $sql = "UPDATE usuarios SET nome = ?, endereco = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssi", $nome, $endereco, $usuario_id);
}

if ($stmt->execute()) {
    // Atualiza o nome na sessão para refletir a mudança imediatamente
    $_SESSION['usuario_nome'] = $nome;
    header('Location: perfil.php?status=sucesso');
} else {
    header('Location: perfil.php?status=erro');
}

$stmt->close();
$conexao->close();
exit();
?>