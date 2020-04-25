<?php

namespace Services;

class Mercure extends StaticAccessClass {

    protected function notify(array $targets, array $topics, \Entities\AbstractJwtPayload $data) {
        $oJwtPublisher = new \Entities\MercureJwtPayload();
        $oJwtPublisher->addPublish('*');
        $jwtPublisher = \Firebase\JWT\JWT::encode($oJwtPublisher, \MERCURE_JWT_KEY, \MERCURE_JWT_ALGORITHM);

        // init mercure publisher
        $mercurePublisher = new \Symfony\Component\Mercure\Publisher(\MERCURE_URL, new \Symfony\Component\Mercure\Jwt\StaticJwtProvider($jwtPublisher));
        // Serialize the update, and dispatch it to the hub, that will broadcast it to the clients
        return $mercurePublisher(new \Symfony\Component\Mercure\Update($topics, json_encode($data), $targets));
    }

    public function notifyGamePlayerJoin(String $hashGame, String $playerPosition, String $playerName) {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('playerjoin');
        $payload->addData('hashGame', $hashGame);
        $payload->addData('newPlayerPosition', $playerPosition);
        $payload->addData('newPlayerName', $playerName);

        $this->notify([
            \BASE_URL . '/game/' . $hashGame
        ], [
            \BASE_URL . '/game/' . $hashGame
        ], $payload);
    }

    public function notifyGameStart(String $hashGame) {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('launchgame');
        $this->notify([
            \BASE_URL . '/game/' . $hashGame
        ], [
            \BASE_URL . '/game/' . $hashGame
        ], $payload);
    }

    public function notifyRoundStart(String $hashGame, int $numRound, String $firstPlayer, String $player, array $cards, String $proposedTrumpCard) {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('showproposedtrump');
        $payload->addData('hashGame', $hashGame);
        $payload->addData('numRound', $numRound);
        $payload->addData('firstPlayer', $firstPlayer);
        $payload->addData('cards', $cards);
        $payload->addData('proposedTrumpCard', $proposedTrumpCard);

        $this->notify([
            \BASE_URL . '/game/' . $hashGame . '/' . $player
        ], [
            \BASE_URL . '/game/' . $hashGame . '/' . $player
        ], $payload);
    }
}