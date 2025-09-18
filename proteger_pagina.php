<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    // Se não existir um usuário logado, destrói a sessão por segurança
    session_destroy();
    
    // E redireciona para a página de login
    header('Location: login.php');
    exit();
}
?>