<?php

namespace App\Models;

use App\Models\BD;
use PDO;
use Exception;

class CadastroFamilia
{
    public static function inserir($id_usuario, $tipoParentesco, $endereco)
    {
        // Validações básicas
        if (empty($id_usuario) || !is_numeric($id_usuario)) {
            throw new Exception("ID de usuário inválido.");
        }
        if (empty($tipoParentesco)) {
            throw new Exception("O tipo de parentesco é obrigatório.");
        }
        if (empty($endereco)) {
            throw new Exception("O endereço é obrigatório.");
        }

        try {
            $conn = BD::getConnection();
            $sql = "INSERT INTO cadastroFamilia (id_usuario, tipoParentesco, endereco) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_usuario, $tipoParentesco, $endereco]);

            // Retorna o ID gerado
            return $conn->lastInsertId();
        } catch (\PDOException $e) {
            throw new Exception("Erro ao inserir dados de família: " . $e->getMessage());
        }
    }
}
