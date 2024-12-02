# Documentação do Sistema de Controle de Estoque

## Introdução

Este repositório contém o código para um **Sistema de Controle de Estoque** desenvolvido com **Laravel**, **Vue.js**, e **MySQL**. O sistema é responsável pelo cadastro e movimentação de produtos em estoque, com a capacidade de registrar entradas, saídas e gerar relatórios detalhados.

## Estrutura do Projeto

### Backend (Laravel)
- **Framework**: Laravel 11
- **Banco de Dados**: MySQL
- **Autenticação**: Laravel Sanctum para autenticação baseada em tokens
- **Testes**: PHPUnit e Laravel Test Factory

### Frontend (Vue.js)
- **Framework**: Vue.js
- **Gerenciamento de Estado**: Pinia

## Funcionalidades Principais

1. **Cadastro de Produtos**: Permite cadastrar novos produtos, incluindo informações como nome, descrição, preço e quantidade inicial.
2. **Movimentação de Estoque**: Registra entradas e saídas de produtos, associando cada movimento a um usuário.
3. **Relatórios**: Geração de relatórios detalhados de movimentação de estoque.
4. **Autenticação de Usuários**: Sistema de login e gerenciamento de usuários com controle de permissões baseado em roles (administrador, gerente, comum).

## API - Endpoints

A API é construída como uma **API RESTful** e utiliza autenticação via tokens gerados pelo **Laravel Sanctum**. Todos os endpoints exigem um **token de autenticação** que deve ser incluído no cabeçalho da requisição.

### 1. Cadastro de Produto

- **Endpoint**: `POST /api/products`
- **Descrição**: Cria um novo produto no estoque.
- **Requisição**:
  - **Headers**: 
    - `Authorization: Bearer <token>`
  - **Body**:
    ```json
    {
      "name": "Notebook Acer",
      "code": "F100",
      "description": "I5, 8 GB, 512 GB SSD",
      "category_id": 1,
      "supplier_id": 1,
      "cost_price": 900.50,
      "sale_price": 1595.90,
      "min_stock": 100,
      "stock": 50,
      "expiration_date": "2024-11-28"
    }
    ```
- **Resposta**:
  - Status: `201 Created`
  - Corpo:
    ```json
    {
      "id": 1,
      "name": "Notebook Acer",
      "description": "I5, 8 GB, 512 GB SSD",
      "category_id": 1,
      "supplier_id": 1,
      "cost_price": 900.50,
      "sale_price": 1595.90,
      "min_stock": 100,
      "stock": 50,
      "expiration_date": "2024-11-28"
    }
    ```

### 2. Movimentação de Estoque

- **Endpoint**: `POST /api/movements`
- **Descrição**: Registra uma entrada, devolução, saída, perda ou ajuste manual de produto no estoque.
- **Requisição**:
  - **Headers**: 
    - `Authorization: Bearer <token>`
  - **Body**:
    ```json
    {
      "product_id": 1,
      "user_id": 1,
      "type": "entrada",  // ou "saida, devolução, perda, manual"
      "quantity": 10,
      "price": 595.50,
      "reason": "Promoção"
    }
    ```
- **Resposta**:
  - Status: `201 Created`
  - Corpo:
    ```json
    {
      "id": 1,
      "product_id": 1,
      "user_id": 1,
      "type": "entrada",  
      "quantity": 10,
      "price": 595.50,
      "reason": "Promoção"
      "created_at": "2024-11-29T10:00:00Z"
    }
    ```

### 3. Listar Movimentos de Estoque

- **Endpoint**: `GET /api/movements`
- **Descrição**: Retorna todos os movimentos de estoque.
- **Requisição**:
  - **Headers**:
    - `Authorization: Bearer <token>`
- **Resposta**:
  - Status: `200 OK`
  - Corpo:
    ```json
    [
      {
        "id": 1,
        "product_id": 1,
        "type": "entrada",
        "quantity": 10,
        "user_id": 1,
        "price": 595.50,
        "reason": "Promoção",
        "created_at": "2024-11-29T10:00:00Z"
      },
      {
        "id": 2,
        "product_id": 1,
        "type": "saida",
        "quantity": 5,
        "user_id": 2,
        "price": 299.0,
        "reason": "Queima de estoque",
        "created_at": "2024-11-29T12:00:00Z"
      }
    ]
    ```

## Rotas e Controladores

### Produtos

- **Controlador**: `ProductController`
- **Métodos**:
  - `store(Request $request)`: Cria um novo produto.
  - `index()`: Retorna todos os produtos.
  - `show($id)`: Retorna um produto específico.

### Movimentos

- **Controlador**: `MovementController`
- **Métodos**:
  - `store(Request $request)`: Registra um movimento de estoque (entrada, devolução, saída, perda, manual).
  - `index()`: Retorna todos os movimentos de estoque.
  - `index()`: Caso papel igual a usuário comum retorna os movimentos de estoque somente do usuário autenticado.

### Autenticação

- **Controlador**: `AuthController`
- **Métodos**:
  - `login(Request $request)`: Realiza o login e retorna um token de autenticação.
  - `register(Request $request)`: Registra um novo usuário.

## Decisões de Design

1. **Arquitetura MVC**: A aplicação segue o padrão MVC (Model-View-Controller) para separar responsabilidades.
2. **Banco de Dados Relacional**: Utiliza MySQL para persistência de dados, garantindo a integridade e a relação entre produtos, movimentos e usuários.
3. **Autenticação com Laravel Sanctum**: Foi escolhido o Laravel Sanctum para autenticação baseada em tokens, permitindo fácil integração com clientes móveis ou sistemas externos.

## Testes

Os testes automatizados são implementados utilizando o framework PHPUnit e Laravel Test Factory; Cypress no Vue. Existem testes unitários e de integração para garantir a funcionalidade correta das operações principais, como criação de produtos, movimentação de estoque e autenticação de usuários.

## Conclusão

Este sistema foi projetado para ser modular, escalável e fácil de manter. Ele proporciona um controle eficiente sobre o estoque e garante segurança, com autenticação baseada em tokens e controle de permissões por tipos de usuário.
