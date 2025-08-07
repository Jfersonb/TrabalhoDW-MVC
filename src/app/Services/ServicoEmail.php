<?php

namespace App\Services;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ServicoEmail {
    public static function enviar($destinatario, $assunto, $mensagem) {
             
        //Verifica se o formulário foi enviado
        // if(! isset($_POST['destinatario'])){
        //     die("Informe o email para prosseguir com o envio"); 
        // }

        // Obtém o host da variável de ambiente, ou 'localhost' como default
        $rabbitmqHost = getenv('RABBITMQ_HOST') ?: 'localhost'; 

        // Conexão com o RabbitMQ
        $connection = new AMQPStreamConnection($rabbitmqHost, 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('fila_emails', false, true, false, false);

        //Recebe os dados enviados pelo formulário
        // $to = $destinatario;
        // $subject = $titulo;
        // $body = $mensagem;
        // Dados do e-mail
        $email = [
            'to' => $destinatario,
            'subject' => $assunto,
            'body' => $mensagem
        ];

        //Converte os dados do email para JSON
        $dados = json_encode($email);

        //Gera a mensagem a ser inserida na fila
        $msg = new AMQPMessage($dados, ['delivery_mode' => 2]); // persistente

        //Publica a mensagem na fila
        $channel->basic_publish($msg, '', 'fila_emails');

        //Encerra a conexão
        $channel->close();
        $connection->close();

        return true;
    }
}
