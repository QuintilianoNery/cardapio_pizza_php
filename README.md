# Cardápio de Pizzas - PHP Orientado a Objetos

Atividade da disciplina **Programação OO** (Análise e Desenvolvimento de Sistemas - IFES Campus de Alegre, 2026/2).

A aplicação exibe um cardápio de pizzas usando a classe `PaginaProdutos`, subclasse de `Pagina`. O corpo da página é montado dinamicamente a partir de um array de objetos `Produto`, e o usuário pode incluir novas pizzas por um formulário.

## Funcionalidades

- Lista de 6 pizzas predefinidas exibidas em cartões
- Resumo da categoria: quantidade de sabores, preço médio e faixa de preços
- Ordenação por nome, menor preço ou maior preço
- Formulário para incluir novas pizzas (guardadas na sessão do navegador)
- Botão para remover as pizzas incluídas pelo usuário
- Validação dos dados e mensagens de sucesso/erro

## Estrutura do projeto

```
cardapio_pizza_php/
├── Pagina.class.php          # Superclasse: estrutura base da página (cabeçalho, corpo, rodapé)
├── PaginaProdutos.class.php  # Subclasse de Pagina: lista dinâmica de produtos de uma categoria
├── Produto.class.php         # Representa uma pizza (nome, descrição, preço, vegetariana)
├── cardapio.php              # Implementação: trata o formulário, cria os objetos e exibe a página
├── index.php                 # Redireciona para cardapio.php
└── estilo.css                # Estilos visuais
```

### Relação entre as classes

```
Pagina
  └── PaginaProdutos  (extends Pagina)
          └── possui um array de Produto
```

- **`Pagina`**: define título e CSS, o método `exibir()` e os métodos `gerarCabecalho()`, `gerarCorpo()` e `gerarRodape()`.
- **`PaginaProdutos`**: herda tudo de `Pagina` e sobrescreve apenas `gerarCorpo()` para montar resumo, ordenação, cartões e formulário.
- **`Produto`**: atributos privados acessados por getters/setters, que validam os dados e lançam exceção quando algo é inválido.
- **`cardapio.php`**: não gera HTML diretamente; instancia `PaginaProdutos`, adiciona os objetos `Produto` e chama `exibir()`.

### Conceitos de POO aplicados

| Conceito | Onde aparece |
|---|---|
| Classes e objetos | `new PaginaProdutos(...)`, `new Produto(...)` em `cardapio.php` |
| Herança | `class PaginaProdutos extends Pagina` |
| Sobrescrita de método | `PaginaProdutos::gerarCorpo()` substitui `Pagina::gerarCorpo()` |
| Encapsulamento | Atributos `private`/`protected` com getters e setters |
| Construtor | `__construct()` em todas as classes; `parent::__construct()` na subclasse |
| Destrutor | `Pagina::__destruct()` deixa um comentário no fim do HTML gerado |
| Arrays | Array de objetos `Produto`, `array_map`, `array_sum`, `array_merge`, `usort` |
| Exceções | Setters lançam `InvalidArgumentException`, tratada com `try/catch` |

## Requisitos

- PHP 7.4 ou superior (testado no PHP 8.4)
- Extensão `mbstring` habilitada (padrão no XAMPP/WAMP)

## Como executar

### Opção 1: servidor embutido do PHP

Na pasta do projeto, execute:

```bash
php -S localhost:8000
```

Depois acesse no navegador: <http://localhost:8000/cardapio.php>

### Opção 2: XAMPP / WAMP

1. Copie a pasta do projeto para `htdocs` (XAMPP) ou `www` (WAMP).
2. Inicie o Apache.
3. Acesse <http://localhost/cardapio_pizza_php/cardapio.php>.

## Como usar

1. A página abre com o cardápio predefinido.
2. Use os botões **Ordenar por** para reorganizar a lista.
3. Preencha o formulário **Incluir nova pizza** e clique em **Adicionar ao cardápio**.
4. As pizzas incluídas ficam salvas enquanto o navegador estiver aberto (sessão PHP).
5. Clique em **Remover as pizzas que eu adicionei** para voltar ao cardápio original.

> Para ver o destrutor em ação, clique com o botão direito na página e escolha **Exibir código-fonte**: a última linha é o comentário gerado pelo `__destruct()`.
