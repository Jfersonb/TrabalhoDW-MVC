<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPMailer\PHPMailer\PHPMailer;


// Conexão com RabbitMQ

// Obtém o host da variável de ambiente, ou 'localhost' como default
$rabbitmqHost = getenv('RABBITMQ_HOST') ?: 'localhost'; 

$connection = new AMQPStreamConnection('host.docker.internal', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('fila_emails', false, true, false, false);

echo "Aguardando mensagens de e-mail...\n";

$callback = function ($msg) {
    $dados = json_decode($msg->body, true);

    // Servidor de email
    $SMTP_HOST = "smtp.gmail.com";
    $SMTP_PORT = 587;
    $SMTP_USER = "jeferson.silva2@estudante.ifto.edu.br";
    $SMTP_PASS = "hinp ltdk idrv xpoi";
    $SMTP_NAME = "Jeferson Estudante";

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = $SMTP_USER;
        $mail->Password = $SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $SMTP_PORT;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom($SMTP_USER, $SMTP_NAME);
        $mail->addAddress($dados['to']);
        $mail->Subject = $dados['subject'];
        $mail->Body = mb_convert_encoding($dados['body'], "utf-8");

        $mail->send();
        echo "E-mail enviado para: {$dados['to']}\n";
    } catch (Exception $e) {
        echo "Erro ao enviar e-mail: {$mail->ErrorInfo}\n";
    }
};

$channel->basic_consume('fila_emails', '', false, true, false, false, $callback);

// Loop infinito
while ($channel->is_consuming()) {
    $channel->wait();
}