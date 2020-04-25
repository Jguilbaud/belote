<?php

namespace Services;

class JwtCookie extends StaticAccessClass {

    private function setMercureJwtCookie(\Entities\MercureJwtPayload $oPayload) {
        $jwt = \Firebase\JWT\JWT::encode($oPayload, \MERCURE_JWT_KEY, \MERCURE_JWT_ALGORITHM);
        setcookie(\MERCURE_COOKIE_NAME, $jwt, time() + 3600, '/', \MERCURE_COOKIE_DOMAIN);
    }

    private function setBeloteGameJwtCookie(String $hashGame, \Entities\GameJwtPayload $oPayload) {
        $jwt = \Firebase\JWT\JWT::encode($oPayload, \MERCURE_JWT_KEY, \MERCURE_JWT_ALGORITHM);
        setcookie(\BELOTE_GAME_COOKIE_BASENAME . $hashGame, $jwt, time() + (3600 * 24), '/');
    }

    public function decodeJwtCookie(String $jwt): \stdClass {
        return \Firebase\JWT\JWT::decode($jwt, \MERCURE_JWT_KEY, [
            \MERCURE_JWT_ALGORITHM
        ]);
    }

    /**
     * Positionne le cookie mercure avec les targets autorisées
     *
     * @param String $hashGame
     */
    public function setOrUpdateMercureJoinCookie(String $hashGame, String $playerPosition = 'guest') {
        //TODO gérer partie multiple avec le cookie mercure qui se fait écraser pour le moment
        $oPayload = new \Entities\MercureJwtPayload();
        $oPayload->addSubscribe(\BASE_URL . '/game/' . $hashGame);
        $oPayload->addSubscribe(\BASE_URL . '/game/' . $hashGame . '/' . $playerPosition);
        $this->setMercureJwtCookie($oPayload);
    }

    public function setOrUpdateBeloteGameCookie(String $hashGame, String $playerPosition = 'guest') {
        $oPayload = new \Entities\GameJwtPayload();
        $oPayload->setHashGame($hashGame);
        $oPayload->setPlayerPosition(strtolower($playerPosition));
        $this->setBeloteGameJwtCookie($hashGame, $oPayload);
    }

    public function getBeloteGameCookie(String $hashGame): ?\Entities\GameJwtPayload {
        if (isset($_COOKIE[\BELOTE_GAME_COOKIE_BASENAME . $hashGame])) {
            $jwtPayload =  \Services\JwtCookie::get()->decodeJwtCookie($_COOKIE[\BELOTE_GAME_COOKIE_BASENAME . $hashGame]);
            $oPayload = new \Entities\GameJwtPayload();
            $oPayload->fromStdClass($jwtPayload);
            return $oPayload;
        }
        return null;
    }
}



