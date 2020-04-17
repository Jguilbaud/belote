<?php
namespace Controllers;

abstract class AbstractController {
    public function __contruct(){
        //init twig
        $loader = new \Twig\Loader\FilesystemLoader(VIEWS_PATH);

        $this->tplEngine = new \Twig\Environment($loader, array(
           // 'cache' => TMP_PATH . 'cache',
            'debug' => DEBUG
        ));
    }

}