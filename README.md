## Como rodar o projeto

- Duplique o arquivo `.env.example` para `.env`
- Utilize o comando `make up` para subir o projeto
- Utilize o comando `make shell` para acessar o terminal do docker, acesse a pasta `web` e use o comando `drush sqlc < ../database/initdb.sql` para importar o banco de dados.
