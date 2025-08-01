<?php

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Services\FileUploadService;
use App\Models\Medicamento;

class MedicamentoController{

     public function lista(){
        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);
        $twig->addGlobal('session', $_SESSION);

        $medicamentoModel = new Medicamento();

        $busca = isset($_GET['busca']) ? $_GET['busca'] : "";
        
        if(!empty($busca)){
            $medicamentos = $medicamentoModel->buscar($busca);
        }else{
            $medicamentos = $medicamentoModel->getAll();
        }
      
        echo $twig->render("medicamento/lista.html.twig", [
            "titulo" => "Listar medicamento",
            "medicamentos" => $medicamentos,
            "busca" => $busca
        ]);
    }

    public function cadastro(){
        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);
        $twig->addGlobal('session', $_SESSION);

        echo $twig->render("medicamento/cadastro.html.twig", [
            "titulo" => "Cadastrar medicamento"
        ]);
    }

    public function processaCadastro(){

        // Verifica se usuário está logado
        if (!isset($_SESSION['id'])) {
            die("Usuário não autenticado.");
        }

        $idUsuario = $_SESSION['id'];

        // Coleta os dados do formulário
        $nome = $_POST['nomeMedicamento'] ?? '';
        $tipo = $_POST['tipoMedicamento'] ?? '';
        $quantidadeCaixas = (int) ($_POST['quantidadeCaixas'] ?? 0);
        $quantidadePorCaixa = (int) ($_POST['quantidadePorCaixa'] ?? 0);


        $notaFiscal = $_FILES['notaFiscal'];

        // Insere no banco de dados
        try {
            
            $medicamentoModel = new Medicamento();
            $id = $medicamentoModel->inserir($idUsuario, $nome, $tipo, $quantidadeCaixas, $quantidadePorCaixa, $notaFiscal);

            header("Location: /medicamento/lista");
            exit;

        } catch (PDOException $e) {
            error_log("Erro ao cadastrar medicamento: " . $e->getMessage());
            header("Location: /PHP/CadastroMedicamentos.php?msg=erro");
            exit;
        }
    }

}