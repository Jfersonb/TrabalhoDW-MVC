<?php

use App\Controllers\AppController;
use App\Controllers\UserController;
use App\Controllers\MedicamentoController;
// use App\Services\ServicoEmail;
use App\Controllers\SenhaController;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/router.php';

session_start();


// ##################################################
// Rotas elaboradas utilizando a biblioteca PHPRouter - https://phprouter.com/
// ##################################################

get('/', function () {
  $controller = new AppController();
  $controller->index();
});

get('/logout', function(){
  $controller = new UserController();
  $controller->logout();
});

get('/logar', function(){
  $controller = new UserController();
  $controller->logar();
});

post('/logar', function(){
  $controller = new UserController();
  $controller->processaLogin();
});

get('/medicamento/cadastro', function(){
 $controller = new MedicamentoController();
 $controller->cadastro();
});

post('/medicamento/cadastro', function(){
 $controller = new MedicamentoController();
 $controller->processaCadastro();
});

get('/medicamento/lista', function(){
 $controller = new MedicamentoController();
 $controller->lista();
});

get('/informacao', function () {
  $controller = new UserController();
  $controller->informacao();
});

get('/senha/reset', function () {
  $controller = new SenhaController();
  $controller->resetSenha();
  //ServicoEmail::enviar("jeferson.silva2@estudante.ifto.edu.br", "Olá Teste", "Teste de envio");
});

post('/senha/reset', function () {
    $controller = new SenhaController();
    $controller->processaReset();
});

post('/users/create', function () {
  $controller = new UserController();
  $controller->insert();
});

get('/users/$id/update', function ($id) {
  $controller = new UserController();
  $controller->edit($id);
});

post('/users/$id/update', function ($id) {
  $controller = new UserController();
  $controller->update($id);
});

get('/users/$id/delete', function ($id) {
  $controller = new UserController();
  $controller->confirmDelete($id);
});

post('/users/$id/delete', function ($id) {
  $controller = new UserController();
  $controller->delete($id);
});


// For GET or POST
// The 404.php which is inside the views folder will be called
// The 404.php has access to $_GET and $_POST
any('/404', 'views/404.php');
