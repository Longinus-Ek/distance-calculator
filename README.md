# Este projeto tem como finalidade:
  ```
  -Cálculo de distância entre duas geo localizações ordenadas por CEP
  -Consumo API CEPABERTO -> https://www.cepaberto.com/
  -Cache das pesquisas
  -Cadastro e armazenamento das localizações
  ```
# Tecnologias utilizadas:
```
  -Docker
  -PHP 8.1-flm
  -Laravel 10
  -Mysql
  -Redis
  -RabbitMQ
```
# Utilização:
```
Para utilização basta clonar o reposítório e executar o comando:

Em um terminal dentro da pasta raiz do projeto laravel [distance], execute:
composer install
Em um terminal dentro da pasta raiz do VMI Docker [Docker], execute:
docker-compose -d --build

Após subir os containers execute, obs: Dentro do terminal do Docker:
php artisan optimize:clear
php artisan key:generate
php artisan migrate

```
# Acessos:
```
  localhost:8080 -> Aplicação
  localhost:8888 -> phpMyAdmin [user: root, password: root]
```
# AUTOR
  ## Érick Dias - derickbass4@gmail.com
  ## Para melhores informações entrar em contato via whatsapp: (48) 9 9940-2516 

# ERROS

```
  Caso aconteça algum erro se session, basta rodar o comando php artisan optimize:clear no terminal do container setup-php
```
  
  
