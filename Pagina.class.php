<?php

/**
 * Superclasse: página HTML genérica (cabeçalho, corpo e rodapé).
 * Subclasses herdam a estrutura e sobrescrevem o que precisarem.
 */
class Pagina
{
    // protected: acessível nesta classe e nas subclasses; private: só nesta classe
    protected string $titulo;
    protected string $corpo = '';
    private string $arquivoCss;

    // Construtor usa os setters para aproveitar as validações
    public function __construct(string $titulo, string $arquivoCss = 'estilo.css')
    {
        $this->setTitulo($titulo);
        $this->setArquivoCss($arquivoCss);
    }

    // Destrutor: chamado ao fim do script; deixa um comentário no código-fonte do HTML
    public function __destruct()
    {
        echo "\n<!-- Página \"" . $this->escapar($this->titulo) . "\" destruída pelo __destruct(). -->\n";
    }

    // ---------- Getters e Setters ----------

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): void
    {
        $titulo = trim($titulo);

        if ($titulo === '') {
            throw new InvalidArgumentException('O título da página não pode ser vazio.');
        }

        $this->titulo = $titulo;
    }

    public function getCorpo(): string
    {
        return $this->corpo;
    }

    public function setCorpo(string $corpo): void
    {
        $this->corpo = $corpo;
    }

    public function getArquivoCss(): string
    {
        return $this->arquivoCss;
    }

    public function setArquivoCss(string $arquivoCss): void
    {
        $this->arquivoCss = $arquivoCss;
    }

    // ---------- Montagem da página ----------

    // Se a subclasse sobrescrever algum gerar*(), o PHP chama a versão dela
    public function exibir(): void
    {
        echo $this->gerarCabecalho();
        echo $this->gerarCorpo();
        echo $this->gerarRodape();
    }

    // Heredoc (<<<HTML ... HTML;) permite escrever HTML com variáveis {$assim}
    protected function gerarCabecalho(): string
    {
        $titulo = $this->escapar($this->titulo);
        $css = $this->escapar($this->arquivoCss);

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$titulo}</title>
    <link rel="stylesheet" href="{$css}">
</head>
<body>
    <header class="topo">
        <h1>{$titulo}</h1>
    </header>
    <main class="conteudo">

HTML;
    }

    // Corpo padrão: conteúdo fixo definido via setCorpo()
    protected function gerarCorpo(): string
    {
        return $this->corpo;
    }

    protected function gerarRodape(): string
    {
        $ano = date('Y');

        return <<<HTML

    </main>
    <footer class="rodape">
        <p>&copy; {$ano} &middot; Página gerada com PHP Orientado a Objetos</p>
    </footer>
</body>
</html>
HTML;
    }

    // Escapa caracteres especiais para evitar HTML/JS injetado (XSS)
    protected function escapar(string $texto): string
    {
        return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
    }
}
