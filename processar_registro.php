<?php
// 1. CONEXÃO COM O BANCO
// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

// 2. PEGAR DADOS DO FORMULÁRIO
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha_plana = $_POST['senha'];

// 3. VERIFICAR SE O E-MAIL JÁ EXISTE
$sql_verifica = "SELECT id FROM usuarios WHERE email = ?";
$stmt_verifica = $conexao->prepare($sql_verifica);
$stmt_verifica->bind_param("s", $email);
$stmt_verifica->execute();
$resultado = $stmt_verifica->get_result();

if ($resultado->num_rows > 0) {
    // E-mail já cadastrado, redireciona de volta com erro
    header('Location: registrar.php?erro=email_existente');
    exit();
}

// 4. GERAR O HASH SEGURO DA SENHA
$hash_senha = password_hash($senha_plana, PASSWORD_DEFAULT);

// 5. INSERIR O NOVO USUÁRIO NO BANCO
// O cargo é fixo como 'vendedor' por segurança
$sql_insert = "INSERT INTO usuarios (nome, email, senha, cargo) VALUES (?, ?, ?, 'vendedor')";
$stmt_insert = $conexao->prepare($sql_insert);
$stmt_insert->bind_param("sss", $nome, $email, $hash_senha);

if ($stmt_insert->execute()) {
    // Sucesso, redireciona para o login com mensagem de sucesso
    header('Location: login.php?sucesso=cadastro_ok');
    exit();
} else {
    // Falha, redireciona de volta com erro genérico
    header('Location: registrar.php?erro=generico');
    exit();
}
?>