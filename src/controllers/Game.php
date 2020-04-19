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
                switch ($jwtBeloteCookie->playerPosition) {
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

                //Si le jeu a démarré on renvoi vers le board
                if($oGame->getName_north() != '' && $oGame->getName_south() != '' && $oGame->getName_west() != '' && $oGame->getName_east() != ''){
                    header('Location: /belote/play/'.$hashGame);
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
        if($oGame->getName_south() != ''){
            $this->tplVars['playerNameSouth'] = $oGame->getName_south();
            $this->tplVars['html_disabled_s'] = 'disabled="disabled"';
        }
        if($oGame->getName_east() != ''){
            $this->tplVars['playerNameEast'] = $oGame->getName_east();
            $this->tplVars['html_disabled_e'] = 'disabled="disabled"';
        }
        if($oGame->getName_west() != ''){
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
        \Repositories\DbGame::get()->update($oGame);
        // On positionne les cookies
        \Services\JwtCookie::get()->setOrUpdateMercureJoinCookie($oGame->getHash(), $playerPosition);
        \Services\JwtCookie::get()->setOrUpdateBeloteGameCookie($oGame->getHash(), $playerPosition);

        // On notifie via mercure les autres joueurs de l'arrivée du joueur
        \Services\Mercure::get()->notifyGamePlayerJoin($hashGame, $playerPosition, $playerName);

        // si on a tous les joueurs, on démarre la partie
        if($oGame->getName_north() != '' && $oGame->getName_south() != '' && $oGame->getName_west() != '' && $oGame->getName_east() != ''){
            \Services\Mercure::get()->notifyGameStart($hashGame);
        }

        // On répond à la requete
        return array(
            'response' => 'ok'
        );
    }

    public function showPlayPage(String $hashGame){
        try {
            $jwtBeloteCookie = \Services\JwtCookie::get()->getBeloteGameCookie($hashGame);
            $oGame = \Repositories\DbGame::get()->findOneByHash($hashGame);
            // Si on a déjà le cookie du jeu
            if ($jwtBeloteCookie != null) {
                $this->tplName = 'gameboard.tpl.html';
                $this->tplVars['hashGame'] = $hashGame;
                $this->tplVars['playerName_n'] = $oGame->getName_north();
                $this->tplVars['playerName_s'] = $oGame->getName_south();
                $this->tplVars['playerName_w'] = $oGame->getName_west();
                $this->tplVars['playerName_e'] = $oGame->getName_east();
                if($jwtBeloteCookie->playerPosition != ''){
                    switch($jwtBeloteCookie->playerPosition){
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

                $this->tplVars['idRound'] = $oGame->getId_current_round();









                parent::renderPage();
            }else{
                // On redirige vers la salle d'attente
                header('Location: /belote/join/' . $oGame->getHash());
            }

        } catch ( \Exceptions\BeloteException $e ) {
            throw new \Exceptions\BeloteException('Id partie inconnu');
        }
    }
}