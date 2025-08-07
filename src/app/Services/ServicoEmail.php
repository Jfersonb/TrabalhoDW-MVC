<?php

namespace App\Services;

class ServicoEmail {
    public static function enviar($destinatario, $assunto, $mensagem) {
        // Aqui pode ser PHPMailer, SendGrid, etc.
        // Exemplo simples:
        mail($destinatario, $assunto, $mensagem);
    }
}
