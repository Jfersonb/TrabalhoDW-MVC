<?php

namespace App\Services;

use Exception;

class ImageUploadService
{

    const uploadDir = "public/imagens/";
    const tiposPermitidos = ["image/jpeg", "image/png"];
    const maxSize = 5 * 1024 * 1024;//5MB

    /**
     * Faz o upload da imagem de perfil do usuário
     * @param mixed $file
     * @throws \Exception
     * @return string
     */
    public static function uploadImage($file)
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
        $name = uniqid("img_") . "." . $ext;

        if (move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/" . self::uploadDir . $name)) {
            return $name;
        } else {
            die("Erro no upload");
        }
    }

    /**
     * Retorna o caminho onde a foto está salva, caso não exista retorna uma imagem padrão
     * @param mixed $foto
     * @return string
     */
    public static function getPathImage($foto)
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . "/" . self::uploadDir;
        if (is_file($path . $foto)) {
            return "/" . self::uploadDir . $foto;
        }

        return null;
    }

    public static function deleteImage($filename)
    {
        $fileOriginal = $_SERVER['DOCUMENT_ROOT'] . "/" . self::uploadDir . $filename;
        if (file_exists($fileOriginal)) {
            return unlink($fileOriginal);
        }

        return false;
    }
}