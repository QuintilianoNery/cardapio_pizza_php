<?php

/**
 * Representa um item do cardápio (uma pizza).
 */
class Produto
{
    // Limites usados nas validações
    public const TAMANHO_MAXIMO_NOME = 50;
    public const TAMANHO_MAXIMO_DESCRICAO = 200;
    public const PRECO_MAXIMO = 999.99;

    // Atributos privados: alteração só pelos setters (encapsulamento)
    private string $nome;
    private string $descricao;
    private float $preco;
    private bool $vegetariana;

    // Valida tudo pelos setters, então nunca existe um Produto inválido
    public function __construct(string $nome, string $descricao, float $preco, bool $vegetariana = false)
    {
        $this->setNome($nome);
        $this->setDescricao($descricao);
        $this->setPreco($preco);
        $this->setVegetariana($vegetariana);
    }

    // ---------- Getters e Setters (lançam exceção se o valor for inválido) ----------

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $nome = trim($nome);

        if (mb_strlen($nome) < 2) {
            throw new InvalidArgumentException('O nome da pizza precisa ter pelo menos 2 caracteres.');
        }

        if (mb_strlen($nome) > self::TAMANHO_MAXIMO_NOME) {
            throw new InvalidArgumentException('O nome da pizza pode ter no máximo ' . self::TAMANHO_MAXIMO_NOME . ' caracteres.');
        }

        $this->nome = $nome;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): void
    {
        $descricao = trim($descricao);

        if ($descricao === '') {
            throw new InvalidArgumentException('Informe os ingredientes da pizza.');
        }

        if (mb_strlen($descricao) > self::TAMANHO_MAXIMO_DESCRICAO) {
            throw new InvalidArgumentException('Os ingredientes podem ter no máximo ' . self::TAMANHO_MAXIMO_DESCRICAO . ' caracteres.');
        }

        $this->descricao = $descricao;
    }

    public function getPreco(): float
    {
        return $this->preco;
    }

    public function setPreco(float $preco): void
    {
        if ($preco <= 0 || $preco > self::PRECO_MAXIMO) {
            throw new InvalidArgumentException('O preço deve estar entre R$ 0,01 e ' . self::formatarMoeda(self::PRECO_MAXIMO) . '.');
        }

        $this->preco = round($preco, 2);
    }

    // Getter de booleano costuma começar com "is"
    public function isVegetariana(): bool
    {
        return $this->vegetariana;
    }

    public function setVegetariana(bool $vegetariana): void
    {
        $this->vegetariana = $vegetariana;
    }

    public function getPrecoFormatado(): string
    {
        return self::formatarMoeda($this->preco);
    }

    // Método estático: chamado pela classe (Produto::formatarMoeda), sem precisar de objeto
    public static function formatarMoeda(float $valor): string
    {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }
}
