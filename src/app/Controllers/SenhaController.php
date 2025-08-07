<?php

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Models\Senha;
use App\Services\SenhaService;
use PDOException;


class SenhaController {

    public function resetSenha()
    {
        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);
        $twig->addGlobal('session', $_SESSION);

        // Limpa mensagem após exibir
        $mensagem = $_SESSION['mensagem'] ?? null;
        unset($_SESSION['mensagem']);

        echo $twig->render("senha/reset.html.twig", [
            "titulo" => "Resetar senha",
            "mensagem" => $mensagem
        ]);
    }

    public function processaReset()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';

            try {
                $service = new SenhaService();
                $resetado = $service->resetarSenha($email);

                if ($resetado) {
                    $_SESSION['mensagem'] = "Nova senha enviada para: $email";
                    header("Location: /senha/reset");
                } else {
                    $_SESSION['mensagem'] = "Nova senha enviada para: $email";
                    header("Location: /senha/reset");
                }
                exit;
            } catch (PDOException $e) {
                $_SESSION['mensagem'] = "Erro ao resetar a senha!.";
                header("Location: /senha/reset");
                exit;
            }
        }
    }
}
