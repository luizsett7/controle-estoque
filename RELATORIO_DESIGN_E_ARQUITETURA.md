# Relatório de Design e Arquitetura do Sistema de Controle de Estoque

## 1. Introdução

Este documento descreve as principais decisões de design e arquitetura tomadas para o desenvolvimento do Sistema de Controle de Estoque. O sistema foi implementado utilizando as tecnologias **Laravel**, **Vue.js** e **MySQL**, com o objetivo de fornecer um gerenciamento eficiente de estoque para as empresas, permitindo o cadastro de produtos, controle de entradas e saídas, e a geração de relatórios.

## 2. Arquitetura Geral

### 2.1 Arquitetura em Camadas (Layered Architecture)

O sistema foi estruturado com uma **arquitetura em camadas** para garantir a separação de responsabilidades e facilitar a manutenção e escalabilidade. As camadas principais são:

- **Camada de Apresentação**: Responsável pela interface com o usuário. Utiliza **Vue.js** para renderizar componentes dinâmicos no frontend.
- **Camada de Aplicação**: Contém a lógica de negócios, que é gerenciada pelo **Laravel**. Essa camada é responsável por interagir com o banco de dados, realizar validações, e processar os dados.
- **Camada de Persistência**: Utiliza **MySQL** como sistema de gerenciamento de banco de dados. O Laravel ORM, **Eloquent**, é utilizado para abstrair as operações de banco de dados e interagir com as tabelas do estoque, produtos, entradas, saídas e usuários.

### 2.2 Padrão de Arquitetura MVC

O sistema segue o padrão **MVC (Model-View-Controller)**, que ajuda a separar a lógica de apresentação (View), a lógica de controle (Controller) e a lógica de dados (Model).

- **Model**: As entidades principais, como `Product`, `Movement` e `User`, são representadas por modelos Eloquent. Cada modelo interage diretamente com a tabela correspondente no banco de dados.
- **View**: Utiliza **Vue.js** para a criação de componentes interativos e dinâmicos, como formulários de cadastro e visualização de produtos e movimentos.
- **Controller**: A camada de controle no Laravel lida com as requisições HTTP, interage com os modelos e retorna respostas para a visualização.

### 2.3 Autenticação e Autorização

Para autenticação e controle de acesso, o **Laravel Sanctum** foi utilizado, permitindo a geração de tokens de autenticação para o sistema. Com isso, o sistema garante que apenas usuários autenticados possam acessar determinadas funcionalidades, como a criação de novos produtos ou o registro de entradas e saídas de estoque.

## 3. Decisões de Design

### 3.1 Modelo de Dados

O modelo de dados foi projetado para refletir as operações de controle de estoque de forma eficiente. As principais tabelas e suas relações são:

- **Produtos (`products`)**: Contém informações sobre cada item em estoque, como nome, descrição e quantidade.
- **Movimentos (`movements`)**: Registra as entradas e saídas de produtos, com informações sobre tipo de movimento (entrada ou saída), quantidade e a data do movimento.
- **Usuários (`users`)**: Armazena os dados dos usuários, como nome, email, e tipo de usuário (administrador, gerente e comum).

A tabela `movements` possui um relacionamento com a tabela `products` (muitos para um) e com a tabela `users` (muitos para um), garantindo que cada movimento de estoque seja associado a um produto e a um usuário responsável.

### 3.2 Validação de Dados

As validações de dados são feitas no controlador usando o **Laravel Form Request**. Isso garante que os dados enviados para o servidor estejam no formato correto e atendam a todas as regras de negócio, como:

- Validação de campos obrigatórios (ex: nome do produto, quantidade).
- Verificação de quantidade suficiente em estoque para realizar uma saída.

### 3.3 Controle de Concorrência

O sistema foi projetado para garantir que múltiplos usuários possam interagir com o estoque simultaneamente sem causar inconsistências. Para isso, foram utilizadas **transações** no banco de dados, garantindo que operações de entrada e saída sejam atômicas.

### 3.4 Desempenho e Escalabilidade

A aplicação utiliza **indexação** de campos importantes no banco de dados, como `product_id` e `user_id`, para otimizar consultas frequentes. Além disso, foi adotada a abordagem de **lazy loading** e **eager loading** nas relações Eloquent, garantindo um desempenho eficiente ao carregar os dados necessários.

## 4. Design de Frontend

### 4.1 Vue.js como Framework Frontend

O frontend foi desenvolvido com **Vue.js**, um framework JavaScript que facilita a criação de interfaces de usuário interativas e dinâmicas. As principais decisões de design no frontend incluem:

- **Componentização**: A interface é construída a partir de componentes reutilizáveis. Exemplos de componentes incluem `Product`, `Movement`, e `StockSummary`.
- **Gerenciamento de Estado**: O estado da aplicação é gerenciado utilizando **Pinia**, um sistema de gerenciamento de estado centralizado, para garantir que os dados do usuário sejam sincronizados entre os componentes.
- **Consumo da API**: O Vue.js consome a API RESTful do backend utilizando **Axios**, permitindo a comunicação entre o frontend e o backend para operações como criação de produtos, movimentação de estoque e geração de relatórios.

## 5. Decisões de Segurança

### 5.1 Segurança de Dados

A segurança dos dados é uma prioridade no sistema. Algumas das decisões de segurança incluem:

- **Criptografia de Senhas**: As senhas dos usuários são armazenadas de forma segura utilizando o **Hashing** do Laravel (`bcrypt`), para garantir que as senhas não sejam armazenadas em texto simples.
- **Proteção Contra CSRF**: O Laravel vem com proteção contra ataques de **Cross-Site Request Forgery (CSRF)**, garantindo que apenas requisições legítimas sejam processadas pelo sistema.
- **Controle de Acesso Baseado em Funções**: O sistema possui controle de acesso baseado no tipo de usuário (por exemplo, `admin` pode visualizar todos os produtos e movimentos, enquanto um `user` pode acessar apenas os movimentos relacionados a si).

### 5.2 Autenticação com Tokens

Para autenticar os usuários, foi utilizado o **Laravel Sanctum** para fornecer autenticação baseada em tokens, permitindo que o sistema seja facilmente consumido por clientes móveis ou outras plataformas.

## 6. Testabilidade

### 6.1 Testes Automatizados

O sistema foi projetado com **testes automatizados** em mente, utilizando o framework de testes do Laravel para garantir a confiabilidade do código. A aplicação inclui:

- **Testes Unitários**: São realizados para garantir que os métodos de negócios, como criação de produtos ou movimentação de estoque, funcionem corretamente.
- **Testes de Integração**: Testes que garantem a interação correta entre diferentes partes do sistema, como o backend e o banco de dados.
- **Testes de Interface (Frontend)**: São realizados utilizando ferramentas como **Cypress** para garantir que os componentes da interface funcionem como esperado.

### 6.2 Testes de Performance

A aplicação foi monitorada em termos de desempenho, garantindo que as operações de entrada e saída de estoque sejam rápidas e escaláveis, mesmo com grandes volumes de dados.

## 7. Conclusão

O Sistema de Controle de Estoque foi projetado de maneira modular, escalável e segura, utilizando as melhores práticas de design e arquitetura para garantir uma experiência eficiente e confiável. As escolhas de tecnologia, como **Laravel**, **Vue.js** e **MySQL**, garantem uma solução robusta e flexível, enquanto as decisões de design e arquitetura garantem que o sistema seja fácil de manter e expandir no futuro.
