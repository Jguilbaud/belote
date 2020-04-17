<?php

namespace Controllers;

class Game extends AbstractController {

    public function showJoinPage($hashGame) {
        // debug :
        setcookie('mercureAuthorization','eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InN1YnNjcmliZSI6WyIqIl0sInB1Ymxpc2giOlsiKiJdfX0.aFuPpA3XL8PhSoZ1S4EhwvgB2iTSVGrYGyE1fT2pd6g');

        echo 'Joindre partie #' . $hashGame;
    }
}