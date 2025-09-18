<?php
$servidor = "localhost";
$usuario_db = "root";
$senha_db = "";
$banco = "projeto_contato";

// Cria a conexão
$conexao = new mysqli($servidor, $usuario_db, $senha_db, $banco);

// Checa por erros na conexão
if ($conexao->connect_error) {
    // Para a execução do script e mostra o erro
    die("Falha na conexão com o banco de dados: " . $conexao->connect_error);
}
?>