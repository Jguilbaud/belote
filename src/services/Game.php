<?php

namespace Services;

class Game extends StaticAccessClass {

    /**
     * Pique : Spade
     * Coeur : Heart
     * Carreau : Diamond
     * Trèfles : Club
     *
     * @var array
     */


    public function create(?\Entities\Game &$oGame = null): int {
        $deck = DECK_CARDS_LIST;
        shuffle($deck);
        // On créé la partie en base
        if($oGame == null){
            $oGame = new \Entities\Game();
        }
        $oGame->setHash(Utils::generateHash(10));
        $oGame->setCartes($deck);
        \Repositories\DbGame::get()->create($oGame);

        return $oGame->getId();
    }

    public function startNewRound(int $idGame): \Entities\Round {
        // On récupère la partie pour avoir notamment le deck
        $oGame = \Repositories\DbGame::get()->findOneById($idGame);

        // On créé la nouvelle manche
        $oRound = new \Entities\Round();
        $oRound->setId_Partie($oGame->getId());

        // Cas première manche
        if ($oGame->getId_manche_courante() == 0) {
            $dealer = \PLAYERS[rand(0, 3)];
            $newRound = 1;
        } else {
            // On récupère le précédent donneur
            $oPrecedentRound = \Repositories\DbRound::get()->findOneById($oGame->getId_manche_courante());
            $newRound = $oPrecedentRound->getNum_manche() + 1;
            $dealer = $this->getNextPlayerFromOne($oPrecedentRound->getDonneur());
        }
        $oRound->setDonneur($dealer);
        $oRound->setNum_manche($newRound);
        \Repositories\DbRound::get()->create($oRound);

        // On met à jour la partie
        $oGame->setId_manche_courante($oRound->getId());
        \Repositories\DbGame::get()->update($oGame);

        return $oRound;
    }

    public function startNewTurn(int $idRound, String $firstPlayer): \Entities\Turn {
        $oRound = \Repositories\DbRound::get()->findOneById($idRound);

        $oTurn = new \Entities\Turn();
        $oTurn->setId_manche($idRound);
        $oTurn->setPremier_joueur($firstPlayer);

        // On calcule le numéro de tour
        if ($oRound->getId_tour_courant() == 0) {
            $newTurn = 1;
        } else {
            // On récupère le précédent donneur
            $oPrecedentTurn = \Repositories\DbTurn::get()->findOneById($oRound->getId_tour_courant());
            $newTurn = $oPrecedentTurn->getNum_tour() + 1;
            // On détecte si on essaye pas de créer une manche de trop
            if ($newTurn >= 9) {
                throw new \Exceptions\TurnNumberOutofBound();
            }
        }
        $oTurn->setNum_tour($newTurn);
        \Repositories\DbTurn::get()->create($oTurn);

        // On met à jour la partie
        $oRound->setId_tour_courant($oTurn->getId());
        \Repositories\DbRound::get()->update($oRound);

        return $oTurn;
    }

    public function getNextPlayerFromOne(String $currentPlayer = 'N') {
        switch ($currentPlayer) {
            case 'N' :
                return 'E';
            case 'E' :
                return 'S';
            case 'S' :
                return 'O';
            case 'O' :
                return 'N';
        }
    }

    public function getPrecedentPlayerFromOne(String $currentPlayer = 'N') {
        switch ($currentPlayer) {
            case 'N' :
                return 'O';
            case 'E' :
                return 'N';
            case 'S' :
                return 'E';
            case 'O' :
                return 'S';
        }
    }

    /**
     * Distribue à chaque joueur 3 puis 2 cartes et retourne la carte proposée comme atout
     * @param int $idRound
     * @return String Carte proposée comme atout
     */
    public function dealCards(int $idRound) : String {
        $oRound = \Repositories\DbRound::get()->findOneById($idRound);
        $oGame = \Repositories\DbGame::get()->findOneById($oRound->getId_partie());

        $currentDealedPlayer = $oRound->getDonneur();
        // On donne les 3 + 2 cartes à chacun
        for($i = 0; $i < 4; $i++) {
            $currentDealedPlayer = $this->getNextPlayerFromOne($currentDealedPlayer);

            $arrayPart1 = array_slice($oGame->getCartes(), $i * 3, 3, true);
            $arrayPart2 = array_slice($oGame->getCartes(), $i * 2 + 12, 2, true);

            $oHand = new \Entities\Hand();
            $oHand->setId_manche($idRound);
            $oHand->setJoueur($currentDealedPlayer);
            $oHand->setCartes(array_merge($arrayPart1, $arrayPart2));
            \Repositories\DbHand::get()->create($oHand);
        }

        // On enleve du deck les cartes ditribuées
        $oGame->setCartes(array_slice($oGame->getCartes(), 20, 32, true));
        \Repositories\DbGame::get()->update($oGame);

        return array_values($oGame->getCartes())[0];
    }

    /**
     * Prendre l'atout
     *
     * @param String $color
     * @param String $player
     */
    public function takeTrumpAndDeal(int $idRound, String $color, String $player) {
        $oRound = \Repositories\DbRound::get()->findOneById($idRound);
        $oGame = \Repositories\DbGame::get()->findOneById($oRound->getId_partie());
        $oRound->setPreneur($player);
        $oRound->setAtout($color[0]);

        $cardsOffset = 1;
        $currentDealedPlayer = $oRound->getDonneur();
        // On donne les 3 + 2 cartes à chacun
        for($i = 0; $i < 4; $i++) {
            $currentDealedPlayer = $this->getNextPlayerFromOne($currentDealedPlayer);

            // On récupère la main du joueur
            $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oRound->getId(), $currentDealedPlayer);

            // Si c'est le preneur
            if ($currentDealedPlayer == $player) {
                $nbCards = 2;
                $newCards = array(
                    $oGame->getCartes()[0]
                );
            } else {
                $nbCards = 3;
                $newCards = array();
            }
            $newCards = array_merge($newCards, array_slice($oGame->getCartes(), $cardsOffset, $nbCards));
            $oHand->setCartes(array_merge($oHand->getCartes(), $newCards));
            \Repositories\DbHand::get()->update($oHand);

            $cardsOffset += $nbCards;
        }

        // On enleve du deck les cartes ditribuées, c'est à dire toutes :)
        $oGame->setCartes(array());
        \Repositories\DbGame::get()->update($oGame);

        \Repositories\DbRound::get()->update($oRound);
        \Repositories\DbGame::get()->update($oGame);
    }


    public function cutDeck(int $idGame, int $cut = 16): void {
        if ($cut < 0 || $cut > 31) {
            throw new \Exceptions\CutOutOfRange();
        }

        $oGame = \Repositories\DbGame::get()->findOneById($idGame);
        $arrayPart1 = array_slice($oGame->getCartes(), 0, $cut, true);
        $arrayPart2 = array_slice($oGame->getCartes(), $cut, 32, true);
        $oGame->setCartes(array_merge($arrayPart2, $arrayPart1));
        \Repositories\DbGame::get()->update($oGame);
    }

    /**
     *
     * @param int $idTurn
     * @param String $player
     * @param String $card
     * @throws \Exceptions\PlayerHasAlreadyPlayedCard
     * @throws \Exceptions\IllegalCard
     * @return
     */
    public function playCard(int $idTurn, String $player, String $card): \Entities\Turn {

        // On récupère le tour de jeu
        $oTurn = \Repositories\DbTurn::get()->findOneById($idTurn);

        // On récupère la main du joueur
        $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oTurn->getId_manche(), $player);

        // On vérifie qu'il n'a pas déjà joué une carte
        $method = 'getCarte_' . strtolower($player);
        if ($oTurn->$method() != '') {
            throw new \Exceptions\PlayerHasAlreadyPlayedCard();
        }
        // On vérifie que le joueur a bien cette carte en main
        if (!in_array($card, $oHand->getCartes())) {
            throw new \Exceptions\IllegalCard();
        }
        $method = 'setCarte_' . strtolower($player);

        // On joue la carte du joueur
        $oTurn->$method($card);
        // On enlève la carte de la main du joueur
        $handCards = $oHand->getCartes();
        if (($key = array_search($card, $handCards)) !== false) {
            unset($handCards[$key]);
            $oHand->setCartes($handCards);
        }
        \Repositories\DbHand::get()->update($oHand);
        \Repositories\DbTurn::get()->update($oTurn);
        return $oTurn;
    }

    public function calculateTurnWinner(\Entities\Turn &$oTurn) {
        if ($oTurn->getCarte_n() == '' || $oTurn->getCarte_e() == '' || $oTurn->getCarte_s() == '' || $oTurn->getCarte_o() == '') {
            throw new \Exceptions\TurnIsIncomplete();
        }

        // On récupère la manche
        $oRound = \Repositories\DbRound::get()->findOneById($oTurn->getId_manche());

        $currentPlayer = $oTurn->getPremier_joueur();
        $method = 'getCarte_' . strtolower($currentPlayer);
        $askedColor = $oTurn->$method()[0];
        $bestCardColor = $oTurn->$method()[0];
        $bestCardChar = substr($oTurn->$method(), 1);
        if ($bestCardColor == strtolower($oRound->getAtout())) {
            $bestCardValue = \CARDS_TRUMP_VALUES[$bestCardChar];
        } else {
            $bestCardValue = \CARDS_VALUES[substr($oTurn->$method(), 1)];
        }

        $bestPlayer = $currentPlayer;
        // On parcoure les cartes jouées par les autres joueurs
        for($i = 0; $i < 3; $i++) {
            // On passe au joueur suivant
            $currentPlayer = $this->getNextPlayerFromOne($currentPlayer);

            // On regarde si c'est la couleur demandée n'est pas de l'atout que l'atout n'est pas la meilleure carte du pli
            $method = 'getCarte_' . strtolower($currentPlayer);
            $currentColor = $oTurn->$method()[0];
            $currentValue = substr($oTurn->$method(), 1);
            // Si c'est la couleur demandée et que ce n'est pas de l'atout
            if ($currentColor == $askedColor && $bestCardColor != strtolower($oRound->getAtout())) {
                if (\CARDS_VALUES[$currentValue] > $bestCardValue) {
                    $bestCardValue = \CARDS_VALUES[$currentValue];
                    $bestPlayer = $currentPlayer;
                }
            } elseif ($currentColor == strtolower($oRound->getAtout())) { // Sinon on joue de l'atout
                // Si la meilleure carte pour le moment n'est pas de l'atout,c'est la premiere coupe du pli
                // ou si de l'atout a déjà été joué, on regarde s'il est plus fort
                if ($bestCardColor != strtolower($oRound->getAtout()) || \CARDS_TRUMP_VALUES[$currentValue] > $bestCardValue) {
                    $bestCardValue = \CARDS_TRUMP_VALUES[$currentValue];
                    $bestPlayer = $currentPlayer;
                    $bestCardColor = $currentColor;
                }
            }
            // Sinon le joueur pisse, il ne peu donc pas remporter le pli
        }

        $oTurn->setVainqueur($bestPlayer);
        \Repositories\DbTurn::get()->update($oTurn);
        return $bestPlayer;
    }

    public function closeRound(int $idRound) : \Entities\Round{
        $oRound = \Repositories\DbRound::get()->findOneById($idRound);
        $oGame = \Repositories\DbGame::get()->findOneById($oRound->getId_partie());

        // On récupère les tours
        $turns = \Repositories\DbTurn::get()->findAll(array(), array(
            'id_manche' => $oRound->getId()
        ));


        $roundPointsNS = 0;
        $roundPointsOE = 0;
        $cardsWonByNS = array();
        $cardsWonByOE = array();
        $currentPlayer = $this->getNextPlayerFromOne($oRound->getDonneur());

        // On calcule les points de la manche et on reconstitue le deck
        foreach ( $turns as $oTurn ) {
            $turnPoints = 0;
            $turnCards = array();

            for($i=0;$i<4;$i++){
                $method = 'getCarte_' . strtolower($currentPlayer);
                $card = $oTurn->$method();
                $cardColor = $card[0];
                $cardChar = substr($card, 1);

                // On reconstitue les cartes du pli
                $turnCards[] = $card;

                // On calcule les points
                // - Si c'est la couleur d'atout
                if ($cardColor == $oRound->getAtout()) {
                    $turnPoints += \CARDS_TRUMP_VALUES[$cardChar];
                } else {
                    $turnPoints += \CARDS_VALUES[$cardChar];
                }
                // On passe au joueur suivant en fin de boucle
                $currentPlayer = $this->getNextPlayerFromOne($currentPlayer);
            }

            // - On donne les points et on met els cartes dans le tas de la bonne équipe
            if($oTurn->getVainqueur() == 'N' || $oTurn->getVainqueur() == 'S'){
                $roundPointsNS += $turnPoints;
                $cardsWonByNS = array_merge($turnCards,$cardsWonByNS);
            }else{
                $roundPointsOE += $turnPoints;
                $cardsWonByOE = array_merge($turnCards,$cardsWonByOE);
            }
            $currentPlayer = $oTurn->getVainqueur();
        }

        // On donne le 10 de DER au dernier vainqueur
        if($oTurn->getVainqueur() == 'N' || $oTurn->getVainqueur() == 'S' ){
            $roundPointsNS += 10;
        }else{
            $roundPointsOE += 10;
        }

        // On vérifie que le preneur a rempli son contrat
        if($oRound->getPreneur() == 'N' || $oRound->getPreneur() == 'S' ){
            $team = 'ns';
            $otherTeam = 'oe';
        }else{
            $team = 'oe';
            $otherTeam = 'ns';
        }
        // SI l'équipe preneuse a fait moins de point que l'équipe adverse, le contrat n'est pas rempli
        if(${'roundPoints'.strtoupper($team)} < ${'roundPoints'.strtoupper($otherTeam)}){
            ${'roundPoints'.strtoupper($team)} = 0;
            ${'roundPoints'.strtoupper($otherTeam)} = 162;
        }

        $oRound->setPoints_NS($roundPointsNS);
        $oRound->setPoints_OE($roundPointsOE);
        \Repositories\DbRound::get()->update($oRound);

        $totalPointNS = $oGame->getTotal_Points_NS()+$roundPointsNS;
        $totalPointOE = $oGame->getTotal_Points_OE()+$roundPointsOE;
        $oGame->setTotal_Points_NS($totalPointNS);
        $oGame->setTotal_Points_OE($totalPointOE);
        //On alterne quel paquet va sur l'autre
        if(rand(0,1)){
            $oGame->setCartes(array_merge($cardsWonByNS,$cardsWonByOE));
        }else{
            $oGame->setCartes(array_merge($cardsWonByOE,$cardsWonByNS));
        }


        \Repositories\DbGame::get()->update($oGame);


        // On vérifie que la partie n'est pas terminée
        if($totalPointNS >= 1000 || $totalPointOE >= 1000){
            throw new \Exceptions\GameIsFinished();
        }

        return $oRound;
    }
}



