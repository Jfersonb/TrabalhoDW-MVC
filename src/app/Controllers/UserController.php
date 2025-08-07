<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\ImageUploadService;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use PDOException;

class UserController
{

    public function list()
    {

        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);

        $user = new User();

        $users = $user->getAll();

        echo $twig->render("user/index.html.twig", [
            "title" => "Usuários cadastrados",
            "users" => $users
        ]);
    }

    public function create()
    {
        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);

        echo $twig->render("user/create.html.twig", [
            "title" => "Cadastro de Usuário"
        ]);
    }

    public function insert()
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        //Caso continui dando erro voltar
        // $pass = $_POST['password'];
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $foto = isset($_FILES["image"]) ? $_FILES["image"] : null;

        if (is_array($foto)) {
            $foto = $_FILES["image"]['size'] > 0 ? $_FILES["image"] : null;
        }

        $userModel = new User();

        $id = $userModel->inserir($name, $email, $pass, $foto);

        if ($id) {
            header("Location: /users/$id");
            exit();
        } else {
            die("Ocorreu um erro durante o cadastro, tente novamente.");
        }
    }

    public function edit($id)
    {

        $userModel = new User();

        $user = $userModel->getById($id);

        if (!$user) {
            die("Usuário não encontrado!");
        }

        $user->path_image = ImageUploadService::getPathImage($user->image);

        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);

        echo $twig->render("user/edit.html.twig", [
            "title" => "Alteração de Usuário",
            "user" => $user
        ]);
    }


    public function update($id)
    {
        $userModel = new User();

        $user = $userModel->getById($id);

        if (!$user) {
            die("Usuário não encontrado!");
        }

        $name = $_POST['name'];
        $email = $_POST['email'];
        //Caso continui dando erro voltar
        //$pass = $_POST['password'];
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $foto = isset($_FILES["image"]) ? $_FILES["image"] : null;

        $userModel = new User();

        $id = $userModel->atualizar($user->id, $name, $email, $pass, $foto);

        if ($id) {
            header("Location: /users");
            exit();
        } else {
            die("Ocorreu um erro durante a atualização do cadastro, tente novamente.");
        }
    }



    public function view($id)
    {

        $userModel = new User();

        $user = $userModel->getById($id);

        if (!$user) {
            die("Usuário não encontrado!");
        }

        $user->path_image = ImageUploadService::getPathImage($user->image);

        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);

        echo $twig->render("user/view.html.twig", [
            "title" => "Visualização de Usuário",
            "user" => $user
        ]);
    }



    public function confirmDelete($id)
    {

        $userModel = new User();

        $user = $userModel->getById($id);

        if (!$user) {
            die("Usuário não encontrado!");
        }

        $user->path_image = ImageUploadService::getPathImage($user->image);

        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);

        echo $twig->render("user/delete.html.twig", [
            "title" => "Exclusão de Usuário",
            "user" => $user
        ]);
    }
    public function delete($id)
    {
        $userModel = new User();

        $user = $userModel->getById($id);

        if (!$user) {
            die("Usuário não encontrado!");
        }

        $userModel->delete($user->id);

        header("Location: /users");
        exit();
    }

    public function logout()
    {
        session_unset();

        header("Location: /");
        exit();
    }

    public function logar($mensagemErro = "")
    {
        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);
        $twig->addGlobal('session', $_SESSION);

        echo $twig->render("user/logar.html.twig", [
            "titulo" => "Acessar o sistema",
            "mensagemErro" => $mensagemErro
        ]);
    }

    public function processaLogin()
    {

        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);
        $twig->addGlobal('session', $_SESSION);

        if (isset($_POST["email"], $_POST["password"])) {
            $email = trim($_POST["email"]);
            $senhaDigitada = $_POST["password"];

            try {
                $userModel = new User();
                $User = $userModel->getByEmail($email);

                if ($User) {
                    // Verificar a senha usando SHA2
                    //Caso continui dando erro voltar
                    //$senhaDigitadaHash = hash("sha256", $senhaDigitada);

                    //Caso continui dando erro voltar
                    //if($senhaDigitadaHash === $User["senha"]){
                    if (password_verify($senhaDigitada, $User["senha"])) {

                        $_SESSION["logado"] = true;
                        $_SESSION["id"] = $User["id"];
                        $_SESSION["nome"] = $User["nome"];
                        $_SESSION["perfil"] = $User["perfil"];
                        header("Location: /");
                        exit;
                    } else {
                        $this->logar("Senha ou usuário incorreto!");
                    }
                } else {
                    $this->logar("Senha ou usuário incorreto!");
                }
            } catch (PDOException $e) {
                $mensagemErro = "Erro de banco de dados: " . $e->getMessage();
            }
        }
    }

    public function informacao($mensagemErro = "")
    {
        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);
        $twig->addGlobal('session', $_SESSION);

        echo $twig->render("user/informacao.html.twig", [
            "titulo" => "Informações sobre o sistema"
        ]);
    }
}
