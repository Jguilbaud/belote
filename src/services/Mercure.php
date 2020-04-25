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

    public function notifyGamePlayerJoin(String $hashGame, String $playerPosition, String $playerName): void {
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

    public function notifyGameStart(String $hashGame): void {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('launchgame');
        $this->notify([
            \BASE_URL . '/game/' . $hashGame
        ], [
            \BASE_URL . '/game/' . $hashGame
        ], $payload);
    }

    public function notifyRoundStart(String $hashGame, int $numRound, String $firstPlayer, String $player, array $cards, String $proposedTrumpCard): void {
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

    public function notifyChooseTrumpPassed(String $hashGame, String $precedentPlayer, String $newCurrentPlayer, bool $isFirstTurnTrumpChoice) {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('chooseTrumpNextPlayer');
        $payload->addData('hashGame', $hashGame);
        $payload->addData('newPlayer', $newCurrentPlayer);
        $payload->addData('precedentPlayer', $precedentPlayer);
        $payload->addData('isFirstChoiceTurn', $isFirstTurnTrumpChoice);

        $this->notify([
            \BASE_URL . '/game/' . $hashGame
        ], [
            \BASE_URL . '/game/' . $hashGame
        ], $payload);
    }

    public function notifyChosenTrump(String $hashGame, String $trumpColor, String $taker, String $newCurrentPlayer, String $player, array $cards) {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('startfirstturn');
        $payload->addData('hashGame', $hashGame);
        $payload->addData('taker', $taker);
        $payload->addData('newPlayer', $newCurrentPlayer);
        $payload->addData('cards', $cards);
        $payload->addData('trumpColor', $trumpColor);

        $this->notify([
            \BASE_URL . '/game/' . $hashGame . '/' . $player
        ], [
            \BASE_URL . '/game/' . $hashGame . '/' . $player
        ], $payload);
    }
}