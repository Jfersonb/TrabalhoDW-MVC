<?php

namespace App\Models;

use App\Models\BD;
use PDO;

class CadastroCuidador
{
    public static function inserir($id_usuario, $cursos)
    {
        $conn = BD::getConnection();
        $sql = "INSERT INTO cadastroCuidador (id_usuario, cursos)
                VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_usuario, $cursos]);
    }
}
