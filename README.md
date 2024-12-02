# Controle de Estoque

Este projeto é um sistema de controle de estoque desenvolvido utilizando **Laravel** para o backend e **Vue.js** para o frontend. O sistema permite gerenciar produtos, categorias, fornecedores e movimentações de estoque, com funcionalidades como cadastro, edição, exclusão e visualização de informações.

---

## Funcionalidades

<ul>
  <li><strong>Gestão de Produtos</strong>:
    <ul>
      <li>Cadastro de produtos com informações como nome, categoria, fornecedor, preço e quantidade.</li>
      <li>Listagem, edição e exclusão de produtos.</li>
      <li>Alerta para produtos com estoque baixo ou data de validade próxima.</li>
    </ul>
  </li>
  <li><strong>Gestão de Categorias</strong>:
    <ul>
      <li>Cadastro, edição e exclusão de categorias de produtos.</li>
      <li>Relatório de estoque por categoria.</li>
    </ul>
  </li>
  <li><strong>Gestão de Fornecedores</strong>:
    <ul>
      <li>Cadastro, edição e exclusão de fornecedores.</li>
    </ul>
  </li>
  <li><strong>Movimentações de Estoque</strong>:
    <ul>
      <li>Registro de entradas e saídas de estoque.</li>
      <li>Relatórios de movimentação, incluindo motivo, usuário responsável e data.</li>
      <li>Cálculo de saldo atual por produto, categoria e fornecedor.</li>
    </ul>
  </li>
  <li><strong>Autenticação e Controle de Acesso</strong>:
    <ul>
      <li>Login e registro de usuários utilizando Laravel Sanctum.</li>
      <li>Controle de permissões por tipo de usuário (admin, gerente, usuário comum).</li>
    </ul>
  </li>
</ul>

---

## Tecnologias Utilizadas

<h3>Backend:</h3>
<ul>
  <li><strong>Laravel 11</strong></li>
  <li><strong>Sanctum</strong> (para autenticação)</li>
  <li><strong>Eloquent ORM</strong> (para manipulação do banco de dados)</li>
  <li><strong>MySQL</strong> (banco de dados)</li>
</ul>

<h3>Frontend:</h3>
<ul>
  <li><strong>Vue.js 3</strong></li>
  <li><strong>Axios</strong> (para chamadas API)</li>
  <li><strong>Bootstrap 5</strong> (para estilização)</li>
</ul>

---

## Requisitos

<h3>Pré-requisitos:</h3>
<ul>
  <li><strong>PHP >= 8.1</strong></li>
  <li><strong>Composer</strong></li>
  <li><strong>Node.js >= 16</strong></li>
  <li><strong>MySQL</strong></li>
</ul>

---

## Instalação e Configuração

<h3>Backend (Laravel):</h3>

<ol>
  <li>Clone o repositório:
    <pre><code>git clone https://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@github.com/luizsett7/controle-estoque.git    
cd controle-estoque</code></pre>
  </li>
  <li>Instale as dependências do Laravel:
    <pre><code>cd estoque-api</code></pre>
    <pre><code>composer install</code></pre>
  </li>
  <li>Configure o arquivo <code>.env</code>:
    <ul>
      <li>Copie o <code>.env.example</code>:
        <pre><code>cp .env.example .env</code></pre>
      </li>
      <li>Configure as credenciais do banco de dados.</li>
    </ul>
  </li>
  <li>Gere a chave da aplicação:
    <pre><code>php artisan key:generate</code></pre>
  </li>  
  <li>Execute as migrações e seeders:
    <pre><code>php artisan migrate --seed</code></pre>
  </li>
  <li>Inicie o servidor de desenvolvimento:
    <pre><code>php artisan serve</code></pre>
  </li>
</ol>

<h3>Frontend (Vue.js):</h3>

<ol>
  <li>Acesse a pasta <code>frontend</code>:
    <pre><code>cd estoque-frontend</code></pre>
  </li>
  <li>Instale as dependências:
    <pre><code>npm install</code></pre>
  </li>
  <li>Inicie o servidor de desenvolvimento:
    <pre><code>npm run dev</code></pre>
  </li>
</ol>

---

## Uso

<ol>
  <li>Acesse a aplicação no navegador:
    <ul>
      <li><a href="http://localhost:5173">http://localhost:5173</a></li>
    </ul>
  </li>
  <li>Faça login com o usuário: admin@admin.com e senha: 12345678 para acessar as funcionalidades.</li>
</ol>

---

## Estrutura do Projeto

<h3>Backend:</h3>
<ul>
  <li><strong>App/Http/Controllers</strong>: Controladores das rotas da aplicação.</li>
  <li><strong>App/Models</strong>: Modelos para interagir com o banco de dados.</li>
  <li><strong>Routes/api.php</strong>: Rotas do backend.</li>
</ul>

<h3>Frontend:</h3>
<ul>
  <li><strong>src/components</strong>: Componentes Vue.js.</li>
  <li><strong>src/views</strong>: Páginas principais.</li>
  <li><strong>src/services</strong>: Configuração do Axios para comunicação com a API.</li>
</ul>

---

## Testes Unitários e de Integração

<h3>Backend:</h3>
<ul>
  <li>php artisan test</li>
</ul>

<h3>Frontend:</h3>
<ul>  
  <li>npx cypress open</li>
</ul>

---

## Melhorias Futuras

<ul>
  <li>Implementação de gráficos para visualização do saldo de estoque.</li>
  <li>Relatórios em PDF para exportação.</li>
  <li>Notificações em tempo real para movimentações.</li>
</ul>

---

## Contribuição

<ol>
  <li>Faça um fork do projeto.</li>
  <li>Crie uma branch para sua funcionalidade:
    <pre><code>git checkout -b feature/minha-funcionalidade</code></pre>
  </li>
  <li>Faça o commit das suas alterações:
    <pre><code>git commit -m "Adicionei nova funcionalidade"</code></pre>
  </li>
  <li>Envie para o repositório remoto:
    <pre><code>git push origin feature/minha-funcionalidade</code></pre>
  </li>
  <li>Abra um pull request.</li>
</ol>

---

## Licença

Este projeto é distribuído sob a licença MIT.

---

<strong>Desenvolvido com ❤️ por [Luiz Guilherme]</strong>
