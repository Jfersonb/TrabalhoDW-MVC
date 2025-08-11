<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\CadastroFamilia;
use App\Models\CadastroCuidador;
use App\Models\CadastroEnfermeiro;
use App\Models\CadastroMedico;
use App\Models\CadastroIdoso;
use App\Services\FileUploadService;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Exception;

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
        try {
            // Verifica se usuário está logado
            if (!isset($_SESSION['id'])) {
                die("Usuário não autenticado.");
            }

            $idUsuario = $_SESSION['id'];

            // Coleta os dados do formulário
            $tipo_usuario = $_POST['userType'] ?? '';
            $nome = trim($_POST['nomeCompleto'] ?? '');
            $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
            $telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha1'] ?? '';
            $senha2 = $_POST['senha2'] ?? '';
            $arquivo = $_FILES['arquivo'] ?? null;

            // Validações básicas
            if ($senha !== $senha2) {
                throw new Exception("As senhas não coincidem.");
            }
            if (strlen($senha) < 8) {
                throw new Exception("A senha deve ter pelo menos 8 caracteres.");
            }

            // Valida arquivo
            if (!$arquivo || $arquivo['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Erro ao enviar o arquivo.");
            }
            $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            $permitidos = ['pdf', 'jpg', 'jpeg', 'png'];
            if (!in_array($extensao, $permitidos)) {
                throw new Exception("Tipo de arquivo não permitido.");
            }
            if ($arquivo['size'] > 10 * 1024 * 1024) {
                throw new Exception("Arquivo muito grande. Máximo 10MB.");
            }

            // Cadastra usuário na tabela principal
            $userModel = new User();
            $id_usuario = $userModel->inserir($nome, $cpf, $telefone, $email, $senha, $arquivo);

            // Insere dados específicos
            switch ($tipo_usuario) {
                case "1": // Familiar
                    $parentesco = $_POST['tipoParentesco'] ?? '';
                    $endereco = $_POST['endereco'] ?? '';
                    CadastroFamilia::inserir($id_usuario, $parentesco, $endereco);
                    break;
                case "2": // Cuidador
                    $cursos = $_POST['cursos'] ?? '';
                    CadastroCuidador::inserir($id_usuario, $cursos);
                    break;
                case "3": // Enfermeiro
                    $coren = $_POST['coren'] ?? '';
                    $cip = $_POST['cip'] ?? '';
                    CadastroEnfermeiro::inserir($id_usuario, $coren, $cip);
                    break;
                case "4": // Médico
                    $crm = $_POST['crm'] ?? '';
                    CadastroMedico::inserir($id_usuario, $crm);
                    break;
                case "5": // Idoso
                    $responsavel = $_POST['responsavelLegal'] ?? '';
                    $condicao = $_POST['condicaoMedicaImportante'] ?? '';
                    $medicamentos = $_POST['medicamentosUso'] ?? '';
                    $restricao = $_POST['resticaoAlimentar'] ?? '';
                    $alergias = $_POST['alergias'] ?? '';
                    CadastroIdoso::inserir($id_usuario, $responsavel, $condicao, $medicamentos, $restricao, $alergias);
                    break;
                default:
                    throw new Exception("Tipo de usuário inválido.");
            }

            // Redireciona com sucesso
            $_SESSION['sucesso_cadastro'] = "Cadastro realizado com sucesso!";
            header("Location: /Index.php?msg=sucesso");
            exit;
        } catch (Exception $e) {
            $_SESSION['erro_cadastro'] = $e->getMessage();
            $_SESSION['valores_cadastro'] = $_POST;
            header("Location: /user/cadastro");
            exit;
        }
    }



    private function render($view, $params = [])
    {
        global $twig; // se estiver usando container, injete
        return $twig->render($view, $params);
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
            } catch (Exception $e) {
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
