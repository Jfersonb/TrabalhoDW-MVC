<?php

namespace App\Services;

use Exception;

class FileUploadService
{

    const uploadDir = "public/arquivos/";
    const tiposPermitidos = ['application/pdf', 'image/jpeg', 'image/png'];
    const maxSize = 10 * 1024 * 1024;//5MB

    /**
     * Faz o upload do arquivo
     * @param mixed $file
     * @throws \Exception
     * @return string
     */
    public static function uploadFile($file)
    {
        $type = $file["type"];
        $size = $file["size"];

        if (!in_array($type, self::tiposPermitidos)) {
            throw new Exception("Tipo de arquivo não permitido");
        }

        if ($size > self::maxSize) {
            throw new Exception("Arquivo maior que o permitido");
        }

        $ext = pathinfo(basename($file["name"]), PATHINFO_EXTENSION);
        $name = uniqid("file_") . "." . $ext;

        if (move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/" . self::uploadDir . $name)) {
            return $name;
        } else {
            die("Erro no upload");
        }
    }

    /**
     * Retorna o caminho onde a arquivo está salva, caso não exista retorna uma imagem padrão
     * @param mixed $arquivo
     * @return string
     */
    public static function getPathFile($arquivo)
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . "/" . self::uploadDir;
        if (is_file($path . $arquivo)) {
            return "/" . self::uploadDir . $arquivo;
        }

        return null;
    }

    public static function deleteFile($filename)
    {
        $fileOriginal = $_SERVER['DOCUMENT_ROOT'] . "/" . self::uploadDir . $filename;
        if (file_exists($fileOriginal)) {
            return unlink($fileOriginal);
        }

        return false;
    }
}