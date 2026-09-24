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
