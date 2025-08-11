<?php

namespace App\Models;

use App\Models\BD;
use PDO;

class CadastroMedico
{
    public static function inserir($id_usuario, $crm)
    {
        $conn = BD::getConnection();
        $sql = "INSERT INTO cadastroMedico (id_usuario, crm)
                VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_usuario, $crm]);
    }
}
