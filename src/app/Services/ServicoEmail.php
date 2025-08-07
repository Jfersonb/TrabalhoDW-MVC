<?php

namespace App\Services;

class ServicoEmail {
    public static function enviar($destinatario, $assunto, $mensagem) {
        $headers = "From: no-reply@vidaserena.com.br\r\n";
        $headers .= "Reply-To: no-reply@vidaserena.com.br\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        return mail($destinatario, $assunto, $mensagem, $headers);
    }
}
