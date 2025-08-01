<?php
namespace App\Models;

use App\Models\BD;
use App\Services\FileUploadService;
use Exception;
use PDO;

class Medicamento{

    /**
     * Busca todos os medicamentos cadastrados
     * @return array
     */
    public static function getAll()
    {
        $conn = BD::getConnection();

        $sql = $conn->query("SELECT * FROM cadastroMedicamentos");

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscar($texto)
    {
        $conn = BD::getConnection();

        $texto = strtolower($texto);
        
        $sql = $conn->prepare("SELECT * FROM cadastroMedicamentos WHERE LOWER(nomeMedicamento) LIKE :busca");
        $sql->bindValue(":busca", "%$texto%", PDO::PARAM_STR);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    public function inserir($idUsuario, $nome, $tipo, $quantidadeCaixas, $quantidadePorCaixa, $notaFiscal)
    {
        $conn = BD::getConnection();

        //Upload da nota
        if (is_array($notaFiscal) and is_uploaded_file($notaFiscal['tmp_name'])) {
            $notaFiscal = FileUploadService::uploadFile($notaFiscal);
        }else{
            die("Erro no upload da Nota fiscal");
        }

        //Executa o sql de inserção
        $sql = $conn->prepare("INSERT INTO cadastroMedicamentos 
                (id_usuario, nomeMedicamento, tipoMedicamento, quantDeCaixa, quantPorCaixa, notaFiscal) 
                VALUES (:idUsuario, :nome, :tipo, :qtdCaixas, :qtdPorCaixa, :nota)");

            $sql->bindValue(":idUsuario", $idUsuario, PDO::PARAM_INT);
            $sql->bindValue(":nome", $nome);
            $sql->bindValue(":tipo", $tipo);
            $sql->bindValue(":qtdCaixas", $quantidadeCaixas, PDO::PARAM_INT);
            $sql->bindValue(":qtdPorCaixa", $quantidadePorCaixa, PDO::PARAM_INT);
            $sql->bindValue(":nota", $notaFiscal, PDO::PARAM_LOB);

            $sql->execute();

        //Retorna o ID do medicamento criado
        return $conn->lastInsertId();
    }

}