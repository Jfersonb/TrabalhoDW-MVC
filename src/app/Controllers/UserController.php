<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\ImageUploadService;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use PDOException;

class UserController
{

    // public function list()
    // {
    //     $loader = new FilesystemLoader(__DIR__ . "/../Views");
    //     $twig = new Environment($loader);

    //     $user = new User();
    //     $users = $user->getAll();

    //     echo $twig->render("user/index.html.twig", [
    //         "title" => "Usuários cadastrados",
    //         "users" => $users
    //     ]);
    // }

    public function cadastro()
    {
        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);
        $twig->addGlobal('session', $_SESSION);

        echo $twig->render("user/cadastro.html.twig", [
            "title" => "Cadastro de Usuário",
            "mensagemErro" => $_SESSION['erro_cadastro'] ?? null,
            "mensagemSucesso" => $_SESSION['sucesso_cadastro'] ?? null,
            "valores" => $_SESSION['valores_cadastro'] ?? []
        ]);

        // Limpa mensagens para não reaparecer
        unset($_SESSION['erro_cadastro'], $_SESSION['sucesso_cadastro'], $_SESSION['valores_cadastro']);
    }

    public function processaCadastro()
    {
        session_start(); // garante que a sessão está ativa

        if (!isset($_SESSION['id'])) {
            die("Usuário não autenticado.");
        }

        $idUsuario = $_SESSION['id'];

        // Coleta dados
        $nome = $_POST['nomeCompleto'] ?? '';
        $cpf = $_POST['cpf'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha1 = $_POST['senha1'] ?? '';
        $senha2 = $_POST['senha2'] ?? '';
        $perfil = $_POST['perfil'] ?? '';
        $arquivo = (isset($_FILES['arquivo']) && $_FILES['arquivo']['size'] > 0) ? $_FILES['arquivo'] : null;

        // Salva valores para repopular formulário
        $_SESSION['valores_cadastro'] = $_POST;

        // Validações
        if (!$nome || !$cpf || !$telefone || !$email || !$senha1 || !$senha2 || !$perfil) {
            $_SESSION['erro_cadastro'] = "Preencha todos os campos obrigatórios.";
            header("Location: /user/cadastro");
            exit;
        }

        if ($senha1 !== $senha2) {
            $_SESSION['erro_cadastro'] = "As senhas não conferem.";
            header("Location: /user/cadastro");
            exit;
        }

        if (strlen($senha1) < 8) {
            $_SESSION['erro_cadastro'] = "A senha deve ter pelo menos 8 caracteres.";
            header("Location: /user/cadastro");
            exit;
        }

        try {
            $userModel = new User();

            // Verifica e-mail duplicado
            if ($userModel->getByEmail($email)) {
                $_SESSION['erro_cadastro'] = "E-mail já cadastrado.";
                header("Location: /user/cadastro");
                exit;
            }

            // Inserção
            $id = $userModel->inserir($nome, $cpf, $telefone, $email, $senha1, $perfil, $arquivo);

            if ($id) {
                $_SESSION['id'] = $id;
                $_SESSION['nome'] = $nome;
                $_SESSION['perfil'] = $perfil;

                $_SESSION['sucesso_cadastro'] = "Usuário cadastrado com sucesso.";
                header("Location: /user/cadastro");
                exit;
            } else {
                $_SESSION['erro_cadastro'] = "Erro ao cadastrar, tente novamente.";
                header("Location: /user/cadastro");
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['erro_cadastro'] = "Erro de banco de dados: " . $e->getMessage();
            header("Location: /user/cadastro");
            exit;
        }
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

    // public function edit($id)
    // {
    //     $userModel = new User();
    //     $user = $userModel->getById($id);

    //     if (!$user) {
    //         die("Usuário não encontrado!");
    //     }

    //     $user->path_image = ImageUploadService::getPathImage($user->image);

    //     $loader = new FilesystemLoader(__DIR__ . "/../Views");
    //     $twig = new Environment($loader);

    //     echo $twig->render("user/edit.html.twig", [
    //         "title" => "Alteração de Usuário",
    //         "user" => $user
    //     ]);
    // }


    // public function cadastro($id)
    // {
    //     $userModel = new User();

    //     $user = $userModel->getById($id);

    //     if (!$user) {
    //         die("Usuário não encontrado!");
    //     }

    //     $name = $_POST['name'];
    //     $email = $_POST['email'];
    //     $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    //     $foto = isset($_FILES["image"]) ? $_FILES["image"] : null;

    //     $userModel = new User();

    //     $id = $userModel->atualizar($user->id, $name, $email, $pass, $foto);

    //     if ($id) {
    //         header("Location: /user");
    //         exit();
    //     } else {
    //         die("Ocorreu um erro durante a atualização do cadastro, tente novamente.");
    //     }
    // }

    // public function view($id)
    // {

    //     $userModel = new User();

    //     $user = $userModel->getById($id);

    //     if (!$user) {
    //         die("Usuário não encontrado!");
    //     }

    //     $user->path_image = ImageUploadService::getPathImage($user->image);

    //     $loader = new FilesystemLoader(__DIR__ . "/../Views");
    //     $twig = new Environment($loader);

    //     echo $twig->render("user/view.html.twig", [
    //         "title" => "Visualização de Usuário",
    //         "user" => $user
    //     ]);
    // }

    // public function confirmDelete($id)
    // {

    //     $userModel = new User();

    //     $user = $userModel->getById($id);

    //     if (!$user) {
    //         die("Usuário não encontrado!");
    //     }

    //     $user->path_image = ImageUploadService::getPathImage($user->image);

    //     $loader = new FilesystemLoader(__DIR__ . "/../Views");
    //     $twig = new Environment($loader);

    //     echo $twig->render("user/delete.html.twig", [
    //         "title" => "Exclusão de Usuário",
    //         "user" => $user
    //     ]);
    // }
    // public function delete($id)
    // {
    //     $userModel = new User();

    //     $user = $userModel->getById($id);

    //     if (!$user) {
    //         die("Usuário não encontrado!");
    //     }

    //     $userModel->delete($user->id);

    //     header("Location: /users");
    //     exit();
    // }


}
