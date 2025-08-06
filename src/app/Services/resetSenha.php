<?php

namespace App\Services;

use Exception;
class resetSenha {

    public function resetSenha() {
        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);
        $twig->addGlobal('session', $_SESSION);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $novaSenhaPadrao = '123senha';
            //caso precise gerar senhas aleatorias
            //$novaSenhaPadrao = bin2hex(random_bytes(4)); // ex: 8 caracteres aleatórios

            $hashSenha = password_hash($novaSenhaPadrao, PASSWORD_DEFAULT);

            $conn = Conexao::getConexao(); // Sua classe de conexão

            // Verifica se o e-mail existe
            $sql = $conn->prepare("SELECT id FROM cadastroUsers WHERE email = :email");
            $sql->bindValue(":email", $email);
            $sql->execute();
            $usuario = $sql->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                // Atualiza a senha
                $update = $conn->prepare("UPDATE cadastroUsers SET senha = :senha WHERE email = :email");
                $update->bindValue(":senha", $hashSenha);
                $update->bindValue(":email", $email);

                if ($update->execute()) {
                    // Envia e-mail
                    ServicoEmail::enviar($email, "Reset de Senha", "Sua nova senha é: {$novaSenhaPadrao}");

                    header("Location: /logar?msg=sucesso");
                    exit;
                } else {
                    header("Location: /logar?msg=erro");
                    exit;
                }
            } else {
                header("Location: /logar?msg=erro");
                exit;
            }
        }

        // Se for GET, renderiza o formulário
        echo $twig->render("senha/reset.html.twig");
    }
}
