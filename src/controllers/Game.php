<?php

namespace Controllers;

class Game extends AbstractController {

    public function showJoinPage(String $hashGame, ?String $jwtCookie = null) {    
        ignore_user_abort( true );
        try {
            $jwtBeloteCookie = \Services\JwtCookie::get()->getBeloteGameCookie($hashGame);
            $oGame = \Repositories\DbGame::get()->findOneByHash($hashGame);

            $playerPosition = 'guest';
            // Si on a déjà le cookie du jeu
            if ($jwtBeloteCookie != null) {
                $this->tplVars['html_disabled_pseudo'] = 'disabled="disabled"';
                $this->tplVars['html_disabled_s'] = 'disabled="disabled"';
                $this->tplVars['html_disabled_w'] = 'disabled="disabled"';
                $this->tplVars['html_disabled_e'] = 'disabled="disabled"';
                switch ($jwtBeloteCookie->getPlayerPosition()) {
                    case 'n' :
                        $this->tplVars['currentPlayerName'] = $oGame->getName_north();
                        $playerPosition = 'n';
                        break;
                    case 'w' :
                        $this->tplVars['currentPlayerName'] = $oGame->getName_west();
                        $playerPosition = 'w';
                        break;
                    case 'e' :
                        $this->tplVars['currentPlayerName'] = $oGame->getName_east();
                        $playerPosition = 'e';
                        break;
                    case 's' :
                        $this->tplVars['currentPlayerName'] = $oGame->getName_south();
                        $playerPosition = 's';
                        break;
                }

                // Si le jeu a démarré on renvoi vers le board
                if ($oGame->getName_north() != '' && $oGame->getName_south() != '' && $oGame->getName_west() != '' && $oGame->getName_east() != '') {
                    header('Location: ' . \Conf::BASE_URL . '/play/' . $hashGame);
                }
            } else {
                $this->tplVars['html_disabled'] = '';
                $this->tplVars['currentPlayerName'] = '';
            }

            // On positionne un cookie pour écouter les events mercure
            \Services\JwtCookie::get()->setCookies($hashGame, $playerPosition);
        } catch ( \Exceptions\BeloteException $e ) {
            throw new \Exceptions\BeloteException('Id partie inconnu');
        }

        $this->tplName = 'gamejoin.tpl.html';
        $this->tplVars['hashGame'] = $hashGame;
        $this->tplVars['playerNameNorth'] = $oGame->getName_north();
        if ($oGame->getName_south() != '') {
            $this->tplVars['playerNameSouth'] = $oGame->getName_south();
            $this->tplVars['html_disabled_s'] = 'disabled="disabled"';
        }
        if ($oGame->getName_east() != '') {
            $this->tplVars['playerNameEast'] = $oGame->getName_east();
            $this->tplVars['html_disabled_e'] = 'disabled="disabled"';
        }
        if ($oGame->getName_west() != '') {
            $this->tplVars['playerNameWest'] = $oGame->getName_west();
            $this->tplVars['html_disabled_w'] = 'disabled="disabled"';
        }

        parent::renderPage();
    }

    public function create(String $playerName) {
        // on créé une nouvelle partie
        $oGame = new \Entities\Game();
        $oGame->setName_north($playerName);
        \Services\Game::get()->create($oGame);

        // On positionne les cookies
        \Services\JwtCookie::get()->setCookies($oGame->getHash(), 'N');

        // On redirige vers la salle d'attente
        header('Location: ' . \Conf::BASE_URL . '/join/' . $oGame->getHash());
    }

    public function join(String $hashGame, String $playerName, String $playerPosition) {
        $playerPosition = strtolower($playerPosition);
        switch ($playerPosition) {
            case 'n' :
                $methodPartName = 'north';
                break;
            case 's' :
                $methodPartName = 'south';
                break;
            case 'o' :
            case 'w' :
                $methodPartName = 'west';
                break;
            case 'e' :
                $methodPartName = 'east';
                break;
            default :
                return array(
                    'response' => 'error',
                    'error_msg' => 'Position de joueur invalide'
                );
        }
        $methodSet = 'setName_' . $methodPartName;
        $methodGet = 'getName_' . $methodPartName;

        try {
            $oGame = \Repositories\DbGame::get()->findOneByHash($hashGame);
        } catch ( \Exceptions\BeloteException $e ) {
            return array(
                'response' => 'error',
                'error_msg' => 'Id de Partie inconnue'
            );
        }
        if (strtolower($oGame->$methodGet()) != null) {
            return array(
                'response' => 'error',
                'error_msg' => 'Position de joueur déjà utilisée'
            );
        }

        $oGame->$methodSet($playerName);

        // On positionne les cookies
        \Services\JwtCookie::get()->setCookies($oGame->getHash(), $playerPosition);

        // si on a tous les joueurs, on démarre la partie
        if ($oGame->getName_north() != '' && $oGame->getName_south() != '' && $oGame->getName_west() != '' && $oGame->getName_east() != '') {
            \Services\Game::get()->startNewRound($oGame);
            \Services\Mercure::get()->notifyGameStart($hashGame);
        }else{
            // On notifie via mercure les autres joueurs de l'arrivée du joueur
            \Services\Mercure::get()->notifyGamePlayerJoin($hashGame, $playerPosition, $playerName);
        }
        \Repositories\DbGame::get()->update($oGame);
        // On répond à la requete
        return array(
            'response' => 'ok'
        );
    }

    public function showPlayPage(String $hashGame): void {
        try {
            $jwtBeloteCookie = \Services\JwtCookie::get()->getBeloteGameCookie($hashGame);
            $oGame = \Repositories\DbGame::get()->findOneByHash($hashGame);
            $oRound = \Repositories\DbRound::get()->findOneById($oGame->getId_current_round());

            // Si on a déjà le cookie du jeu
            if ($jwtBeloteCookie != null) {
                $this->tplName = 'gameboard.tpl.html';
                $this->tplVars['hashGame'] = $hashGame;
                $this->tplVars['idRound'] = $oRound->getNum_round();
                $this->tplVars['playerPosition'] = $jwtBeloteCookie->getPlayerPosition();
                $this->tplVars['playerName_n'] = $oGame->getName_north();
                $this->tplVars['playerName_s'] = $oGame->getName_south();
                $this->tplVars['playerName_w'] = $oGame->getName_west();
                $this->tplVars['playerName_e'] = $oGame->getName_east();
                $this->tplVars['myCards'] = '';
                if ($jwtBeloteCookie->getPlayerPosition() != '') {
                    switch ($jwtBeloteCookie->getPlayerPosition()) {
                        case 'n' :
                            $this->tplVars['currentPlayerName'] = $oGame->getName_north();
                            $this->tplVars['currentPlayerTeam'] = 'ns';
                            break;
                        case 's' :
                            $this->tplVars['currentPlayerName'] = $oGame->getName_south();
                            $this->tplVars['currentPlayerTeam'] = 'ns';
                            break;
                        case 'w' :
                            $this->tplVars['currentPlayerName'] = $oGame->getName_west();
                            $this->tplVars['currentPlayerTeam'] = 'we';
                            break;
                        case 'e' :
                            $this->tplVars['currentPlayerName'] = $oGame->getName_east();
                            $this->tplVars['currentPlayerTeam'] = 'we';
                            break;
                    }
                }
                

               $playerPosition = $jwtBeloteCookie->getPlayerPosition();
                    if( $playerPosition == "guest"){
                        $playerPosition = 's';
                    }
                
                // On remet le cookie au cas où il aurait disparu
                \Services\JwtCookie::get()->setCookies($hashGame, $playerPosition);

                // On affiche les points
                $htmlPoints = '';
                $rounds = array();
                if ($oRound->getNum_round() > 1) {

                    $rounds = \Repositories\DbRound::get()->findAllByGameId($oGame->getId());
                    $tPoints_ns = 0;
                    $tPoints_we = 0;
                    foreach ( $rounds as $toRound ) {
                        // SI le jeu est en cours, on n'affiche pas la manche en cours qui n'a pas encore de points
                        if ($oGame->getStep() == \Entities\Game::STEP_FINISHED || $toRound->getId() != $oGame->getId_current_round()) {
                            $tPoints_ns += $toRound->getPoints_ns();
                            $tPoints_we += $toRound->getPoints_we();
                            $htmlPoints .= '<tr><td>' . $toRound->getNum_Round() . '</td> <td>' . $toRound->getPoints_ns() . ' (' . $tPoints_ns . ')</td> <td>' . $toRound->getPoints_we() . ' (' . $tPoints_we . ')</td> </tr>' . "\n";
                        }
                    }
                }
                $this->tplVars['htmlPoints'] = $htmlPoints;

                // - action en cours
                $this->tplVars['currentPlayer'] = $this->tplVars['playerName_' . $oGame->getCurrent_player()];
                $this->tplVars['hideChooseTrump'] = 'hidden';
                $this->tplVars['hideChooseTrumpBtn'] = 'disabled="disabled"';
                $this->tplVars['hideCutDeck'] = 'hidden';
                $this->tplVars['hidePlayCardBtn'] = 'disabled="disabled"';
                $this->tplVars['hideTurnCards'] = 'hidden';

                $this->tplVars['trumpHeartDisabled'] = 'disabled';
                $this->tplVars['trumpDiamondDisabled'] = 'disabled';
                $this->tplVars['trumpClubDisabled'] = 'disabled';
                $this->tplVars['trumpSpadeDisabled'] = 'disabled';

                switch ($oGame->getStep()) {
                    case \Entities\Game::STEP_CUT_DECK :
                        if ($jwtBeloteCookie->getPlayerPosition() == $oGame->getCurrent_player()) {
                            $this->tplVars['hideCutDeck'] = '';
                        }
                        break;
                    case \Entities\Game::STEP_CHOOSE_TRUMP :

                    
                        $this->showHand($oGame->getId_current_round(), $playerPosition);
                        $this->tplVars['hideChooseTrump'] = '';
                        if ($jwtBeloteCookie->getPlayerPosition() == $oGame->getCurrent_player()) {
                            $this->tplVars['hideChooseTrumpBtn'] = '';
                        }
                        $this->tplVars['proposedTrumpCardSrcImg'] = \Services\Utils::getCardImgUrl($oGame->getCards()[0]);
                        $this->tplVars['trump' . ucfirst(\CARDS_COLORS[substr($oGame->getCards()[0], 0, 1)]) . 'Disabled'] = '';

                        break;
                    case \Entities\Game::STEP_CHOOSE_TRUMP_2 :
                        $this->showHand($oGame->getId_current_round(), $jwtBeloteCookie->getPlayerPosition());
                        $this->tplVars['hideChooseTrump'] = '';
                        if ($jwtBeloteCookie->getPlayerPosition() == $oGame->getCurrent_player()) {
                            $this->tplVars['hideChooseTrumpBtn'] = '';
                        }
                        $this->tplVars['proposedTrumpCardSrcImg'] = \Services\Utils::getCardImgUrl($oGame->getCards()[0]);
                        $this->tplVars['trumpHeartDisabled'] = '';
                        $this->tplVars['trumpDiamondDisabled'] = '';
                        $this->tplVars['trumpClubDisabled'] = '';
                        $this->tplVars['trumpSpadeDisabled'] = '';
                        // Cet atout a été refusé au premier tour, on ne peut donc plus le prendre
                        $this->tplVars['trump' . ucfirst(\CARDS_COLORS[substr($oGame->getCards()[0], 0, 1)]) . 'Disabled'] = 'disabled';
                        break;

                    case \Entities\Game::STEP_PLAY_CARD :
                        $oRound = \Repositories\DbRound::get()->findOneById($oGame->getId_current_round());
                        $this->tplVars['hideTurnCards'] = '';
                        $method = 'getName_' . \PLAYERS[$oRound->getTaker()];
                        $this->tplVars['takerName'] = $oGame->$method();
                        $this->tplVars['trumpColorImg'] = '<img src="' . \Conf::BASE_URL . '/img/' . \CARDS_COLORS[$oRound->getTrump_color()] . '.png" />';
                        $this->showHand($oGame->getId_current_round(), $jwtBeloteCookie->getPlayerPosition());
                        if ($jwtBeloteCookie->getPlayerPosition() == $oGame->getCurrent_player()) {
                            $this->tplVars['hidePlayCardBtn'] = '';
                        }

                        // On affiche les cartes du pli
                        $oTurn = \Repositories\DbTurn::get()->findOneById($oRound->getId_current_turn());
                        $tPlayer = $oTurn->getFirst_player();
                        for($i = 1; $i < 5; $i++) {
                            $methodCard = 'getCard_' . $tPlayer;
                            $card = $oTurn->$methodCard();
                            $methodPlayer = 'getName_' . \PLAYERS[$tPlayer];
                            if ($card != '') {
                                $this->tplVars['turnCard_' . $i] = '<img src="' . \Services\Utils::getCardImgUrl($card) . '" />';
                                $this->tplVars['turnPlayer_' . $i] = $oGame->$methodPlayer();
                            } else {
                                $this->tplVars['turnCard_' . $i] = '';
                                $this->tplVars['turnPlayer_' . $i] = '';
                            }
                            $tPlayer = \Services\Utils::getNextPlayerFromOne($tPlayer);
                        }

                        break;
                    // TODO autres etapes - fin du jeu ?
                }

                parent::renderPage();
            } else {
                // On redirige vers la salle d'attente
                header('Location: ' . \Conf::BASE_URL . '/join/' . $oGame->getHash());
            }
        } catch ( \Exceptions\BeloteException $e ) {
            echo 'Une erreur interne s\est produite' . "<br />\n";
            if (\Conf::DEBUG) {
                echo $e->getMessage() . "<br />\n";
                echo $e->getFile() . ' (' . $e->getLine() . ')' . "<br />\n";
                echo $e->getTraceAsString();
            }
        }
    }

    private function showHand(int $idRound, String $playerPosition) {

        $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($idRound, $playerPosition);
        $cards = $oHand->getCards();
        sort($cards);
        foreach ( $cards as $card ) {
            $this->tplVars['myCards'] .= '<img src="' . \Conf::BASE_URL . '/img/cards/' . $card . '.png" id="mycard_' . $card . '" />';
        }
    }

    /**
     * Vérification préliminaire lors d'une action soumise dans le jeu
     *
     * @param String $hashGame
     * @param array $requestedGameStep
     * @param String $playerPosition
     * @throws \Exceptions\BeloteException
     * @return \Entities\Game|NULL
     */
    private function checkActionRequestAndGetGame(String $hashGame, array $requestedGameStep, String &$playerPosition): ?\Entities\Game {
        $jwtBeloteCookie = \Services\JwtCookie::get()->getBeloteGameCookie($hashGame);
        // On vérifie que le joueur participe bien à cette partie
        if ($jwtBeloteCookie == null || $jwtBeloteCookie->getHashGame() != $hashGame) {
            throw new \Exceptions\BeloteException('Vous ne participez pas à ce jeu');
        }
        $playerPosition = $jwtBeloteCookie->getPlayerPosition();
        $oGame = \Repositories\DbGame::get()->findOneByHash($hashGame);

        // On vérifie qu'on est bien à cette étape du jeu
        if (!in_array($oGame->getStep(), $requestedGameStep) || $oGame->getCurrent_player() != $playerPosition) {
            throw new \Exceptions\BeloteException('Action  impossible à ce moment du jeu');
        }

        return $oGame;
    }

    public function cutDeck(String $hashGame, int $cutValue): array {
        $playerPosition = '';
        try {
            // Vérification qu'on peut bien faire cette action
            $oGame = $this->checkActionRequestAndGetGame($hashGame, [
                \Entities\Game::STEP_CUT_DECK
            ], $playerPosition);

            // On récupère la manche
            $oRound = \Repositories\DbRound::get()->findOneById($oGame->getId_current_round());

            // On défini le prochain joueur
            $oGame->setCurrent_player(\Services\Utils::getNextPlayerFromOne($oRound->getDealer()));

            // On coupe le deck
            \Services\Game::get()->cutDeck($oGame, $cutValue);

            // On distribue les cartes
            $proposedTrumpCard = \Services\Game::get()->dealCardsBeforeTrumpChoose($oGame, $oRound);

            // On envoie les mains à chaque joueur via mercure
            foreach ( array_keys(\PLAYERS) as $player ) {
                $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oGame->getId_current_round(), $player);
                $cards = $oHand->getCards();
                sort($cards);
                \Services\Mercure::get()->notifyRoundStart($oGame->getHash(), $oRound->getNum_round(), \Services\Utils::getNextPlayerFromOne($oRound->getDealer()), $player, $cards, $proposedTrumpCard);
            }
        } catch ( \Exceptions\CutOutOfRange $e ) {
            // On répond à la requete
            return array(
                'response' => 'error',
                'error_msg' => 'Vous ne pouvez pas couper en dehors du deck'
            );
        } catch ( \Exceptions\BeloteException $e ) {
            // On répond à la requete
            return array(
                'response' => 'error',
                'error_msg' => $e->getMessage()
            );
        }
        // On répond à la requete
        return array(
            'response' => 'ok'
        );
    }

    /**
     * Action lors du choix de l'atout (choix atout ou passer)
     *
     * @param String $hashGame
     * @param String $trumpColor
     * @return array
     */
    public function chooseTrump(String $hashGame, String $trumpColor): array {
        try {
            $playerPosition = '';
            $oGame = $this->checkActionRequestAndGetGame($hashGame, [
                \Entities\Game::STEP_CHOOSE_TRUMP,
                \Entities\Game::STEP_CHOOSE_TRUMP_2
            ], $playerPosition);

            // On vérifie que la couleur demandée est valide
            $trumpColor = strtolower($trumpColor);
            if ($trumpColor != 'pass' && !in_array($trumpColor, \CARDS_COLORS)) {
                return array(
                    'response' => 'error',
                    'error_msg' => 'Couleur inconnue'
                );
            }

            // On vérifie qu'on essaye de prendre l'atout proposé au tour de choix 1 ou un autre que celui proposé au tour de choix 2
            if ($trumpColor != 'pass' && (($oGame->getStep() == \Entities\Game::STEP_CHOOSE_TRUMP && $trumpColor != \CARDS_COLORS[substr($oGame->getCards()[0], 0, 1)]) || ($oGame->getStep() == \Entities\Game::STEP_CHOOSE_TRUMP && $trumpColor == substr($oGame->getCards()[0], 0, 1)))) {
                return array(
                    'response' => 'error',
                    'error_msg' => 'Couleur impossible à ce tour de choix'
                );
            }

            $oRound = \Repositories\DbRound::get()->findOneById($oGame->getId_current_round());

            if ($trumpColor == 'pass') {
                $newCurrentPlayer = \Services\Utils::getNextPlayerFromOne($playerPosition);
                $oGame->setCurrent_player($newCurrentPlayer);
                // On regarde si on passe au deuxième tour de passe ou si tout le monde a passé 2 fois
                if ($oRound->getDealer() == $playerPosition) {
                    if ($oGame->getStep() == \Entities\Game::STEP_CHOOSE_TRUMP_2) {
                        // Personne ne prend d'atout, il faut redistribuer les cartes
                        \Services\Game::get()->noTrumpChosen($oGame, $oRound);

                        // On repasse à l'étape de coupe
                        \Services\Mercure::get()->notifyRecutDeck($hashGame, $oGame->getCurrent_player());
                    } else {
                        // On passe au deuxième tour de choix d'atout
                        $oGame->setStep(\Entities\Game::STEP_CHOOSE_TRUMP_2);
                    }
                }

                \Repositories\DbGame::get()->update($oGame);

                // Si on ne doit pas redistribuer les cartes, on indique que c'est au joueur suivant de s'exprimer
                if ($oGame->getStep() != \Entities\Game::STEP_CUT_DECK) {
                    \Services\Mercure::get()->notifyChooseTrumpPassed($oGame->getHash(), $playerPosition, $newCurrentPlayer, ($oGame->getStep() == \Entities\Game::STEP_CHOOSE_TRUMP));
                }
            } else {
                // Si on ne passe c'est qu'on prend une couleur
                \Services\Game::get()->chooseTrumpAndDeal($oGame, $oRound, array_search($trumpColor, \CARDS_COLORS), $playerPosition);
                // On envoie les mains à chaque joueur via mercure
                foreach ( array_keys(\PLAYERS) as $player ) {
                    $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oGame->getId_current_round(), $player);
                    $cards = $oHand->getCards();
                    sort($cards);
                    \Services\Mercure::get()->notifyChosenTrump($oGame->getHash(), array_search($trumpColor, \CARDS_COLORS), $playerPosition, $oGame->getCurrent_player(), $player, $cards);
                }
            }
        } catch ( \Exceptions\BeloteException $e ) {
            // On répond à la requete
            return array(
                'response' => 'error',
                'error_msg' => $e->getMessage()
            );
        }

        // On répond à la requete
        return array(
            'response' => 'ok'
        );
    }

    public function playCard(String $hashGame, String $card) {
        try {
            $playerPosition = '';
            $oGame = $this->checkActionRequestAndGetGame($hashGame, [
                \Entities\Game::STEP_PLAY_CARD
            ], $playerPosition);

            $oRound = \Repositories\DbRound::get()->findOneById($oGame->getId_current_round());
            $oTurn = \Services\Game::get()->playCard($oRound->getId_current_turn(), $playerPosition, $card);

            // on calcule la position de la carte (1, 2 3 ou 4) dans le tour
            $cardPosition = 4;
            $tNextPlayer = \Services\Utils::getNextPlayerFromOne($playerPosition);

            while ( $oTurn->getFirst_player() != $tNextPlayer ) {
                $cardPosition--;
                $tNextPlayer = \Services\Utils::getNextPlayerFromOne($tNextPlayer);
            }

            // On regarde si le tour est terminé
            if (\Services\Utils::getNextPlayerFromOne($playerPosition) == $oTurn->getFirst_player()) {
                $winner = \Services\Game::get()->calculateTurnWinner($oRound, $oTurn);
                $oGame->setCurrent_player($winner);
                try {
                    // On démarre un nouveau tour, lève une exception catchée plus bas si la manche est terminée
                    $oNewTurn = \Services\Game::get()->startNewTurn($oRound, $oTurn);

                    // On notifie de la fin de tour, et on commence un nouveau tour
                    \Services\Mercure::get()->notifyChangeTurn($oGame->getHash(), $playerPosition, $cardPosition, $card, $winner, $oNewTurn->getNum_turn());
                } catch ( \Exceptions\TurnNumberOutofBound $e ) {

                    // Si la manche est terminée, on la clot et on enregistre au préalable le tour en cours                    
                    \Repositories\DbTurn::get()->update($oTurn);
                    $isGameFinished = \Services\Game::get()->closeRound($oGame, $oRound);
                    $points = array();
                    $points['numRound'] = $oRound->getNum_round();
                    $points['pointsNS'] = $oRound->getPoints_ns();
                    $points['totalPointsNS'] = $oGame->getTotal_points_ns();
                    $points['pointsWE'] = $oRound->getPoints_we();
                    $points['totalPointsWE'] = $oGame->getTotal_points_we();

                    if ($isGameFinished) {
                        \Services\Mercure::get()->notifyGameEnd($oGame->getHash(), $playerPosition, $cardPosition, $card, $winner, $points);
                        $oGame->setStep(\Entities\Game::STEP_FINISHED);
                    } else {
                        // on en démarre une nouvelle manche
                        $oNewRound = \Services\Game::get()->startNewRound($oGame, $oRound);

                        \Services\Mercure::get()->notifyChangeRound($oGame->getHash(), $playerPosition, $cardPosition, $card, $winner, $oNewRound->getNum_Round(), $oNewRound->getDealer(), \Services\Utils::getPrecedentPlayerFromOne($oNewRound->getDealer()), $points);
                    }
                }
            } else {

                // sinon, On notifie la carte jouée et qui est le prochain joueur

                $oGame->setCurrent_player(\Services\Utils::getNextPlayerFromOne($playerPosition));

                \Services\Mercure::get()->notifyCardPlayed($oGame->getHash(), $playerPosition, $cardPosition, $card, $oGame->getCurrent_player());
            }
            // On enregistre les mises à jour en base
            \Repositories\DbGame::get()->update($oGame);
            \Repositories\DbRound::get()->update($oRound);
            \Repositories\DbTurn::get()->update($oTurn);
        } catch ( \Exceptions\BeloteException $e ) {
            // On répond à la requete
            return array(
                'response' => 'error',
                'error_msg' => $e->getMessage()
            );
        }

        // On répond à la requete
        return array(
            'response' => 'ok'
        );
    }
}
