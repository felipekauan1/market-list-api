# 🛒 Market List API

> API REST para gerenciamento de listas de compras mensais — construída com Laravel 13 e MySQL, integrada ao [Auth Service](https://github.com/felipekauan1/auth-service) para autenticação.

## 📋 Sobre o projeto

O **Market List API** é uma API de lista de compras mensais que permite criar listas por mês, adicionar itens com categoria e quantidade, marcar itens como comprados e reutilizar a lista do mês anterior com auto preenchimento.

O projeto foi desenvolvido como portfólio para demonstrar conhecimentos em arquitetura de microsserviços, consumo de APIs externas via middleware, relacionamentos Eloquent e boas práticas de desenvolvimento back-end com Laravel.

> ⚠️ Este projeto depende do [Auth Service](https://github.com/felipekauan1/auth-service) rodando para autenticação. Leia a seção de instalação com atenção.

## ✨ Funcionalidades

| Ação | Descrição |
|---|---|
| **Criar lista** | Cria uma lista de compras para um mês específico |
| **Listar listas** | Retorna todas as listas do usuário autenticado |
| **Ver lista** | Retorna uma lista com todos os seus itens |
| **Adicionar item** | Adiciona um item à lista com nome, categoria e quantidade |
| **Editar item** | Atualiza parcialmente os dados de um item |
| **Marcar como comprado** | Marca um item como comprado |
| **Apagar item** | Remove um item da lista |
| **Auto preencher** | Copia os itens da lista anterior para a lista atual |

## 🛠️ Tecnologias utilizadas

- **PHP 8.5** + **Laravel 13**
- **MySQL** — banco de dados relacional
- **Eloquent ORM** — mapeamento objeto-relacional e relacionamentos
- **Laravel Http Client** — consumo do Auth Service
- **Form Request** — validação separada por operação
- **Middleware personalizado** — validação de token via Auth Service
- **Postman** — testes de endpoints durante desenvolvimento

## 🏗️ Arquitetura

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ShoppingListController.php   # CRUD de listas e autoFill
│   │   └── ItemController.php           # CRUD de itens e purchase
│   ├── Middleware/
│   │   └── EnsureTokenIsValid.php       # Valida token no Auth Service
│   └── Requests/
│       ├── StoreListRequest.php         # Validação na criação de lista
│       ├── StoreItemRequest.php         # Validação na criação de item
│       └── UpdateItemRequest.php        # Validação na edição de item
└── Models/
    ├── ShoppingList.php                 # hasMany Items
    └── Item.php                         # belongsTo ShoppingList

database/
└── migrations/
    ├── create_lists_table.php
    └── create_items_table.php

routes/
└── api.php                              # Todas as rotas protegidas pelo middleware
```

**Relacionamentos:**

```
ShoppingList  →  hasMany   →  Item
Item          →  belongsTo →  ShoppingList
```

**Fluxo de autenticação:**

```
Requisição com token Bearer
        ↓
EnsureTokenIsValid middleware
        ↓
GET http://auth-service/api/validate-token
        ↓
Token válido → user_id injetado na requisição → Controller executa
Token inválido → 401 Unauthorized
```

## 🧠 Decisões técnicas

### Por que um middleware personalizado em vez do Sanctum?
A autenticação está centralizada no Auth Service separado. O middleware `EnsureTokenIsValid` faz uma requisição ao Auth Service para validar o token e injeta o `user_id` na requisição via `$request->merge()`. Assim os Controllers acessam o usuário sem saber nada sobre autenticação.

### Por que `unsignedBigInteger` em vez de `foreignId()->constrained()`?
A tabela `users` não existe nesse projeto — ela vive no Auth Service. Usar `constrained()` causaria erro de chave estrangeira. O `unsignedBigInteger` armazena o ID do usuário sem criar dependência de tabela local.

### Por que `$list->items()->create()` em vez de `Item::create()`?
O método `items()` com parênteses retorna o query builder do relacionamento, que automaticamente preenche o `list_id` correto. É mais seguro e expressivo do que passar o `list_id` manualmente.

### Por que `$request->only()` no update?
Permite edições parciais — o usuário pode atualizar só o nome sem precisar reenviar todos os campos. Campos não enviados preservam os valores atuais no banco.

### Por que autoFill busca por `id != $list->id` e não pelo mês anterior?
Essa abordagem é mais robusta — pega a lista mais recente do usuário independente de meses pulados. Se o usuário não criou lista em março, o autoFill de abril ainda funciona buscando a última lista disponível.

## 🚀 Como rodar localmente

### Pré-requisitos

- PHP 8.3+
- Composer
- MySQL
- **[Auth Service](https://github.com/felipekauan1/auth-service) rodando na porta 8000**

### Instalação

```bash
# 1. Clone o repositório
git clone https://github.com/felipekauan1/market-list-api.git
cd market-list-api

# 2. Instale as dependências
composer install

# 3. Configure o ambiente
cp .env.example .env
php artisan key:generate

# 4. Configure o banco de dados no .env
DB_DATABASE=market_list_api
DB_USERNAME=root
DB_PASSWORD=sua_senha

# 5. Crie o banco e rode as migrations
php artisan migrate
```

### Rodando o projeto

O Auth Service precisa estar rodando antes:

```bash
# Terminal 1 — Auth Service (porta 8000)
cd auth-service
php artisan serve

# Terminal 2 — Market List API (porta 8001)
cd market-list-api
php artisan serve --port=8001
```

## 🔐 Autenticação

Todas as rotas exigem um token válido gerado pelo Auth Service.

**1. Faça login no Auth Service:**
```
POST http://localhost:8000/api/login
Content-Type: application/json

{
    "email": "seu@email.com",
    "password": "sua_senha"
}
```

**2. Use o token retornado em todas as requisições:**
```
Authorization: Bearer {token}
```

## 📡 Endpoints da API

**Base URL:** `http://localhost:8001/api`

> Todas as rotas exigem o header: `Authorization: Bearer {token}`

### Listas

#### Criar lista
```
POST /lists
Content-Type: application/json

{
    "month": "2026-06"
}
```

**Resposta (201):**
```json
{
    "lista": {
        "id": 1,
        "user_id": 1,
        "month": "2026-06",
        "created_at": "2026-06-07T..."
    }
}
```

#### Listar todas as listas
```
GET /lists
```

#### Ver lista com itens
```
GET /lists/{id}
```

**Resposta (200):**
```json
{
    "lista": {
        "id": 1,
        "user_id": 1,
        "month": "2026-06",
        "items": [
            {
                "id": 1,
                "name": "Arroz",
                "category": "Mercearia",
                "quantity": 2,
                "purchased": false,
                "notes": "Arroz tipo 1"
            }
        ]
    }
}
```

#### Auto preencher com lista anterior
```
POST /lists/{id}/auto-fill
```

**Resposta (200):**
```json
{
    "message": "Itens copiados com sucesso"
}
```

### Itens

#### Adicionar item
```
POST /lists/{list}/items
Content-Type: application/json

{
    "name": "Arroz",
    "category": "Mercearia",
    "quantity": 2,
    "notes": "Arroz tipo 1"
}
```

**Categorias disponíveis:** `Açougue e Peixaria`, `Laticínios e Frios`, `Mercearia`, `Padaria`, `Bebidas`, `Limpeza`, `Higiene e Beleza`, `Pet Shop`, `Utilidades Domésticas`

#### Editar item
```
PUT /lists/{list}/items/{item}
Content-Type: application/json

{
    "quantity": 3
}
```

Todos os campos são opcionais — envie apenas o que deseja atualizar.

#### Marcar como comprado
```
PATCH /lists/{list}/items/{item}/purchase
```

**Resposta (200):**
```json
{
    "item": {
        "id": 1,
        "purchased": true
    }
}
```

#### Apagar item
```
DELETE /lists/{list}/items/{item}
```

**Resposta (200):**
```json
{
    "mensagem": "Item deletado com sucesso!"
}
```

## 🔗 Projetos relacionados

| Projeto | Descrição |
|---|---|
| **[Auth Service](https://github.com/felipekauan1/auth-service)** | Serviço de autenticação consumido por esta API |

## 📌 Possíveis melhorias futuras

- Histórico de listas por ano
- Estatísticas de gastos por categoria
- Compartilhar lista com outro usuário
- Notificação quando todos os itens forem comprados
- Testes automatizados com PHPUnit

## 👨‍💻 Autor

Desenvolvido por **[@felipekauan1](https://github.com/felipekauan1)**

## 📄 Licença

Este projeto está sob a licença MIT.
