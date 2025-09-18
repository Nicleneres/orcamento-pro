<?php
// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

// 2. CRIAR A CONEXÃO
$conexao = new mysqli($servidor, $usuario_db, $senha_db, $banco);

// 3. CHECAR A CONEXÃO
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// 4. CRIAR A INSTRUÇÃO SQL PARA BUSCAR OS PRODUTOS
$sql = "SELECT id, nome, preco_final, preco_meio, preco_loja FROM produtos ORDER BY nome ASC";
$resultado = $conexao->query($sql);

// 5. TRANSFORMAR O RESULTADO EM UM ARRAY
$produtos = [];
if ($resultado->num_rows > 0) {
    // Loop para pegar cada linha de resultado
    while($linha = $resultado->fetch_assoc()) {
        $produtos[] = $linha;
    }
}

// 6. FECHAR A CONEXÃO
$conexao->close();

// 7. ENVIAR A RESPOSTA EM FORMATO JSON
// Isso permite que o JavaScript leia os dados facilmente
header('Content-Type: application/json');
echo json_encode($produtos);

?>