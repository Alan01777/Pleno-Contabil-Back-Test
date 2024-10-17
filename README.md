# Backend - Pleno Contabilidade Mobile App

Este projeto foi desenvolvido para suportar o frontend (aplicativo móvel) do `Pleno Contabilidade`.

## Visão Geral

O principal objetivo do projeto é fornecer e gerenciar arquivos em um bucket (container MinIO), além de fazer o gerenciamento de usuários cadastrados no aplicativo.

### Funcionalidades Básicas

O projeto inclui as seguintes funcionalidades básicas:

* CRUD de usuários clientes do escritório;
* Gerenciamento de arquivos para cada cliente cadastrado;
* Envio de e-mails com tokens para a realização de reset de senhas dos usuários cadastrados;
* Envio de notificações específicas, em datas específicas, para usuários com o aplicativo instalado.

## Serviços Utilizados no Projeto

O projeto utiliza quatro serviços para seu funcionamento completo. Esses serviços podem ser analisados em mais detalhes no arquivo `compose.yaml` do Docker. Os serviços são:

* **app:** Container onde a API é executada. Utiliza a imagem `php:8.3-fpm-alpine` como base. A imagem final utilizada pelo container é gerada automaticamente ao executar o workflow `push_image.yaml`, localizado no diretório `.github/workflows`.
* **nginx:** Container utilizado para servir a aplicação aos usuários. Utiliza o método de "reverse-proxy" para que o nginx possa servir a API adequadamente.
* **db:** Container utilizado como banco de dados do projeto. Utiliza a imagem `postgres:alpine3.20` como base.
* **minio:** Container utilizado como bucket para armazenar e servir os arquivos dos usuários. Também é possível acessar sua interface gráfica para uma melhor experiência no gerenciamento dos arquivos.

### Bucket MinIO

Cada usuário cadastrado terá diretórios próprios criados automaticamente dentro do bucket. Os diretórios são:

- PESSOAL/CONTRATOS
- FISCAL/DAS
- FISCAL/PARCELAMENTO
- FISCAL/PIS
- FISCAL/COFINS
- FISCAL/ICMS
- PESSOAL/FOLHAS
- PESSOAL/FGTS
- CERTIDOES
- EMPRESA
- FATURAMENTOS

### Nginx

Utilizamos o Nginx como reverse-proxy em conjunto com o PHP-fpm para permitir a comunicação adequada com o Laravel. As requisições chegam através do Nginx, são processadas pelo container do Laravel e a resposta é enviada ao cliente pelo Nginx.

Os arquivos de configuração do Nginx estão localizados no diretório `nginx/conf.d`. Lá, você encontrará dois arquivos:

- **app.conf:** Este arquivo foi criado para funcionar apenas em ambientes de desenvolvimento. Ao usar este arquivo, o Nginx usará o protocolo HTTP para se comunicar, atuando apenas como um reverse-proxy.
- **app.conf.example:** Este arquivo serve como exemplo de como configurar o arquivo anterior para um ambiente de produção, usando o protocolo HTTPS para uma conexão mais segura. É necessário ter um domínio e um certificado SSL para usar esta configuração.

Se você deseja usar a configuração para o ambiente de desenvolvimento, não é necessário fazer alterações nos arquivos de configuração.

Se você deseja usar o protocolo HTTPS, precisará personalizar o arquivo `app.conf` com suas configurações. Recomendamos usar o arquivo `app.conf.example` como modelo.

### DB

Este é o container do banco de dados. Por padrão, o container é exposto para fora da rede do Docker, ou seja, fica acessível para usuários externos. Isso pode ser alterado removendo as seguintes linhas do arquivo `compose.yaml`:

```yaml
    ports:
        - 5432:5432
```

### App

Container principal da aplicação. Usa o PHP-fpm como imagem base, além do framework laravel 11 para sua execução.

## Configuração/Setup

Antes de subir os containers do projeto, é preciso criar um arquivo `.env`. Nele, serão preenchidas todas as variáveis de ambiente necessárias para a execução do projeto. Um arquivo `.env.example` está disponível para servir de modelo.

### Banco de Dados

```text
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=example
DB_USERNAME=user_example
DB_PASSWORD=password_example
```

As credencias a serem usadas pelo banco de dados.

### Emails

```text
MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_PORT=your_port
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your.email@email.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Preencha com as informações do seu mail provider, como brevo, mailchimp, etc.

### Minio - Bucket

```text
MINIO_ENDPOINT=http://bucket:9000
MINIO_USE_PATH_STYLE_ENDPOINT=true
MINIO_ACCESS_KEY=some_key
MINIO_SECRET_KEY=some_secret_key
MINIO_DEFAULT_REGION=us-east-1
MINIO_BUCKET=your_bucket
MINIO_ROOT_USER=your_user
MINIO_ROOT_PASSWORD=your_password
MINIO_DEFAULT_BUCKET=your_default_bucket
```

Use estes campos para configurar o container do bucket. Para gerar as credenciais de `MINIO_ACCESS_KEY` e `MINIO_SECRET_KEY`, primeiro é ncessário acessar a interface do container através do navegador, em: `http://<your_host>:9001`. Entre com as credencias de usuário e senha configurados anteriormente, prossiga para Access Keys > Create.

Serão geradas duas chaves, as quais deverão ser inseridas nos campos `MINIO_ACCESS_KEY` e `MINIO_SECRET_KEY` do arquivo `.env`. Reinicie os containers e o container `app` deverá ter as permissões necessárias para usar o bucket.

### Subindo os Containers

Com o arquivo `.env` propriamente criado e configurado, suba os containers usando o docker:
`docker compose up -d`.

## Endpoints da API

A aplicação roda nas portas 80 (HTTP) e 443 (HTTPS).

### Autenticação

* `POST /auth/login`: Faz o login de um usuário.
  * Corpo da Solicitação:

    ```json
    {
            "email": "usuario@exemplo.com",
            "password": "senha123"
    }
    ```

* `POST /auth/register`: Registra um novo usuário.
  * Corpo da Solicitação:

    ```json
    {
            "cnpj": "00.000.000/0000-00",
            "nome_fantasia": "Exemplo Teste",
            "razao_social": "Teste Exemplo",
            "endereco": "Algum lugar bonito",
            "telefone": "(00) 00000-0000",
            "email": "usuario@exemplo.com",
            "password": "senha_segura",
            "c_password": "senha_segura"
    }
    ```

* `POST /auth/logout`: Faz o logout do usuário atual (requer autenticação).

* `GET /auth/user`: Obtém os detalhes do usuário atual (requer autenticação).

#### Recuperação de Senha

Estes endpoints requerem autenticação.

* `POST /auth/password/recovery`: Envia um token de recuperação de senha.
  * Corpo da Solicitação:

    ```json
    {
            "email": "usuario@exemplo.com"
    }
    ```

* `POST /auth/password/validate`: Valida um token de recuperação de senha.
  * Corpo da Solicitação:

    ```json
    {
            "token": "tokenDeRecuperacao"
    }
    ```

* `PUT /auth/password/reset`: Redefine a senha de um usuário.
  * Corpo da Solicitação:

    ```json
    {
            "password": "senha_segura",
            "c_password": "senha_segura"
    }
    ```

### Operações de Arquivo

Estes endpoints requerem autenticação.

* `GET /files/{directory?}`: Lista arquivos em um diretório.
* `GET /file/{path}`: Obtém um arquivo específico.
* `POST /file/upload`: Faz o upload de um arquivo.
  * Corpo da Solicitação: Dados do formulário com arquivo.
* `DELETE /file/{path}`: Deleta um arquivo específico.

### Notificações de Token de Push

* `POST /pushToken`: Armazena um token de notificação de push (requer autenticação).
  * Corpo da Solicitação:

    ```json
    {
            "token": "tokenDeNotificacaoPush"
    }
    ```

### Operações de Usuário

Estes endpoints requerem autenticação.

* `GET /user`: Obtém os detalhes do usuário atual.
* `PUT /user/update`: Atualiza os detalhes do usuário atual.
  * Corpo da Solicitação:

    ```json
    {
            "cnpj": "00.000.000/0000-00",
            "nome_fantasia": "Exemplo Teste",
            "razao_social": "Teste Exemplo",
            "endereco": "Algum lugar bonito",
            "telefone": "(00) 00000-0000",
            "email": "usuario@exemplo.com",
            "password": "senha_segura",
    }
    ```

### Endpoints - Containers
