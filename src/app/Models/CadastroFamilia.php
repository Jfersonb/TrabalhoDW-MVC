<?php

namespace App\Models;

use App\Models\BD;
use PDO;

class CadastroFamilia
{
    public static function inserir($id_usuario, $tipoParentesco, $endereco)
    {
        $conn = BD::getConnection();
        $sql = "INSERT INTO cadastroFamilia (id_usuario, tipoParentesco, endereco) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_usuario, $tipoParentesco, $endereco]);
    }
}
