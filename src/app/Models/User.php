<?php

namespace App\Models;

use App\Models\BD;
use App\Services\ImageUploadService;
use Exception;
use PDO;

class User{

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

        $sql = $conn->prepare("SELECT * FROM cadastroUsers WHERE email = :email");
        $sql->bindValue(":email", $email);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Função para inserir usuário no banco
     * @param mixed $nome
     * @param mixed $email
     * @param mixed $senha
     * @param mixed $foto
     * @return int
     */
    public function inserir($nome, $email, $senha, $foto = null)
    {
        $conn = BD::getConnection();
        //criptografa a senha
        $hash = self::hashSenha($senha);

        //Upload da foto
        if (is_array($foto) and is_uploaded_file($foto['tmp_name'])) {
            $foto = ImageUploadService::uploadImage($foto);
        }

        //Executa o sql de inserção
        $sql = $conn->prepare("INSERT INTO users(name, email, password, image) VALUES (:name,:mail,:password,:image)");
        $sql->bindValue(":name", $nome);
        $sql->bindValue(":mail", $email);
        $sql->bindValue(":password", $hash);
        $sql->bindValue(":image", $foto);
        $sql->execute();

        //Retorna o ID do usuário criado
        return $conn->lastInsertId();
    }

    /**
     * Função para atualizar o usuário
     * @param mixed $id
     * @param mixed $nome
     * @param mixed $email
     * @param mixed $senha
     * @param mixed $foto
     * @throws \Exception
     * @return bool
     */
    public function atualizar($id, $nome, $email, $senha = null, $foto = null)
    {
        $conn = BD::getConnection();

        //Consulta ao BD
        $user = self::getById($id);

        if (!$user) {
            throw new Exception("Usuário não encontrado");
        }

        if ($senha) {
            //criptografa a senha
            $hash = self::hashSenha($senha);
        } else {
            $hash = $user->password;
        }

        //Upload da foto, caso seja atualizada
        if (is_array($foto) and is_uploaded_file($foto['tmp_name'])) {
            $filename = ImageUploadService::uploadImage($foto);

            ImageUploadService::deleteImage($user->image);
        } else {
            $filename = $user->image;
        }

        //Executa o sql de inserção
        $sql = $conn->prepare("UPDATE users SET name = :name, email = :mail, password = :password, image = :image WHERE id = :id");
        $sql->bindValue(":name", $nome);
        $sql->bindValue(":mail", $email);
        $sql->bindValue(":password", $hash);
        $sql->bindValue(":image", $filename);
        $sql->bindValue(":id", $user->id);
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