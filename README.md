# Grupo Criar - Desafio Web Back-End

## Sobre o projeto

O projeto é um grupo criar, onde você deve criar uma api para gerenciar estados, cidades, clusters, campanhas, descontos e produtos.

## Tecnologias Utilizadas

- PHP 8.4
- Laravel Framework
- Swoole (Laravel Octane)
- MySQL
- Docker e Docker Compose
- Pest (Testes)

## Instalação

### Requisitos

- [Docker](https://docs.docker.com/engine/install/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Executando o projeto

1. Clone o repositório

```bash
git clone https://github.com/gustavocabreira/grupo-criar-api.git
```

2. Entre na pasta do projeto

```bash
cd grupo-criar-api
```

3. Entre na pasta docker/local

```bash
cd docker/local
```

4. Execute o comando para instalar o projeto

```bash
sh install.sh --app-name=grupo-criar-api
```

5. Após a instalação, utilize o comando para iniciar o projeto

```bash
sh start.sh
```

6. Acesse a aplicação em http://localhost

7. Você pode acessar a documentação do projeto em http://localhost/docs/api
