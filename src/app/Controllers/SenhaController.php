<?php

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Services\FileUploadService;
use App\Models\Senha;
use PDOException;

class SenhaController{
    
    public function formulario()
    {
        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);
        $twig->addGlobal('session', $_SESSION);

        

        echo $twig->render("senha/reset.html.twig", [
            "titulo" => "Resetar senha"
        ]);
    }

    public function processaReset()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $novaSenhaPadrao = '123senha';

            try {
                $senha = new senha();
                $senha = $senha->buscarPorEmail($email);

                if ($senha) {
                    $senha->resetarSenha($email, $novaSenhaPadrao);
                    header("Location: /login?msg=sucesso");
                    exit;
                } else {
                    header("Location: /reset?msg=email-nao-encontrado");
                    exit;
                }
            } catch (PDOException $e) {
                error_log("Erro ao resetar senha: " . $e->getMessage());
                header("Location: /reset?msg=erro");
                exit;
            }
        }
    }
}
