<?php

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AppController{

    public function index()  {
        $loader = new FilesystemLoader(__DIR__."/../Views");
        $twig = new Environment($loader);      
        $twig->addGlobal('session', $_SESSION);

        echo $twig->render("index.html.twig",[
            "title" => "Página inicial"
        ]);
    }
}