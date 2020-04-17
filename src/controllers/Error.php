<?php
namespace Controllers;

class Error extends AbstractController {

    public function show404Page() {
        echo 'page introuvable';
    }
}