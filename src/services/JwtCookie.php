<?php

namespace Services;

class JwtCookie extends StaticAccessClass {

    private function setMercureJwtCookie(\Entities\MercureJwtPayload $oPayload) {
        $jwt = \Firebase\JWT\JWT::encode($oPayload, \MERCURE_JWT_KEY, \MERCURE_JWT_ALGORITHM);
        setcookie(\MERCURE_COOKIE_NAME, $jwt, time() + 3600, '/', \MERCURE_COOKIE_DOMAIN);
    }

    private function setBeloteGameJwtCookie(String $hashGame, \stdClass $oPayload) {
        $jwt = \Firebase\JWT\JWT::encode($oPayload, \MERCURE_JWT_KEY, \MERCURE_JWT_ALGORITHM);
        setcookie(\BELOTE_GAME_COOKIE_BASENAME . $hashGame, $jwt, time() + (3600 * 24));
    }

    public function readJwtCookie(): \stdClass {
    }

    /**
     * Positionne le cookie mercure avec les targets autorisées
     *
     * @param String $hashGame
     */
    public function setMercureJoinCookie(String $hashGame, String $playerPosition = 'guest') {
        $oPayload = new \Entities\MercureJwtPayload();
        $oPayload->addSubscribe('http://localhost/belote/game/' . $hashGame); // TODO mettre domaine dans conf/constante
        $oPayload->addSubscribe('http://localhost/belote/game/' . $hashGame . '/' . $playerPosition); // TODO mettre domaine dans conf/constante
        $this->setMercureJwtCookie($oPayload);
    }

    public function setBeloteJoinCookie(String $hashGame, String $playerPosition = 'guest') {
        $oPayload = new \stdClass();
        $oPayload->hashGame = $hashGame;
        $oPayload->playerPosition = $playerPosition;

        $this->setBeloteGameJwtCookie($hashGame, $oPayload);
    }
}



