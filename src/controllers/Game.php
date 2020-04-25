<?php

namespace Controllers;

class Game extends AbstractController {

    public function showJoinPage(String $hashGame, ?String $jwtCookie = null) {
        try {
            $jwtBeloteCookie = \Services\JwtCookie::get()->getBeloteGameCookie($hashGame);
            $oGame = \Repositories\DbGame::get()->findOneByHash($hashGame);

            // Si on a déjà le cookie du jeu
            if ($jwtBeloteCookie != null) {
                $this->tplVars['html_disabled_pseudo'] = 'disabled="disabled"';
                $this->tplVars['html_disabled_s'] = 'disabled="disabled"';
                $this->tplVars['html_disabled_w'] = 'disabled="disabled"';
                $this->tplVars['html_disabled_e'] = 'disabled="disabled"';
                switch ($jwtBeloteCookie->getPlayerPosition()) {
                    case 'n' :
                        $this->tplVars['currentPlayerName'] = $oGame->getName_north();
                        break;
                    case 'w' :
                        $this->tplVars['currentPlayerName'] = $oGame->getName_west();
                        break;
                    case 'e' :
                        $this->tplVars['currentPlayerName'] = $oGame->getName_east();
                        break;
                    case 's' :
                        $this->tplVars['currentPlayerName'] = $oGame->getName_south();
                        break;
                }

                // Si le jeu a démarré on renvoi vers le board
                if ($oGame->getName_north() != '' && $oGame->getName_south() != '' && $oGame->getName_west() != '' && $oGame->getName_east() != '') {
                    header('Location: /belote/play/' . $hashGame);
                }
            } else {
                $this->tplVars['html_disabled'] = '';
                $this->tplVars['currentPlayerName'] = '';
            }

            // On positionne un cookie pour écouter les events mercure
            \Services\JwtCookie::get()->setOrUpdateMercureJoinCookie($hashGame);
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
        \Services\JwtCookie::get()->setOrUpdateMercureJoinCookie($oGame->getHash(), 'N');
        \Services\JwtCookie::get()->setOrUpdateBeloteGameCookie($oGame->getHash(), 'N');

        // On redirige vers la salle d'attente
        header('Location: /belote/join/' . $oGame->getHash());
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
        \Services\JwtCookie::get()->setOrUpdateMercureJoinCookie($oGame->getHash(), $playerPosition);
        \Services\JwtCookie::get()->setOrUpdateBeloteGameCookie($oGame->getHash(), $playerPosition);

        // On notifie via mercure les autres joueurs de l'arrivée du joueur
        \Services\Mercure::get()->notifyGamePlayerJoin($hashGame, $playerPosition, $playerName);

        // si on a tous les joueurs, on démarre la partie
        if ($oGame->getName_north() != '' && $oGame->getName_south() != '' && $oGame->getName_west() != '' && $oGame->getName_east() != '') {
            \Services\Game::get()->startNewRound($oGame);
            \Services\Mercure::get()->notifyGameStart($hashGame);
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
            // Si on a déjà le cookie du jeu
            if ($jwtBeloteCookie != null) {
                $this->tplName = 'gameboard.tpl.html';
                $this->tplVars['hashGame'] = $hashGame;
                $this->tplVars['idRound'] = $oGame->getId_current_round();
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

                // On remet le cookie mercure au cas où il aurait disparu
                \Services\JwtCookie::get()->setOrUpdateMercureJoinCookie($hashGame, $jwtBeloteCookie->getPlayerPosition());

                // TODO
                // - points

                // - action en cours
                $this->tplVars['currentPlayer'] = $this->tplVars['playerName_' . $oGame->getCurrent_player()];
                $this->tplVars['hideChooseTrump'] = 'hidden';
                $this->tplVars['hideCutDeck'] = 'hidden';
                $this->tplVars['hidePlayCardBtn'] = 'disabled="disabled"';

                switch ($oGame->getStep()) {
                    case \Entities\Game::STEP_CUT_DECK :
                        if ($jwtBeloteCookie->getPlayerPosition() == $oGame->getCurrent_player()) {
                            $this->tplVars['hideCutDeck'] = '';
                        }
                        break;
                    case \Entities\Game::STEP_CHOOSE_TRUMP :
                        $this->showHand($oGame->getId_current_round(), $jwtBeloteCookie->getPlayerPosition());
                        $this->tplVars['hideChooseTrump'] = '';
                        $this->tplVars['proposedTrumpCard'] = $oGame->getCards()[0];
                        $this->tplVars['trumpHeartDisabled'] = 'disabled';
                        $this->tplVars['trumpDiamondDisabled'] = 'disabled';
                        $this->tplVars['trumpClubDisabled'] = 'disabled';
                        $this->tplVars['trumpSpadeDisabled'] = 'disabled';
                        if ($jwtBeloteCookie->getPlayerPosition() == $oGame->getCurrent_player()) {
                            $this->tplVars['trump'.ucfirst(\CARDS_COLORS[substr($oGame->getCards()[0],0,1)]).'Disabled'] = '';
                        }
                        break;

                    // TODO autres etapes
                }

                parent::renderPage();
            } else {
                // On redirige vers la salle d'attente
                header('Location: /belote/join/' . $oGame->getHash());
            }
        } catch ( \Exceptions\BeloteException $e ) {
            throw new \Exceptions\BeloteException('Id partie inconnu');
        }
    }

    private function showHand(int $idRound, String $playerPosition){
        $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($idRound, $playerPosition);
        foreach ( $oHand->getCards() as $card ) {
            $this->tplVars['myCards'] .= '<img src="' . \BASE_URL . '/img/cards/' . $card . '.png" id="mycard_' . $card . '" />';
        }
    }

    private function checkActionRequestAndGetGame(String $hashGame, String $gameStep, String &$playerPosition): ?\Entities\Game {
        $jwtBeloteCookie = \Services\JwtCookie::get()->getBeloteGameCookie($hashGame);
        // On vérifie que le joueur participe bien à cette partie
        if ($jwtBeloteCookie == null || $jwtBeloteCookie->getHashGame() != $hashGame) {
            throw new \Exceptions\BeloteException('Vous ne participez pas à ce jeu');
        }
        $playerPosition = $jwtBeloteCookie->getPlayerPosition();
        $oGame = \Repositories\DbGame::get()->findOneByHash($hashGame);

        // On vérifie qu'on est bien à cette étape du jeu

        if ($oGame->getStep() != $gameStep || $oGame->getCurrent_player() != $playerPosition) {
            throw new \Exceptions\BeloteException('Action ' . $gameStep . ' impossible à ce moment du jeu');
        }

        return $oGame;
    }

    public function cutDeck(String $hashGame, int $cutValue): array {
        $playerPosition = '';
        try {
            // Vérification qu'on peut bien faire cette action
            $oGame = $this->checkActionRequestAndGetGame($hashGame, \Entities\Game::STEP_CUT_DECK, $playerPosition);

            // On récupère la manche
            $oRound = \Repositories\DbRound::get()->findOneById($oGame->getId_current_round());

            // On coupe le deck
            \Services\Game::get()->cutDeck($oGame, $cutValue);

            // On distribue les cartes
            $proposedTrumpCard = \Services\Game::get()->dealCards($oGame, $oRound);

            // On envoie les mains à chaque joueur via mercure
            foreach ( \PLAYERS as $player ) {
                $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oGame->getId_current_round(), $player);
                \Services\Mercure::get()->notifyRoundStart($oGame->getHash(), $oRound->getNum_round(), \Services\Game::get()->getNextPlayerFromOne($oRound->getDealer()), $player, $oHand->getCards(), $proposedTrumpCard);
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

    public function chooseTrump(String $hashGame, String $trumpColor): array {
        try {
            $playerPosition = '';
            $oGame = $this->checkActionRequestAndGetGame($hashGame, \Entities\Game::STEP_CHOOSE_TRUMP, $playerPosition);

            // On vérifie que la couleur demandée est valide
            $trumpColor = strtolower($trumpColor);
            if ($trumpColor != 'pass' && !in_array($trumpColor, \CARDS_COLORS)) {
                return array(
                    'response' => 'error',
                    'error_msg' => 'Couleur inconnue'
                );
            }

            if ($trumpColor == 'pass') {
                $oGame->setCurrentPlayer(\Services\Game::get()->getNextPlayerFromOne($playerPosition));
                // TODO mercure event
            } else {
                \Services\Game::get()->chooseTrumpAndDeal($oGame->getId_current_round(), $trumpColor, $playerPosition);
                // TODO mercure event
            }
        } catch ( \Exceptions\BeloteException $e ) {
            // On répond à la requete
            return array(
                'response' => 'error',
                'error_msg' => $e->getMEssage()
            );
        }

        // On répond à la requete
        return array(
            'response' => 'ok'
        );
    }
}