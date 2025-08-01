CRUD PHP com MVC
===================================
O repositório está em um container docker com:
* __PHP 8.4__
* __MySQL__
* __PHPMyAdmin__

Para o gerenciamento das rotas foi utilizado o pacote *https://phprouter.com/*

Nas views, foi utilizado o **Twig**, um pacote para gerencimanto de templates.

Para executar o projeto, é preciso antes instalar as depedências do composer, para isso basta executar os seguintes comandos:

> ### Acessar a pasta src ###
> > **cd ./src/**
>
> ### Instalar as dependências do composer ###
> > **composer install**
> 
> ### Para subir o container ###
> 
> volte para a pasta raiz do repositório
> 
> > **cd ..**
> 
> Para subir e criar o container
> 
> > **docker-compose up --build**

Container configurado. 

Para acessar o sistema:
* localhost:8080

Para acessar o phpmyadmin:
* localhost:8081

Ao abrir o PhpMyAdmin, é preciso executar o script **BD.sql** para criar a estrutura do banco de dados


Nas próximas execuções basta apenas executar o comando 
> **docker-compose up**

