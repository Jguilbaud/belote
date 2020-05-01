<?php

namespace Services;

class Mercure extends StaticAccessClass {

    /**
     * Envoie une notification via Mercure
     *
     * @param array $targets
     * @param array $topics
     * @param \Entities\AbstractJwtPayload $data
     * @return
     */
    protected function notify(array $targets, array $topics, \Entities\AbstractJwtPayload $data) {
        $oJwtPublisher = new \Entities\MercureJwtPayload();
        $oJwtPublisher->addPublish('*');
        $jwtPublisher = \Firebase\JWT\JWT::encode($oJwtPublisher, \Conf::MERCURE_JWT_KEY, \MERCURE_JWT_ALGORITHM);

        // init mercure publisher
        $mercurePublisher = new \Symfony\Component\Mercure\Publisher(\Conf::MERCURE_URL, new \Symfony\Component\Mercure\Jwt\StaticJwtProvider($jwtPublisher));
        // Serialize the update, and dispatch it to the hub, that will broadcast it to the clients
        return $mercurePublisher(new \Symfony\Component\Mercure\Update($topics, json_encode($data), $targets));
    }

    /**
     * Notifie qu'un joueur a rejoint la partie dans la salle d'attente
     *
     * @param String $hashGame
     * @param String $playerPosition
     * @param String $playerName
     */
    public function notifyGamePlayerJoin(String $hashGame, String $playerPosition, String $playerName): void {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('playerjoin');
        $payload->addData('hashGame', $hashGame);
        $payload->addData('newPlayerPosition', $playerPosition);
        $payload->addData('newPlayerName', $playerName);

        $this->notify([
            \Conf::BASE_URL . '/game/' . $hashGame
        ], [
            \Conf::BASE_URL . '/game/' . $hashGame
        ], $payload);
    }

    /**
     * Notifie que tous les joueurs ont pris place et que le jeu démarre
     *
     * @param String $hashGame
     */
    public function notifyGameStart(String $hashGame): void {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('launchgame');
        $this->notify([
            \Conf::BASE_URL . '/game/' . $hashGame
        ], [
            \Conf::BASE_URL . '/game/' . $hashGame
        ], $payload);
    }

    /**
     * Notifie que la manche démarre et affiche l'atout proposé
     *
     * @param String $hashGame
     * @param int $numRound
     * @param String $firstPlayer
     * @param String $player
     * @param array $cards
     * @param String $proposedTrumpCard
     */
    public function notifyRoundStart(String $hashGame, int $numRound, String $firstPlayer, String $player, array $cards, String $proposedTrumpCard): void {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('showproposedtrump');
        $payload->addData('hashGame', $hashGame);
        $payload->addData('numRound', $numRound);
        $payload->addData('newPlayer', $firstPlayer);
        $payload->addData('cards', $cards);
        $payload->addData('proposedTrumpCard', $proposedTrumpCard);

        $this->notify([
            \Conf::BASE_URL . '/game/' . $hashGame . '/' . $player
        ], [
            \Conf::BASE_URL . '/game/' . $hashGame . '/' . $player
        ], $payload);
    }

    /**
     * Notifie qu'un joueur a passé au tour de choix d'atout
     *
     * @param String $hashGame
     * @param String $precedentPlayer
     * @param String $newCurrentPlayer
     * @param bool $isFirstTurnTrumpChoice
     */
    public function notifyChooseTrumpPassed(String $hashGame, String $precedentPlayer, String $newCurrentPlayer, bool $isFirstTurnTrumpChoice) {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('choosetrumpnextplayer');
        $payload->addData('hashGame', $hashGame);
        $payload->addData('newPlayer', $newCurrentPlayer);
        $payload->addData('precedentPlayer', $precedentPlayer);
        $payload->addData('isFirstChoiceTurn', $isFirstTurnTrumpChoice);

        $this->notify([
            \Conf::BASE_URL . '/game/' . $hashGame
        ], [
            \Conf::BASE_URL . '/game/' . $hashGame
        ], $payload);
    }

    /**
     * Notifie qu'un joueur a pris à un atout
     *
     * @param String $hashGame
     * @param String $trumpColor
     * @param String $taker
     * @param String $newCurrentPlayer
     * @param String $player
     * @param array $cards
     */
    public function notifyChosenTrump(String $hashGame, String $trumpColor, String $taker, String $newCurrentPlayer, String $player, array $cards) {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('startfirstturn');
        $payload->addData('hashGame', $hashGame);
        $payload->addData('taker', $taker);
        $payload->addData('newPlayer', $newCurrentPlayer);
        $payload->addData('cards', $cards);
        $payload->addData('trumpColor', $trumpColor);

        $this->notify([
            \Conf::BASE_URL . '/game/' . $hashGame . '/' . $player
        ], [
            \Conf::BASE_URL . '/game/' . $hashGame . '/' . $player
        ], $payload);
    }

    /**
     * Notifie qu'une carte a été jouée dans le tour
     *
     * @param String $hashGame
     * @param String $player
     * @param int $cardTurnPosition
     * @param String $card
     * @param String $newPlayer
     */
    public function notifyCardPlayed(String $hashGame, String $player, int $cardTurnPosition, String $card, String $newPlayer) {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('cardplayed');
        $payload->addData('hashGame', $hashGame);
        $payload->addData('player', $player);
        $payload->addData('card', $card);
        $payload->addData('cardPosition', $cardTurnPosition);
        $payload->addData('newPlayer', $newPlayer);

        $this->notify([
            \Conf::BASE_URL . '/game/' . $hashGame
        ], [
            \Conf::BASE_URL . '/game/' . $hashGame
        ], $payload);
    }

    /**
     * Notifie que le tour (pli) est terminé et qu'un nouveau démarre
     *
     * @param String $hashGame
     * @param String $player
     * @param int $cardTurnPosition
     * @param String $card
     * @param String $winner
     * @param int $newTurnNum
     */
    public function notifyChangeTurn(String $hashGame, String $player, int $cardTurnPosition, String $card, String $winner, int $newTurnNum) {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('changeturn');
        $payload->addData('hashGame', $hashGame);
        $payload->addData('player', $player);
        $payload->addData('card', $card);
        $payload->addData('cardPosition', $cardTurnPosition);
        $payload->addData('winner', $winner);
        $payload->addData('newTurnNum', $newTurnNum);

        $this->notify([
            \Conf::BASE_URL . '/game/' . $hashGame
        ], [
            \Conf::BASE_URL . '/game/' . $hashGame
        ], $payload);
    }

    /**
     * Notifie que la manche est terminée et qu'une nouvelle démarre
     *
     * @param String $hashGame
     * @param String $player
     * @param int $cardTurnPosition
     * @param String $card
     * @param String $winner
     * @param int $newRoundNum
     * @param String $dealer
     * @param String $cutter
     * @param array $points
     */
    public function notifyChangeRound(String $hashGame, String $player, int $cardTurnPosition, String $card, String $winner, int $newRoundNum, String $dealer, String $cutter, array $points) {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('changeround');
        $payload->addData('hashGame', $hashGame);
        $payload->addData('points', $points);
        $payload->addData('newRoundNum', $newRoundNum);
        $payload->addData('dealer', $dealer);
        $payload->addData('cutter', $cutter);
        $payload->addData('player', $player);
        $payload->addData('card', $card);
        $payload->addData('cardPosition', $cardTurnPosition);
        $payload->addData('winner', $winner);

        $this->notify([
            \Conf::BASE_URL . '/game/' . $hashGame
        ], [
            \Conf::BASE_URL . '/game/' . $hashGame
        ], $payload);
    }

    /**
     * On notifie qu'il faut recouper le deck (cas où personne n'a voulu prendre l'atout)
     *
     * @param String $hashGame
     * @param String $player nouveau joueur actif (celui qui coupe le deck)
     */
    public function notifyRecutDeck(String $hashGame, String $player) {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('recutdeck');
        $payload->addData('hashGame', $hashGame);
        $payload->addData('player', $player);

        $this->notify([
            \Conf::BASE_URL . '/game/' . $hashGame
        ], [
            \Conf::BASE_URL . '/game/' . $hashGame
        ], $payload);
    }

    /**
     * Notifie la fin de partie suite à la dernière carte posée
     *
     * @param String $hashGame
     * @param String $player
     * @param int $cardTurnPosition
     * @param String $card Carte posée (qui est donc la dernière du tour, de la manche et de la partie)
     * @param String $winner Gagnant du pli
     * @param array $points Points de la manche
     */
    public function notifyGameEnd(String $hashGame, String $player, int $cardTurnPosition, String $card, String $winner, array $points) {
        $payload = new \Entities\MercureEventBelotePayload();
        $payload->setAction('changeround');
        $payload->addData('hashGame', $hashGame);
        $payload->addData('points', $points);
        $payload->addData('player', $player);
        $payload->addData('card', $card);
        $payload->addData('cardPosition', $cardTurnPosition);
        $payload->addData('winner', $winner);

        $this->notify([
            \Conf::BASE_URL . '/game/' . $hashGame
        ], [
            \Conf::BASE_URL . '/game/' . $hashGame
        ], $payload);
    }
}
