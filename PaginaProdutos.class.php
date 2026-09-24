<?php

require_once __DIR__ . '/Pagina.class.php';
require_once __DIR__ . '/Produto.class.php';

/**
 * Subclasse de Pagina: exibe a lista de produtos de uma categoria.
 * Herda cabeçalho, rodapé e exibir(); sobrescreve apenas gerarCorpo().
 */
class PaginaProdutos extends Pagina
{
    // Chave vai na URL (?ordem=nome), valor é o texto do botão
    private const ORDENS_VALIDAS = [
        'padrao'      => 'Ordem do cardápio',
        'nome'        => 'Nome (A-Z)',
        'menor-preco' => 'Menor preço',
        'maior-preco' => 'Maior preço',
    ];

    private string $categoria;

    /** @var Produto[] */
    private array $produtos = [];

    private string $ordem = 'padrao';
    private string $mensagem = '';
    private string $tipoMensagem = 'sucesso';

    // parent::__construct inicializa os atributos herdados (título e CSS)
    public function __construct(string $titulo, string $categoria, string $arquivoCss = 'estilo.css')
    {
        parent::__construct($titulo, $arquivoCss);
        $this->setCategoria($categoria);
    }

    // ---------- Getters e Setters ----------

    public function getCategoria(): string
    {
        return $this->categoria;
    }

    public function setCategoria(string $categoria): void
    {
        $categoria = trim($categoria);

        if ($categoria === '') {
            throw new InvalidArgumentException('A categoria não pode ser vazia.');
        }

        $this->categoria = $categoria;
    }

    /** @return Produto[] */
    public function getProdutos(): array
    {
        return $this->produtos;
    }

    public function getOrdem(): string
    {
        return $this->ordem;
    }

    // Ordem desconhecida na URL cai no padrão em vez de gerar erro
    public function setOrdem(string $ordem): void
    {
        $this->ordem = array_key_exists($ordem, self::ORDENS_VALIDAS) ? $ordem : 'padrao';
    }

    // $tipo: "sucesso" ou "erro" (define a cor via CSS)
    public function setMensagem(string $texto, string $tipo = 'sucesso'): void
    {
        $this->mensagem = $texto;
        $this->tipoMensagem = ($tipo === 'erro') ? 'erro' : 'sucesso';
    }

    // ---------- Manipulação do array de produtos ----------

    // Tipagem "Produto" garante que só objetos Produto entram na lista
    public function adicionarProduto(Produto $produto): void
    {
        $this->produtos[] = $produto;
    }

    public function getQuantidadeProdutos(): int
    {
        return count($this->produtos);
    }

    // Extrai só os preços do array de objetos
    private function obterPrecos(): array
    {
        return array_map(fn (Produto $produto) => $produto->getPreco(), $this->produtos);
    }

    public function calcularPrecoMedio(): float
    {
        if ($this->getQuantidadeProdutos() === 0) {
            return 0.0;
        }

        return array_sum($this->obterPrecos()) / $this->getQuantidadeProdutos();
    }

    // Ordena uma cópia do array; <=> compara e retorna -1, 0 ou 1
    private function obterProdutosOrdenados(): array
    {
        $lista = $this->produtos;

        switch ($this->ordem) {
            case 'nome':
                usort($lista, fn (Produto $a, Produto $b) => strcasecmp($a->getNome(), $b->getNome()));
                break;
            case 'menor-preco':
                usort($lista, fn (Produto $a, Produto $b) => $a->getPreco() <=> $b->getPreco());
                break;
            case 'maior-preco':
                usort($lista, fn (Produto $a, Produto $b) => $b->getPreco() <=> $a->getPreco());
                break;
        }

        return $lista;
    }

    // ---------- Montagem do HTML ----------

    // Sobrescrita: exibir() (herdado) chama esta versão em vez da Pagina::gerarCorpo()
    protected function gerarCorpo(): string
    {
        return $this->gerarMensagem()
            . $this->gerarResumo()
            . $this->gerarOrdenacao()
            . $this->gerarListaProdutos()
            . $this->gerarFormulario();
    }

    private function gerarMensagem(): string
    {
        if ($this->mensagem === '') {
            return '';
        }

        $texto = $this->escapar($this->mensagem);

        return "        <p class=\"mensagem mensagem-{$this->tipoMensagem}\">{$texto}</p>\n";
    }

    // Quantidade, preço médio e faixa de preços da categoria
    private function gerarResumo(): string
    {
        $categoria = $this->escapar($this->categoria);
        $quantidade = $this->getQuantidadeProdutos();
        $rotulo = ($quantidade === 1) ? 'sabor' : 'sabores';
        $precoMedio = Produto::formatarMoeda($this->calcularPrecoMedio());

        $faixa = '-';
        if ($quantidade > 0) {
            $precos = $this->obterPrecos();
            $faixa = Produto::formatarMoeda(min($precos)) . ' a ' . Produto::formatarMoeda(max($precos));
        }

        return <<<HTML
        <section class="resumo">
            <h2>{$categoria}</h2>
            <ul>
                <li><strong>{$quantidade}</strong> {$rotulo}</li>
                <li>Preço médio: <strong>{$precoMedio}</strong></li>
                <li>De <strong>{$faixa}</strong></li>
            </ul>
        </section>

HTML;
    }

    // Um link por opção de ordenação; marca a opção atual como ativa
    private function gerarOrdenacao(): string
    {
        $html = "        <nav class=\"ordenacao\">\n            <span>Ordenar por:</span>\n";

        foreach (self::ORDENS_VALIDAS as $chave => $rotulo) {
            $classe = ($chave === $this->ordem) ? ' class="ativo"' : '';
            $html .= "            <a href=\"?ordem={$chave}\"{$classe}>{$rotulo}</a>\n";
        }

        return $html . "        </nav>\n";
    }

    // Parte dinâmica: um cartão por objeto do array, sem HTML fixo por pizza
    private function gerarListaProdutos(): string
    {
        if ($this->getQuantidadeProdutos() === 0) {
            return "        <p class=\"vazio\">Nenhuma pizza cadastrada nesta categoria ainda.</p>\n";
        }

        $html = "        <section class=\"lista-produtos\">\n";

        foreach ($this->obterProdutosOrdenados() as $produto) {
            $html .= $this->gerarCartaoProduto($produto);
        }

        return $html . "        </section>\n";
    }

    // escapar() é herdado da Pagina
    private function gerarCartaoProduto(Produto $produto): string
    {
        $nome = $this->escapar($produto->getNome());
        $descricao = $this->escapar($produto->getDescricao());
        $preco = $produto->getPrecoFormatado();
        $selo = $produto->isVegetariana() ? '<span class="selo">Vegetariana</span>' : '';

        return <<<HTML
            <article class="cartao">
                <div class="cartao-topo">
                    <h3>{$nome}</h3>
                    {$selo}
                </div>
                <p class="descricao">{$descricao}</p>
                <p class="preco">{$preco}</p>
            </article>

HTML;
    }

    // Validações do HTML ajudam o usuário; a validação real fica nos setters do Produto
    private function gerarFormulario(): string
    {
        $maxNome = Produto::TAMANHO_MAXIMO_NOME;
        $maxDescricao = Produto::TAMANHO_MAXIMO_DESCRICAO;
        $precoMaximo = Produto::PRECO_MAXIMO;

        return <<<HTML
        <section class="formulario">
            <h2>Incluir nova pizza</h2>
            <form method="post" action="cardapio.php">
                <input type="hidden" name="acao" value="adicionar">

                <label for="nome">Nome da pizza</label>
                <input type="text" id="nome" name="nome" maxlength="{$maxNome}" placeholder="Ex.: Napolitana" required>

                <label for="descricao">Ingredientes</label>
                <textarea id="descricao" name="descricao" maxlength="{$maxDescricao}" rows="3" placeholder="Ex.: Molho de tomate, mussarela, tomate e parmesão" required></textarea>

                <label for="preco">Preço (R$)</label>
                <input type="number" id="preco" name="preco" min="0.01" max="{$precoMaximo}" step="0.01" placeholder="45.90" required>

                <label class="opcao">
                    <input type="checkbox" name="vegetariana" value="1"> Pizza vegetariana
                </label>

                <button type="submit">Adicionar ao cardápio</button>
            </form>

            <form method="post" action="cardapio.php" class="form-limpar">
                <input type="hidden" name="acao" value="limpar">
                <button type="submit" class="botao-secundario">Remover as pizzas que eu adicionei</button>
            </form>
        </section>

HTML;
    }
}
