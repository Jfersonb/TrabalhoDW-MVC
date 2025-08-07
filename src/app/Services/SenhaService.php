<?php

namespace App\Services;

use App\Models\Senha;
use App\Services\ServicoEmail;

class SenhaService
{

    public function resetarSenha(string $email): bool
    {

        $senhaModel = new Senha();
        $usuario = $senhaModel->buscarPorEmail($email);

        if (!$usuario) {
            return false;
        }

        $novaSenha = '123senha';
        
        //caso precise gerar senhas aleatorias
        //$novaSenhaPadrao = bin2hex(random_bytes(4)); // ex: 8 caracteres aleatórios

        $senhaModel->resetarSenha($email, $novaSenha);

        // Conteúdo do e-mail
        $titulo = "Reset de Senha - Vida Serena\n\n
        Sua senha foi alterado com sucesso!";
        $mensagem = "Olá,\n\nSua senha foi redefinida com sucesso.\n\nNova senha: $novaSenha\n\nRecomendamos alterá-la após o login.\n\nAtenciosamente,\nEquipe Vida Serena.";
        ServicoEmail::enviar($email, $titulo, $mensagem);

        return true;
    }
}
