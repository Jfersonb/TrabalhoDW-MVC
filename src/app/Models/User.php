<?php

namespace App\Models;

use App\Models\BD;
use App\Services\ImageUploadService;
use Exception;
use PDO;

class User
{

    /**
     * Busca todos os usuários cadastrados
     * @return array
     */
    public static function getAll()
    {
        $conn = BD::getConnection();

        $sql = $conn->query("SELECT * FROM cadastroUsers");

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id)
    {
        $conn = BD::getConnection();

        $sql = $conn->prepare("SELECT * FROM cadastroUsers WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public static function getByEmail($email)
    {
        $conn = BD::getConnection();

        $sql = $conn->prepare("SELECT * FROM cadastroUsers WHERE email = :email LIMIT 1");
        $sql->bindValue(':email', $email);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Função para inserir usuário no banco
     * @param mixed $nome
     * @param mixed $email
     * @param mixed $senha
     * @param mixed $arquivo
     * @return int
     */
    public function inserir($nome, $cpf, $telefone, $email, $senha, $arquivo = null)
    {
        $conn = BD::getConnection();
        //criptografa a senha
        $hash = self::hashSenha($senha);

        //Upload da arquivo
        if (is_array($arquivo) and is_uploaded_file($arquivo['tmp_name'])) {
            $arquivo = ImageUploadService::uploadImage($arquivo);
        }

        //Executa o sql de inserção
        $sql = "INSERT INTO cadastroUsers (nome, cpf, telefone, email, senha, perfil, arquivo)
            VALUES (:nome, :cpf, :telefone, :email, :arquivo)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':cpf', $cpf);
        $stmt->bindValue(':telefone', $telefone);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':senha', $hash);
        $stmt->bindValue(':arquivo', $arquivo);
        $stmt->execute();

        //Retorna o ID do usuário criado
        return $conn->lastInsertId();
    }

    /**
     * Função para atualizar o usuário
     * @param mixed $id
     * @param mixed $nome
     * @param mixed $email
     * @param mixed $senha
     * @param mixed $arquivo
     * @throws \Exception
     * @return bool
     */
    public function atualizar($id, $nome, $cpf, $telefone, $email, $senha = null, $arquivo = null)
    {
        $conn = BD::getConnection();

        //Consulta ao BD
        $user = self::getById($id);
        if (!$user) {
            throw new Exception("Usuário não encontrado");
        }

        // if ($senha) {
        //     //criptografa a senha
        //     $hash = self::hashSenha($senha);
        // } else {
        //     $hash = $user->password;
        // }

        // mantém senha antiga se não informada
        $hash = $user['senha'];
        if ($senha) {
            $hash = self::hashSenha($senha);
        }

        //Upload da arquivo, caso seja atualizada
        if (is_array($arquivo) && is_uploaded_file($arquivo['tmp_name'])) {
            $filename = ImageUploadService::uploadImage($arquivo);
            // deletar antigo se existir
            if (!empty($user['arquivo'])) {
                ImageUploadService::deleteImage($user['arquivo']);
            }
        } else {
            $filename = $user['arquivo'];
        }

        //Executa o sql de inserção
        $sql = $conn->prepare("UPDATE cadastroUsers SET nome = :nome, cpf = :cpf, telefone = :telefone, email = :email, senha = :senha, arquivo = :arquivo WHERE id = :id");
        $sql->bindValue(':nome', $nome);
        $sql->bindValue(':cpf', $cpf);
        $sql->bindValue(':telefone', $telefone);
        $sql->bindValue(':email', $email);
        $sql->bindValue(':senha', $hash);
        $sql->bindValue(':arquivo', $filename);
        $sql->bindValue(':id', $id);
        $sql->execute();

        return true;
    }

    /**
     * Exclui um usuário
     * @param mixed $id
     * @throws \Exception
     * @return bool
     */
    public static function delete($id)
    {
        $conn = BD::getConnection();

        //Consulta ao BD
        $user = self::getById($id);

        if (!$user) {
            throw new Exception("Usuário não encontrado");
        }

        $sql = $conn->prepare("DELETE FROM users WHERE id = :id");
        $sql->bindValue(":id", $user->id);
        $sql->execute();

        return true;
    }

    /**
     * Criptografa a senha informada
     * @param mixed $senha
     * @return string
     */
    public static function hashSenha($senha)
    {
        return password_hash($senha, PASSWORD_BCRYPT);
    }
}
