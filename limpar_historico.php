<?php
require_once 'proteger_pagina.php';

// Ação super restrita a administradores
if ($_SESSION['usuario_cargo'] !== 'administrador') {
    die('Acesso negado.');
}

$servidor = "localhost";
$usuario_db = "root";
$senha_db = "";
$banco = "projeto_contato";
$conexao = new mysqli($servidor, $usuario_db, $senha_db, $banco);

// Desativa a checagem de chaves estrangeiras temporariamente para o TRUNCATE
$conexao->query('SET FOREIGN_KEY_CHECKS=0');

// TRUNCATE limpa a tabela e reseta o auto-incremento do ID
$conexao->query('TRUNCATE TABLE orcamento_itens');
$conexao->query('TRUNCATE TABLE orcamentos');

// Reativa a checagem
$conexao->query('SET FOREIGN_KEY_CHECKS=1');

$conexao->close();

header('Location: historico.php?status=limpo');
exit();
?>