<?php
namespace App\Models;

use App\Models\BD;
use App\Services\FileUploadService;
use Exception;
use PDO;

class Senha{

    public function buscarPorEmail($email)
    {
        $conn = BD::getConnection();
        $sql = $conn->prepare("SELECT id FROM cadastroUsers WHERE email = :email");
        $sql->bindValue(":email", $email);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function resetarSenha($email, $novaSenha)
    {
        $conn = BD::getConnection();
        $hashSenha = password_hash($novaSenha, PASSWORD_DEFAULT);

        $sql = $conn->prepare("UPDATE cadastroUsers SET senha = :senha WHERE email = :email");
        $sql->bindValue(":senha", $hashSenha);
        $sql->bindValue(":email", $email);
        return $sql->execute();
    }
}