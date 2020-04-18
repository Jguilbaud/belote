<?php

namespace Controllers;

class Game extends AbstractController {

    public function showJoinPage(String $hashGame, ?String $jwtCookie = null) {
        try {
            $oGame = \Repositories\DbGame::get()->findOneByHash($hashGame);
        }catch(\Exceptions\BeloteException $e){
            throw new \Exceptions\BeloteException('Id partie inconnu');
        }


        $this->tplName = 'gamejoin.tpl.html';
        $this->tplVars['hashGame'] = $hashGame;
        $this->tplVars['playerNameNorth'] = $oGame->getNom_nord();

        parent::renderPage();

    }

    public function create(String $playerName) {
        // on créé une nouvelle partie
        $oGame = new \Entities\Game();
        $oGame->setNom_nord($playerName);
        \Services\Game::get()->create($oGame);

        // On positionne les cookies
        \Services\Mercure::get()->setJoinCookie($oGame->getHash(),'N');

        // On redirige vers la salle d'attente
        header('Location: /belote/join/' . $oGame->getHash());
    }

    public function join(String $hashGame, String $playerName, String $playerPosition) {
        $playerPosition = strtoupper($playerPosition);
        switch ($playerPosition) {
            case 'N' :
                $methodPartName = 'nord';
                break;
            case 'S' :
                $methodPartName = 'sud';
                break;
            case 'O' :
            case 'W' :
                $methodPartName = 'ouest';
                break;
            case 'E' :
                $methodPartName = 'est';
                break;
            default :
                return array('error' => 'Position de joueur invalide');
        }
        $methodSet = 'setNom_'.$methodPartName;
        $methodGet = 'getNom_'.$methodPartName;

        try {
            $oGame = \Repositories\DbGame::get()->findOneByHash($hashGame);
        }catch(\Exceptions\BeloteException $e){
            return array('error' => 'Id de Partie inconnue');
        }

        if(strtolower($oGame->$methodGet()) != $methodPartName){
            return array('error' => 'Position de joueur déjà utilisée');
        }

        $oGame->$methodSet($playerName);
      //DEBUG  \Repositories\DbGame::get()->update($oGame);

        // On positionne les cookies
        \Services\JwtCookie::get()->setMercureJoinCookie($oGame->getHash(),$playerPosition);

        // On notifie via mercure les autres joueurs
        \Services\Mercure::get()->notifyGamePlayerJoin($hashGame,$playerPosition,$playerName);

        //TODO si on a tous les joueurs, on démarre la partie

        // On répond à la requete
        return array('response' => 'ok');

    }
}