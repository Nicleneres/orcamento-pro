<?php
session_start(); // SEMPRE inicie a sessão no topo dos arquivos que a utilizam

// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

// 2. PEGAR DADOS DO FORMULÁRIO DE LOGIN
$email = $_POST['email'];
$senha_digitada = $_POST['senha'];

// 3. BUSCAR USUÁRIO NO BANCO PELO E-MAIL
$sql = "SELECT id, nome, senha, cargo FROM usuarios WHERE email = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();
    
    // 4. VERIFICAR A SENHA
    if (password_verify($senha_digitada, $usuario['senha'])) {
        // Senha correta!
        // 5. GUARDAR INFORMAÇÕES DO USUÁRIO NA SESSÃO
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_cargo'] = $usuario['cargo'];
        
        // 6. REDIRECIONAR PARA A PÁGINA PRINCIPAL
        header('Location: index.php');
        exit();
    }
}

// Se chegou até aqui, o e-mail não existe ou a senha está errada
header('Location: login.php?erro=1');
exit();
?>