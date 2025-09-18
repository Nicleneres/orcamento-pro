<?php
// ATENÇÃO: Este é um script auxiliar e deve ser deletado após o uso.

// Defina a senha que você quer usar
$senha_plana = 'nil1234'; 

// Gera o hash seguro da senha
$hash_seguro = password_hash($senha_plana, PASSWORD_DEFAULT);

// Imprime o hash na tela
echo "Senha Plana: " . $senha_plana . "<br>";
echo "Hash Gerado: " . $hash_seguro;
?>