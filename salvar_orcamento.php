<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Bloco novo com apenas 1 linha!
require_once 'conexao.php';

// 2. RECEBER OS DADOS ENVIADOS PELO JAVASCRIPT
// Como o JavaScript envia um JSON, precisamos ler o corpo da requisição
$dados = json_decode(file_get_contents('php://input'), true);

// 3. INICIAR UMA TRANSAÇÃO
// Isso garante que ou salvamos tudo (orçamento + itens) ou não salvamos nada.
$conexao->begin_transaction();

try {
    // 4. SALVAR OS DADOS GERAIS NA TABELA `orcamentos`
// Bloco novo
$usuario_id_logado = $dados['usuario_id']; // Vamos pegar o ID que o JavaScript vai nos enviar

$sql_orcamento = "INSERT INTO orcamentos (cliente_nome, cliente_cnpj, cliente_endereco, cliente_telefone, cliente_contato, valor_total, desconto, forma_pagamento, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt_orcamento = $conexao->prepare($sql_orcamento);
$stmt_orcamento->bind_param("sssssddsi", // Adicionamos 'i' para o integer do usuario_id
    $dados['cliente']['nome'], 
    $dados['cliente']['cnpj'],
    $dados['cliente']['endereco'],
    $dados['cliente']['telefone'],
    $dados['cliente']['contato'],
    $dados['pagamento']['totalFinal'],
    $dados['pagamento']['desconto'],
    $dados['pagamento']['metodo'],
    $usuario_id_logado
);
    $stmt_orcamento->execute();

    // 5. PEGAR O ID DO ORÇAMENTO QUE ACABAMOS DE INSERIR
    $orcamento_id = $conexao->insert_id;

// Bloco novo
$sql_item = "INSERT INTO orcamento_itens (orcamento_id, produto_nome, quantidade, preco_unitario, preco_total, tipo_preco) VALUES (?, ?, ?, ?, ?, ?)";
$stmt_item = $conexao->prepare($sql_item);
// ...
foreach ($dados['itens'] as $item) {
    $preco_total_item = $item['quantity'] * $item['price'];
    // Adicionamos 's' para a string do tipo_preco
    $stmt_item->bind_param("isidds", 
        $orcamento_id,
        $item['name'],
        $item['quantity'],
        $item['price'],
        $preco_total_item,
        $item['priceType'] // Enviamos o tipo de preço
    );
    $stmt_item->execute();
}
    
    // 7. SE TUDO DEU CERTO, CONFIRMAR A TRANSAÇÃO
    $conexao->commit();
     // 7. SE TUDO DEU CERTO, CONFIRMAR A TRANSAÇÃO
    $conexao->commit();
    
    // --- INÍCIO DO NOVO BLOCO DE CÓDIGO PARA CRIAR NOTIFICAÇÃO ---

    // Pega o nome do vendedor que está logado
    $nome_vendedor = $_SESSION['usuario_nome'];

    // Monta a mensagem e o link da notificação
    $mensagem_notificacao = "O vendedor {$nome_vendedor} criou o orçamento #{$orcamento_id}.";
    $link_notificacao = "ver_orcamento.php?id={$orcamento_id}";

    // Encontra todos os administradores para notificá-los
    $sql_admins = "SELECT id FROM usuarios WHERE cargo = 'administrador'";
    $resultado_admins = $conexao->query($sql_admins);

    if ($resultado_admins->num_rows > 0) {
        $sql_notificacao = "INSERT INTO notificacoes (usuario_id, mensagem, link) VALUES (?, ?, ?)";
        $stmt_notificacao = $conexao->prepare($sql_notificacao);
        
        while ($admin = $resultado_admins->fetch_assoc()) {
            $admin_id = $admin['id'];
            $stmt_notificacao->bind_param("iss", $admin_id, $mensagem_notificacao, $link_notificacao);
            $stmt_notificacao->execute();
        }
        $stmt_notificacao->close();
    }

    // --- FIM DO NOVO BLOCO DE CÓDIGO ---
    
    // Retorna uma resposta de sucesso para o JavaScript
    echo json_encode(['status' => 'sucesso', 'orcamento_id' => $orcamento_id]);

} catch (Exception $e) {
    // 8. SE ALGO DEU ERRADO, DESFAZER TUDO (ROLLBACK)
    $conexao->rollback();
    
    // Retorna uma resposta de erro para o JavaScript
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}

$stmt_orcamento->close();
$stmt_item->close();
$conexao->close();
?>