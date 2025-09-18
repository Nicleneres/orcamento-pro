<?php
// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

// 2. PEGAR DADOS DO FORMULÁRIO
$nome = $_POST['companyNameInput'];
$endereco = $_POST['companyAddressInput'];
$telefone = $_POST['companyPhoneInput'];
$email = $_POST['companyEmailInput'];
$logo_url = $_POST['logoUrlInput'];
$chave_pix = $_POST['companyPixInput'];

// 3. VERIFICAR SE JÁ EXISTE UM REGISTRO DA EMPRESA (SEMPRE USAREMOS ID = 1)
$sql_verifica = "SELECT id FROM empresa WHERE id = 1";
$resultado = $conexao->query($sql_verifica);

if ($resultado->num_rows > 0) {
    // SE EXISTE, FAZ UPDATE
    $sql = "UPDATE empresa SET nome=?, endereco=?, telefone=?, email=?, logo_url=?, chave_pix=? WHERE id=1";
} else {
    // SE NÃO EXISTE, FAZ INSERT
    $sql = "INSERT INTO empresa (nome, endereco, telefone, email, logo_url, chave_pix, id) VALUES (?, ?, ?, ?, ?, ?, 1)";
}

$stmt = $conexao->prepare($sql);
$stmt->bind_param("ssssss", $nome, $endereco, $telefone, $email, $logo_url, $chave_pix);

// 4. EXECUTAR E REDIRECIONAR
if ($stmt->execute()) {
    header("Location: index.php?status=empresa_salva");
    exit();
} else {
    echo "Erro ao salvar dados da empresa: " . $stmt->error;
}

$stmt->close();
$conexao->close();
?>