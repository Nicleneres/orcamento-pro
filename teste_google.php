<?php
echo "<h1>Relatório de Teste da Biblioteca Google</h1>";

ini_set('display_errors', 1);
error_reporting(E_ALL);

$caminho_autoload = __DIR__ . '/vendor/autoload.php';
echo "<strong>Passo 1:</strong> Tentando incluir o arquivo: <code>" . $caminho_autoload . "</code><br>";

if (file_exists($caminho_autoload)) {
    echo "<span style='color:green;'>Arquivo autoload.php ENCONTRADO.</span><br>";
    require_once $caminho_autoload;
    echo "<strong>Passo 2:</strong> Autoloader incluído com sucesso.<br>";
} else {
    echo "<strong style='color:red;'>ERRO CRÍTICO:</strong> Arquivo autoload.php NÃO FOI ENCONTRADO neste caminho.<br>";
    die("Verifique se a pasta 'vendor' existe e se o comando 'composer install/require' foi executado corretamente na raiz do projeto.");
}

echo "<strong>Passo 3:</strong> Tentando criar a classe `\\Google\\Client`...<br>";

try {
    $client = new \Google\Client();
    echo "<h2 style='color:green;'>SUCESSO! A classe \\Google\\Client foi criada com êxito!</h2>";
    echo "<p>Isso confirma que o Composer e o Autoloader estão funcionando corretamente.</p>";
} catch (Throwable $e) {
    echo "<h2 style='color:red;'>ERRO FATAL: A classe não pôde ser criada.</h2>";
    echo "<p><strong>Mensagem de erro:</strong> " . $e->getMessage() . "</p>";
}

echo "<br>Fim do teste.";
?>