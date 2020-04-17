<?php

namespace Controllers;

class Game extends AbstractController {

    public function showJoinPage($hashGame) {
        echo 'Joindre partie #' . $hashGame;
    }
}