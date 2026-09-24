<?php

/**
 * Página de implementação: trata o formulário, cria os objetos e exibe o cardápio.
 */

// Sessão guarda as pizzas adicionadas entre uma requisição e outra
session_start();
date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/PaginaProdutos.class.php';

// Garante que o campo recebido é texto (evita arrays enviados de propósito)
function lerCampo(array $origem, string $campo): string
{
    return (isset($origem[$campo]) && is_string($origem[$campo])) ? $origem[$campo] : '';
}

// Cardápio fixo: array de arrays associativos, cada um vira um objeto Produto
$pizzasDoCardapio = [
    [
        'nome'        => 'Margherita',
        'descricao'   => 'Molho de tomate, mussarela de búfala, tomate fatiado e manjericão fresco.',
        'preco'       => 42.90,
        'vegetariana' => true,
    ],
    [
        'nome'        => 'Calabresa',
        'descricao'   => 'Molho de tomate, mussarela, calabresa artesanal fatiada e cebola roxa.',
        'preco'       => 44.90,
        'vegetariana' => false,
    ],
    [
        'nome'        => 'Portuguesa',
        'descricao'   => 'Mussarela, presunto, ovos cozidos, cebola, ervilha e azeitonas pretas.',
        'preco'       => 49.90,
        'vegetariana' => false,
    ],
    [
        'nome'        => 'Frango com Catupiry',
        'descricao'   => 'Frango desfiado temperado, Catupiry original e milho verde.',
        'preco'       => 51.90,
        'vegetariana' => false,
    ],
    [
        'nome'        => 'Quatro Queijos',
        'descricao'   => 'Mussarela, provolone, parmesão e gorgonzola, finalizada com orégano.',
        'preco'       => 52.90,
        'vegetariana' => true,
    ],
    [
        'nome'        => 'Pepperoni',
        'descricao'   => 'Molho de tomate apimentado, mussarela e fatias generosas de pepperoni.',
        'preco'       => 54.90,
        'vegetariana' => false,
    ],
];

if (!isset($_SESSION['pizzasAdicionadas'])) {
    $_SESSION['pizzasAdicionadas'] = [];
}

// ---------- Tratamento do formulário ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = lerCampo($_POST, 'acao');

    if ($acao === 'adicionar') {
        // Se algum setter do Produto lançar exceção, cai no catch e mostra o erro
        try {
            // Aceita "45.90" e "45,90"
            $precoTexto = str_replace(',', '.', trim(lerCampo($_POST, 'preco')));

            if (!is_numeric($precoTexto)) {
                throw new InvalidArgumentException('Informe um preço válido, por exemplo 45,90.');
            }

            $novaPizza = new Produto(
                lerCampo($_POST, 'nome'),
                lerCampo($_POST, 'descricao'),
                (float) $precoTexto,
                isset($_POST['vegetariana'])
            );

            // Guarda só os dados na sessão; o objeto é recriado a cada requisição
            $_SESSION['pizzasAdicionadas'][] = [
                'nome'        => $novaPizza->getNome(),
                'descricao'   => $novaPizza->getDescricao(),
                'preco'       => $novaPizza->getPreco(),
                'vegetariana' => $novaPizza->isVegetariana(),
            ];

            $_SESSION['mensagem'] = [
                'texto' => 'Pizza "' . $novaPizza->getNome() . '" adicionada ao cardápio!',
                'tipo'  => 'sucesso',
            ];
        } catch (InvalidArgumentException $erro) {
            $_SESSION['mensagem'] = ['texto' => $erro->getMessage(), 'tipo' => 'erro'];
        }
    } elseif ($acao === 'limpar') {
        $_SESSION['pizzasAdicionadas'] = [];
        $_SESSION['mensagem'] = ['texto' => 'As pizzas que você adicionou foram removidas.', 'tipo' => 'sucesso'];
    }

    // Post/Redirect/Get: evita reenviar o formulário ao apertar F5
    header('Location: cardapio.php');
    exit;
}

// ---------- Criação dos objetos e exibição ----------
$pagina = new PaginaProdutos('Pizzaria Forno de Pedra', 'Cardápio de Pizzas');

// Junta o cardápio fixo com as pizzas adicionadas pelo usuário
$todasAsPizzas = array_merge($pizzasDoCardapio, $_SESSION['pizzasAdicionadas']);

foreach ($todasAsPizzas as $dados) {
    $pagina->adicionarProduto(new Produto(
        $dados['nome'],
        $dados['descricao'],
        $dados['preco'],
        $dados['vegetariana']
    ));
}

$pagina->setOrdem(lerCampo($_GET, 'ordem'));

// Mensagem gravada antes do redirect: exibe uma vez e remove
if (isset($_SESSION['mensagem'])) {
    $pagina->setMensagem($_SESSION['mensagem']['texto'], $_SESSION['mensagem']['tipo']);
    unset($_SESSION['mensagem']);
}

// exibir() vem da Pagina, mas usa o gerarCorpo() da PaginaProdutos
$pagina->exibir();
