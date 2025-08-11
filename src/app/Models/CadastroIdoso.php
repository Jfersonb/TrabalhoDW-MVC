<?php

namespace App\Models;

use App\Models\BD;
use PDO;

class CadastroIdoso
{
    public static function inserir($id_usuario, $responsavel, $condicao, $medicamentos, $restricao, $alergias)
    {
        $conn = BD::getConnection();
        $sql = "INSERT INTO cadastroIdoso (id_usuario, responsavelLegal, condicaoMedicaImportante, medicamentosUso, resticaoAlimentar, alergias)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_usuario, $responsavel, $condicao, $medicamentos, $restricao, $alergias]);
    }
}
