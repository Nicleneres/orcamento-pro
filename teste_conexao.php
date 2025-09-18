<?php
echo "<h1>Teste de Conexão Segura (cURL com SSL)</h1>";
ini_set('display_errors', 1);
error_reporting(E_ALL);

$url = 'https://www.google.com';
echo "<p>Tentando conectar a: " . htmlspecialchars($url) . "</p>";

// --- IMPORTANTE: COLOQUE AQUI O CAMINHO EXATO DO SEU ARQUIVO cacert.pem ---
$caminho_certificado = 'C:\wamp64\bin\php\php8.4.0\extras\ssl\cacert.pem';
echo "<p>Usando o arquivo de certificado em: <code>" . htmlspecialchars($caminho_certificado) . "</code></p><hr>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Adicionando a verificação do certificado
curl_setopt($ch, CURLOPT_CAINFO, $caminho_certificado);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Força a verificação

$response = curl_exec($ch);
$errno = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);

if ($errno != 0) {
    echo "<h2 style='color:red;'>FALHA NA CONEXÃO cURL!</h2>";
    echo "<p><strong>Código do Erro:</strong> " . $errno . "</p>";
    echo "<p><strong>Mensagem do Erro:</strong> " . htmlspecialchars($error) . "</p>";
    echo "<hr><p><strong>Diagnóstico:</strong> Seu PHP não está conseguindo fazer conexões seguras (HTTPS) a partir do seu WampServer. Isso confirma que o problema está na configuração do seu ambiente local (WAMP, Firewall, Antivírus) e não no código do projeto de login do Google. A solução mais recomendada é a reinstalação do WAMP.</p>";
} else {
    echo "<h2 style='color:green;'>SUCESSO!</h2>";
    echo "<p>A conexão cURL segura com o Google foi bem-sucedida.</p>";
    echo "<hr><p><strong>Diagnóstico:</strong> Isso é muito raro. Significa que a conexão básica funciona, mas a biblioteca Guzzle (usada pelo Google) tem um conflito profundo e específico com a sua versão do PHP. Mesmo neste caso, a solução mais garantida para eliminar esse conflito de uma vez por todas ainda é reinstalar uma versão mais nova e limpa do WampServer.</p>";
}
?>