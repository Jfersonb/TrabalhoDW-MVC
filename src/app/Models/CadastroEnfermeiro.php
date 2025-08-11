<?php

namespace App\Models;

use App\Models\BD;
use PDO;

class CadastroEnfermeiro
{
    public static function inserir($id_usuario, $coren, $cip)
    {
        $conn = BD::getConnection();
        $sql = "INSERT INTO cadastroEnfermeiro (id_usuario, coren, cip)
                VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_usuario, $coren, $cip]);
    }
}
