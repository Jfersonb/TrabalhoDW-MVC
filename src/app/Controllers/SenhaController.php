<?php

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Models\Senha;
use PDOException;

class ResetSenhaController
{
    public function formulario()
    {
        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);

        echo $twig->render("resetsenha/formulario.html.twig", [
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
