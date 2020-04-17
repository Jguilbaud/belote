<?php

namespace Controllers;

class Home extends AbstractController {

    public function showHomePage() {
        $this->tplName = 'home.tpl.html';

        parent::renderPage();
    }
}